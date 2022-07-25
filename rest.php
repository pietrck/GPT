<?php

require_once __DIR__."/App/Model/DB.php";

$db = new DB();

if (isset($_POST['apontar'])) {
	$db->apontar($_POST['id']);
}elseif (isset($_POST['ignorar'])) {
	$db->ignorar($_POST['id']);
}if (isset($_POST['limpar'])) {
	$db->limpar($_POST['id']);
}