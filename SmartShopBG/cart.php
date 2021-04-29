<?php
	require_once 'controllers/shopController.php';
	
	if (!isset($_SESSION['id'])) {
		header('location: login.php');
		exit();
	}
	
	if (empty($_SESSION['shopping_cart']) && !isset($_SESSION['has-just-finished-order'])) {
		$_SESSION['message'] = "Your shopping cart is empty";
		$_SESSION['alert-class'] = "alert-warning";
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- Bootstrap 4 CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="styles.css">
		<title>Cart</title>
		<script>
			function processPhoneAdd(phone_id) {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				window.location = "<?php echo getCurrentURL(); ?>" + "?add=" + phone_id + "&scroll=" + scrollTop;
			}
			
			function processPhoneRemove(phone_id) {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				window.location = "<?php echo getCurrentURL(); ?>" + "?remove=" + phone_id + "&scroll=" + scrollTop;
			}
			
			function restoreScroll() {
				<?php if (isset($_SESSION['scrollTop'])): ?>
					document.documentElement.scrollTop = document.body.scrollTop = <?php echo $_SESSION['scrollTop']; ?>;
					<?php unset($_SESSION['scrollTop']); ?>
				<?php endif; ?>
			}
		</script>
		<style>
			/* For some reason icon doesn't show if this code isn't here  */
			.add-button {
				background: transparent url('add.png');
				background-size: 100% 100%;
				width: 24px;
				height: 24px;
			}
		</style>
	</head>
	<body onload="restoreScroll();">
		<?php include 'navbar.php' ?>
		<div class="container">
			<div class="row">
				<div class="col-md-12 form-div center-message">
					<h3>Order Details</h3>
					<?php if (isset($_SESSION['message'])): ?>
						<div class="alert <?php echo $_SESSION['alert-class']; ?> center-message">
							<?php
								echo $_SESSION['message'];
								unset($_SESSION['message']);
								unset($_SESSION['alert-class']);
								unset($_SESSION['has-just-finished-order']);
							?>
						</div>
					<?php else: ?>
						<div class="alert">&nbsp;</div>
					<?php endif; ?>
					<?php
						if (!empty($_SESSION['shopping_cart'])) {
						?>
							<table>
								<thead>
									<tr>
										<th></th>
										<th>Phone Make</th>
										<th>Phone Model</th>
										<th>Phone Quantity</th>
										<th>Phone Single Price</th>
										<th>Phone Total Price</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$total = 0;
									foreach ($_SESSION['shopping_cart'] as $key => $value) {
										?>
											<tr>
												<input type="hidden" name="scroll-top" id="scroll-top" value="">
												<td><img src="<?php echo $value['phone_image'] ?>" style="width: 100px; height: 100px;"></td>
												<td><?php echo $value['phone_make'] ?></td>
												<td><?php echo $value['phone_model'] ?></td>
												<td><?php echo $value['phone_quantity'] ?></td>
												<td><?php echo number_format($value['phone_price'], 2) ?> BGN</td>
												<td><?php echo number_format($value['phone_quantity'] * $value['phone_price'], 2); ?> BGN</td>
												<td>
													<button type="button" class="btn add-button" onclick="javascript:processPhoneAdd(<?php echo $value['phone_id']; ?>);"></button>
													<a href="https://en.wikipedia.org/wiki/<?php echo preg_replace( '~\s~', '_', $value['phone_make'] . " " . $value['phone_model']); ?>" target="_blank">
														<button type="button" class="btn info-button"></button>
													</a>
													<button type="button" class="btn remove-button" onclick="javascript:processPhoneRemove(<?php echo $value['phone_id']; ?>);"></button>
												</td>
											</tr>
										<?php
											$total = $total + ($value['phone_quantity'] * $value['phone_price']);
									}
								?>
								</tbody>
							</table>
							<br>
							<p style="text-align: center;">
								Total Order Price: <?php echo number_format($total, 2); ?> BGN
							</p>
							<form action="cart.php" method="post">
								<button type="submit" name="finish-cart-order-btn" class="btn btn-primary btn-lg">Finish Order</button>
							</form>
							<br>
							</div>
						<?php
						}
					?>
			</div>
		</div>
	</body>
</html>