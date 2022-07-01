<?php

require_once __DIR__."/Class/DB.php";

$db = new DB();

if (isset($_POST['apontar'])) {
	$db->apontar($_POST['id']);
}elseif (isset($_POST['ignorar'])) {
	$db->ignorar($_POST['id']);
}