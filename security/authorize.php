<?php

// check if logged in
if (!isset($exception) && !Auth::check()) {

	header("Location: http://" . URL);
	exit();
} 