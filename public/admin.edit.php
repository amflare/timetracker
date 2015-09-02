<?php 

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

$type = "first";
if (Input::has("accountType")) {
	$type = Input::get("accountType");
}

//fetch students
$select = "SELECT id, first_name, last_name 
			FROM students
			ORDER BY first_name";
$stmt = $dbc->query($select);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

//fetch admins
$select = "SELECT id, email 
			FROM admins
			ORDER BY email";
$stmt = $dbc->query($select);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

function parseEmail($email){
	$name = explode("@", $email);
	$handle = ucfirst($name[0]);
	return $handle;
}


?>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Timothy Birrell">

	
	<title>New Account</title>

	<!-- Bootstrap core CSS -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<!-- DatePicker -->
	<link href="/css/classic.css" rel="stylesheet">
	<link href="/css/classic.date.css" rel="stylesheet">
	<link href="/css/classic.time.css" rel="stylesheet">
	<!-- Alertify -->
	<link rel="stylesheet" href="/css/alertify.core.css">
	<link rel="stylesheet" href="/css/alertify.default.css">
	
	<link href="/css/custom.css" rel="stylesheet">

	<style>
	.hidden {
		display: none;
	}
	.panel {
		margin: 20px auto;
		max-width: 100%;
		width: 500px;
	}
	</style>
</head>
<body class="container">
	<?php require_once '../views/headeradmin.php'; ?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Choose Account</h3>
		</div>
		<div class="panel-body">
			<!-- PICK TYPE -->
			<form class="typePick form-inline hidden" method="post">
				<div class="form-group">
					<select name="accountType" id="accountType" class="form-control">
						<option>Select Account Type</option>
						<option value="student">Student</option>
						<option value="admin">Admin</option>
					</select>
				</div>
				<div class="form-group">
					<button class="btn btn-success typePickBtn"><span class="glyphicon glyphicon-search"></span></button>
				</div>
			</form>

			<!-- PICK STUDENT -->
			<form class="studentPick hidden form-inline" action="edit/account">
				<div class="form-group">
					<select name="id" class="form-control">
						<option>Pick Student</option>
						<?php foreach ($students as $student) : ?>
							<option value="<?= $student["id"] ?>"><?= $student["first_name"] ?> <?= $student["last_name"] ?></option>
						<?php endforeach; ?>
					</select>
					<input type="hidden" name="type" value="student">
				</div>
				<div class="form-group">
					<button class="btn btn-success"><span class="glyphicon glyphicon-search"></span></button>
				</div>
			</form>

			<!-- PICK ADMIN -->
			<form class="adminPick hidden form-inline" action="edit/account">
				<div class="form-group">
					<select name="id" class="form-control">
						<option>Pick Admin</option>
						<?php foreach ($admins as $admin) : ?>
							<option value="<?= $admin["id"] ?>"><?= parseEmail($admin["email"]) ?></option>
						<?php endforeach; ?>
					</select>
					<input type="hidden" name="type" value="admin">
				</div>
				<div class="form-group">
					<button class="btn btn-success"><span class="glyphicon glyphicon-search"></span></button>
				</div>
			</form>
		</div>
	</div>

	<?php require_once '../views/footer.php'; ?>
<script>
var type = "<?= $type ?>";


if (type == "student") {
	$(".studentPick").toggleClass("hidden");
} else if (type == "admin") {
	$(".adminPick").toggleClass("hidden");
} else if (type == "first") {
	$(".typePick").toggleClass("hidden");
}

</script>
</body>
</html>