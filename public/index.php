<html>
<head>
	<title>Time Tracker</title>
	<?php require_once '../bootstrap.php'; ?>
</head>
<body class="container">
	<?php require_once '../views/header.php'; ?>
	<div class="panel panel-default">
		<div class="panel-body">
		<section ng-controller="TabController as tab">
			<ul class="nav nav-tabs nav-justified">
				<li ng-class="{ active: tab.isSet(1) }" role="presentation">
					<a href ng-click="tab.setTab(1)">Overview</a>
				</li>
				<li ng-class="{ active: tab.isSet(2) }" role="presentation">
					<a href ng-click="tab.setTab(2)">Milestones</a>
				</li>
			</ul>
			<!-- STUDENT -->
			<div class="panel" ng-show="tab.isSet(1)">
				<form>
					<div class="form-group">
						<label for="studentLogin">Username</label>
						<input type="text" class"form-control" id="studentLogin" name="studentLogin">
					</div>
				</form>
			</div>
			<!-- ADMIN -->
			<div class="panel" ng-show="tab.isSet(2)">
			</div>
		</section>
		</div>
	</div>
	<script>
	// ---AngularJS---
	var app = angular.module('budget', []);
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