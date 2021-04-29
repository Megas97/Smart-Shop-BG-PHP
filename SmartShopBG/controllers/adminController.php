<?php
	session_start();
	require 'config/db.php';
	
	$errors = array();
	$make = "";
	$model = "";
	$price = "";
	$selectedType = "";
	$selectedPhone = "";
	
	// if admin clicks on the upload phone button
	if (isset($_POST['upload-phone-btn'])) {
		if ($_SESSION['verified'] === 0) {
			$_SESSION['message'] = "You need to verify your account first";
			$_SESSION['alert-class'] = "alert-warning";
			header('location: admin.php');
			exit();
		}
		
		// the path where to store the uploaded image
		$folder = "uploads/";
		
		$make = $_POST['make'];
		$model = $_POST['model'];
		$price = $_POST['price'];
		$image = basename($_FILES['image']['name']);
		$type = $_POST['type'];
		$selectedType = $type;
		
		if (empty($make)) {
			$errors['make'] = "Phone make required";
		}
		if (empty($model)) {
			$errors['model'] = "Phone model required";
		}
		if (empty($price)) {
			$errors['price'] = "Phone price required";
		}
		if (empty($image)) {
			$errors['image'] = "Phone image required";
		}
		if (empty($type)) {
			$errors['type'] = "Phone type required";
		}
		
		if (count($errors) === 0) {
			$sql = "SELECT * FROM phones WHERE make = ? AND model = ? LIMIT 1";
			$statement = $conn->prepare($sql);
			$statement->bind_param('ss', $make, $model);
			$statement->execute();
			$result = $statement->get_result();
			$phone = $result->fetch_assoc();
			$statement->close();
			if ($phone) {
				$_SESSION['message'] = "Phone already exists in the database";
				$_SESSION['alert-class'] = "alert-warning";
				header('location: admin.php');
				exit();
			}
			
			$sql = "INSERT INTO phones (make, model, price, image, type, likes, dislikes) VALUES (?, ?, ?, ?, ?, 0, 0)";
			$statement = $conn->prepare($sql);
			$statement->bind_param('ssdss', $make, $model, $price, $image, $type);
			$statement->execute();
			$statement->close();
			
			$inserted_phone_id = $conn->insert_id;
			$extention = pathinfo($image, PATHINFO_EXTENSION);
			$path = $folder . $inserted_phone_id . "." . $extention;
			
			$sql = "UPDATE phones SET image = ? WHERE id = ?";
			$statement = $conn->prepare($sql);
			$statement->bind_param('si', $path, $inserted_phone_id);
			$statement->execute();
			$statement->close();
			
			if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
				$_SESSION['message'] = "Phone successfully uploaded";
				$_SESSION['alert-class'] = "alert-success";
			} else {
				$_SESSION['message'] = "Failed to upload phone";
				$_SESSION['alert-class'] = "alert-warning";
			}
			
			$_SESSION['scrollTop'] = $_POST['scroll-top-upload'];
			header('location: admin.php');
			exit();
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top-upload'];
	}
	
	// if admin clicks on the change phone price button
	if (isset($_POST['change-phone-price-btn'])) {
		if ($_SESSION['verified'] === 0) {
			$_SESSION['message2'] = "You need to verify your account first";
			$_SESSION['alert-class2'] = "alert-warning";
			header('location: admin.php');
			exit();
		}
		
		$phone_id = "";
		$price = $_POST['price'];
		
		if (!array_key_exists('phonesList1', $_POST)) {
			$errors['phone'] = "Phone required";
		} else {
			$phone_id = $_POST['phonesList1'];
			$selectedPhone = $phone_id;
		}
		
		if (empty($phone_id)) {
			$errors['phone'] = "Phone required";
		}
		if (empty($price)) {
			$errors['price2'] = "Phone price required";
		}
		
		if (count($errors) === 0) {
			$sql = "UPDATE phones SET price = ? WHERE id = ?";
			$statement = $conn->prepare($sql);
			$statement->bind_param('di', $price, $phone_id);
			if ($statement->execute()) {
				$statement->close();
				$_SESSION['message2'] = "Phone price successfully updated";
				$_SESSION['alert-class2'] = "alert-success";
				
				$_SESSION['scrollTop'] = $_POST['scroll-top-change'];
				header('location: admin.php');
				exit();
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top-change'];
	}
	
	// if admin clicks on the delete phone button
	if (isset($_POST['delete-phone-btn'])) {
		if ($_SESSION['verified'] === 0) {
			$_SESSION['message3'] = "You need to verify your account first";
			$_SESSION['alert-class3'] = "alert-warning";
			header('location: admin.php');
			exit();
		}
		
		$phone_id = "";
		
		if (!array_key_exists('phonesList2', $_POST)) {
			$errors['phone2'] = "Phone required";
		} else {
			$phone_id = $_POST['phonesList2'];
		}
		
		if (empty($phone_id)) {
			$errors['phone2'] = "Phone required";
		}
		
		if (count($errors) === 0) {
			$sql = "SELECT * FROM phones WHERE id = ? LIMIT 1";
			$statement = $conn->prepare($sql);
			$statement->bind_param('i', $phone_id);
			$statement->execute();
			$result = $statement->get_result();
			$phone = $result->fetch_assoc();
			$statement->close();
			if ($phone) {
				$imagePath = $phone['image'];
				$sql = "DELETE FROM votes WHERE phone_id = ?";
				$statement = $conn->prepare($sql);
				$statement->bind_param('i', $phone['id']);
				if ($statement->execute()) {
					$statement->close();
					$sql = "DELETE FROM phones WHERE id = ?";
					$statement = $conn->prepare($sql);
					$statement->bind_param('i', $phone['id']);
					if ($statement->execute()) {
						$statement->close();
						unlink($imagePath);
						$_SESSION['message3'] = "Phone successfully deleted";
						$_SESSION['alert-class3'] = "alert-success";
						
						$_SESSION['scrollTop'] = $_POST['scroll-top-delete'];
						header('location: admin.php');
						exit();
					}
				}
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top-delete'];
	}
	
	// if admin clicks on the give admin rights button
	if (isset($_POST['give-user-admin-btn'])) {
		if ($_SESSION['verified'] === 0) {
			$_SESSION['message4'] = "You need to verify your account first";
			$_SESSION['alert-class4'] = "alert-warning";
			header('location: admin.php');
			exit();
		}
		
		$user_id = "";
		
		if (!array_key_exists('usersList', $_POST)) {
			$errors['user'] = "User required";
		} else {
			$user_id = $_POST['usersList'];
		}
		
		if (empty($user_id)) {
			$errors['user'] = "User required";
		}
		
		if (count($errors) === 0) {
			$sql = "UPDATE users SET admin = 1 WHERE id = ? LIMIT 1";
			$statement = $conn->prepare($sql);
			$statement->bind_param('i', $user_id);
			if ($statement->execute()) {
				$statement->close();
				$_SESSION['message4'] = "User status successfully updated";
				$_SESSION['alert-class4'] = "alert-success";
				
				$_SESSION['scrollTop'] = $_POST['scroll-top-give-admin'];
				header('location: admin.php');
				exit();
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top-give-admin'];
	}
	
	// if admin clicks on the revoke admin rights button
	if (isset($_POST['revoke-user-admin-btn'])) {
		if ($_SESSION['verified'] === 0) {
			$_SESSION['message5'] = "You need to verify your account first";
			$_SESSION['alert-class5'] = "alert-warning";
			header('location: admin.php');
			exit();
		}
		
		$user_id = "";
		
		if (!array_key_exists('usersList', $_POST)) {
			$errors['user2'] = "User required";
		} else {
			$user_id = $_POST['usersList'];
		}
		
		if (empty($user_id)) {
			$errors['user2'] = "User required";
		}
		
		if (count($errors) === 0) {
			$sql = "UPDATE users SET admin = 0 WHERE id = ? LIMIT 1";
			$statement = $conn->prepare($sql);
			$statement->bind_param('i', $user_id);
			if ($statement->execute()) {
				$statement->close();
				$_SESSION['message5'] = "User status successfully updated";
				$_SESSION['alert-class5'] = "alert-success";
				
				$_SESSION['scrollTop'] = $_POST['scroll-top-revoke-admin'];
				header('location: admin.php');
				exit();
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top-revoke-admin'];
	}
?>