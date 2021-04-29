<?php
	require_once 'controllers/adminController.php';
	
	if (!isset($_SESSION['id'])) {
		header('location: login.php');
		exit();
	}
	if (!isset($_SESSION['admin']) || (isset($_SESSION['admin']) && $_SESSION['admin'] === 0)) {
		$_SESSION['message'] = "You are not an administrator!<br>You will now be logged out!";
		$_SESSION['alert-class'] = "alert-danger";
		header('location: error.php');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<!-- Bootstrap 4 CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="styles.css">
		<title>Admin Panel</title>
		<script>
			function saveScrollPosition(action) {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				document.getElementById("scroll-top-" + action).value = scrollTop;
			}
			
			function restoreScroll() {
				<?php if (isset($_SESSION['scrollTop'])): ?>
					document.documentElement.scrollTop = document.body.scrollTop = <?php echo $_SESSION['scrollTop']; ?>;
					<?php unset($_SESSION['scrollTop']); ?>
				<?php endif; ?>
			}
		</script>
	</head>
	<body onload="restoreScroll();">
		<?php include 'navbar.php' ?>
		<div class="container">
			<div class="row">
				<div class="col-md-5 offset-md-4 form-div">
					<form method="post" action="admin.php" enctype="multipart/form-data">
						<h3 class="text-center">Upload new phone</h3>
						<input type="hidden" name="scroll-top-upload" id="scroll-top-upload" value="">
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
						<div class="form-group">
							<label for="make">Phone make: </label>
							<input type="text" name="make" value="<?php echo $make; ?>" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('make', $errors)) ? $errors['make'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="model">Phone model: </label>
							<input type="text" name="model" value="<?php echo $model; ?>" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('model', $errors)) ? $errors['model'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="price">Phone price: </label>
							<input type="number" step="0.01" min="0" lang="en" name="price" value="<?php echo $price; ?>" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('price', $errors)) ? $errors['price'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="image">Phone image: <span style="font-size: 0.76em;"><?php echo (array_key_exists('image', $errors)) ? $errors['image'] : ''; ?></span></label>
							<br>
							<input style="width: 100px;" type="file" name="image" accept="image/*">
						</div>
						<div class="form-group">
							<label for="type">Phone type: </label>
							<br>
							<select class="dropdown-full-width" name="type">
								<?php
									$selected1 = "";
									$selected2 = "";
									if ($selectedType == "smartphone") {
										$selected1 = "selected";
									} else if ($selectedType == "feature_phone") {
										$selected2 = "selected";
									}
								?>
								<option <?php echo $selected1; ?> value="smartphone">Smartphone</option>
								<option <?php echo $selected2; ?> value="feature_phone">Feature Phone</option>  
							</select>
						</div>
						<div class="form-group">
							<button type="submit" name="upload-phone-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition('upload');">Upload new phone</button>
						</div>
					</form>
				</div>
				<div class="col-md-5 offset-md-4 form-div">
					<form method="post" action="admin.php">
						<h3 class="text-center">Change phone price</h3>
						<input type="hidden" name="scroll-top-change" id="scroll-top-change" value="">
						<?php if (isset($_SESSION['message2'])): ?>
							<div class="alert <?php echo $_SESSION['alert-class2']; ?> center-message">
								<?php
									echo $_SESSION['message2'];
									unset($_SESSION['message2']);
									unset($_SESSION['alert-class2']);
								?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<div class="form-group">
							<label for="phonesList1">Choose phone: <span style="font-size: 0.76em;"><?php echo (array_key_exists('phone', $errors)) ? $errors['phone'] : ''; ?></span></label>
							<br>
							<select class="dropdown-full-width" name="phonesList1">
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'smartphone' LIMIT 1";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									if (mysqli_num_rows($results) > 0) {
										?>
											<option disabled>Smartphones:</option>
										<?php
									}
								?>
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'smartphone' ORDER BY make ASC";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									$selected = "";
									foreach ($results as $result) {
										if ($selectedPhone == $result['id']) {
											$selected = "selected";
										} else {
											$selected = "";
										}
										echo "<option " . $selected . " value='" . $result['id'] . "'>" . $result['make'] . " " . $result['model'] . "</option>";
									}
								?>
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'feature_phone' LIMIT 1";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									if (mysqli_num_rows($results) > 0) {
										$sql = "SELECT * FROM phones WHERE type = 'smartphone' LIMIT 1";
										$statement = $conn->prepare($sql);
										$statement->execute();
										$results = $statement->get_result();
										$statement->close();
										if (mysqli_num_rows($results) > 0) {
											?>
												<option disabled></option>
											<?php
										}
										?>
											<option disabled>Feature Phones:</option>
										<?php
									}
								?>
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'feature_phone' ORDER BY make ASC";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									$selected = "";
									foreach ($results as $result) {
										if ($selectedPhone == $result['id']) {
											$selected = "selected";
										} else {
											$selected = "";
										}
										echo "<option " . $selected . " value='" . $result['id'] . "'>" . $result['make'] . " " . $result['model'] . "</option>";
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="price">Phone price: </label>
							<input type="number" step="0.01" min="0" lang="en" name="price" value="<?php echo $price; ?>" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('price2', $errors)) ? $errors['price2'] : ''; ?>">
						</div>
						<div class="form-group">
							<button type="submit" name="change-phone-price-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition('change');">Change phone price</button>
						</div>
					</form>
					<hr style="height: 11px;">
					<form method="post" action="admin.php">
						<h3 class="text-center">Delete existing phone</h3>
						<input type="hidden" name="scroll-top-delete" id="scroll-top-delete" value="">
						<?php if (isset($_SESSION['message3'])): ?>
							<div class="alert <?php echo $_SESSION['alert-class3']; ?> center-message">
								<?php
									echo $_SESSION['message3'];
									unset($_SESSION['message3']);
									unset($_SESSION['alert-class3']);
								?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<div class="form-group">
							<label for="phonesList2">Choose phone: <span style="font-size: 0.76em;"><?php echo (array_key_exists('phone2', $errors)) ? $errors['phone2'] : ''; ?></span></label>
							<br>
							<select class="dropdown-full-width" name="phonesList2">
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'smartphone' LIMIT 1";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									if (mysqli_num_rows($results) > 0) {
										?>
											<option disabled>Smartphones:</option>
										<?php
									}
								?>
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'smartphone' ORDER BY make ASC";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									foreach ($results as $result) {
										echo "<option value='" . $result['id'] . "'>" . $result['make'] . " " . $result['model'] . "</option>";
									}
								?>
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'feature_phone' LIMIT 1";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									if (mysqli_num_rows($results) > 0) {
										$sql = "SELECT * FROM phones WHERE type = 'smartphone' LIMIT 1";
										$statement = $conn->prepare($sql);
										$statement->execute();
										$results = $statement->get_result();
										$statement->close();
										if (mysqli_num_rows($results) > 0) {
											?>
												<option disabled></option>
											<?php
										}
										?>
											<option disabled>Feature Phones:</option>
										<?php
									}
								?>
								<?php
									$sql = "SELECT * FROM phones WHERE type = 'feature_phone' ORDER BY make ASC";
									$statement = $conn->prepare($sql);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									foreach ($results as $result) {
										echo "<option value='" . $result['id'] . "'>" . $result['make'] . " " . $result['model'] . "</option>";
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<button type="submit" name="delete-phone-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition('delete');">Delete existing phone</button>
						</div>
					</form>
				</div>
				<div class="col-md-5 offset-md-4 form-div">
					<form method="post" action="admin.php">
						<h3 class="text-center">Give admin to user:</h3>
						<input type="hidden" name="scroll-top-give-admin" id="scroll-top-give-admin" value="">
						<?php if (isset($_SESSION['message4'])): ?>
							<div class="alert <?php echo $_SESSION['alert-class4']; ?> center-message">
								<?php
									echo $_SESSION['message4'];
									unset($_SESSION['message4']);
									unset($_SESSION['alert-class4']);
								?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<div class="form-group">
							<label for="usersList">Choose user: <span style="font-size: 0.76em;"><?php echo (array_key_exists('user', $errors)) ? $errors['user'] : ''; ?></span></label>
							<br>
							<select class="dropdown-full-width" name="usersList">
								<?php
									$sql = "SELECT * FROM users WHERE admin = 0 AND id != ? ORDER BY username ASC";
									$statement = $conn->prepare($sql);
									$statement->bind_param('i', $_SESSION['id']);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									foreach ($results as $result) {
										echo "<option value='" . $result['id'] . "'>" . $result['username'] . "</option>";
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<button type="submit" name="give-user-admin-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition('give-admin');">Give admin to user</button>
						</div>
					</form>
				</div>
				<div class="col-md-5 offset-md-4 form-div">
					<form method="post" action="admin.php">
						<h3 class="text-center">Revoke admin from user:</h3>
						<input type="hidden" name="scroll-top-revoke-admin" id="scroll-top-revoke-admin" value="">
						<?php if (isset($_SESSION['message5'])): ?>
							<div class="alert <?php echo $_SESSION['alert-class5']; ?> center-message">
								<?php
									echo $_SESSION['message5'];
									unset($_SESSION['message5']);
									unset($_SESSION['alert-class5']);
								?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<div class="form-group">
							<label for="usersList">Choose user: <span style="font-size: 0.76em;"><?php echo (array_key_exists('user2', $errors)) ? $errors['user2'] : ''; ?></span></label>
							<br>
							<select class="dropdown-full-width" name="usersList">
								<?php
									$sql = "SELECT * FROM users WHERE admin = 1 AND id != ? ORDER BY username ASC";
									$statement = $conn->prepare($sql);
									$statement->bind_param('i', $_SESSION['id']);
									$statement->execute();
									$results = $statement->get_result();
									$statement->close();
									foreach ($results as $result) {
										echo "<option value='" . $result['id'] . "'>" . $result['username'] . "</option>";
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<button type="submit" name="revoke-user-admin-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition('revoke-admin');">Revoke admin from user</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>