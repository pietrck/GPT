<?php

require_once __DIR__."/App/Controller/Controller.php";

$controller = new Controller();

$uri = isset($_GET['uri'])?$_GET['uri']:"";

switch ($uri) {
	case 'customer.php':
		echo $controller->clientes($_POST);
		break;

	case 'table.php':
		echo $controller->apontamentos($_POST);
		break;

	case 'rest.php':
		echo $controller->rest($_POST, $_FILES);
		break;

	case 'index.php':
		echo $controller->home();
		break;

	case 'users.php':
		echo $controller->users($_POST);
		break;
	
	default:
		echo $controller->home();
		break;
}
