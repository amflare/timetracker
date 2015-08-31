<?php 

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

// in case of edit
$_SESSION["last_timesheet"] = Input::get("id");

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

// check if modal prompted refresh
if (Input::has("standingChange")) {
	// change standing in DB
	$id = Input::get("id");
	$standing = Input::get("standingChange");

	$update = "UPDATE students 
 				SET standing = :standing 
 				WHERE id = :id";
	$stmt = $dbc->prepare($update);
	$stmt->bindValue(':standing', $standing, PDO::PARAM_STR);
	$stmt->bindValue(':id', $id, PDO::PARAM_STR);
	$stmt->execute();

}

// check if add timelog promted refresh
if (Input::has("pickDate")) {
	$insert = "INSERT INTO timelogs (date_logged, clock_in, goal, student_id) 
				VALUES (:date_logged, :clock_in, :goal, :student_id)";
	$stmt = $dbc->prepare($insert);
	$stmt->bindValue(':date_logged', Input::get('pickDate'), PDO::PARAM_STR);
	$stmt->bindValue(':clock_in', Input::get('pickTime'), PDO::PARAM_STR);
	$stmt->bindValue(':goal', Input::get('setGoal'), PDO::PARAM_STR);
	$stmt->bindValue(':student_id', Input::get('id'), PDO::PARAM_STR);

	$stmt->execute();
}

// get timelogs for student
$studentid = Input::get("id");
$select = "SELECT * , t.id
			FROM timelogs t
			JOIN students s ON s.id = t.student_id
			WHERE t.student_id = $studentid 
			ORDER BY t.id DESC 
			LIMIT 25";
$stmt = $dbc->query($select);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// determine time total
// find monday
$dw = date( "w", strtotime(date("Y-m-d")));
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
$schoolHours = $totalTime->format('h:i:s');

// caluclate percentage
//find time in seconds
$str_time = $schoolHours;
sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

//divide to find percent
$twelve = 43200;
$percent = ($time_seconds / $twelve) * 100;
$percent = round($percent);
if (empty($lengths)) {
	$percent = 0;
	$schoolHours = "00:00:00";

}

//format name
$name = $logs[0]["username"];
$name = explode(".", $name);
$firstName = ucfirst($name[0]);
$lastName = ucfirst($name[1]);
$fullName = $firstName . " " . $lastName;


?>
<html ng-app="login">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Timothy Birrell">

	
	<title><?= $fullName ?></title>

	<!-- Bootstrap core CSS -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/custom.css" rel="stylesheet">
	<link href="/css/classic.css" rel="stylesheet">
	<link href="/css/classic.date.css" rel="stylesheet">
	<link href="/css/classic.time.css" rel="stylesheet">

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
	.studentName {
		text-align: center;
		margin: 20px;
		margin-bottom: 0px;
		font-size: 3em;
	}
	.standing {
		text-align: center;
		margin: 3px;
		font-size: 1.4em;
	}
	.editStanding {
		font-size: 1.4rem;
	}
	.btn {
		margin-bottom: 20px;
	}
	#standingModal {
		top: 25%;
	}
	#addLogModal {
		top: 25%;
	}
	.formMargin {
		margin: 10px;
	}

	</style>
</head>
<body class="container">
	<?php require_once '../views/headeradmin.php'; ?>
	<div class="modal fade" id="standingModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<form class="formMargin" method="post">
					<div class="form-group">
						<select class="form-control" name="standingChange">
							<option value="Good">Good</option>
							<option value="Written Warning">Written Warning</option>
							<option value="Probation">Probation</option>
							<option value="Loss of Scholarship">Loss of Scholarship</option>
							<option value="Inactive">Inactive</option>
						</select>
					</div>
					<div class="form-group">
						<button class="btn btn-success">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade" id="addLogModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<form class="formMargin" method="post">
					<div class="form-group">
						<input type="text" id="pickDate" name="pickDate" class="form-control" placeholder="Pick Day...">
					</div>
					<div class="form-group">
						<input type="text" id="pickTime" name="pickTime" class="form-control" placeholder="Pick Time..." required>
					</div>
					<div class="form-group">
						<textarea name="setGoal" class="form-control" placeholder="Add Daily Goal"></textarea>
					</div>
					<div class="form-group">
						<button class="btn btn-success">Add</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="studentName">
		-- <?= $fullName ?> --
	</div>
	<div class="standing">
		<?= $logs[0]["standing"] ?> <a href="#" data-toggle="modal" data-target="#standingModal"><span class="editStanding glyphicon glyphicon-edit"></span></a>
	</div>
	<div class="everything">
		<div class="progress">
			<div class="progress-bar progress-bar-success progress-bar-striped <?php if ($percent < 10) {echo "black";} ?>" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percent ?>%">
				<?= $schoolHours; ?>
			</div>
		</div>
	</div>
	<button class="btn btn-primary" data-toggle="modal" data-target="#addLogModal">Add Timelog</button>
	<div class="panel panel-default signin">
		<table class="table table-striped">
			<tr>
				<th>Edit</th>
				<th>Clocked In</th>
				<th>Clocked Out</th>
				<th>Time Spent</th>
				<th>Goal</th>
				<th>Done?</th>
			</tr>
			<?php foreach ($logs as $log) : ?>
				<tr>
					<td><a href="http://<?= URL ?>/admin/edit/log?id=<?= $log["id"] ?>"><span class="glyphicon glyphicon-edit"></span></a></td>
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
	<script>
		// ---DatePicker---
		$("#pickDate").pickadate({
			format: "yyyy/mm/dd"
		});
		// ---TimePicker---
		$("#pickTime").pickatime({
			format: "h:i a",
			formatSubmit: "HH:i:00",
			hiddenName: true,
			interval: 15,
			min: [9,0],
  			max: [17,0]
		});
	</script>
</body>
</html>