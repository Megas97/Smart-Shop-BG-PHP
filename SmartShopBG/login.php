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
		<title>Login</title>
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
					<form action="login.php" method="post">
						<h3 class="text-center">Login</h3>
						<?php if (array_key_exists('login_fail', $errors)): ?>
							<div class="alert alert-danger center-message">
								<?php echo $errors['login_fail']; ?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<input type="hidden" name="scroll-top" id="scroll-top" value="">
						<div class="form-group">
							<label for="username">Username or email</label>
							<input type="text" name="usernameOrEmail" value="<?php echo $username; ?>" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('usernameOrEmail', $errors)) ? $errors['usernameOrEmail'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('password', $errors)) ? $errors['password'] : ''; ?>">
						</div>
						<div class="form-group">
							<button type="submit" name="login-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition();">Login</button>
						</div>
						<p class="text-center">Not yet a member? <a href="register.php">Register</a></p>
						<div style="font-size: 0.8em; text-align: center;" class="text-center">
							<a href="forgot_password.php">Forgot your password?</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>