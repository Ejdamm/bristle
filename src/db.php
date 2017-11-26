<?php

class DB_CONNECT
{
    private $db = null;

    public function __construct()
    {
        $this->connect();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function connect()
    {
        include __DIR__ . '/conf.php';

        $this->db = new mysqli($DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_DATABASE);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }

        return $this->db;
    }

    public function close()
    {
        if ($this->db != null) {
            $this->db->close();
        }
    }

    public function countEventsPerDay($days)
    {
        $firstDayFormat = "Y-m-d 00:00:00";
        $hours = 24 * 3600;
        switch ($days) {
        case 7:
            $format = "%W";
            break;
        case 30:
            $format = "%d/%m";
            break;
        case 365:
            $format = "%M";
            $followingMonth = date("m", time() - 364 * 24 * 3600) % 12 + 1;
            $firstDayFormat = "Y-$followingMonth-01 00:00:00";
            break;
        case 1:
        default:
            $format = "%H:00";
            $days = 2;
            $hours = 23 * 3600;
            $firstDayFormat = "Y-m-d H:00:00";
        }
        $firstDay = date($firstDayFormat, time() - ($days - 1) * $hours);
        $sql = "SELECT DATE_FORMAT(timestamp, '$format') as date,
                COUNT(*) as nrOfEvents, sig_priority as priority
                FROM event
                INNER JOIN signature ON event.signature = signature.sig_id
                WHERE DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') >= '$firstDay'
                GROUP BY date, priority";
        if (!$result = $this->db->query($sql)) {
            die("Error description: " . $this->db->error);
        }
        $arr = array();
        while ($row = $result->fetch_assoc()) {
            $arr[$row['date']][$row['priority']] = $row['nrOfEvents'];
        }

        return $arr;
    }

    public function getEvents($offset, $limit)
    {
        if (!is_numeric($offset)) {
            return array();
        }
        $sql = "SELECT event.sid, event.cid, sig_name, DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
                DATE_FORMAT(timestamp, '%H:%i') AS time, sig_priority,
                inet_ntoa(ip_src) as ip_src, inet_ntoa(ip_dst) as ip_dst
                FROM event
                INNER JOIN signature on event.signature = signature.sig_id
                INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
                ORDER BY date DESC, time DESC
                LIMIT $limit
                OFFSET $offset";
        if (!$result = $this->db->query($sql)) {
            die("Error description: " . $this->db->error);
        }
        $arr = array();
        while ($row = $result->fetch_assoc()) {
            $arr[] = $row;
        }

        return $arr;
    }

    public function getSingleEvent($sid, $cid)
    {
        $sql = "SELECT data_payload, tcp_sport, tcp_dport, udp_sport, udp_dport
                FROM event
                INNER JOIN data on event.sid = data.sid AND event.cid = data.cid
                INNER JOIN signature on event.signature = signature.sig_id
                INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
                LEFT JOIN tcphdr on event.sid = tcphdr.sid AND event.cid = tcphdr.cid
                LEFT JOIN udphdr on event.sid = udphdr.sid AND event.cid = udphdr.cid
                LEFT JOIN icmphdr on event.sid = icmphdr.sid AND event.cid = icmphdr.cid
                WHERE event.sid = $sid AND event.cid = $cid";
        if (!$result = $this->db->query($sql)) {
            die("Error description: " . $this->db->error);
        }
        $arr = array();
        return $result->fetch_assoc();
    }

    public function countEvents()
    {
        $sql = "SELECT COUNT(*) as amount FROM event";
        if (!$result = $this->db->query($sql)) {
            die("Error description: " . $this->db->error);
        }
        $arr = array();
        return $result->fetch_assoc();
    }

    public function getLastEvents()
    {
        $sql = "SELECT sig_name, MAX(timestamp) AS timestamp FROM event
                INNER JOIN signature on event.signature = signature.sig_id
                GROUP BY sig_name
                ORDER BY timestamp DESC
                LIMIT 5";
        if (!$result = $this->db->query($sql)) {
            die("Error description: " . $this->db->error);
        }
        $arr = array();
        while ($row = $result->fetch_assoc()) {
            $arr[] = $row;
        }

        return $arr;
    }

    public function getCommonEvents()
    {
        $sql = "SELECT sig_name, COUNT(sig_name) AS amount FROM event
                INNER JOIN signature on event.signature = signature.sig_id
                WHERE timestamp BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()
                GROUP BY sig_name
                ORDER BY amount DESC
                LIMIT 5";
        if (!$result = $this->db->query($sql)) {
            die("Error description: " . $this->db->error);
        }
        $arr = array();
        while ($row = $result->fetch_assoc()) {
            $arr[] = $row;
        }

        return $arr;
    }

    public function getFrequentIP()
    {
        $sql = "SELECT inet_ntoa(ip_src) as ip_src, COUNT(inet_ntoa(ip_src)) as amount
                FROM event
                INNER JOIN signature on event.signature = signature.sig_id
                INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
                WHERE timestamp BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()
                GROUP BY ip_src
                ORDER BY amount DESC
                LIMIT 5";
        if (!$result = $this->db->query($sql)) {
            die("Error description: " . $this->db->error);
        }
        $arr = array();
        while ($row = $result->fetch_assoc()) {
            $arr[] = $row;
        }

        return $arr;
    }
}
