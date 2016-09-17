<?php 

$_SESSION["pageRank"] = "student";

require_once '../bootstrap-student.php';

date_default_timezone_set('America/Chicago');

function convertDate($date, $time) {
	if ($date && $time) {
		$date = new DateTime($date);
		$fDate = $date->format('M j');
		$time = new DateTime($time);
		$fTime = $time->format('g:i a');
		return $fDate . ", " . $fTime;
	} else {
		return " ";
	}
}

// get timelogs for student
$studentid = $_SESSION["id"];
$select = "SELECT * 
			FROM timelogs 
			WHERE student_id = $studentid 
			ORDER BY id DESC 
			LIMIT 25";
$stmt = $dbc->query($select);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// determine time total
// find monday
$dw = date( "w", strtotime(date("Y-m-d")));
if ($dw == 0) {
	$dw = 8;
}
$date = new DateTime(date("Y-m-d"));
$date->sub(new DateInterval('P' . ($dw - 1) . 'D'));
$monday = $date->format('Y-m-d');

// fetch logs
$select = "SELECT length 
			FROM timelogs 
			WHERE student_id = $studentid 
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

//add clocked in time
//fetch from db
$today = date('Y-m-d');
$select = "SELECT clock_in
			FROM timelogs
			WHERE length IS NULL
			AND date_logged = :today";
$stmt = $dbc->prepare($select);
$stmt->bindValue(':today', $today, PDO::PARAM_STR);
$res = $stmt->execute();
$clockIn = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get diff
$now = new DateTime(date('Y-m-d H:i:s'));
$then = new DateTime($clockIn[0]["clock_in"]);
$diff = $then->diff($now);

$time = new DateTime($diff->h .':'. $diff->i .':'. $diff->s);
$time = $time->format('\P\TH\Hi\Ms\S');
$totalTime->add(new DateInterval($time));

$schoolHours = $totalTime->format('H:i:s');

// caluclate percentage
//find time in seconds
$str_time = $schoolHours;
sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

//divide to find percent
$twelve = 43200;
$percent = ($time_seconds / $twelve) * 100;
$percent = round($percent);
if ((empty($lengths) || $length["length"] == null) && empty($clockIn)) {
	$percent = 0;
	$schoolHours = "00:00:00";

}

?>
<html ng-app="login">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Timothy Birrell">

	
	<title>Time Tracker</title>

	<!-- Bootstrap core CSS -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/custom.css" rel="stylesheet">

	<style>
	.everything {
		width: 700px;
		max-width: 100%;
		margin: 25px auto;
	}

	.panel {
		max-width: 100%;
	}

	.progress {
		height: 40px;
	}
	.progress-bar {
		font-size: 18px;
		line-height: 40px;
	}
	.icon {
		text-align: center;
	}
	.black {
		color: #00305A;
		font-weight: bold;
	}

	</style>
</head>
<body class="container">
	<?php require_once '../views/headerstudent.php'; ?>
	<div class="everything">
		<div class="progress">
			<div class="progress-bar progress-bar-success progress-bar-striped <?php if ($percent < 10) {echo "black";} ?>" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percent ?>%">
			    <?= $schoolHours; ?>
			</div>
		</div>
	</div>
	<div class="panel panel-default signin">
		<table class="table table-striped">
			<tr>
				<th>Clocked In</th>
				<th>Clocked Out</th>
				<th>Time Spent</th>
				<th>Goal</th>
				<th>Done?</th>
			</tr>
			<?php foreach ($logs as $log) : ?>
				<tr>
					<td><?= convertDate($log["date_logged"], $log["clock_in"]); ?></td>
					<td><?= convertDate($log["date_out"], $log["clock_out"]); ?></td>
					<td><?= $log["length"]; ?></td>
					<td><?= $log["goal"]; ?></td>
					<td class="icon"><span <?php if ($log["goal_reached"] == "1") {echo 'class="glyphicon glyphicon-ok"';} ?>></span></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php require_once '../views/footer.php'; ?>
</body>
</html>