<?php
	// DB Information
	$db_host = 'localhost';
	$db_user = ''; // Change this line
	$db_pass = ''; // Change this line
	$db_name = ''; // Change this line
	$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);


	// Site Information
	$name = "Jake Hamblin";
	$logo = "https://jakehamblin.com/images/logo.png";
	$color1 = "3fa3eb";
	$color2 = "142430";
	$color3 = "0c151c";
	$color4 = "0a1117";
	$description = "Software programmer and website developer";
	$domain = "https://projects.jakehamblin.com/clientarea";

	// Discord Information
	$oauth2clientid = "";
	$oauth2clientsecret = "";
	$ownerids = [
		"XXX",
		"XXX",
	];

	// Add to dropdown
	$dropdownenabled = "no"; // To enable the dropdown, do "yes". To disable, do "no". If the value is no, don't do anything below
	$filetype = "zip"; // Put the filetype here. IE: zip or rar
	$foldername = "name_of_folder";
	$products = [
		"Item 1",
		"Item 2",
		"Item 3",
	]
?>