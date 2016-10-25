<?php

//file needed to break up seesion start and authorization

require_once 'database/config.php';
require_once 'database/connect.php';
require_once 'utilities/Input.php';
require_once 'security/Auth.php';
require_once 'security/session_start.php';

$_SESSION["pageRank"] = "student";

require_once 'security/authorize.php';

$select = "SELECT a.calendly
            FROM admins as a
            JOIN cohorts as c on a.id = c.admin
            JOIN students as s on c.id = s.cohort
            WHERE s.id = :id ";
$stmt = $dbc->prepare($select);
$stmt->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();

$_SESSION['calendly'] = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['calendly'];