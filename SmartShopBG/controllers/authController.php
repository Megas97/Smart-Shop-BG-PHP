<?php
	session_start();
	require 'config/db.php';
	require_once 'controllers/emailController.php';
	$errors = array();
	$username = "";
	$email = "";
	$address = "";
	
	// if user clicks on the register button
	if (isset($_POST['register-btn'])) {
		$username = $_POST['username'];
		$email = $_POST['email'];
		$address = $_POST['address'];
		$password = $_POST['password'];
		$passwordConf = $_POST['passwordConf'];
		
		// validation
		if (empty($username)) {
			$errors['username'] = "Username required";
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors['email'] = "Email address is invalid";
		}
		if (empty($email)) {
			$errors['email'] = "Email required";
		}
		if (empty($address)) {
			$errors['address'] = "Address required";
		}
		if (empty($password)) {
			$errors['password'] = "Password required";
		}
		if ($password !== $passwordConf) {
			$errors['password'] = "The two passwords do not match";
		}
		
		$emailQuery = "SELECT * FROM users WHERE email = ? LIMIT 1";
		$statement = $conn->prepare($emailQuery);
		$statement->bind_param('s', $email);
		$statement->execute();
		$result = $statement->get_result();
		$userCount = $result->num_rows;
		$statement->close();
		if ($userCount > 0) {
			$errors['register_fail'] = "Email already exists";
		}
		
		$usernameQuery = "SELECT * FROM users WHERE username = ?";
		$statement = $conn->prepare($usernameQuery);
		$statement->bind_param('s', $username);
		$statement->execute();
		$result = $statement->get_result();
		$userCount = $result->num_rows;
		$statement->close();
		if ($userCount > 0) {
			$errors['register_fail'] = "Username already exists";
		}
		
		if (count($errors) === 0) {
			$password = password_hash($password, PASSWORD_DEFAULT);
			$token = bin2hex(random_bytes(50));
			$verified = 0;
			$admin = 0;
			$totalUsersQuery = "SELECT * FROM users";
			$statement = $conn->prepare($totalUsersQuery);
			$statement->execute();
			$result = $statement->get_result();
			$totalUsersCount = $result->num_rows;
			$statement->close();
			if ($totalUsersCount == 0) {
				$admin = 1;
				$sql = "INSERT INTO users (username, email, address, verified, token, password, admin) VALUES (?, ?, ?, ?, ?, ?, 1)";
			} else {
				$admin = 0;
				$sql = "INSERT INTO users (username, email, address, verified, token, password, admin) VALUES (?, ?, ?, ?, ?, ?, 0)";
			}
			$statement = $conn->prepare($sql);
			$statement->bind_param('sssbss', $username, $email, $address, $verified, $token, $password);
			if ($statement->execute()) {
				// login user
				$user_id = $conn->insert_id;
				$_SESSION['id'] = $user_id;
				$_SESSION['username'] = $username;
				$_SESSION['email'] = $email;
				$_SESSION['address'] = $address;
				$_SESSION['verified'] = $verified;
				$_SESSION['admin'] = $admin;
				
				sendVerificationEmail($email, $token);
				
				// set flash message
				$_SESSION['message'] = "You are now logged in";
				$_SESSION['alert-class'] = "alert-success";
				$statement->close();
				
				$_SESSION['scrollTop'] = $_POST['scroll-top'];
				header('location: profile.php');
				exit();
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top'];
	}

	// if user clicks on the login button
	if (isset($_POST['login-btn'])) {
		$username = $_POST['usernameOrEmail'];
		$password = $_POST['password'];
		
		// validation
		if (empty($username)) {
			$errors['usernameOrEmail'] = "Username or email required";
		}
		if (empty($password)) {
			$errors['password'] = "Password required";
		}
		
		if (count($errors) === 0) {
			$sql = "SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1";
			$statement = $conn->prepare($sql);
			$statement->bind_param('ss', $username, $username);
			$statement->execute();
			$result = $statement->get_result();
			$user = $result->fetch_assoc();
			
			if ($user && password_verify($password, $user['password'])) {
				// login success
				$_SESSION['id'] = $user['id'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['email'] = $user['email'];
				$_SESSION['address'] = $user['address'];
				$_SESSION['verified'] = $user['verified'];
				$_SESSION['admin'] = $user['admin'];
				// set flash message
				$_SESSION['message'] = "You are now logged in";
				$_SESSION['alert-class'] = "alert-success";
				$statement->close();
				
				header('location: profile.php');
				exit();
			} else {
				$errors['login_fail'] = "Wrong credentials";
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top'];
	}

	// logout user
	if (isset($_GET['logout'])) {
		session_destroy();
		
		header('location: login.php');
		exit();
	}

	// verify user by token
	function verifyUser($token) {
		global $conn;
		
		$sql = "SELECT * FROM users WHERE token = ? LIMIT 1";
		$statement = $conn->prepare($sql);
		$statement->bind_param('s', $token);
		$statement->execute();
		$result = $statement->get_result();
		$usersCount = $result->num_rows;
		$statement->close();
		if ($usersCount > 0) {
			$user = $result->fetch_assoc();
			$update_query = "UPDATE users SET verified = 1 WHERE token = ?";
			$statement = $conn->prepare($update_query);
			$statement->bind_param('s', $token);
			if ($statement->execute()) {
				// log user in
				$_SESSION['id'] = $user['id'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['email'] = $user['email'];
				$_SESSION['verified'] = 1;
				$_SESSION['admin'] = $user['admin'];
				// set flash message
				if ($user['verified'] === 1) {
					$_SESSION['message'] = "Your account has already been verified";
				} else {
					$_SESSION['message'] = "Your account was successfully verified";
				}
				$_SESSION['alert-class'] = "alert-success";
				
				header('location: profile.php');
				exit();
			}
		} else {
			$_SESSION['message'] = "No user matching the given token was found";
			$_SESSION['alert-class'] = "alert-danger";
			unset($_SESSION['id']);
			unset($_SESSION['username']);
			unset($_SESSION['email']);
			unset($_SESSION['verified']);
			unset($_SESSION['admin']);
			
			header('location: error.php');
			exit();
		}
	}

	// if user clicks on the forgot password button
	if (isset($_POST['forgot-password'])) {
		$email = $_POST['email'];
		if (empty($email)) {
			$errors['email'] = "Email required";
		} else {
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors['forgot_password_fail'] = "Email address is invalid";
			}
		}
		if (count($errors) == 0) {
			$sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
			$statement = $conn->prepare($sql);
			$statement->bind_param('s', $email);
			$statement->execute();
			$result = $statement->get_result();
			$usersCount = $result->num_rows;
			$statement->close();
			if ($usersCount > 0) {
				$user = $result->fetch_assoc();
				$token = $user['token'];
				sendPasswordResetLink($email, $token);
				$_SESSION['passResetEmail'] = $email;
				
				header('location: password_reset_message.php');
				exit();
			} else {
				$_SESSION['message'] = "No user matching the given email was found";
				$_SESSION['alert-class'] = "alert-danger";
				unset($_SESSION['id']);
				unset($_SESSION['username']);
				unset($_SESSION['email']);
				unset($_SESSION['verified']);
				unset($_SESSION['admin']);
				
				header('location: error.php');
				exit();
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top'];
	}

	// if user clicked on the reset password button
	if (isset($_POST['reset-password-btn'])) {
		$password = $_POST['password'];
		$passwordConf = $_POST['passwordConf'];
		if (empty($password) || empty($passwordConf)) {
			$errors['password'] = "Password required";
		}
		if ($password !== $passwordConf) {
			$errors['password'] = "The two passwords do not match";
		}
		if (!isset($_SESSION['email'])) {
			$errors['reset_pass_fail'] = "This page can only be opened and used via an email link";
		}
		
		if (count($errors) === 0) {
			$password = password_hash($password, PASSWORD_DEFAULT);
			$email = $_SESSION['email'];
			$sql = "UPDATE users SET password = ? WHERE email = ?";
			$statement = $conn->prepare($sql);
			$statement->bind_param('ss', $password, $email);
			$statement->execute();
			$statement->close();
			
			header('location: login.php');
			exit();
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top'];
	}

	function resetPassword($token) {
		global $conn;
		
		$sql = "SELECT * FROM users WHERE token = ? LIMIT 1";
		$statement = $conn->prepare($sql);
		$statement->bind_param('s', $token);
		$statement->execute();
		$result = $statement->get_result();
		$usersCount = $result->num_rows;
		$statement->close();
		if ($usersCount > 0) {
			$user = $result->fetch_assoc();
			$_SESSION['email'] = $user['email'];
			
			header('location: reset_password.php');
			exit();
		} else {
			$_SESSION['message'] = "No user matching the given token was found";
			$_SESSION['alert-class'] = "alert-danger";
			unset($_SESSION['id']);
			unset($_SESSION['username']);
			unset($_SESSION['email']);
			unset($_SESSION['verified']);
			unset($_SESSION['admin']);
			
			header('location: error.php');
			exit();
		}
	}
	
	// if user clicks on the change address button
	if (isset($_POST['change-address-btn'])) {
		if (empty($_POST['address'])) {
			$errors['address'] = "Address required";
		}
		
		if (count($errors) === 0) {
			$sql = "UPDATE users SET address = ? WHERE email = ?";
			$statement = $conn->prepare($sql);
			$statement->bind_param('ss', $_POST['address'], $_SESSION['email']);
			if ($statement->execute()) {
				$_SESSION['message'] = "You successfully changed your address";
				$_SESSION['alert-class'] = "alert-success";
				$statement->close();
				$_SESSION['address'] = $_POST['address'];
				
				$_SESSION['scrollTop'] = $_POST['scroll-top'];
				header('location: profile.php');
				exit();
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top'];
	}
	
	// if user clicks on the delete account button
	if (isset($_POST['delete-account-btn'])) {
		$sql = "DELETE FROM votes WHERE user_id = ?";
		$statement = $conn->prepare($sql);
		$statement->bind_param('i', $_SESSION['id']);
		if ($statement->execute()) {
			$sql = "DELETE FROM users WHERE id = ?";
			$statement = $conn->prepare($sql);
			$statement->bind_param('i', $_SESSION['id']);
			if ($statement->execute()) {
				$_SESSION['message'] = "You successfully deleted your account";
				$_SESSION['alert-class'] = "alert-success";
				$statement->close();
				session_destroy();
				
				header('location: register.php');
				exit();
			}
		}
	}
?>