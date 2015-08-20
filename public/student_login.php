<?php

require_once '../bootstrap.php';;

if (Input::has("studentUser")) {
	$_SESSION["rank"] = "student";
	$username = Input::get('studentUser');

	$select = "SELECT id FROM students WHERE username = :username";
	$stmt = $dbc->prepare($select);
	$stmt->bindValue(':username', Input::get('studentUser'), PDO::PARAM_STR);
	$stmt->execute();
	$id = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$_SESSION["id"] = $id[0]['id'];
	header("Location: http://" . URL . "/student.index.php");
	exit();
} else {
	$_SESSION["failedLogin"] = true;
	header("Location: http://" . URL);
	exit();
}

