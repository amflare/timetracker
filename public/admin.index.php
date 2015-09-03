<?php 

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

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

// determine time total
// find monday
$dw = date( "w", strtotime(date("Y-m-d")));
$date = new DateTime(date("Y-m-d"));
$date->sub(new DateInterval('P' . ($dw - 1) . 'D'));
$monday = $date->format('Y-m-d');

// fetch logs
$select = "SELECT * 
			FROM timelogs t 
			JOIN students s ON s.id = t.student_id
			WHERE t.date_logged >= :monday
			AND t.student_id = 2
			ORDER BY t.id DESC 
			LIMIT 25";
$stmt = $dbc->prepare($select);
$stmt->bindValue(':monday', $monday, PDO::PARAM_STR);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<html ng-app="login">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Timothy Birrell">

	
	<title>Admin Panel</title>

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
		color: #00305A;
		font-weight: bold;
	}

	</style>
</head>
<body class="container">
	<?php require_once '../views/headeradmin.php'; ?>
	<div class="everything">
		<div class="panel panel-default signin">
			<table class="table table-striped">
				<tr>
					<th>Student</th>
					<th>Clocked In</th>
					<th>Clocked Out</th>
					<th>Time Spent</th>
					<th>Goal</th>
					<th>Done?</th>
				</tr>
				<?php foreach ($logs as $log) : ?>
					<tr>
						<td><a href="http://<?= URL ?>/admin/student?id=<?= $log["student_id"] ?>"><?= $log["first_name"] . " " . $log["last_name"]?></a></td>
						<td><?= convertDate($log["date_logged"], $log["clock_in"]); ?></td>
						<td><?= convertDate($log["date_out"], $log["clock_out"]); ?></td>
						<td><?= $log["length"]; ?></td>
						<td><?= $log["goal"]; ?></td>
						<td class="icon"><span <?php if ($log["goal_reached"] == "1") {echo 'class="glyphicon glyphicon-ok"';} ?>></span></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
	<?php require_once '../views/footer.php'; ?>
</body>
</html>