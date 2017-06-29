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
			die("Connection failed: " . $this->db->connect_error);
 
		return $this->db;
	}
 
	public function close() {
		if($this->db != null)
			$this->db->close();	
	}
	
	public function countSeverities() {
		$sql = "SELECT COUNT(*) as count, sig_priority FROM event 
			INNER JOIN signature on event.signature = signature.sig_id
			GROUP BY sig_priority
			ORDER BY sig_priority";
		if (!$result = $this->db->query($sql))
			die("Error description: " . $this->db->error);
		$arr = array();
		while ($row = $result->fetch_assoc())
			$arr[] = $row;	
		
		return $arr;
	}

	public function getEvents($offset, $limit) {
		if(!is_numeric($offset))
			return array();
		$sql = "SELECT sig_name, DATE_FORMAT(timestamp, '%Y-%m-%d') AS date, DATE_FORMAT(timestamp, '%H:%i') AS time, 
			sig_priority, inet_ntoa(ip_src) as ip_src, inet_ntoa(ip_dst) as ip_dst, 
			COUNT(DATE_FORMAT(timestamp, '%H:%i')) AS sessions
			FROM event 
			INNER JOIN signature on event.signature = signature.sig_id
			INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
			GROUP BY sig_name, sig_priority, ip_src, ip_dst, date, time
			ORDER BY date DESC, time DESC
			LIMIT $limit
			OFFSET $offset";
		if (!$result = $this->db->query($sql))
			die("Error description: " . $this->db->error);
		$arr = array();
		while ($row = $result->fetch_assoc())
			$arr[] = $row;	
		
		return $arr;
	}

	public function countEvents() {
		$sql = "SELECT COUNT(*) AS amount FROM 
				(SELECT sig_name, DATE_FORMAT(timestamp, '%Y-%m-%d') AS date, DATE_FORMAT(timestamp, '%H:%i') AS time, 
				sig_priority, inet_ntoa(ip_src) as ip_src, inet_ntoa(ip_dst) as ip_dst 
				FROM event 
				INNER JOIN signature on event.signature = signature.sig_id
				INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
				GROUP BY sig_name, sig_priority, ip_src, ip_dst, date, time) AS total";
		if (!$result = $this->db->query($sql))
			die("Error description: " . $this->db->error);
		$arr = array();
		return $result->fetch_assoc();
	}

	public function getLastEvents() {
		$sql = "SELECT sig_name, MAX(timestamp) AS timestamp FROM event 
			INNER JOIN signature on event.signature = signature.sig_id
			GROUP BY sig_name
			ORDER BY timestamp DESC
			LIMIT 5
		";
		if (!$result = $this->db->query($sql))
			die("Error description: " . $this->db->error);
		$arr = array();
		while ($row = $result->fetch_assoc())
			$arr[] = $row;	
		
		return $arr;
	}

	public function getCommonEvents() {
		$sql = "SELECT sig_name, COUNT(sig_name) AS amount FROM event 
			INNER JOIN signature on event.signature = signature.sig_id
			WHERE timestamp BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()
			GROUP BY sig_name
			ORDER BY amount DESC
			LIMIT 5
		";
		if (!$result = $this->db->query($sql))
			die("Error description: " . $this->db->error);
		$arr = array();
		while ($row = $result->fetch_assoc())
			$arr[] = $row;	
		
		return $arr;
	}

	public function getFrequentIP() {
		$sql = "SELECT inet_ntoa(ip_src) as ip_src, COUNT(inet_ntoa(ip_src)) as amount
			FROM event 
			INNER JOIN signature on event.signature = signature.sig_id
			INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
			WHERE timestamp BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()
			GROUP BY ip_src
			ORDER BY amount DESC
			LIMIT 5
		";
		if (!$result = $this->db->query($sql))
			die("Error description: " . $this->db->error);
		$arr = array();
		while ($row = $result->fetch_assoc())
			$arr[] = $row;	
		
		return $arr;
	}
}
