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

	public function select($columns = "*", $where = ""):array
	{

		$query = $this->conn->prepare("select {$columns} from `tickets` {$where};");
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function select_table($start, $end)
	{
		$query = $this->conn->prepare("SELECT tickets.*, clientes.classificacao, users.nome FROM `tickets` left join users on users.nome = tickets.agente left join clientes on clientes.nome = tickets.cliente where tickets.data between $start and $end group by id order by tickets.id asc");
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function apontar($id)
	{
		$query = $this->conn->prepare("update tickets set apontado = 1, labels = 'lightgreen' where id = :id;");
		$query->bindParam("id", $id);
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function ignorar($id)
	{
		$query = $this->conn->prepare("update tickets set labels = '#A865C9' where id = :id;");
		$query->bindParam("id", $id);
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}

	public function limpar($id)
	{
		$query = $this->conn->prepare("update tickets set labels = '', apontado = 0 where id = :id;");
		$query->bindParam("id", $id);
		$query->execute();

		return $query->fetchall(PDO::FETCH_ASSOC);
	}
}
