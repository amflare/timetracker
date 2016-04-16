<?php 

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

// fetch logs
$select = "SELECT *
			FROM cohorts";
$stmt = $dbc->query($select);
$cohorts = $stmt->fetchAll(PDO::FETCH_ASSOC);



function findStudents($cohort, $dbc){
	$select = "SELECT id, username 
				FROM students
				WHERE cohort = $cohort
				AND standing != 'inactive'
				ORDER BY username";
	$stmt = $dbc->query($select);
	$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $students;
}

function findTime($id, $dbc, $monday = null) {
	if (!$monday) {
		//if not historical record
		// find monday
		$dw = date( "w", strtotime(date("Y-m-d")));
		if ($dw == 0) {
			$dw = 8;
		}
		$date = new DateTime(date("Y-m-d"));
		$date->sub(new DateInterval('P' . ($dw - 1) . 'D'));
		$monday = $date->format('Y-m-d');
		//find saturday (not really saturday, but you should not have records past today)
		$saturday = new DateTime(date("Y-m-d"));
		$saturday = $saturday->format('Y-m-d');
	} else {
		//if histroical record
		$date = new DateTime($monday);
		$monday = $date->format('Y-m-d');
		$saturday = new DateTime($monday);
		$saturday->add(new DateInterval('P5D'));
		$saturday = $saturday->format('Y-m-d');
	}

	// fetch logs
	$select = "SELECT length 
				FROM timelogs 
				WHERE student_id = $id 
				AND date_logged >= :monday
				AND date_logged <= :saturday";
	$stmt = $dbc->prepare($select);
	$stmt->bindValue(':monday', $monday, PDO::PARAM_STR);
	$stmt->bindValue(':saturday', $saturday, PDO::PARAM_STR);
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
	<!-- DatePicker -->
	<link href="/css/classic.css" rel="stylesheet">
	<link href="/css/classic.date.css" rel="stylesheet">
	<link href="/css/classic.time.css" rel="stylesheet">
	<!-- Alertify -->
	<link rel="stylesheet" href="/css/alertify.core.css" />
	<link rel="stylesheet" href="/css/alertify.default.css" />

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
   		white-space: nowrap;
	}
	.bar {
		width: 100%; 
	}
	.progress {
		margin-bottom: 0;
	}
	.cohortBtn {
		margin-bottom: 10px;
	}

	</style>
</head>
<body class="container">
	<?php require_once '../views/headeradmin.php'; ?>
	<div class="everything">
		<form class="form-inline" id="formMonday" method="post">
			<div class="form-group">
				<input type="text" name="pickMonday" id="pickMonday" class="form-control"placeholder="Pick a Monday">
			</div>
			<div class="form-group">
				<button class="btn btn-primary btnMonday"><span class="glyphicon glyphicon-search"></span></button>
			</div>
		</form>
		<a href="http://<?= URL ?>/admin/reports">
			<button class="btn btn-success cohortBtn">Students by Name</button>
		</a>		
		<?php foreach ($cohorts as $cohort) : ?>
		<div class="panel panel-primary signin">
			<div class="panel-heading">
				<h3 class="panel-title"><?= $cohort["name"] ?></h3>
			</div>
			<table class="table table-striped table-condensed">
				<?php $students = findStudents($cohort["id"], $dbc) ?>
				<?php foreach ($students as $student) : ?>
				<?php 
				$fullName = parseName($student["username"]);
				if (Input::has("pickMonday")) {
					$time = findTime($student["id"], $dbc, Input::get("pickMonday"));
				} else {
					$time = findTime($student["id"], $dbc);
				}
				$percentage = calculatePercentage($time);
				?>
				<tr>
					<td class="name"><a href="http://<?= URL ?>/admin/student?id=<?= $student["id"] ?>"><?= $fullName ?></a></td>
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
		<?php endforeach; ?>
	</div>
	<?php require_once '../views/footer.php'; ?>
	<script>
		$("#pickMonday").pickadate({
			format: "yyyy/mm/dd",
			firstDay: 1,
		});
		$(".btnMonday").click(function(e){
			e.preventDefault();
			var date = $("#pickMonday").val();
			date = new Date(date);
			date = date.getDay();
			if (date == 1) {
				console.log("test");
				$("#formMonday").submit();
			} else {
				alertify.error('You need to choose a Monday');
			}

		})
	</script>
</body>
</html>