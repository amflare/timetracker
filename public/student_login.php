<?php

require_once '../bootstrap.php';;

if (Input::has("studentUser")) {
	$_SESSION["rank"] = "student";
	$username = Input::get('studentUser');

	$select = "SELECT id FROM students WHERE username = :username";
	$stmt = $dbc->prepare($select);
	$stmt->bindValue(':username', Input::get('studentUser'), PDO::PARAM_STR);
	$id = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$_SESSION["id"] = $id;

	var_dump($id);
	var_dump($select);
	var_dump($stmt);

	var_dump($stmt->bindValue(':username', Input::get('studentUser'), PDO::PARAM_STR));
	var_dump($_SESSION["id"]);
	var_dump($username);
	var_dump(Input::get('studentUser'));
} else {
}

