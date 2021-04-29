<?php
	require_once 'controllers/authController.php';
	
	if (isset($_SESSION['id'])) {
		$_SESSION['message'] = "You cannot access this page while logged in!<br>You will now be logged out!";
		$_SESSION['alert-class'] = "alert-warning";
		header('location: error.php');
		exit();
	}
	
	if (!isset($_SESSION['passResetEmail'])) {
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
		<title>Password Reset Message</title>
	</head>
	<body>
		<?php include 'navbar.php' ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-8 col-lg-5 form-div login">
					<h3 class="text-center">Password reset message</h3>
					<div class="alert">&nbsp;</div>
					<p>We just emailed you at <strong><?php echo $_SESSION['passResetEmail'] ?></strong> with a link to reset your password.</p>
					<?php unset($_SESSION['passResetEmail']); ?>
				</div>
			</div>
		</div>
	</body>
</html>