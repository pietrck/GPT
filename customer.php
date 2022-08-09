<?php

require_once __DIR__."/App/Model/DB.php";
require_once __DIR__."/App/View/Views.php";
$views = new Views();

$db = new DB();

$table = "";
$toastr = "";
$clientes = [];

if (isset($_POST['name'])) {
	if($db->insert_customer($_POST)){
		$toastr = '<script>toastr.success("Cliente '.$_POST['name'].' cadastrado");</script>';
	}
}

$result = $db->select(from: "clientes", order: "order by nome asc;");

if (count($result) > 0) {
	foreach ($result as $row) {
		$table .= "<tr>";
		$table .= "<td>".$row['nome']."</td>";
		$table .= "<td>".$row['classificacao']."</td>";
		$table .= "</tr>";
		$clientes[trim($row['nome'])]= "";
	}
}

$options = $db->select_customer(array: $clientes);

echo $views->page("customer", [
	"active" => "document.getElementById('customer').classList.add('active')",
	"table" => $table,
	"options" => $options,
	"toastr" => $toastr,
],[
	'toastr',
]);
