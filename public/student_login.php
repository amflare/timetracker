<?php

var_dump($_POST);

$exception = true;

require_once '../bootstrap-student.php';


if (Input::has("studentUser")) {
	$_SESSION["rank"] = "student";
	$username = Input::get('studentUser');

	$select = "SELECT id FROM students WHERE username = :username";
	$stmt = $dbc->prepare($select);
	$stmt->bindValue(':username', Input::get('studentUser'), PDO::PARAM_STR);
	$stmt->execute();
	$id = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$_SESSION["id"] = $id[0]['id'];
	var_dump("Loggedin");
	var_dump($_SESSION);
	header("Location: http://" . URL . "/clock");
	exit();
} else {
	$_SESSION["failedLoginStudent"] = true;
	var_dump("notloggedin");
	var_dump($_SESSION);
	header("Location: http://" . URL);
	exit();
}

