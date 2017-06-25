<?php

class DB_CONNECT {
	private $db = null;

	public function __construct() {
		$this->connect();
	}
 
	public function __destruct() {
		$this->close();
	}

	public function connect() {
		include __DIR__ . '/conf.php';
	
		$this->db = new mysqli($DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_DATABASE);
	
		if ($this->db->connect_error)
		{
			die("Connection failed: " . $this->db->connect_error);
		}
 
		return $this->db;
	}
 
	public function close() {
		if($this->db != null)
			$this->db->close();	
	}
	
	public function fetchAll($table) {
		$sql = "SELECT * FROM `$table`";
		if (!$result = $this->db->query($sql))
		{
			die("Error description: " . $this->db->error);
		}
		$arr = array();
		while ($row = $result->fetch_assoc())
		{
			$arr[] = $row;	
		}
		return $arr;
	}

	public function countSeverities() {
		$sql = "SELECT COUNT(*) as count, sig_priority FROM event 
		INNER JOIN signature on event.signature = signature.sig_id
		GROUP BY sig_priority
		ORDER BY sig_priority";
		if (!$result = $this->db->query($sql))
		{
			die("Error description: " . $this->db->error);
		}
		$arr = array();
		while ($row = $result->fetch_assoc())
		{
			$arr[] = $row;	
		}
		return $arr;
	}
}
