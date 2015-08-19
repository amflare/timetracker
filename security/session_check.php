<?php

// session initialization
session_start();
$sessionId = session_id();

// // check if logged in
// if (!Auth::check() && !isset($onFrontPage)) {
// 	header("Location: http://" . URL);
// 	exit();
// } 