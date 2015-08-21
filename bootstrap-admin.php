<?php 

//file needed to break up seesion start and authorization

require_once 'database/config.php';
require_once 'database/connect.php';
require_once 'Input.php';
require_once 'security/Auth.php';
require_once 'security/session_start.php';

$_SESSION["pageRank"] = "admin";

require_once 'security/authorize.php';