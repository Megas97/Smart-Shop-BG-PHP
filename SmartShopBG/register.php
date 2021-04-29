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
		<title>Register</title>
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
				<div class="col-sm-8 col-lg-4 form-div">
					<form action="register.php" method="post">
						<h3 class="text-center">Register</h3>
						<?php if (array_key_exists('register_fail', $errors)): ?>
							<div class="alert alert-danger center-message">
								<?php echo $errors['register_fail']; ?>
							</div>
						<?php else: ?>
							<div class="alert">&nbsp;</div>
						<?php endif; ?>
						<input type="hidden" name="scroll-top" id="scroll-top" value="">
						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" value="<?php echo $username; ?>" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('username', $errors)) ? $errors['username'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="email">Email</label>
							<input type="email" name="email" value="<?php echo $email; ?>"class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('email', $errors)) ? $errors['email'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="address">Address</label>
							<input type="text" name="address" value="<?php echo $address; ?>"class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('address', $errors)) ? $errors['address'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" class="form-control form-control-lg" placeholder="<?php echo (array_key_exists('password', $errors)) ? $errors['password'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="passwordConf">Confirm Password</label>
							<input type="password" name="passwordConf" class="form-control form-control-lg">
						</div>
						<div class="form-group">
							<button type="submit" name="register-btn" class="btn btn-primary btn-block btn-lg" onclick="saveScrollPosition();">Register</button>
						</div>
						<p class="text-center">Already a member? <a href="login.php">Login</a></p>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>