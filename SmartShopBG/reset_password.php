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
		<title>Reset Password</title>
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
					<form action="reset_password.php" method="post">
						<h3 class="text-center">Reset your password</h3>
						<?php if (array_key_exists('reset_pass_fail', $errors)): ?>
							<div class="alert alert-danger center-message">
								<?php echo $errors['reset_pass_fail']; ?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<input type="hidden" name="scroll-top" id="scroll-top" value="">
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('password', $errors)) ? $errors['password'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="passwordConf">Confirm Password</label>
							<input type="password" name="passwordConf" class="form-control form-control-lg">
						</div>
						<div class="form-group">
							<button type="submit" name="reset-password-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition();">Reset Password</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>