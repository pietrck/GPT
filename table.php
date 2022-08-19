<?php

require_once __DIR__."/App/Model/DB.php";
require_once __DIR__."/App/View/Views.php";
$views = new Views();

$db = new DB();
$conn = $db->open();

$table = $options = $options_clientes = "";
$agentes = [];

if (isset($_POST['date'])) {
	$daterange = explode(" - ", $_POST['date']);
	$dateStart = explode("/", $daterange[0]);
	$start = $dateStart[2].$dateStart[1].$dateStart[0];
	$dateEnd = explode("/", $daterange[1]);
	$end = $dateEnd[2].$dateEnd[1].$dateEnd[0];
	$result = $db->select_table(start: $start, end: $end, agente: $_POST['agente'], cliente: $_POST['cliente']);

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
			$table .= "<td title='".$row['cliente']."' style='max-width:50px; overflow: hidden;'><a data-toggle='modal' data-target='#modal-cliente' style='cursor: pointer;' onclick='modal(".$row['id'].",".'"'.$row['cliente'].'"'.")'>".$row['cliente']."</a></td>";
			$table .= "<td style='max-width:50px; overflow: hidden;'><a href='https://totvscuritiba.movidesk.com/Ticket/Edit/".$row['ticket']."' target='_blank'>".$row['ticket']."</a></td>";
			$table .= "<td title='".$row['assunto']."' style='max-width:50px; overflow: hidden;'>".$row['assunto']."</td>";
			$table .= "<td style='max-width:30px; overflow: hidden;'>".$row['n_acao']."</td>";
			$table .= "<td title='".$row['acao']."' style='max-width: 200px; overflow: hidden;'>".nl2br($row['acao'])."</td>";
			$table .= "<td style='display: none'>".$row['categoria']."</td>";
			$table .= "<td style='max-width:200px; overflow: hidden;'>".$row['agente']."</td>";
			$table .= "<td style='display: none'>".$row['equipe']."</td>";
			$table .= "<td style='max-width:100px; overflow: hidden;'>".$date."</td>";
			$table .= "<td style='max-width:70px; overflow: hidden;'>".$row['horas']."</td>";
			if ($row['labels'] == "#A865C9") {
				$apontado = "Não";
				$button = '<button class="btn-sm btn btn-success" onclick="'."apontar('".$row['id']."')".'" disabled="">Apontar</button>';
			}elseif($row['apontado']){
				$apontado = "Apontado";
				$button = '<button class="btn-sm btn btn-warning" onclick="'."limpar('".$row['id']."', 'apontado')".'">Limpar</button>';
			}else{
				$apontado = "Não";
				$button = '<button class="btn-sm btn btn-success" onclick="'."apontar('".$row['id']."')".'">Apontar</button>';
			}
			$table .= "<td style='display: none'>".$apontado."</td>";
			$table .= '<td>';
			$table .= '<button class="btn-sm btn btn-info" onclick="'."copytoclipboard('".$row['id']."')".'">Copiar</button>';
			$table .= $button;
			if ($row['labels'] == "#A865C9") {
				$table .= '<button class="btn-sm btn btn-warning" onclick="'."limpar('".$row['id']."')".'">Limpar</button>';
			}elseif($row['apontado']){
				$table .= '<button class="btn-sm btn btn-danger" onclick="'."ignorar('".$row['id']."')".'" disabled="">Ignorar</button>';
			}else{
				$table .= '<button class="btn-sm btn btn-danger" onclick="'."ignorar('".$row['id']."')".'">Ignorar</button>';
			}
			$table .= '</td>';
			$table .= "<td style='display: none'>".$row['classificacao']."</td>";
			$table .= "<td style='display: none'>".$row['nome']."</td>";
			$table .= "</tr>";

			if (!array_search($row['agente'], $agentes)) {
				$agentes[] = $row['agente'];
			}
		}
	}

	$active = "document.getElementById('apontamentos').classList.add('active');document.getElementById('bpost').remove();";
}else{
	$result = $db->select(columns: "agente", order: " group by agente;");

	if (count($result) > 0) {
		foreach ($result as $row) {
			$options .= '<option value="'.$row['agente'].'">'.$row['agente'].'</option>';
		}
	}

	$active = "document.getElementById('apontamentos').classList.add('active');document.getElementById('apost').remove();";
}

$options_clientes = $db->select_customer();

echo $views->page("table", [
	"table" => $table,
	"datepicker" => $views->render("datepicker"),
	"active" => $active,
	"options" => $options,
	"options_clientes" => $options_clientes,
	"startdate" => '"01/'.date("m").'/'.date("Y").'"',
	"enddate" => '"'.date("t").'/'.date("m").'/'.date("Y").'"',
],
[
	"toastr",
	"bscustomfile",
	"datatable",
	"cookie"
]
);
