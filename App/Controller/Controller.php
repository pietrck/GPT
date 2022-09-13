<?php 

require_once __DIR__."/../View/Views.php";
require_once __DIR__."/../Model/DB.php";

class Controller
{
	public $db;
	public $views;

	public function __construct()
	{
		$this->views = new Views();
		$this->db = new DB();
	}

	public function home()
	{
		$this->views = new Views();
		$this->db = new DB();

		$ignorados = $apontados = $scc = $falta_apontar = 0;
		$data = $agente = $cliente = [];

		setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo');

		$start = date("Ym").'01';
		$end = date("Ymt");

		$result = $this->db->select(where: "where data between '".$start."' and '".$end."'");

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
						$falta_apontar++;
						break;
				}
			}
		}

		$total = count($result);

		$falta_apontar_percent = ($falta_apontar == 0)? "#":number_format(($falta_apontar/$total)*100,2)."%";
		$apontados_percent = ($apontados == 0)? "#":number_format(($apontados/$total)*100,2)."%";
		$ignorados_percent = ($ignorados == 0)? "#":number_format(($ignorados/$total)*100,2)."%";
		$scc_percent = ($scc == 0)? "#":number_format(($scc/$total)*100,2)."%";

		$table = "<tr><td>1</td><td>Apontados</td><td>".$apontados."</td><td>".$apontados_percent."</td></tr>";
		$table .= "<tr><td>2</td><td>Ignorados</td><td>".$ignorados."</td><td>".$ignorados_percent."</td></tr>";
		$table .= "<tr><td>3</td><td>SCC</td><td>".$scc."</td><td>".$scc_percent."</td></tr>";
		$table .= "<tr><td>4</td><td>Falta apontar</td><td>".$falta_apontar."</td><td>".$falta_apontar_percent."</td></tr>";
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

		return $this->views->page("home", [
			"active" => "document.getElementById('home').classList.add('active');",
			"mes" => ucfirst(strftime("%B")),
			"table" => $table,
			"table2" => $table2,
			"table3" => $table3,
			"table4" => $table4,
		]);
	}

	public function apontamentos($post = [])
	{
		$table = $options = $options_clientes = "";
		$agentes = [];

		if (isset($post['date'])) {
			$daterange = explode(" - ", $post['date']);
			$dateStart = explode("/", $daterange[0]);
			$start = $dateStart[2].$dateStart[1].$dateStart[0];
			$dateEnd = explode("/", $daterange[1]);
			$end = $dateEnd[2].$dateEnd[1].$dateEnd[0];
			$result = $this->db->select_table(start: $start, end: $end, agente: $post['agente'], cliente: $post['cliente']);

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
					$table .= "<td title='".str_replace(["'",'"'], "", $row['acao'])."' style='max-width: 200px; overflow: hidden;'>".nl2br($row['acao'])."</td>";
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
			$result = $this->db->select(columns: "agente", order: " group by agente;");

			if (count($result) > 0) {
				foreach ($result as $row) {
					$options .= '<option value="'.$row['agente'].'">'.$row['agente'].'</option>';
				}
			}

			$active = "document.getElementById('apontamentos').classList.add('active');document.getElementById('apost').remove();";
		}

		$options_clientes = $this->db->select_customer();

		return $this->views->page("table", [
			"table" => $table,
			"datepicker" => $this->views->render("datepicker"),
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
	}

	public function clientes($post = [])
	{
		$table = "";
		$toastr = "";
		$clientes = [];

		if (isset($post['name']) || isset($post['name_writing'])) {
			if($this->db->insert_customer($post)){
				$toastr = '<script>toastr.success("Cliente '.$post['name'].' cadastrado");</script>';
			}
		}

		$result = $this->db->select(from: "clientes", order: "order by nome asc;");

		if (count($result) > 0) {
			foreach ($result as $row) {
				$table .= "<tr>";
				$table .= "<td>".$row['nome']."</td>";
				$table .= "<td>".$row['classificacao']."</td>";
				$table .= "</tr>";
				$clientes[trim($row['nome'])]= "";
			}
		}

		$options = $this->db->select_customer(array: $clientes);

		echo $this->views->page("customer", [
			"active" => "document.getElementById('customer').classList.add('active')",
			"table" => $table,
			"options" => $options,
			"toastr" => $toastr,
		],[
			'toastr',
		]);
	}

	public function rest($post = [], $files = [])
	{
		if (isset($post['apontar'])) {
			$this->db->apontar($post['id']);
		}elseif (isset($post['ignorar'])) {
			$this->db->ignorar($post['id']);
		}elseif (isset($post['limpar'])) {
			$this->db->limpar($post['id']);
		}elseif (isset($post['alter'])) {
			$this->db->alter($post['alter'], $post['costumer']);
		}elseif (isset($files['file'])) {
			if (isset($files['file'])) {
				require_once __DIR__."/../Model/Upload.php";
				$upload = new Upload();
				echo $upload->upload_table($files['file']);
			}
		}
	}

	private function convert_percent($value='')
	{
		// code...
	}

}
