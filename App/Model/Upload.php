<?php

require_once __DIR__."/DB.php";

class Upload extends DB
{
	public $conn;
	
	public function __construct()
	{
		$this->conn = $this->open();
	}

	public function upload_table($file)
	{
		$dados = $apontamentos = $users = $clientes = [];

		$result = $this->select("ticket, n_acao");

		if (count($result) > 0) {
			foreach ($result as $row) {
				array_push($apontamentos, $row['ticket'].$row['n_acao']);
			}
		}

		$result_users = $this->select(from: "users", columns: "nome");

		if (count($result_users) > 0) {
			foreach ($result_users as $row) {
				array_push($users, $row['nome']);
			}
		}

		$result_clientes = $this->select(from: "clientes", where: "where classificacao = 'SCC'");

		if (count($result_clientes) > 0) {
			foreach ($result_clientes as $row) {
				array_push($clientes, $row['nome']);
			}
		}

		$content = explode("\n", file_get_contents($file['tmp_name']));

		unset($content[0]);

		foreach ($content as $key => $value) {
			$content[$key] = explode(";", $value);
			if (array_search($content[$key][4].$content[$key][7], $apontamentos) === false) {
				$temp = [
					trim($content[$key][0]),
					$content[$key][4],
					$content[$key][6],
					$content[$key][7],
					$content[$key][8],
					$content[$key][9],
					$content[$key][10],
					$content[$key][11],
					$content[$key][12],
					$content[$key][15],
					$content[$key][17],
					$content[$key][18],
				];

				array_push($dados, $temp);
			}
		}

		foreach ($dados as $dado) {
			$date = explode("/", $dado[8]);
			$date = $date[2].$date[1].$date[0];
			$query = $this->conn->prepare("insert into `tickets` (`cliente`, `ticket`, `assunto`, `n_acao`, `acao`, `categoria`, `agente`, `equipe`, `data`, `horas`, `horas_contabilizadas`, `tipo_hora`, `labels`) values (:cliente, :ticket, :assunto, :n_acao, :acao, :categoria, :agente, :equipe, :data, :horas, :horas_contabilizadas, :tipo_hora, :labels);");
			$query->bindParam(":cliente", $dado[0]);
			$query->bindParam(":ticket", $dado[1]);
			$query->bindParam(":assunto", $dado[2]);
			$query->bindParam(":n_acao", $dado[3]);
			$query->bindParam(":acao", $dado[4]);
			$query->bindParam(":categoria", $dado[5]);
			$query->bindParam(":agente", $dado[6]);
			$query->bindParam(":equipe", $dado[7]);
			$query->bindParam(":data", $date);
			$query->bindParam(":horas", $dado[9]);
			$query->bindParam(":horas_contabilizadas", $dado[10]);
			$query->bindParam(":tipo_hora", $dado[11]);

			$labels = "";

			if ($dado[9] == "00:01") {
				$labels = "#A865C9";
			}elseif ($dado[6] == "FSW Totvs Curitiba") {
				$labels = "#A865C9";
			}elseif(array_search($dado[0], $clientes) !== false && array_search($dado[6], $users) !== false ){
				$labels = "lightblue";
			}

			$query->bindParam(":labels", $labels);

			if ($query->execute()) {
			}
		}

		return count($dados);
	}
}
