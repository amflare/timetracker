<?php 

require_once '../bootstrap-student.php';


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
			<?php  ?>
			<form method="POST" action="clockin.php">
				<div class="form-group">
					<textarea class="form-control" name="dailyGoal" placeholder="What is your goal today?"></textarea>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-success">Clock In</button>
				</div>
			</form>
			<?php  ?>
			<div class="alert alert-info"><span id="goal">Goal:</span><?php  ?></div>
			<form method="POST" action="clockout.php">
				<div class="form-group">
					<input type="checkbox" name="metGoal">
					I achived my goal today
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-success">Clock Out</button>
				</div>
			</form>
			<?php  ?>
		</div>
	</div>
	<?php require_once '../views/footer.php'; ?>

<script>

// clock
	function startTime() {
	    var today=new Date();
	    var h=today.getHours();
	    var m=today.getMinutes();
	    var s=today.getSeconds();
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