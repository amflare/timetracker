<?php

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

$type = Input::get("type");
$id = Input::get("id");
$error = false;
$success = false;

//fetch cohorts
$select = "SELECT *
			FROM cohorts";
$stmt = $dbc->query($select);
$cohorts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if editing
if (Input::has("add")) {
	//editing student
	if ($type == "student") {
		$student = addStudent($id, $dbc);
		$success = "Student Edited";

	// editing admin
	} else if ($type == "admin") {
		$admin = addAdmin($id, $dbc);

		//did it throw an error?
		if (is_string($admin) && $admin == "error") {
			$error = "Old Password not correct. Please try again".
			$admin = "";
			$admin = fetchAdmin($id, $dbc);
		} else {
			$success = "Admin Edited";
		}
	}
} else {
	if ($type == "student") {
		$student = fetchStudent($id, $dbc);
	} else if ($type == "admin") {
		$admin = fetchAdmin($id, $dbc);
	}
}

function fetchStudent($id, $dbc) {
	$select = "SELECT *
				FROM students
				WHERE id = $id";
	$stmt = $dbc->query($select);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchAdmin($id, $dbc) {
	$select = "SELECT *
				FROM admins
				WHERE id = $id";
	$stmt = $dbc->query($select);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addStudent($id, $dbc) {
	$update = "UPDATE students
 				SET username = :username,
 				first_name = :first_name,
 				last_name = :last_name,
 				email = :email,
 				cohort = :cohort
 				WHERE id = $id";
	$stmt = $dbc->prepare($update);
	$stmt->bindValue(':username', Input::get('username'), PDO::PARAM_STR);
	$stmt->bindValue(':first_name', Input::get('firstName'), PDO::PARAM_STR);
	$stmt->bindValue(':last_name', Input::get('lastName'), PDO::PARAM_STR);
	$stmt->bindValue(':email', Input::get('email'), PDO::PARAM_STR);
	$stmt->bindValue(':cohort', Input::get('cohort'), PDO::PARAM_STR);
	$stmt->execute();

	// re populate form
	return fetchStudent($id, $dbc);
}

function addAdmin($id, $dbc) {
	// if wants to change admin pass
	if (Input::has("willChangePass")) {
		//find old pass
		$select = "SELECT password
					FROM admins
					WHERE id = $id";
		$stmt = $dbc->query($select);
		$pass = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// check to see if old password matches
		if (password_verify(Input::get("oldPass"), $pass[0]["password"])) {
			$update = "UPDATE admins
						SET email = :email,
						password = :password
						WHERE id = $id";
			$stmt = $dbc->prepare($update);
			$stmt->bindValue(':email', Input::get('email'), PDO::PARAM_STR);
			$stmt->bindValue(':password', password_hash(Input::get('newPass'), PASSWORD_DEFAULT), PDO::PARAM_STR);
			$stmt->execute();
		} else {
		// if not
			return "error";
		}
	//if changing other info
	} else {
		$update = "UPDATE admins
					SET email = :email, calendly = :calendly
					WHERE id = $id";
		$stmt = $dbc->prepare($update);
		$stmt->bindValue(':email', Input::get('email'), PDO::PARAM_STR);
		$stmt->bindValue(':calendly', Input::get('calendly'), PDO::PARAM_STR);
		$stmt->execute();
	}


	return fetchAdmin($id, $dbc);
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
			<h3 class="panel-title">Edit <?= ucfirst($type)  ?></h3>
		</div>
		<div class="panel-body">
			<?php if ($type == "student") : ?>
				<!-- EDIT STUDENT -->
				<div class="alert alert-info" role="alert">
					Note: Username will not automatically change to match name changes. It must be done manually.
				</div>
				<form class="studentForm" method="post">
					<div class="form-group">
						<label for="username">Username</label>
						<input type="text" class="form-control" name="username" id="username" value="<?= $student[0]["username"] ?>">
					</div>
					<div class="form-group">
						<label for="firstName">First Name</label>
						<input type="text" class="form-control" name="firstName" id="firstName" value="<?= $student[0]["first_name"] ?>">
					</div>
					<div class="form-group">
						<label for="lastName">Last Name</label>
						<input type="text" class="form-control" name="lastName" id="lastName" value="<?= $student[0]["last_name"] ?>">
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" class="form-control" name="email" id="email" value="<?= $student[0]["email"] ?>">
					</div>
					<div class="form-group">
						<label for="cohort">Cohort</label>
						<select class="form-control" id="cohort" name="cohort">
							<?php foreach ($cohorts as $cohort) : ?>
								<option value="<?= $cohort["id"] ?>"><?= $cohort["name"] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<input type="hidden" name="add" value="true">
						<button class="btn btn-success studentSave">Save</button>
					</div>
				</form>
			<?php endif; ?>

			<?php if ($type == "admin") : ?>
				<!-- EDIT ADMIN -->
				<form class="adminForm" method="post">
					<div class="form-group">
						<input type="email" class="form-control" name="email" value="<?= $admin[0]["email"] ?>" placeholder="Email">
					</div>
					<div class="form-group">
						<input type="text" class="form-control" name="calendly" value="<?= $admin[0]["calendly"] ?>" placeholder="Calendly Link">
					</div>
					<div class="form-group">
						I am changing the password
						<input type="checkbox" name="willChangePass">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="oldPass" placeholder="Old Password">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="newPass" id="newPass" placeholder="New Password">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="confirmPass" id="confirmPass" placeholder="Confirm Password">
					</div>
					<div class="form-group">
						<input type="hidden" name="add" value="true">
						<button class="btn btn-success adminSave">Save</button>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>

	<?php require_once '../views/footer.php'; ?>
<script>

// pick correct cohort for student
<?php if ($type == "student") : ?>
	$("#cohort").val(<?= $student[0]["cohort"] ?>)
<?php endif; ?>

// status alerts
<?php if ($error) : ?>
	var message = "<?= $error ?>";
	alertify.set({ delay: 10000 });
	alertify.error(message);
<?php endif; ?>

<?php if ($success) : ?>
	var message = "<?= $success ?>";
	alertify.set({ delay: 10000 });
	alertify.success(message);
<?php endif; ?>

// verify new passwords match
$(".adminSave").click(function(evt) {
	var fPass = $("#newPass").val();
	var cPass = $("#confirmPass").val();
	console.log(fPass);
	console.log(cPass);
	if (fPass != cPass) {
		alertify.error("Passwords do not match");
		evt.preventDefault();
	}
})

</script>
</body>
</html>