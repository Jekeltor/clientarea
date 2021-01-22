<?php
	include("config.php");

	define('OAUTH2_CLIENT_ID', $oauth2clientid);
	define('OAUTH2_CLIENT_SECRET', $oauth2clientsecret);

	$authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
	$tokenURL = 'https://discordapp.com/api/oauth2/token';
	$apiURLBase = 'https://discordapp.com/api/users/@me';

	session_start();

	if (isset($_POST['logout'])) {
	  	error_reporting(0);
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(),'',0,'/');
		session_regenerate_id(true);
		header('Location: '.$domain);
	}

	else if(get('code')) {
	  	// Exchange the auth code for a token
	  	$token = apiRequest($tokenURL, array(
	    	"grant_type" => "authorization_code",
	    	'client_id' => OAUTH2_CLIENT_ID,
	    	'client_secret' => OAUTH2_CLIENT_SECRET,
	    	'redirect_uri' => 'https://yoursite.location/ifyouneedit',
	    	'code' => get('code')
	  	));
	  	$logout_token = $token->access_token;
	  	$_SESSION['access_token'] = $token->access_token;


	  	header('Location: ' . $_SERVER['PHP_SELF']);
	}

	// If the access token exists for Discord
	else if(session('access_token')) {
	  	$user = apiRequest($apiURLBase);

	  	$_SESSION['username'] = $user->username;
	  	$_SESSION['avatar'] = "https://cdn.discordapp.com/avatars/".$user->id."/".$user->avatar;
	  	$_SESSION['id'] = $user->id;
	  	//echo '<pre>';
	    //print_r($user);
	  	//echo '</pre>';

	}

	else if(!isset($_SESSION['username'])) {
		$params = array(
		    'client_id' => OAUTH2_CLIENT_ID,
		    'response_type' => 'code',
		    'scope' => 'identify guilds'
	  	);

	  	// Redirect the user to Discord's authorization page
	  	header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
	  	die();
	}

	if(isset($_SESSION['username'])) {
		$token = $_GET['token'];

		if ($token == "" or null) {
		}

		else if ($token !== "" or null) {
			$select = "SELECT * FROM purchases WHERE token = '".$token."' AND clientid = '".$_SESSION['id']."'";
			$query = mysqli_query($db, $select);
			$result = mysqli_fetch_array($query);

			if (mysqli_num_rows($query) <= 0) {
			}

			elseif (mysqli_num_rows($query) > 0) {
				$realFileName = $result['filename'];
				$file = "files/".$realFileName;
				$fp = fopen($file, 'rb');

				header("Content-Type: application/octet-stream");
				header("Content-Disposition: attachment; filename=$realFileName");
				header("Content-Length: " . filesize($file));
				fpassthru($fp);
			}
		}
	}

	if (isset($_POST['delete'])) {
		$delete = "DELETE FROM purchases WHERE id=".$_POST['orderid'];
		if (mysqli_query($db, $delete)) {
		}
	}

	else if (isset($_POST['create'])) {
		if ($dropdownenabled == "yes") {
			foreach ($_POST['filename'] as $key => $itemname) {
				$randomtoken = md5(uniqid(rand(), true));
				$filename = str_replace(" ", "", strtolower($_POST['filename'][$key]));
				$token = substr($randomtoken, 0, 8);
				$insert = "INSERT INTO purchases (name, clientid, price, date, token, filename) VALUES ('".$itemname."', '".$_POST['clientid']."', '".$_POST['price']."', '".date("n/j/Y")."', '".$token."', '".$filename.'.'.$filetype."')";
				$query = mysqli_query($db, $insert);
			}
		}

		elseif ($dropdownenabled == "no" or "") {
			$randomtoken = md5(uniqid(rand(), true));
			$token = substr($randomtoken, 0, 8);
			$insert = "INSERT INTO purchases (name, clientid, price, date, token, filename) VALUES ('".$_POST['name']."', '".$_POST['clientid']."', '".$_POST['price']."', '".date("n/j/Y")."', '".$token."', '".$_POST['filename']."')";
			$query = mysqli_query($db, $insert);
		}
	}

	else {
	}

	// API function
	function apiRequest($url, $post=FALSE, $headers=array()) {
	  	$ch = curl_init($url);
	 	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	  	$response = curl_exec($ch);


	  	if($post)
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

	  	$headers[] = 'Accept: application/json';

	  	if(session('access_token'))
	    	$headers[] = 'Authorization: Bearer ' . session('access_token');

	  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	  	$response = curl_exec($ch);
	  	return json_decode($response);
	}

	// Get function
	function get($key, $default=NULL) {
	  	return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
	}

	// Session function
	function session($key, $default=NULL) {
	  	return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Client Area - <?php echo $name ?></title>
	<link rel="icon" type="image/png" href="<?php echo $logo ?>" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-0c38nfCMzF8w8DBI+9nTWzApOpr1z0WuyswL4y6x/2ZTtmj/Ki5TedKeUcFusC/k" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet"> 
	<link rel="stylesheet" href="<?php echo $domain ?>/css/main.css">
	<meta name="theme-color" content="#<?php echo $color1 ?>">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:creator" content="@jekeltor">
	<meta property="og:url" content="<?php echo $domain ?>/clientarea">
	<meta property="og:title" content="Client Area - <?php echo $name ?>">
	<meta property="og:description" content="<?php echo $description ?>">
	<meta property="og:image" content="<?php echo $logo ?>">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
		window.onload = function() {
			document.querySelector(".preloader").classList.add("loaded");
		}
	</script>
	<style>
		:root {
			--color-one: #<?php echo $color1 ?>;
			--color-two: #<?php echo $color2 ?>;
			--color-three: #<?php echo $color3 ?>;
			--color-four: #<?php echo $color4 ?>;
		}

		body {
			margin: 0;
			font-family: 'Montserrat', sans-serif;
			background-color: var(--color-two);
		}

		input, form, button {
			font-family: 'Montserrat', sans-serif;
		}

		.preloader {
			display: flex;
			opacity: 1;
			height: 100vh;
			width: 100%;
			position: fixed;
			z-index: 7;
			background-color: var(--color-three);
			transition: all .5s ease;
			align-items: center;
			justify-content: center;
		}

		.preloader.loaded {
			z-index: -1;
			opacity: 0;
		}

		.top {
			display: flex;
			height: 100vh;
			width: 100%;
			background-color: var(--color-two);
			align-items: center;
			justify-content: center;
		}

		.area {
			display: flex;
			position: relative;
			z-index: 2;
			width: 75%;
			height: 75vh;
			align-items: center;
			justify-content: center;
		}

		.area .client, .area .login, .area .register {
			background-color: var(--color-three);
			height: 100%;
		}

		.area div.hidden {
			display: none;
		}

		.area .login {
			display: flex;
			width: 30%;
			text-align: center;
			align-items: center;
			justify-content: center;
			padding: 2vh 5%;
		}

		.area .login .center {
			height: auto;
			width: 100%;
		}

		.area .login .center img {
			height: 25vh;
		}
		
		.area .login .center h1 {
			font-size: 3vh;
			color: #fff;
		}

		.area .login .center p {
			font-size: 1.25vh;
			color: #fff;
			margin: 0;
			margin-top: 2vh;
		}

		.area .login .center p a {
			text-decoration: underline;
			cursor: pointer;
		}

		.area .login .center form.main {
			margin-top: 5vh;
		}

		.area .login .center form.main input[type=text], .area .login .center form.main input[type=password] {
			display: block;
			width: 96%;
			font-size: 1.75vh;
			background-color: var(--color-two);
			border: none;
			outline: none;
			margin-bottom: 3vh;
			padding: .75vh 2%;
			color: #fff;
		}

		.area .login .center form.main input[type=text]::placeholder, .area .login .center form.main input[type=password]::placeholder {
			opacity: 1;
		}

		.area .login .center form.main input[type=submit] {
			-webkit-appearance: none;
			width: 100%;
			margin: 0;
			padding: 1vh 2%;
			background-color: var(--color-one);
			color: #fff;
			border-radius: .5vh;
			font-size: 2vh;
			outline: none;
			border: none;
			cursor: pointer;
			text-align: center;
			margin-top: 1vh;
		}

		.area .login .center form.discord input[type=submit] {
			-webkit-appearance: none;
			width: 60%;
			margin: 0 20%;
			padding: 1vh 2%;
			background-color: #7289DA;
			color: #fff;
			border-radius: .5vh;
			font-size: 2vh;
			outline: none;
			border: none;
			cursor: pointer;
			text-align: center;
			margin-top: 2vh;
		}

		.area .register {
			display: flex;
			width: 30%;
			text-align: center;
			align-items: center;
			justify-content: center;
			padding: 2vh 5%;
		}

		.area .register .center {
			height: auto;
			width: 100%;
		}

		.area .register .center img {
			height: 25vh;
		}
		
		.area .register .center h1 {
			font-size: 3vh;
			color: #fff;
		}

		.area .register .center p {
			font-size: 1.25vh;
			color: #fff;
			margin: 0;
			margin-top: 2vh;
		}

		.area .register .center p a {
			text-decoration: underline;
			cursor: pointer;
		}

		.area .register .center form.main {
			margin-top: 5vh;
		}

		.area .register .center form.main input[type=text], .area .register .center form.main input[type=email], .area .register .center form.main input[type=password] {
			display: block;
			width: 96%;
			font-size: 1.75vh;
			background-color: var(--color-two);
			border: none;
			outline: none;
			margin-bottom: 3vh;
			padding: .75vh 2%;
			color: #fff;
		}

		.area .register .center form.main input[type=text]::placeholder, .area .register .center form.main input[type=email]::placeholder, .area .register .center form.main input[type=password]::placeholder {
			opacity: 1;
		}

		.area .register .center form.main input[type=submit] {
			-webkit-appearance: none;
			width: 100%;
			margin: 0;
			padding: 1vh 2%;
			background-color: var(--color-one);
			color: #fff;
			border-radius: .5vh;
			font-size: 2vh;
			outline: none;
			border: none;
			cursor: pointer;
			text-align: center;
			margin-top: 1vh;
		}

		.area .client {
			width: 100%;
			position: relative;
			overflow: auto;
		}

		.area .client .clienttop {
			display: flex;
			width: 67.5%;
			padding: 1vh 3.75%;
			background-color: var(--color-four);
			align-items: center;
			border-bottom: .1vh solid var(--color-one);
			position: fixed;
		}

		.area .client .clienttop .left, .area .client .clienttop .right {
			display: flex;
			width: 50%;
			align-items: center;
		}

		.area .client .clienttop .left {
			justify-content: flex-start;
		}

		.area .client .clienttop .left span {
			font-size: 1.75vh;
			color: #fff;
			margin-left: 3%;
			font-weight: 500;
		}

		.area .client .clienttop .left .avatar {
			display: inline-block;
			height: 5vh;
			width: 5vh;
			border-radius: 50%;
			background-size: cover;
			background-repeat: no-repeat;
			background-position: center;
		}

		.area .client .clienttop .right {
			text-align: right;
			align-items: center;
			justify-content: flex-end
		}

		.area .client .clienttop .right form {
			margin: 0;
			padding: 0;
		}

		.area .client .clienttop .right form input {
			font-size: 1.75vh;
			color: #fff;
			background-color: transparent;
			border: none;
			outline: none;
			cursor: pointer;
			font-weight: 500;
		}

		.area .client table {
			width: 90%;
			margin: 0 5%;
			margin-top: 12vh;
			border-collapse: collapse;
			margin-bottom: 5vh;
		}

		.area .client th {
			color: var(--color-one);
			font-size: 1.75vh;
			padding-bottom: 1vh;
		}

		.area .client table .header {
			border-bottom: .1vh solid var(--color-one);
		}

		.area .client table td {
			width: 25%;
			padding-top: 2vh;
			font-size: 1.75vh;
			color: #fff;
		}

		.area .client table td a {
			color: #fff;
		}

		.area .client table .a {
			width: 20%;
		}

		.area .client table input[type=submit] {
			font-size: 1.5vh;
			color: #fff;
			background-color: var(--color-one);
			border: none;
			outline: none;
			padding: .75vh 4%;
			cursor: pointer;
		}

		.area .client table .l {
			text-align: left;
		}

		.area .client table .c {
			text-align: center;
		}

		.area .client table .r {
			text-align: right;
		}

		.area .client .create {
			display: grid;
			width: 90%;
			grid-auto-rows: 1fr;
			grid-column-gap: 2%;
			grid-row-gap: 6vh;
			grid-template-columns: repeat(5, 1fr);
			text-align: center;
			margin: 0 5%;
			margin-top: 12vh; 
			margin-bottom: 5vh; 
			text-align: center;
		}

		<?php
			if($dropdownenabled == "yes") {
		?>

		.area .client .create input[type=text] {
			font-size: 1.75vh;
			color: #fff;
			background-color: var(--color-two);
			border: none;
			outline: none;
			padding: .75vh 2%;
			height: 2vh;
		}

		.area .client .create input[type=text]::placeholder {
			opacity: 1;
		}

		.area .client .create select {
			font-size: 1.75vh;
			color: #fff;
			background-color: var(--color-two);
			border: none;
			outline: none;
			padding: .75vh 2%;
			height: 8.75vh;
			overflow-y: auto;
		}

		.area .client .create input[type=submit] {
			font-size: 1.75vh;
			color: #fff;
			background-color: var(--color-one);
			border: none;
			outline: none;
			padding: .75vh 2%;
			cursor: pointer;
			height: 3.5vh;
		}

		<?php
			}

			elseif($dropdownenabled == "no" or "") {
		?>
		.area .client .create input[type=text] {
			font-size: 1.75vh;
			color: #fff;
			background-color: var(--color-two);
			border: none;
			outline: none;
			padding: .75vh 2%;
		}

		.area .client .create input[type=text]::placeholder {
			opacity: 1;
		}

		.area .client .create input[type=submit] {
			font-size: 1.75vh;
			color: #fff;
			background-color: var(--color-one);
			border: none;
			outline: none;
			padding: .75vh 2%;
			cursor: pointer;
		}
		<?php
			}
		?>
		@media screen and (max-width: 600px), (orientation : portrait) {
			.area .client .create {
				grid-template-columns: repeat(1, 1fr);
				grid-row-gap: 1vh;
			}
		}
	</style>
</head>
<body>
	<section class="preloader">
	</section>
	<section class="top">
		<div class="area">
			<?php
				if(isset($_SESSION['username'])) {
			?>	
			<div class="client">
				<div class="clienttop">
					<div class="left">
						<div class="avatar" style="background-image: url(<?php echo $_SESSION['avatar'] ?>)"></div>
						<span>Welcome, <?php echo $_SESSION['username'] ?></span>
					</div>
					<div class="right">
						<form method="POST">
							<input type="submit" name="logout" value="Logout">
						</form>
					</div>
				</div>
					<?php 
					if (in_array($_SESSION['id'], $ownerids)) {
					?>	
						<form method="POST" class="create">
							<input type="text" name="name" placeholder="Name"><input type="text" name="clientid" placeholder="Discord ID"><input type="text" name="price" placeholder="Price">
							<?php if ($dropdownenabled == "yes") { ?>
								<select name="filename[]" multiple><?php 
								foreach ($products as $key => $productname) {
							?>
								<option value="<?php echo $productname ?>"><?php echo $productname ?></option>
							<?php
								}
							?></select><?php } elseif ($dropdownenabled == "no" or "") { ?><input type="text" name="filename" placeholder="File Name"><?php } ?><input type="submit" name="create" value="Create">
						</form>
					<?php
						$select = "SELECT * FROM purchases";
						$query = mysqli_query($db, $select);

						echo '<table style="margin-top: 0vh !important"><tr class="header"><th class="l">Name of Product</th><th class="l">Discord ID</th><th class="c">Price</th><th class="r">Date of purchase</th><th class="r">Delete</th></tr>';

						while ($row = mysqli_fetch_array($query)) {
					?>
						<tr><td class="l a"><?php echo $row["name"] ?></td><td class="l a"><?php echo $row['clientid'] ?></td><td class="c a">$<?php echo $row["price"] ?></td><td class="r a"><?php echo $row["date"] ?></td><td class="r a"><form method="POST"><input type="text" value="<?php echo $row['id'] ?>" name="orderid" hidden><input type="submit" value="Delete" name="delete"></form></td></tr>
					<?php } echo "</table"; }

					elseif (!in_array($_SESSION['id'], $ownerids)) {
						$select = "SELECT * FROM purchases WHERE clientid = '".$_SESSION['id']."'";
						$query = mysqli_query($db, $select);

						echo '<table><tr class="header"><th class="l">Name of Product</th><th class="c">Price</th><th class="c">Date of purchase</th><th class="r">Download</th></tr>';

						while ($row = mysqli_fetch_array($query)) {
					?>
						<tr><td class="l"><?php echo $row["name"] ?></td><td class="c">$<?php echo $row["price"] ?></td><td class="c"><?php echo $row["date"] ?></td><td class="r"><?php if ($row['token'] == "" or null) { ?>Not Available<?php } else if ($row['token'] !== "" or null) {?><a href="<?php echo $domain ?>?token=<?php echo $row['token'] ?>">Click Here</a><?php } ?></td></tr>
					<?php } echo "</table>";} ?>
			</div>
			<?php 
				}
			?>
		</div>
	</section>
	<script src="<?php echo $domain ?>/js/main.js"></script>
</body>
</html>