<?php require_once 'controllers/shopController.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<!-- Bootstrap 4 CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="styles.css">
		<title>Smartphones</title>
		<script>
			function processLikes(page_name, phone_id) {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				var selected_option = document.getElementById("phonesSelect").value;
				window.location = "<?php echo getCurrentURL(); ?>" + "?page=" + page_name + "&like=" + phone_id + "&scroll=" + scrollTop + "&option=" + selected_option;
			}
			
			function processDislikes(page_name, phone_id) {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				var selected_option = document.getElementById("phonesSelect").value;
				window.location = "<?php echo getCurrentURL(); ?>" + "?page=" + page_name + "&dislike=" + phone_id + "&scroll=" + scrollTop + "&option=" + selected_option;
			}
			
			function saveScrollPosition(phone_id) {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				document.getElementById("scroll-top-" + phone_id).value = scrollTop;
			}
			
			function restoreScroll() {
				<?php if (isset($_SESSION['scrollTop'])): ?>
					document.documentElement.scrollTop = document.body.scrollTop = <?php echo $_SESSION['scrollTop']; ?>;
					<?php unset($_SESSION['scrollTop']); ?>
				<?php endif; ?>
			}
			
			function handleDropdownChange(dropdownForm) {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				document.getElementById("scroll-top").value = scrollTop;
				dropdownForm.form.submit();
			}
		</script>
	</head>
	<body onload="restoreScroll();">
		<?php include 'navbar.php' ?>
		<div class="container">
			<?php if (isset($_SESSION['message'])): ?>
				<div class="alert <?php echo $_SESSION['alert-class']; ?> center-message">
					<?php
						echo $_SESSION['message'];
						unset($_SESSION['message']);
						unset($_SESSION['alert-class']);
					?>
				</div>
			<?php else: ?>
				<div class="alert">&nbsp;</div>
			<?php endif; ?>
			<div id="phonesSelectDiv" class="col-sm-6 col-lg-4 col-xs-12">
				<form action="smartphones.php" method="post">
					<input type="hidden" name="page" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">
					<input type="hidden" name="scroll-top" id="scroll-top" value="">
					<select name="phonesSelect" id="phonesSelect" onchange="handleDropdownChange(this);">
						<?php if (isset($_SESSION['selected_option']) && $_SESSION['selected_option'] === "all_phones"): ?>
							<option value="all_phones" selected="selected">All Phones</option>
							<option value="liked_phones">Phones you have liked</option>
							<option value="disliked_phones">Phones you have disliked</option>
						<?php endif; ?>
						<?php if (isset($_SESSION['selected_option']) && $_SESSION['selected_option'] === "liked_phones"): ?>
							<option value="all_phones">All Phones</option>
							<option value="liked_phones" selected="selected">Phones you have liked</option>
							<option value="disliked_phones">Phones you have disliked</option>
						<?php endif; ?>
						<?php if (isset($_SESSION['selected_option']) && $_SESSION['selected_option'] === "disliked_phones"): ?>
							<option value="all_phones">All Phones</option>
							<option value="liked_phones">Phones you have liked</option>
							<option value="disliked_phones" selected="selected">Phones you have disliked</option>
						<?php endif; ?>
						<?php if (!isset($_SESSION['selected_option'])): ?>
							<option value="all_phones">All Phones</option>
							<option value="liked_phones">Phones you have liked</option>
							<option value="disliked_phones">Phones you have disliked</option>
						<?php endif; ?>
						<?php if (isset($_SESSION['selected_option'])) { unset($_SESSION['selected_option']); } ?>
					</select>
				</form>
			</div>
			<div class="row">
				<?php
					if ((isset($_SESSION['phones_to_show']) && $_SESSION['phones_to_show'] === "all_phones") || (!isset($_SESSION['phones_to_show']))) {
						$sql = "SELECT * FROM phones WHERE type = 'smartphone' ORDER BY make ASC";
					} elseif (isset($_SESSION['phones_to_show']) && $_SESSION['phones_to_show'] === "liked_phones") {
						$sql = "SELECT * FROM phones INNER JOIN votes ON phones.id = votes.phone_id WHERE phones.type = 'smartphone' AND votes.vote_type = 'like' AND votes.user_id = ? ORDER BY phones.make ASC";
					} elseif (isset($_SESSION['phones_to_show']) && $_SESSION['phones_to_show'] === "disliked_phones") {
						$sql = "SELECT * FROM phones INNER JOIN votes ON phones.id = votes.phone_id WHERE phones.type = 'smartphone' AND votes.vote_type = 'dislike' AND votes.user_id = ? ORDER BY phones.make ASC";
					}
					$statement = $conn->prepare($sql);
					if (isset($_SESSION['phones_to_show']) && $_SESSION['phones_to_show'] !== "all_phones") {
						$statement->bind_param('i', $_SESSION['id']);
					}
					$statement->execute();
					$results = $statement->get_result();
					$statement->close();
					if (isset($_SESSION['phones_to_show']) && $_SESSION['phones_to_show'] !== "all_phones") {
						$array = array();
						while($res = $results->fetch_array(MYSQLI_ASSOC)) {
							array_push($array, $res);
						}
						for ($i = 0; $i < count($array); $i++) {
							$array[$i]['id'] = $array[$i]['phone_id'];
							unset($array[$i]['phone_id']);
							unset($array[$i]['user_id']);
							unset($array[$i]['vote_type']);
						}
						$results = array_values($array);
					}
					if (isset($_SESSION['phones_to_show'])) {
						unset($_SESSION['phones_to_show']);
					}
					$total_percentage = 140; // same as the width of the container of the bar without the added widths of the like/dislike numbers
					foreach ($results as $result) {
						$like_phone_num = $result['likes'];
						$dislike_phone_num = $result['dislikes'];
						$sum = $like_phone_num + $dislike_phone_num;
						if ($like_phone_num === 0 && $dislike_phone_num === 0) {
							$like_phone_num = 1;
							$dislike_phone_num = 1;
							$sum = $like_phone_num + $dislike_phone_num;
							$like_percent = round($like_phone_num / $sum * $total_percentage);
							$dislike_percent = round($dislike_phone_num / $sum * $total_percentage);
							$like_phone_num = 0;
							$dislike_phone_num = 0;
						} else {
							$like_percent = round($like_phone_num / $sum * $total_percentage);
							$dislike_percent = round($dislike_phone_num / $sum * $total_percentage);
						}
						$message = "<div class='alert'>&nbsp;</div>";
						if (isset($_SESSION['message-' . $result['id']])) {
							$message = "<div class='alert " . $_SESSION['alert-class-' . $result['id']] . " center-message'>" . $_SESSION['message-' . $result['id']] . "</div>";
							unset($_SESSION['message-' . $result['id']]);
							unset($_SESSION['alert-class-' . $result['id']]);
						}
						echo "
							<div class='col-sm-6 col-lg-4 col-xs-12 phone-form'>
								<form action='smartphones.php' method='post'>
									<div style='text-align: center;'>
										" . $message . "
										<input type='hidden' name='phone-id' value='" . $result['id'] . "'>
										<input type='hidden' name='phone-make' value='" . $result['make'] . "'>
										<input type='hidden' name='phone-model' value='" . $result['model'] . "'>
										<input type='hidden' name='phone-image' value='" . $result['image'] . "'>
										<input type='hidden' name='phone-price' value='" . $result['price'] . "'>
										<input type='hidden' name='scroll-top-" . $result['id'] . "' id='scroll-top-" . $result['id'] . "' value=''>
										<input type='hidden' name='page' value='" . basename($_SERVER['PHP_SELF']) . "'>
										<p style='font-size: 1.2em;'>" . $result['make'] . " " . $result['model'] . "</p>
										<img src='" . $result['image'] . "' width='300' height='300'>
										<p style='font-size: 1.1em;'>" . number_format($result['price'], 2) . " BGN</p>
										<a href='https://en.wikipedia.org/wiki/" . preg_replace( '~\s~', '_', $result['make'] . " " . $result['model']) . "' target='_blank'>More Information</a>
										<br>
										<button type='button' name='like-phone-btn' class='btn btn-primary btn-lg like-button' onclick='processLikes(\"" . basename($_SERVER['PHP_SELF']) . "\", " . $result['id'] . ");'></button>
										<button type='submit' name='add-phone-to-cart-btn' class='btn btn-primary btn-lg' onclick='saveScrollPosition(" . $result['id'] . ");'>Add to cart</button>
										<button type='button' name='dislike-phone-btn' class='btn btn-primary btn-lg dislike-button' onclick='processDislikes(\"" . basename($_SERVER['PHP_SELF']) . "\", " . $result['id'] . ");'></button>
										<br>
										<div style='width: 236px; display: inline-block;'>
											<div style='width: 48px; float: left;'>" . $like_phone_num . "</div>
											<div style='width: " . $like_percent . "px; height: 10px; margin-top: 7px; background: green; float: left;'>&nbsp;</div>
											<div style='width: 48px; float: right;'>" . $dislike_phone_num . "</div>
											<div style='width: " . $dislike_percent . "px; height: 10px; margin-top: 7px; background: red; float: right;'>&nbsp;</div>
										</div>
									</div>
								</form>
							</div>
						";
					}
				?>
			</div>
		</div>
	</body>
</html>