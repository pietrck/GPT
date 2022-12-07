<?php

/**
 * 
 */
class DB
{
	public $conn;
	public $server = "localhost";
	// public $server = "172.16.0.13";

	public function __construct()
	{
		$this->conn = $this->open();
	}
	
	public function open()
	{
		$conn = new PDO("mysql:dbname=gpt;host=".$this->server.";","root","");
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

	public function alter($id, $cliente)
	{
		$labels = "";
		$result = $this->select(columns: "cliente, labels", where: "where id = :id;", params: ["id" => $id]);

		if (count($result) > 0) {
			foreach ($result as $row) {
				$this->history("tickets", "alterado o cliente de {$row['cliente']} para {$cliente} do apontamento {$id}");
				$labels = $row['labels'];
			}
		}

		$clientes = $this->select(from: "clientes", columns: "classificacao", where: "where nome = :nome;", params: ["nome" => $cliente]);

		if (count($clientes) > 0) {
			foreach ($clientes as $row) {
				if ($row['classificacao'] == "SCC" && $labels == "") {
					$labels = "lightblue";
				}elseif ($labels == "lightblue") {
					$labels = "";
				}
			}
		}

		$query = $this->conn->prepare("update tickets set cliente = :cliente, labels = :labels where id = :id;");
		$query->bindParam("id", $id);
		$query->bindParam("cliente", $cliente);
		$query->bindParam("labels", $labels);
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
		$clientes = [];

		$result = $this->select(columns: "cliente", order: " group by cliente order by cliente asc;");

		if (count($result) > 0) {
			foreach ($result as $row) {
				if (count($array) > 0) {
					if(!array_key_exists($row['cliente'], $array)){
						$clientes[] = trim($row['cliente']);
					}
				}else{
					$clientes[] = trim($row['cliente']);
				}

			}
		}

		$result = $this->select(columns: "nome", from: "clientes");

		if (count($result) > 0) {
			foreach ($result as $row) {
				if (count($array) > 0) {
					if(!array_key_exists($row['nome'], $array)){
						$clientes[] = trim($row['nome']);
					}
				}else{
					$clientes[] = trim($row['nome']);
				}

			}
		}

		$clientes = array_unique($clientes);
		sort($clientes);

		foreach ($clientes as $key => $value) {
			$options .= '<option value="'.$value.'">'.$value.'</option>';
		}

		return $options;
	}

	public function insert_customer($post)
	{
		$name = $post['name'];
		if ($post['name'] == "") {
			$name = $post['name_writing'];
		}
		$query = $this->conn->prepare("insert into clientes(nome,classificacao) values (:nome,:classificacao)");
		$query->bindParam("nome", $name);
		$query->bindParam("classificacao", $post['classificacao']);
		$query->execute();

		if ($pos['classificacao'] == "SCC") {
			$query_update = $this->conn->prepare("update tickets set labels = 'lightblue' where cliente = :cliente and labels = '' and horas <> '00:00:01' and agente in (select nome from users);");
			$query_update->bindParam("cliente", $name);
			return $query_update->execute();
		}

		return "";
	}

	public function insert_user($post)
	{
		$name = $post['name_writing'];
		$query = $this->conn->prepare("insert into users(nome,data_criacao) values (:nome,CURRENT_TIMESTAMP)");
		$query->bindParam("nome", $name);
		$query->execute();

		$query_update = $this->conn->prepare("update tickets set labels = 'lightblue' where agente = :agente and labels = '' and horas <> '00:00:01' and agente in (select nome from users);");
		$query_update->bindParam("agente", $name);
		return $query_update->execute();
	}
}
