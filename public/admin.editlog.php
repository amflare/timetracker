<?php 

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

//incase first page load
$success = false;

// if just edited
if (Input::has("clockinDay")) {

	// booleanize metGoal
	if (!Input::has("metGoal")) {
		$metGoal = 0;
	} else if (Input::get("metGoal") == "on") {
		$metGoal = 1;
	}

	// recaluate length
	$then = Input::get('clockinDay') . " " . Input::get('clockinTime');
	$now = Input::get('clockoutDay') . " " . Input::get('clockoutTime');

	//calculate difference
	$now = new DateTime($now);
	$then = new DateTime($then);
	$diff = $then->diff($now);
	$diffToLog = $diff->h . ":" . $diff->i . ":" . $diff->s;

	$update = "UPDATE timelogs 
 				SET date_logged = :date_logged,
 				clock_in = :clock_in,
 				date_out = :date_out,
 				clock_out = :clock_out,
 				length = :length,
 				goal = :goal,
 				goal_reached = :goal_reached
 				WHERE id = :id";
	$stmt = $dbc->prepare($update);
	$stmt->bindValue(':date_logged', Input::get('clockinDay'), PDO::PARAM_STR);
	$stmt->bindValue(':clock_in', Input::get('clockinTime'), PDO::PARAM_STR);
	$stmt->bindValue(':date_out', Input::get('clockoutDay'), PDO::PARAM_STR);
	$stmt->bindValue(':clock_out', Input::get('clockoutTime'), PDO::PARAM_STR);
	$stmt->bindValue(':length', $diffToLog, PDO::PARAM_STR);
	$stmt->bindValue(':goal', Input::get('setGoal'), PDO::PARAM_STR);
	$stmt->bindValue(':goal_reached', $metGoal, PDO::PARAM_STR);
	$stmt->bindValue(':id', Input::get('id'), PDO::PARAM_STR);
	$stmt->execute();

	// set success message
	$success = true;
}

// get timelog
$id = Input::get("id");
$select = "SELECT * 
			FROM timelogs
			WHERE id = :id "; 
$stmt = $dbc->prepare($select);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
	
$log = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<html ng-app="login">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Timothy Birrell">

	
	<title>Time Tracker</title>

	<!-- Bootstrap core CSS -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/custom.css" rel="stylesheet">
	<link href="/css/classic.css" rel="stylesheet">
	<link href="/css/classic.date.css" rel="stylesheet">
	<link href="/css/classic.time.css" rel="stylesheet">

	<style>
	.panel {
		width: 700px;
		max-width: 100%;
		margin: 25px auto;
		max-width: 100%;
	}
	.alert {
		width: 700px;
		max-width: 100%;
		margin: 25px auto;
		max-width: 100%;
	}
	.formMargin {
		margin: 10px;
	}

	</style>
</head>
<body class="container">
	<?php require_once '../views/headeradmin.php'; ?>
	<?php if ($success) : ?>
		<div class="alert alert-success" role="alert">
			Time-log successfully updated. <a href="http://<?= URL ?>/admin/student?id=<?= $_SESSION["last_timesheet"] ?>">Click here to go back</a>.
		</div>
	<?php endif; ?>
	<div class="alert alert-info" role="alert">
		Input dates using the following format
		<br>
		YYYY-MM-DD 
		<br>
		e.g. 2015-08-27
	</div>
	<div class="alert alert-info" role="alert">
		Input times using the following format (24 hour clock)
		<br>
		HH:MM:SS 
		<br>
		e.g. 17:43:00
	</div>
	<div class="panel panel-default signin">
		<form class="formMargin" method="post">
			<div class="form-group">
				<label for="clockinDay">Clock-In Date</label>
				<input type="text" class="form-control" name="clockinDay" id="clockinDay" value="<?= $log[0]["date_logged"] ?>">
			</div>
			<div class="form-group">
				<label for="clockinTime">Clock-In Time</label>
				<input type="text" class="form-control" name="clockinTime" id="clockinTime" value="<?= $log[0]["clock_in"] ?>">
			</div>
			<div class="form-group">
				<label for="clockoutDay">Clock-Out Date</label>
				<input type="text" class="form-control" name="clockoutDay" id="clockoutDay" value="<?= $log[0]["date_out"] ?>">
			</div>
			<div class="form-group">
				<label for="clockoutTime">Clock-Out Time</label>
				<input type="text" class="form-control" name="clockoutTime" id="clockoutTime" value="<?= $log[0]["clock_out"] ?>">
			</div>
			<div class="form-group">
				<label for="setGoal">Goal</label>
				<textarea class="form-control" name="setGoal" id="setGoal"><?= $log[0]["goal"] ?></textarea>
			</div>
			<div class="form-group">
				<label for="metGoal">Goal Achieved?</label>
				<input type="checkbox" name="metGoal" id="metGoal" <?php if ($log[0]["goal_reached"] == "1") {echo "checked";} ?>>
			</div>
			<div class="form-group">
				<button class="btn btn-primary">Save</button>		
			</div>
		</form>
	</div>
	<?php require_once '../views/footer.php'; ?>
	<script>
		// ---DatePicker---
		$("#clockinDay").pickadate({
			format: "yyyy/mm/dd",
			editable: true
		});
		$("#clockoutDay").pickadate({
			format: "yyyy/mm/dd",
			editable: true
		});
		// ---TimePicker---
		$("#clockinTime").pickatime({
			format: "h:i a",
			formatSubmit: "HH:i:00",
			hiddenName: true,
			editable: true,
			interval: 15,
			min: [9,0],
  			
		$("#clockoutTime").pickatime({
			format: "h:i a",
			formatSubmit: "HH:i:00",
			hiddenName: true,
			editable: true,
			interval: 15,
			min: [9,0],
  			max: [17,0]
		});
	</script>
</body>
</html>