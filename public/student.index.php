<?php 

require_once '../bootstrap-student.php';

date_default_timezone_set('America/Chicago');

// if just clocked in
if (Input::has("dailyGoal")) {

	$date = date("Y-m-d");
	$time = date("H:i:s");

	$insert = "INSERT INTO timelogs (date_logged, clock_in, goal, student_id) 
				VALUES (:date_logged, :clock_in, :goal, :student_id)";
	$stmt = $dbc->prepare($insert);
	$stmt->bindValue(':date_logged', $date, PDO::PARAM_STR);
	$stmt->bindValue(':clock_in', $time, PDO::PARAM_STR);
	$stmt->bindValue(':goal', Input::get('dailyGoal'), PDO::PARAM_STR);
	$stmt->bindValue(':student_id', $_SESSION["id"], PDO::PARAM_STR);

	$stmt->execute();
}


// if just clocked out
if (Input::has("clockout")) {
	$studentid = $_SESSION["id"];
	$date = date("Y-m-d");
 	$time = date("H:i:s");
 	$now = $date . " " . $time;

 	// fetch clock-in time
 	$select = "SELECT concat(date_logged, ' ', clock_in) as datetime FROM timelogs WHERE id = (SELECT MAX(id) FROM timelogs WHERE student_id = $studentid)";
	$stmt = $dbc->query($select);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$then = $result[0]["datetime"];

	//calculate difference
	$now = new DateTime($now);
	$then = new DateTime($then);
	$diff = $then->diff($now);
	$diffToLog = $diff->h . ":" . $diff->i . ":" . $diff->s;

	// pull id of rox to edit
	$select = "SELECT MAX(id) FROM timelogs WHERE student_id = $studentid";
	$stmt = $dbc->query($select);
	$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$maxId = $r[0]["MAX(id)"];

	// booleanize metGoal
	if (!Input::has("metGoal")) {
		$metGoal = 0;
	} else if (Input::get("metGoal") == "on") {
		$metGoal = 1;
	}

	// update timelogs
 	$update = "UPDATE timelogs 
 				SET date_out = :date_out, 
 				clock_out = :clock_out, 
 				length = :length, 
 				goal_reached = :goal_reached 
 				WHERE id = $maxId";
	$stmt = $dbc->prepare($update);
	$stmt->bindValue(':date_out', $date, PDO::PARAM_STR);
	$stmt->bindValue(':clock_out', $time, PDO::PARAM_STR);
	$stmt->bindValue(':length', $diffToLog, PDO::PARAM_STR);
	$stmt->bindValue(':goal_reached', $metGoal, PDO::PARAM_STR);

	$stmt->execute();
}

//determine clock status
$studentid = $_SESSION["id"];
$select = "SELECT clock_out, goal FROM timelogs WHERE id = (SELECT MAX(id) FROM timelogs WHERE student_id = $studentid)";
$stmt = $dbc->query($select);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($result[0]["clock_out"]) {
	// if clocked in...
	$clocked = true;
} else {
	// if clocked out...
	$clocked = false;
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

		.clockin {
			width: 500px;
			max-width: 100%;
			margin: 35px auto;
		}
		form {
			margin-top: 20px;
		}
		#clock {
			font-size: 4em;
			font-family:'Ubuntu', Helvetica, sans-serif;
			font-weight: bold;
			margin: 0 auto;
			text-align: center;
			color: #FFF;
			background: rgba(0, 48, 90, 0.95);
			width: 260px;
			border-bottom-right-radius: 4px;
			border-bottom-left-radius: 4px;
		}
		#goal {
			font-weight: bold;
		}

	</style>
</head>
<body onload="startTime()">
	<?php require_once '../views/headerstudent.php'; ?>
	<div id="clock"></div>
	<div class="panel panel-default clockin">
		<div class="panel-body">
			<?php if ($clocked) : ?>
			<!-- if clocked in -->
			<form method="POST">
				<div class="form-group">
					<textarea class="form-control" name="dailyGoal" placeholder="What is your goal today?"></textarea>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-success">Clock In</button>
				</div>
			</form>
			<?php else : ?>
			<!-- if clocked out -->
			<div class="alert alert-info"><span id="goal">Goal: </span><?= $result[0]["goal"]  ?></div>
			<form method="POST">
				<div class="form-group">
					<input type="checkbox" name="metGoal">
					I achived my goal today
				</div>
				<div class="form-group">
					<input type="hidden" name="clockout" value="set">
					<button type="submit" class="btn btn-success">Clock Out</button>
				</div>
			</form>
			<?php endif; ?>
		</div>
	</div>
	<?php require_once '../views/footer.php'; ?>

<script>

// clock
	function startTime() {
	    var today = new Date();
	    var hour = today.getHours();
	    // console.log(hour);
	    if (hour > 12) {
	    	var h = hour - 12;
	    } else if (hour == 0) {
	    	var h = 12;
	    } else {
	    	var h = hour;
	    }
    	// var h = hour;
	    var m = today.getMinutes();
	    var s = today.getSeconds();
	    m = checkTime(m);
	    s = checkTime(s);
	    document.getElementById('clock').innerHTML = h+":"+m+":"+s;
	    var t = setTimeout(function(){startTime()},500);
	}

	function checkTime(i) {
	    if (i<10) {i = "0" + i};  // add zero in front of numbers < 10
	    return i;
	}
</script>
</head>


</body>
</html>