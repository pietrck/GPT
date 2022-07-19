<?php

require_once __DIR__."/Class/Views.php";
$views = new Views();

echo $views->page("home", [
	"active" => "document.getElementById('home').classList.add('active')",
]);