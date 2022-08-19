<?php

require_once __DIR__."/App/View/Views.php";
require_once __DIR__."/App/Model/DB.php";

$views = new Views();
$db = new DB();

$ignorados = $apontados = $scc = 0;
$data = $agente = $cliente = [];

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$start = date("Ym").'01';
$end = date("Ymt");

$result = $db->select(where: "where data between '".$start."' and '".$end."'");

if (count($result) > 0) {
	foreach ($result as $row) {
		switch ($row['labels']) {
			case 'lightblue':
				$scc++;
				break;

			case 'lightgreen':
				$apontados++;
				break;

			case '#A865C9':
				$ignorados++;
				break;

			default:
				if (array_key_exists($row['data'], $data)) {
					$data[$row['data']]++;
				}else{
					$data[$row['data']] = 1;
				}

				if (array_key_exists($row['agente'], $agente)) {
					$agente[$row['agente']]++;
				}else{
					$agente[$row['agente']] = 1;
				}

				if (array_key_exists($row['cliente'], $cliente)) {
					$cliente[$row['cliente']]++;
				}else{
					$cliente[$row['cliente']] = 1;
				}
				break;
		}
	}
}

$total = count($result);
$falta_apontar =  $total - $scc - $apontados - $ignorados;

$table = "<tr><td>1</td><td>Apontados</td><td>".$apontados."</td><td>".number_format(($apontados/$total)*100,2)."%</td></tr>";
$table .= "<tr><td>2</td><td>Ignorados</td><td>".$ignorados."</td><td>".number_format(($ignorados/$total)*100,2)."%</td></tr>";
$table .= "<tr><td>3</td><td>SCC</td><td>".$scc."</td><td>".number_format(($scc/$total)*100,2)."%</td></tr>";
$table .= "<tr><td>4</td><td>Falta apontar</td><td>".$falta_apontar."</td><td>".number_format(($falta_apontar/$total)*100,2)."%</td></tr>";
$table .= "<tr><td>5</td><td>Total</td><td>".count($result)."</td><td>100%</td></tr>";

$table2 = $table3 = $table4 = "";
$x = $z = $y = 1;

foreach ($data as $key => $value) {
	$table2 .= "<tr><td>".$x++."</td><td>".date("d/m/y",strtotime($key))."</td><td>".$value."</td><td>".number_format(($value/$falta_apontar)*100,2)."%</td></tr>";
}

foreach ($cliente as $key => $value) {
	$table3 .= "<tr><td>".$y++."</td><td style='max-width: 200px; overflow: hidden'>".$key."</td><td>".$value."</td><td>".number_format(($value/$falta_apontar)*100,2)."%</td></tr>";
}

$table3 .= "<tr><td>".$y++."</td><td>Total</td><td>".array_sum($agente)."</td><td>100%</td></tr>";

ksort($agente);

foreach ($agente as $key => $value) {
	$table4 .= "<tr><td>".$z++."</td><td>".$key."</td><td>".$value."</td><td>".number_format(($value/$falta_apontar)*100,2)."%</td></tr>";
}

$table4 .= "<tr><td>".$z++."</td><td>Total</td><td>".array_sum($agente)."</td><td>100%</td></tr>";

echo $views->page("home", [
	"active" => "document.getElementById('home').classList.add('active');",
	"mes" => ucfirst(strftime("%B")),
	"table" => $table,
	"table2" => $table2,
	"table3" => $table3,
	"table4" => $table4,
]);
