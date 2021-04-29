<?php require_once 'controllers/authController.php'; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- Bootstrap 4 CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="styles.css">
		<title>Error Page</title>
	</head>
	<body>
		<?php include 'navbar.php' ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-8 col-lg-5 form-div login">
					<h3 class="text-center">Error page</h3>
					<div class="alert">&nbsp;</div>
					<?php if (isset($_SESSION['message'])): ?>
						<div class="alert <?php echo $_SESSION['alert-class']; ?> center-message">
							<?php
								echo $_SESSION['message'];
								session_destroy();
							?>
						</div>
					<?php endif; ?>
					<?php if (!isset($_SESSION['message'])): ?>
						<?php header('location: index.php'); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</body>
</html>