<?php

// check if logged in
if (!isset($exception) && !Auth::check()) {
	var_dump(!isset($exception));
	echo "test";

	// header("Location: http://" . URL);
	// exit();
} 