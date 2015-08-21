<?php 

$exception = true;

require_once '../bootstrap.php';


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

		.signin {
			width: 500px;
			max-width: 100%;
			margin: 25px auto;
		}
		form {
			margin-top: 20px;
		}

	</style>
</head>
<body class="container">
	<?php require_once '../views/header.php'; ?>
	<div class="panel panel-default signin">
		<div class="panel-body">
			<section ng-controller="TabController as tab">
				<ul class="nav nav-pills">
					<li ng-class="{ active: tab.isSet(1) }" role="presentation">
						<a href ng-click="tab.setTab(1)">Student</a>
					</li>
					<li ng-class="{ active: tab.isSet(2) }" role="presentation">
						<a href ng-click="tab.setTab(2)">Admin</a>
					</li>
				</ul>
				<!-- STUDENT -->
				<div ng-show="tab.isSet(1)">
					<form method="POST" action="student_login.php">
						<?php if (isset($_SESSION["failedLoginStudent"])) : ?>
							<h6 style="color: #F00; ">Login Failed</h6>
						<?php endif; ?>
						<div class="form-group">
							<input type="text" class="form-control" name="studentUser" placeholder="Username">
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-success">Login</button>
						</div>
					</form>
				</div>
				<!-- ADMIN -->
				<div class="panel" ng-show="tab.isSet(2)">
					<form method="POST" action="../admin_login.php">
						<?php if (isset($_SESSION["failedLoginAdmin"])) : ?>
							<h6 style="color: #F00; ">Login Failed</h6>
						<?php endif; ?>
						<div class="form-group">
							<input type="text" class="form-control" name="adminUser" placeholder="Username">
						</div>
						<div class="form-group">
							<input type="password" class="form-control" name="adminPass" placeholder="Password">
						</div>
						<div class="form-group">
							<a href="#" class="forgotPass">Forgot Password</a>
						</div>
						<div class="form-group">
							<button class="btn btn-success">Login</button>
						</div>
					</form>
				</div>
			</section>
		</div>
	</div>
	<?php require_once '../views/footer.php'; ?>
	<script>
	// ---AngularJS---
	var app = angular.module('login', []);
	app.controller('TabController', function(){
		this.tab = 1;

		this.setTab = function(newValue){
			this.tab = newValue;
		};

		this.isSet = function(tabName){
			return this.tab === tabName;
		};
	});
	</script>
</body>
</html>