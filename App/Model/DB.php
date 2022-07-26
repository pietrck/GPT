<?php

/**
 * 
 */
class DB
{
	public $conn;

	public function __construct()
	{
		$this->conn = $this->open();
	}
	
	public function open()
	{
		$conn = new PDO("mysql:dbname=gpt;host=localhost;","root","");
		return $conn;
	}

	public function select($columns = "*", $where = "", $from = "tickets", $order = "", $params = []):array
	{

		$query = $this->conn->prepare("select {$columns} from {$from} {$where} {$order};");
		foreach ($params as $key => $value) {
			$query->bindParam($key, $value);
		}
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function select_table($start, $end, $agente = "", $cliente = "")
	{
		$query_str = "SELECT tickets.*, clientes.classificacao, users.nome FROM `tickets` left join users on users.nome = tickets.agente left join clientes on clientes.nome = tickets.cliente where tickets.data between $start and $end";

		if ($agente !== "") {
			$query_str .= " and agente = '{$agente}'";
		}

		if ($cliente !== "") {
			$query_str .= " and cliente = '{$cliente}'";
		}

		$query_str .= " group by id order by tickets.id asc";

		$query = $this->conn->prepare($query_str);
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function apontar($id)
	{
		$this->history("tickets", "adicionado o label lightgreen e alterado o campo apontado para 1 no ticket {$id}");
		$query = $this->conn->prepare("update tickets set apontado = 1, labels = 'lightgreen' where id = :id;");
		$query->bindParam("id", $id);
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function ignorar($id)
	{
		$this->history("tickets", "adicionado o label #A865C9 ao ticket {$id}");
		$query = $this->conn->prepare("update tickets set labels = '#A865C9' where id = :id;");
		$query->bindParam("id", $id);
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function limpar($id)
	{
		$result = $this->select(columns: "labels", where: "where id = :id;", params: ["id" => $id]);

		if (count($result) > 0) {
			foreach ($result as $row) {
				$this->history("tickets", "limpado o label {$row['labels']} e o campo apontado do ticket {$id}");
			}
		}

		$query = $this->conn->prepare("update tickets set labels = '', apontado = 0 where id = :id;");
		$query->bindParam("id", $id);
		return $query->execute();
	}

	public function history($table, $description)
	{
		$query = $this->conn->prepare("insert into history(`table`, `description`, `datetime`) values (:table, :description, CURRENT_TIMESTAMP)");
		$query->bindParam("table", $table);
		$query->bindParam("description", $description);
		return $query->execute();
	}

	public function select_customer($array = [])
	{
		$options= "";

		$result = $this->select(columns: "cliente", order: " group by cliente order by cliente asc;");

		if (count($result) > 0) {
			foreach ($result as $row) {
				if (count($array) > 0) {
					if(!array_key_exists($row['cliente'], $array)){
						$options .= '<option value="'.$row['cliente'].'">'.$row['cliente'].'</option>';
					}
				}else{
					$options .= '<option value="'.$row['cliente'].'">'.$row['cliente'].'</option>';
				}

			}
		}
		return $options;
	}
}
