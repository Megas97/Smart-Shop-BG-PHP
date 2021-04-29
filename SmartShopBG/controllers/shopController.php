<?php
	session_start();
	require 'config/db.php';
	$errors = array();
	
	// if user changes the select phone dropdown's value
	if (isset($_POST['phonesSelect'])) {
		if (!isset($_SESSION['id'])) {
			$_SESSION['message'] = "Only logged in users are able to filter the phone list";
			$_SESSION['alert-class'] = "alert-warning";
		} else {
			if ($_SESSION['verified'] === 0) {
				$_SESSION['message'] = "Only verified users are able to filter the phone list";
				$_SESSION['alert-class'] = "alert-warning";
			} else {
				$_SESSION['phones_to_show'] = $_POST['phonesSelect'];
				$_SESSION['selected_option'] = $_POST['phonesSelect'];
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top'];
		header('location: ' . $_POST['page']);
		exit();
	}
	
	// if user clicks on the like phone button
	if (isset($_GET['page']) && isset($_GET['like'])) {
		if (empty($_GET['page']) || empty($_GET['like'])) {
			$errors['vote_fail'] = "Incorrect statement passed";
		}
		if (!isset($_SESSION['id'])) {
			$_SESSION['message-' . $_GET['like']] = "You cannot vote until you login";
			$_SESSION['alert-class-' . $_GET['like']] = "alert-warning";
		} else {
			if (count($errors) === 0) {
				if ($_SESSION['verified'] === 0) {
					$_SESSION['message-' . $_GET['like']] = "Unverified users cannot vote";
					$_SESSION['alert-class-' . $_GET['like']] = "alert-warning";
				} else {
					$user_id = $_SESSION['id'];
					$phone_id = $_GET['like'];
					$sql = "SELECT * FROM phones WHERE id = ? LIMIT 1";
					$statement = $conn->prepare($sql);
					$statement->bind_param('i', $phone_id);
					$statement->execute();
					$result = $statement->get_result();
					$phone = $result->fetch_assoc();
					$statement->close();
					if ($phone) {
						$can_vote = true;
						$vote_type = 'like';
						
						$sql = "SELECT * FROM votes WHERE user_id = ? AND phone_id = ? AND vote_type = ? LIMIT 1";
						$statement = $conn->prepare($sql);
						$statement->bind_param('iis', $user_id, $phone_id, $vote_type);
						$statement->execute();
						$result = $statement->get_result();
						$vote = $result->fetch_assoc();
						$statement->close();
						if ($vote) {
							$can_vote = false;
						}
						
						if ($can_vote) {
							$sql = "SELECT * FROM votes WHERE user_id = ? AND phone_id = ? AND vote_type = ? LIMIT 1";
							$statement = $conn->prepare($sql);
							$opposite_vote = 'dis' . $vote_type;
							$statement->bind_param('iis', $user_id, $phone_id, $opposite_vote);
							$statement->execute();
							$result = $statement->get_result();
							$disliked = $result->fetch_assoc();
							$statement->close();
							if ($disliked) {
								$sql = "DELETE FROM votes WHERE user_id = ? AND phone_id = ? AND vote_type = ?";
								$statement = $conn->prepare($sql);
								$statement->bind_param('iis', $user_id, $phone_id, $opposite_vote);
								$statement->execute();
								
								$dislikes = $phone['dislikes'] - 1;
								$sql = "UPDATE phones SET dislikes = ? WHERE id = ?";
								$statement = $conn->prepare($sql);
								$statement->bind_param('ii', $dislikes, $phone_id);
								$statement->execute();
								$statement->close();
							}
							
							$sql = "INSERT INTO votes (user_id, phone_id, vote_type) VALUES (?, ?, ?)";
							$statement = $conn->prepare($sql);
							$statement->bind_param('iis', $user_id, $phone_id, $vote_type);
							$statement->execute();
							$statement->close();
							
							$likes = $phone['likes'] + 1;
							$sql = "UPDATE phones SET likes = ? WHERE id = ?";
							$statement = $conn->prepare($sql);
							$statement->bind_param('ii', $likes, $phone_id);
							$statement->execute();
							$statement->close();
						} else {
							$_SESSION['message-' . $_GET['like']] = "You have already liked this phone";
							$_SESSION['alert-class-' . $_GET['like']] = "alert-warning";
						}
						$_SESSION['phones_to_show'] = $_GET['option'];
						$_SESSION['selected_option'] = $_GET['option'];
					}
				}
			}
		}
		
		$_SESSION['scrollTop'] = $_GET['scroll'];
		header('location: ' . $_GET['page']);
		exit();
	}
	
	// if user clicks on the dislike phone button
	if (isset($_GET['page']) && isset($_GET['dislike'])) {
		if (empty($_GET['page']) || empty($_GET['dislike'])) {
			$errors['vote_fail'] = "Incorrect statement passed";
		}
		
		if (!isset($_SESSION['id'])) {
			$_SESSION['message-' . $_GET['dislike']] = "You cannot vote until you login";
			$_SESSION['alert-class-' . $_GET['dislike']] = "alert-warning";
		} else {
			if (count($errors) === 0) {
				if ($_SESSION['verified'] === 0) {
					$_SESSION['message-' . $_GET['dislike']] = "Unverified users cannot vote";
					$_SESSION['alert-class-' . $_GET['dislike']] = "alert-warning";
				} else {
					$user_id = $_SESSION['id'];
					$phone_id = $_GET['dislike'];
					$sql = "SELECT * FROM phones WHERE id = ? LIMIT 1";
					$statement = $conn->prepare($sql);
					$statement->bind_param('i', $phone_id);
					$statement->execute();
					$result = $statement->get_result();
					$phone = $result->fetch_assoc();
					$statement->close();
					if ($phone) {
						$can_vote = true;
						$vote_type = 'dislike';
						
						$sql = "SELECT * FROM votes WHERE user_id = ? AND phone_id = ? AND vote_type = ? LIMIT 1";
						$statement = $conn->prepare($sql);
						$statement->bind_param('iis', $user_id, $phone_id, $vote_type);
						$statement->execute();
						$result = $statement->get_result();
						$vote = $result->fetch_assoc();
						$statement->close();
						if ($vote) {
							$can_vote = false;
						}
						
						if ($can_vote) {
							$sql = "SELECT * FROM votes WHERE user_id = ? AND phone_id = ? AND vote_type = ? LIMIT 1";
							$statement = $conn->prepare($sql);
							$opposite_vote = substr($vote_type, 3, 6);
							$statement->bind_param('iis', $user_id, $phone_id, $opposite_vote);
							$statement->execute();
							$result = $statement->get_result();
							$liked = $result->fetch_assoc();
							$statement->close();
							if ($liked) {
								$sql = "DELETE FROM votes WHERE user_id = ? AND phone_id = ? AND vote_type = ?";
								$statement = $conn->prepare($sql);
								$statement->bind_param('iis', $user_id, $phone_id, $opposite_vote);
								$statement->execute();
								
								$likes = $phone['likes'] - 1;
								$sql = "UPDATE phones SET likes = ? WHERE id = ?";
								$statement = $conn->prepare($sql);
								$statement->bind_param('ii', $likes, $phone_id);
								$statement->execute();
								$statement->close();
							}
							
							$sql = "INSERT INTO votes (user_id, phone_id, vote_type) VALUES (?, ?, ?)";
							$statement = $conn->prepare($sql);
							$statement->bind_param('iis', $user_id, $phone_id, $vote_type);
							$statement->execute();
							$statement->close();
							
							$dislikes = $phone['dislikes'] + 1;
							$sql = "UPDATE phones SET dislikes = ? WHERE id = ?";
							$statement = $conn->prepare($sql);
							$statement->bind_param('ii', $dislikes, $phone_id);
							$statement->execute();
							$statement->close();
						} else {
							$_SESSION['message-' . $_GET['dislike']] = "You have already disliked this phone";
							$_SESSION['alert-class-' . $_GET['dislike']] = "alert-warning";
						}
						$_SESSION['phones_to_show'] = $_GET['option'];
						$_SESSION['selected_option'] = $_GET['option'];
					}
				}
			}
		}
		
		$_SESSION['scrollTop'] = $_GET['scroll'];
		header('location: ' . $_GET['page']);
		exit();
	}
	
	// if user clicks on the add phone to cart button
	if (isset($_POST['add-phone-to-cart-btn'])) {
		if ($_SESSION['verified'] === 0) {
			$_SESSION['message-' . $_POST['phone-id']] = "Unverified users cannot buy products";
			$_SESSION['alert-class-' . $_POST['phone-id']] = "alert-warning";
		} else {
			if (!isset($_SESSION['id'])) {
				$_SESSION['message-' . $_POST['phone-id']] = "You cannot buy products until you login";
				$_SESSION['alert-class-' . $_POST['phone-id']] = "alert-warning";
			} else {
				if (isset($_SESSION['shopping_cart'])) {
					$phone_id_array = array_column($_SESSION['shopping_cart'], "phone_id"); // get all values from the 'phone_id' array column
					if (!in_array($_POST['phone-id'], $phone_id_array)) {
						$count = count($_SESSION['shopping_cart']);
						$phone_array = array(
							'phone_id' => $_POST['phone-id'],
							'phone_make' => $_POST['phone-make'],
							'phone_model' => $_POST['phone-model'],
							'phone_image' => $_POST['phone-image'],
							'phone_quantity' => 1,
							'phone_price' => $_POST['phone-price']
						);
						$_SESSION['shopping_cart'][$count] = $phone_array;
						$_SESSION['message-' . $_POST['phone-id']] = "Phone successfully added to cart";
						$_SESSION['alert-class-' . $_POST['phone-id']] = "alert-success";
					} else {
						$key = array_search($_POST['phone-id'], $phone_id_array);
						$phone_array = array(
							'phone_id' => $_POST['phone-id'],
							'phone_make' => $_POST['phone-make'],
							'phone_model' => $_POST['phone-model'],
							'phone_image' => $_POST['phone-image'],
							'phone_quantity' => $_SESSION['shopping_cart'][$key]['phone_quantity'] + 1,
							'phone_price' => $_POST['phone-price']
						);
						$_SESSION['shopping_cart'][$key] = $phone_array;
						$_SESSION['message-' . $_POST['phone-id']] = "Phone quantity successfully updated in cart";
						$_SESSION['alert-class-' . $_POST['phone-id']] = "alert-success";
					}
				} else {
					$phone_array = array(
						'phone_id' => $_POST['phone-id'],
						'phone_make' => $_POST['phone-make'],
						'phone_model' => $_POST['phone-model'],
						'phone_image' => $_POST['phone-image'],
						'phone_quantity' => 1,
						'phone_price' => $_POST['phone-price']
					);
					$_SESSION['shopping_cart'][0] = $phone_array;
					$_SESSION['message-' . $_POST['phone-id']] = "Phone successfully added to cart";
					$_SESSION['alert-class-' . $_POST['phone-id']] = "alert-success";
				}
			}
		}
		
		$_SESSION['scrollTop'] = $_POST['scroll-top-' . $_POST['phone-id']];
		header('location: ' . $_POST['page']);
		exit();
	}
	
	// if user clicks on the add phone button in cart
	if (isset($_GET['add'])) {
		if (isset($_SESSION['shopping_cart'])) {
			$phone_id_array = array_column($_SESSION['shopping_cart'], "phone_id"); // get all values from the 'phone_id' array column
			$key = array_search($_GET['add'], $phone_id_array);
			if ($key !== false) {
				$phone_array = array(
					'phone_id' => $_SESSION['shopping_cart'][$key]['phone_id'],
					'phone_make' => $_SESSION['shopping_cart'][$key]['phone_make'],
					'phone_model' => $_SESSION['shopping_cart'][$key]['phone_model'],
					'phone_image' => $_SESSION['shopping_cart'][$key]['phone_image'],
					'phone_quantity' => $_SESSION['shopping_cart'][$key]['phone_quantity'] + 1,
					'phone_price' => $_SESSION['shopping_cart'][$key]['phone_price']
				);
				$_SESSION['shopping_cart'][$key] = $phone_array;
				$_SESSION['message'] = "Phone quantity successfully updated";
				$_SESSION['alert-class'] = "alert-success";
			}
		}
		
		$_SESSION['scrollTop'] = $_GET['scroll'];
		header('location: cart.php');
		exit();
	}
	
	// if user clicks on the remove phone button in cart
	if (isset($_GET['remove'])) {
		if (isset($_SESSION['shopping_cart'])) {
			$phone_id_array = array_column($_SESSION['shopping_cart'], "phone_id"); // get all values from the 'phone_id' array column
			$key = array_search($_GET['remove'], $phone_id_array);
			if ($key !== false) {
				if ($_SESSION['shopping_cart'][$key]['phone_quantity'] == 1) {
					unset($_SESSION['shopping_cart'][$key]);
					$reindexed_array = array_values($_SESSION['shopping_cart']); // reindex the array from which we just removed an element
					$_SESSION['shopping_cart'] = $reindexed_array;
					$_SESSION['message'] = "Phone successfully removed from cart";
					$_SESSION['alert-class'] = "alert-success";
					
				} else {
					$phone_array = array(
						'phone_id' => $_SESSION['shopping_cart'][$key]['phone_id'],
						'phone_make' => $_SESSION['shopping_cart'][$key]['phone_make'],
						'phone_model' => $_SESSION['shopping_cart'][$key]['phone_model'],
						'phone_image' => $_SESSION['shopping_cart'][$key]['phone_image'],
						'phone_quantity' => $_SESSION['shopping_cart'][$key]['phone_quantity'] - 1,
						'phone_price' => $_SESSION['shopping_cart'][$key]['phone_price']
					);
					$_SESSION['shopping_cart'][$key] = $phone_array;
					$_SESSION['message'] = "Phone quantity successfully updated";
					$_SESSION['alert-class'] = "alert-success";
				}
			}
		}
		
		$_SESSION['scrollTop'] = $_GET['scroll'];
		header('location: cart.php');
		exit();
	}
	
	// if user clicks on the finish order button in cart
	if (isset($_POST['finish-cart-order-btn'])) {
		unset($_SESSION['shopping_cart']);
		$_SESSION['message'] = "Your order is on its way to you at " . $_SESSION['address'];
		$_SESSION['alert-class'] = "alert-success";
		$_SESSION['has-just-finished-order'] = true;
		
		header('location: cart.php');
		exit();
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