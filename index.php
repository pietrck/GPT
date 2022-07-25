<?php

require_once __DIR__."/App/View/Views.php";
$views = new Views();

echo $views->page("home", [
	"active" => "document.getElementById('home').classList.add('active')",
]);