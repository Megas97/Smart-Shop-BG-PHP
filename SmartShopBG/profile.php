<?php
	require_once 'controllers/authController.php';
	
	if (!isset($_SESSION['id'])) {
		header('location: login.php');
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- Bootstrap 4 CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="styles.css">
		<title>My Profile</title>
		<script>
			function saveScrollPosition() {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				document.getElementById("scroll-top").value = scrollTop;
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
				<div class="col-sm-8 col-lg-5 form-div">
					<h3>Welcome, <?php echo $_SESSION['username']; ?></h3>
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
					<?php if (!$_SESSION['verified']): ?>
						<div class="alert alert-warning">
							You need to verify your account.
							Sign in to your email account and click on the
							verification link that we just emailed you at
							<strong><?php echo $_SESSION['email']; ?></strong>
						</div>
					<?php endif; ?>
					<label>Username: <?php echo $_SESSION['username']; ?></label>
					<br>
					<label>Email: <a href="mailto:<?php echo $_SESSION['email']; ?>"><?php echo $_SESSION['email']; ?></a></label>
					<br>
					<address>
						<label>Address: <?php echo $_SESSION['address']; ?></label>
						<div class="mapouter-profile">
							<div class="gmap_canvas-profile">
								<iframe width="376" height="376" src="https://maps.google.com/maps?q=<?php echo preg_replace( '~\s~', '%20', $_SESSION['address']); ?>&t=&z=17&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
							</div>
						</div>
					</address>
					<form method="post" action="profile.php">
						<input type='hidden' name="scroll-top" id="scroll-top" value="">
						<div class="form-group">
							<label for="address">Change address: </label>
							<input type="text" name="address" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('address', $errors)) ? $errors['address'] : ''; ?>">
						</div>
						<div class="form-group">
							<button type="submit" name="change-address-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition();">Change address</button>
						</div>
					</form>
					<hr>
					<form method="post" action="profile.php">
						<div class="form-group">
							<button type="button" class="btn btn-danger btn-block btn-lg" data-toggle="modal" data-target="#myModal">Delete account</button>
							<div class="modal" id="myModal">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title">Account deletion confirmation</h4>
											<button type="button" class="close" data-dismiss="modal">&times;</button>
										</div>
										<div class="modal-body">
											Are you sure you want to delete your account?<br>This action cannot be undone!
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
											<button type="submit" name="delete-account-btn" class="btn btn-danger">Delete</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>