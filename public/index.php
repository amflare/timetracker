<?php 

$frontController = explode("?", $_SERVER['REQUEST_URI']);

switch ($frontController[0]) {
	case '/admin':
		include 'admin.index.php';
		break;
	case '/admin/student':
		include 'admin.student.php';
		break;
	case '/admin/reports':
		include 'admin.view.php';
		break;
	case '/admin/reports/cohort':
		include 'admin.viewcohorts.php';
		break;
	case '/admin/edit':
		include 'admin.edit.php';
		break;
	case '/admin/edit/account':
		include 'admin.editaccount.php';
		break;
	case '/admin/edit/log':
		include 'admin.editlog.php';
		break;
	case '/admin/new':
		include 'admin.newaccount.php';
		break;
	case '/logout':
		include 'logout.php';
		break;
	case '/timeclock':
		include 'student.index.php';
		break;
	case '/student/logs':
		include 'student.logs.php';
		break;
	case '/forgotpass':
		include 'forgot_pass.php';
		break;
	default:
		include 'home.php';
		break;
}
