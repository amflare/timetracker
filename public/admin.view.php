<?php 

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

// find monday
$dw = date( "w", strtotime(date("Y-m-d")));
$date = new DateTime(date("Y-m-d"));
$date->sub(new DateInterval('P' . ($dw - 1) . 'D'));
$monday = $date->format('Y-m-d');

// fetch logs
$select = "SELECT id, username 
			FROM students
			ORDER BY username";
$stmt = $dbc->query($select);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

function findTime($id, $dbc, $monday = null) {
	if (!$monday) {
		// find monday
		$dw = date( "w", strtotime(date("Y-m-d")));
		$date = new DateTime(date("Y-m-d"));
		$date->sub(new DateInterval('P' . ($dw - 1) . 'D'));
		$monday = $date->format('Y-m-d');
	} else {
		$date = new DateTime($monday);
		$monday = $date->format('Y-m-d');
	}

	// fetch logs
	$select = "SELECT length 
				FROM timelogs 
				WHERE student_id = $id 
				AND date_logged >= :monday";
	$stmt = $dbc->prepare($select);
	$stmt->bindValue(':monday', $monday, PDO::PARAM_STR);
	$stmt->execute();
	$lengths = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// add time
	$totalTime = new DateTime('00:00:00');
	foreach ($lengths as $length) {
		if ($length["length"]) {
			$time = new DateTime($length["length"]);
			$time = $time->format('\P\TH\Hi\Ms\S');
			$totalTime->add(new DateInterval($time));
		}
	}
	return $totalTime->format('H:i:s');
}

function calculatePercentage($time) {
	//find time in seconds
	$str_time = $time;
	sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
	$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

	//divide to find percent
	$twelve = 43200;
	$percent = ($time_seconds / $twelve) * 100;
	$percent = round($percent);

	return $percent;
}

function parseName($username) {
	$name = explode(".", $username);
	$firstName = ucfirst($name[0]);
	$lastName = ucfirst($name[1]);
	return $firstName . " " . $lastName;
}

?>

<html ng-app="login">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Timothy Birrell">

	
	<title>View All</title>

	<!-- Bootstrap core CSS -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/custom.css" rel="stylesheet">

	<style>
	.everything {
		max-width: 100%;
		margin: 25px auto;
	}

	.panel {
		max-width: 100%;
	}

	.icon {
		text-align: center;
	}
	.black {
		color: #000;
		font-weight: bold;
	}
	.name {
		width:auto;
   		text-align:right;
   		white-space: nowrap
	}
	.bar {
		width: 100%; 
	}


	</style>
</head>
<body class="container">
	<?php require_once '../views/headeradmin.php'; ?>
	<div class="everything">
		<div class="panel panel-default signin">
			<table class="table table-striped table-condensed">
				<tr>
					<th>Student</th>
					<th>Progress</th>
				</tr>
				<?php foreach ($students as $student) : ?>
				<?php 

				$fullName = parseName($student["username"]);
				$time = findTime($student["id"], $dbc);
				$percentage = calculatePercentage($time);
				?>
				<tr>
					<td class="name"><?= $fullName ?></td>
					<td class="bar">
						<div class="progress">
							<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percentage ?>%">
								<span class="<?php if ($percentage < 10) {echo "black";} ?>"><?= $time ?></span>
							</div>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
	<?php require_once '../views/footer.php'; ?>
</body>
</html>