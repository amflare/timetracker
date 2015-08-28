<?php

$exception = true;

require_once '../bootstrap-admin.php';

if (Input::has("adminUser")) {
	if (Auth::attempt(Input::get("adminPass"), Input::get("adminUser"), $dbc)) {
		header("Location: http://" . URL . "/admin");
		exit();
	} else {
		$_SESSION["failedLoginAdmin"] = true;
		header("Location: http://" . URL);
		exit();
	}
	
} else {
	$_SESSION["failedLoginAdmin"] = true;
	header("Location: http://" . URL);
	exit();
}

