<?php
	require_once 'controllers/authController.php';
	
	if (isset($_SESSION['id'])) {
		$_SESSION['message'] = "You cannot access this page while logged in!<br>You will now be logged out!";
		$_SESSION['alert-class'] = "alert-warning";
		header('location: error.php');
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
		<title>Forgot Password</title>
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
				<div class="col-sm-8 col-lg-4 form-div login">
					<form action="forgot_password.php" method="post">
						<h3 class="text-center">Recover your password</h3>
						<input type="hidden" name="scroll-top" id="scroll-top" value="">
						<?php if (array_key_exists('forgot_password_fail', $errors)): ?>
							<div class="alert alert-danger center-message">
								<?php echo $errors['forgot_password_fail']; ?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<p>
							Please enter the email address which you used to register on this site and we will assist you in recovering your password.
						</p>
						<div class="form-group">
							<input type="email" name="email" class="form-control form-control-lg" value="<?php echo $email; ?>" placeholder="<?php echo (array_key_exists('email', $errors)) ? $errors['email'] : ''; ?>">
						</div>
						<div class="form-group">
							<button type="submit" name="forgot-password" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition();">Recover your password</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>