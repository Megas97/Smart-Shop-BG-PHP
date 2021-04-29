<?php
	require_once 'vendor/autoload.php';
	require_once 'config/constants.php';
	
	$cur_url = getCurrentURL();
	$cur_file = preg_split("/\//", $cur_url)[count(preg_split("/\//", $cur_url))-1];
	$cur_dir = preg_split("/" . $cur_file . "/", $cur_url)[0];

	// Create the Transport
	$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
	  ->setUsername(EMAIL)
	  ->setPassword(PASSWORD)
	;

	// Create the Mailer using your created Transport
	$mailer = new Swift_Mailer($transport);

	function sendVerificationEmail($userEmail, $token) {
		global $mailer;
		global $cur_dir;
		$body = '<!DOCTYPE html>
			<html lang="en">
				<head>
					<meta charset="UTF-8">
				</head>
				<body>
					<div class="wrapper">
						<p>
							Thank you for signing up on our website. Please click on the link below to verify your account.
						</p>
						<br>
						<a href="' . $cur_dir . 'index.php?token=' . $token . '">Verify your account</a>
					</div>
				</body>
			</html>';
		
		// Create a message
		$message = (new Swift_Message('Verify your account'))
			->setFrom([EMAIL => NAME])
			->setTo($userEmail)
			->setBody($body, 'text/html')
			;
		
		// Send the message
		$result = $mailer->send($message);
	}

	function sendPasswordResetLink($userEmail, $token) {
		global $mailer;
		global $cur_dir;
		$body = '<!DOCTYPE html>
			<html lang="en">
				<head>
					<meta charset="UTF-8">
				</head>
				<body>
					<div class="wrapper">
						<p>
							Hello there, <br>Please click on the link below to reset your password.
						</p>
						<br>
						<a href="' . $cur_dir . 'index.php?password-token=' . $token . '">Reset your password</a>
					</div>
				</body>
			</html>';
		
		// Create a message
		$message = (new Swift_Message('Reset your password'))
			->setFrom([EMAIL => NAME])
			->setTo($userEmail)
			->setBody($body, 'text/html')
			;
		
		// Send the message
		$result = $mailer->send($message);
	}
	
	function getCurrentURL(){
		if(isset($_SERVER['HTTPS'])){
			$protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
		}
		else{
			$protocol = 'http';
		}
		return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
?>