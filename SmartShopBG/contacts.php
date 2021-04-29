<?php
	session_start();
	
	$address = "Sofia, Bulgaria, Mladost 3, 45 Nadezhda Street, block 364, entrance 4";
	$telephone = "123-456-7890";
	$email = "smart-shop-bg@abv.bg";
	$facebook = "https://www.facebook.com/megas97";
	$twitter = "https://twitter.com/Moni_Mihailov";
	$instagram = "https://www.instagram.com/moni_mihailov/";
	$skype = "iron-man_21";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- Bootstrap 4 CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
		<!-- Font Awesome CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="styles.css">
		<title>Contacts</title>
	</head>
	<body>
		<?php include 'navbar.php' ?>
		<div class="container">
			<div class="row">
				<div class="col-sm-8 col-lg-4 form-div">
					<address>
						<h3>Contact us</h3>
						<div class="alert">&nbsp;</div>
						<span style="font-weight: bold;">Office address:</span>
						<br>
						<span style="font-style: italic;"><?php echo $address; ?></span>
						<br>
						<div class="mapouter">
							<div class="gmap_canvas">
								<iframe width="276" height="276" src="https://maps.google.com/maps?q=<?php echo preg_replace( '~\s~', '%20', $address); ?>&t=&z=17&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
							</div>
						</div>
						<span style="font-weight: bold;">Telephone:</span>
						<br>
						<span style="font-style: italic;"><a href="tel:<?php echo $telephone; ?>"><?php echo $telephone; ?></a></span>
						<br>
						<span style="font-weight: bold;">Email:</span>
						<br>
						<a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
						<br>
						<br>
						<div id="socialMediaContainer">
							<a href="<?php echo $facebook; ?>" target="_blank" class="fa fa-facebook"></a>
							<a href="<?php echo $twitter; ?>" target="_blank" class="fa fa-twitter"></a>
							<a href="<?php echo $instagram; ?>" target="_blank" class="fa fa-instagram"></a>
							<a href="skype:<?php echo $skype; ?>?chat" class="fa fa-skype"></a>
						</div>
					</address>
				</div>
			</div>
		</div>
	</body>
</html>