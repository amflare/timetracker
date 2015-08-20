<?php

// session initialization
session_start();
$sessionId = session_id();

// check if logged in
if (!Auth::check()) {
	header("Location: http://" . URL);
	exit();
} 