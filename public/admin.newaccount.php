<?php

$_SESSION["pageRank"] = "admin";

require_once '../bootstrap-admin.php';

//fetch cohorts
$select = "SELECT *
			FROM cohorts";
$stmt = $dbc->query($select);
$cohorts = $stmt->fetchAll(PDO::FETCH_ASSOC);

//fetch admins
$select = "SELECT *
			FROM admins";
$stmt = $dbc->query($select);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = false;

// if form has been submitted
if (Input::has("accountType")) {
	// determin the correct type of account to create
	switch (Input::get("accountType")) {
		case 'student':
			$success = addStudent($dbc);
			break;
		case 'admin':
			$success = addAdmin($dbc);
			break;
		case 'cohort':
			$success = addCohort($dbc);
			break;
	}
}

function addStudent($dbc){
	$username = strtolower(Input::get('firstName')) . "." . strtolower(Input::get('lastName'));
	$standing = "Good";
	$insert = "INSERT INTO students (
					username,
					first_name,
					last_name,
					email,
					standing,
					cohort
					)
				VALUES (
					:username,
					:first_name,
					:last_name,
					:email,
					:standing,
					:cohort
					)";
	$stmt = $dbc->prepare($insert);
	$stmt->bindValue(':username', $username, PDO::PARAM_STR);
	$stmt->bindValue(':first_name', Input::get('firstName'), PDO::PARAM_STR);
	$stmt->bindValue(':last_name', Input::get('lastName'), PDO::PARAM_STR);
	$stmt->bindValue(':email', Input::get('studentEmail'), PDO::PARAM_STR);
	$stmt->bindValue(':standing', $standing, PDO::PARAM_STR);
	$stmt->bindValue(':cohort', Input::get("studentCohort"), PDO::PARAM_STR);

	$stmt->execute();

	$success = "New Student Created!";
	return $success;
}
function addAdmin($dbc){
	$password = password_hash(Input::get('adminPass'), PASSWORD_DEFAULT);
	$insert = "INSERT INTO admins (
					email,
					password
					)
				VALUES (
					:email,
					:password
					)";
	$stmt = $dbc->prepare($insert);
	$stmt->bindValue(':email', Input::get('adminEmail'), PDO::PARAM_STR);
	$stmt->bindValue(':password', $password, PDO::PARAM_STR);

	$stmt->execute();

	$success = "New Admin Created!";
	return $success;
}
function addCohort($dbc){
	$insert = "INSERT INTO cohorts (
					name,
					admin
					)
				VALUES (
					:name,
					:admin
					)";
	$stmt = $dbc->prepare($insert);
	$stmt->bindValue(':name', Input::get('cohortName'), PDO::PARAM_STR);
	$stmt->bindValue(':name', Input::get('cohortMentor'), PDO::PARAM_STR);

	$stmt->execute();

	$success = "New Cohort Created!";
	return $success;
}

?>
<html ng-app="newAccount">
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
	.nav-pills > li.active > a,
	.nav-pills > li.active > a:hover,
	.nav-pills > li.active > a:focus {
		background-color: #80A74C;
	}
	.nav-pills > li > a,
	.nav-pills > li > a:hover {
		background-color: rgba(0, 0, 0, 0);
		color: #fff;
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
	<section ng-controller="TabController as tab">
		<div class="panel panel-primary">
				<div class="panel-heading">
					<ul class="nav nav-pills nav-justified">
						<li ng-class="{ active: tab.isSet(1) }" role="presentation">
							<a href ng-click="tab.setTab(1)">New Student</a>
						</li>
						<li ng-class="{ active: tab.isSet(2) }" role="presentation">
							<a href ng-click="tab.setTab(2)">New Admin</a>
						</li>
						<li ng-class="{ active: tab.isSet(3) }" role="presentation">
							<a href ng-click="tab.setTab(3)">New Cohort</a>
						</li>
					</ul>
				</div>
				<div class="panel-body">
					<!-- STUDENT -->
					<div ng-show="tab.isSet(1)">
						<form method="post" action="#">
							<div class="form-group">
								<input type="text" class="form-control" name="firstName" placeholder="First Name">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" name="lastName" placeholder="Last Name">
							</div>
							<div class="form-group">
								<input type="email" class="form-control" name="studentEmail" placeholder="Email">
							</div>
							<div class="form-group">
								<select class="form-control" name="studentCohort">
									<option>Select Cohort</option>
									<?php foreach ($cohorts as $cohort) : ?>
										<option value="<?= $cohort["id"] ?>"><?= $cohort["name"] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<input type="hidden" name="accountType" value="student">
								<button class="btn btn-success">Create</button>
							</div>
						</form>
					</div>
					<!-- ADMIN -->
					<div class="panel" ng-show="tab.isSet(2)">
						<form method="post" action="#">
							<div class="form-group">
								<input type="email" class="form-control" name="adminEmail" placeholder="Email">
							</div>
							<div class="form-group">
								<input type="password" class="form-control" name="adminPass" id="adminPass" placeholder="Password">
							</div>
							<div class="form-group">
								<input type="password" class="form-control" name="confirmPass" id="confirmPass" placeholder="Confirm Password">
							</div>
							<div class="form-group">
								<input type="hidden" name="accountType" value="admin">
								<button class="btn btn-success createAdmin">Create</button>
							</div>
						</form>
					</div>
					<!-- COHORT -->
					<div class="panel" ng-show="tab.isSet(3)">
						<form method="post" action="#">
							<div class="form-group">
								<input type="text" class="form-control" name="cohortName" placeholder="Cohort Name">
							</div>
							<div class="form-group">
								<select class="form-control" name="cohortMentor">
									<option>Select Coach</option>
									<?php foreach ($admins as $admin) : ?>
										<option value="<?= $admin["id"] ?>"><?= ucfirst(explode('@', $admin["email"])[0]) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<input type="hidden" name="accountType" value="cohort">
								<button class="btn btn-success">Create</button>
							</div>
						</form>
					</div>
				</div>
		</div>
	</section>
	<?php require_once '../views/footer.php'; ?>
<script>
// ---AngularJS---
var app = angular.module('newAccount', []);
app.controller('TabController', function(){
	this.tab = 1;

	this.setTab = function(newValue){
		this.tab = newValue;
	};

	this.isSet = function(tabName){
		return this.tab === tabName;
	};
});

// ---OtherJS---
$(".createAdmin").click(function(evt) {
	var fPass = $("#adminPass");
	var cPass = $("#confirmPass");
	if (fPass.val() != cPass.val()) {
		alertify.error("Passwords do not match");
		evt.preventDefault();
	}
})

<?php if ($success) : ?>
	var message = "<?= $success ?>";
	alertify.set({ delay: 10000 });
	alertify.success(message);
<?php endif; ?>

</script>
</body>
</html>