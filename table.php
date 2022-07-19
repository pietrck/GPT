<?php

require_once __DIR__."/Class/DB.php";
require_once __DIR__."/Class/Views.php";
$views = new Views();

$db = new DB();

$table = "";

if (isset($_POST['date'])) {
	$daterange = explode(" - ", $_POST['date']);
	$dateStart = explode("/", $daterange[0]);
	$start = $dateStart[2].$dateStart[1].$dateStart[0];
	$dateEnd = explode("/", $daterange[1]);
	$end = $dateEnd[2].$dateEnd[1].$dateEnd[0];
	$result = $db->select_table(start: $start, end: $end);

	if (count($result) > 0) {
		foreach ($result as $row) {
			$date = explode("-", $row['data']);
			$date = $date[2]."/".$date[1]."/".$date[0];

			if ($row['labels'] !== null) {
				$color = $row['labels'];
			}else{
				$color = "";
			}		

			$table .= "<tr id = ".$row['id']." style='background-color: ".$color."'>";
			$table .= "<td title='".$row['cliente']."' style='max-width:50px; overflow: hidden;'>".$row['cliente']."</td>";
			$table .= "<td style='max-width:50px; overflow: hidden;'>".$row['ticket']."</td>";
			$table .= "<td title='".$row['assunto']."' style='max-width:50px; overflow: hidden;'>".$row['assunto']."</td>";
			$table .= "<td style='max-width:30px; overflow: hidden;'>".$row['n_acao']."</td>";
			$table .= "<td style='max-width: 200px; overflow: hidden;'>".nl2br($row['acao'])."</td>";
			$table .= "<td style='display: none'>".$row['categoria']."</td>";
			$table .= "<td style='max-width:200px; overflow: hidden;'>".$row['agente']."</td>";
			$table .= "<td style='max-width:100px; overflow: hidden;'>".$row['equipe']."</td>";
			$table .= "<td style='max-width:100px; overflow: hidden;'>".$date."</td>";
			$table .= "<td style='max-width:70px; overflow: hidden;'>".$row['horas']."</td>";
			if ($row['apontado']) {
				$apontado = "Apontado";
				$button = '<button onclick="'."apontar('".$row['id']."')".'" disabled="">Apontar</button>';
			}else{
				$apontado = "Não";
				$button = '<button onclick="'."apontar('".$row['id']."')".'">Apontar</button>';
			}
			$table .= "<td style='display: none'>".$apontado."</td>";
			$table .= '<td>';
			$table .= '<button onclick="'."copytoclipboard('".$row['id']."')".'">Copiar</button>';
			$table .= $button;
			$table .= '<button onclick="'."ignorar('".$row['id']."')".'">Ignorar</button>';
			$table .= '</td>';
			$table .= "<td style='display: none'>".$row['classificacao']."</td>";
			$table .= "<td style='display: none'>".$row['nome']."</td>";
			$table .= "</tr>";
		}
	}

	$active = "document.getElementById('apontamentos').classList.add('active');document.getElementById('bpost').remove();";
}else{
	$active = "document.getElementById('apontamentos').classList.add('active');document.getElementById('apost').remove();";
}

echo $views->page("table", [
	"table" => $table,
	"datepicker" => $views->render("datepicker"),
	"active" => $active,
]);