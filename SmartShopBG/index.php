<?php
	require_once 'controllers/authController.php';
	
	// verify the user using token
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
		verifyUser($token);
	}
	
	// reset password using token
	if (isset($_GET['password-token'])) {
		$passwordToken = $_GET['password-token'];
		resetPassword($passwordToken);
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- Bootstrap 4 CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="styles.css">
		<title>Home Page</title>
	</head>
	<body>
		<?php include 'navbar.php' ?>
		<div class="container">
			<div class="col-sm-6 col-md-12 col-xs-12" style="text-align: center;">
				<img src="logo.png" style="width: 50%; height: 50%;">
				<p><i>A phone for anyone</i></p>
			</div>
		</div>
	</body>
</html>