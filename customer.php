<?php

require_once __DIR__."/App/Model/DB.php";
require_once __DIR__."/App/View/Views.php";
$views = new Views();

$db = new DB();
$conn = $db->open();

$table = "";
$clientes = [];

if (isset($_POST['name'])) {
	$query = $conn->prepare("insert into clientes(nome,classificacao) values (:nome,:classificacao)");
	$query->bindParam("nome", $_POST['name']);
	$query->bindParam("classificacao", $_POST['classificacao']);
	$query->execute();
}

$result = $db->select(from: "clientes", order: " order by nome asc;");

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
]);
