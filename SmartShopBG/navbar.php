<!-- no session_start or include here as this page is embedded in all others and they already have the needed includes -->
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="styles.css">
		<title>Navbar</title>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		<link rel="manifest" href="/site.webmanifest">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="theme-color" content="#ffffff">
	</head>
	<body>
		<nav id="navbar" class="navbar navbar-expand-lg">
		  <a class="navbar-brand" href="index.php">SmartShopBG</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="collapsibleNavbar">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link"></a>
				</li>
			</ul>
			<ul class="navbar-nav mx-auto">
			  <li class="nav-item">
				<a class="nav-link" href="smartphones.php">Smartphones</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="feature_phones.php">Feature Phones</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="profile.php">My Profile</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="contacts.php">Contacts</a>
			  </li>
				<?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === 1): ?>
					<li class="nav-item">
						<a class="nav-link" href="admin.php">Admin Panel</a>
					</li>
				<?php endif; ?>
			</ul>
			<ul class="navbar-nav ml-auto">
				<?php if (!isset($_SESSION['id'])): ?>
					<li class="nav-item">
						<a class="nav-link" href="login.php">Login</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="register.php">Register</a>
					</li>
				<?php endif; ?>
				<?php if (isset($_SESSION['id'])): ?>
					<li class="nav-item">
						<?php
							$totalCartPhones = 0;
							if (isset($_SESSION['shopping_cart']) && count($_SESSION['shopping_cart']) > 0) {
								$totalCartPhones = 0;
								foreach ($_SESSION['shopping_cart'] as $key => $value) {
									$totalCartPhones = $totalCartPhones + $value['phone_quantity'];
								}
								$cart_circle_class = "circle-cart";
							} else {
								$totalCartPhones = "&nbsp;";
								$cart_circle_class = "";
							}
						?>
						<a class="nav-link" href="cart.php">&nbsp; Cart <sup class="<?php echo $cart_circle_class; ?>"><?php echo $totalCartPhones; ?></sup></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.php?logout=1">Logout</a>
					</li>
				<?php endif; ?>
			</ul>
		  </div>
		</nav>
		<button id="scrollToTopBtn" type="button" class="btn btn-info">&uarr;</button>
		<script>
            var scrollToTopBtn = document.getElementById("scrollToTopBtn");
            var rootElement = document.documentElement;

            function handleScroll() {
                var scrollTotal = rootElement.scrollHeight - rootElement.clientHeight;
                if ((rootElement.scrollTop / scrollTotal) > 0.10) {
                    scrollToTopBtn.style.display = "block";
                } else {
                    scrollToTopBtn.style.display = "none";
                }
            }

            function scrollToTop() {
                rootElement.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            }

            scrollToTopBtn.addEventListener("click", scrollToTop);
            document.addEventListener("scroll", handleScroll);
        </script>
	</body>
</html>