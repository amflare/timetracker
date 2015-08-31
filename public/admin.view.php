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
			FROM students";
$stmt = $dbc->query($select);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

function findStats($id) {

}

var_dump($students);

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
					<th>Progress</th>
				</tr>
			</table>
		</div>
	</div>
	<?php require_once '../views/footer.php'; ?>
</body>
</html>