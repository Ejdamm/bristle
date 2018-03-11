<?php
require "lib/Database/TSQLQueryBuilderBasic.php";
require "lib/Database/CDatabaseBasic.php";

class DB_QUERY
{
    private $db = null;

    public function __construct()
    {
        require __DIR__ . '/conf.php';
        $this->db = new \Mos\Database\CDatabaseBasic($DB_OPTIONS);
        $this->db->connect();
    }

    private function firstDay($days)
    {
        $firstDayFormat = "Y-m-d 00:00:00";
        $hours = 24 * 3600;
        switch ($days) {
        case 7:
        case 30:
            break;
        case 365:
            $followingMonth = date("m", time() - 364 * 24 * 3600) % 12 + 1;
            $firstDayFormat = "Y-$followingMonth-01 00:00:00";
            break;
        case 1:
        default:
            $days = 2;
            $hours = 23 * 3600;
            $firstDayFormat = "Y-m-d H:00:00";
        }
        $firstDay = date($firstDayFormat, time() - ($days - 1) * $hours);
        return $firstDay;
    }

    public function countEventsPerDay($days)
    {
        switch ($days) {
        case 7:
            $format = "%W";
            break;
        case 30:
            $format = "%d/%m";
            break;
        case 365:
            $format = "%M";
            break;
        case 1:
        default:
            $format = "%H:00";
        }
        $firstDay = $this->firstDay($days);
        $sql = "SELECT DATE_FORMAT(timestamp, ?) as date,
                COUNT(*) as nrOfEvents, sig_priority as priority
                FROM event
                INNER JOIN signature ON event.signature = signature.sig_id
                WHERE DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') >= ?
                GROUP BY date, priority";
        $res = $this->db->executeFetchAll($sql, array($format, $firstDay));
        $arr = array();
        foreach($res as $row) {
            $arr[$row->date][$row->priority] = $row->nrOfEvents;
        }
        return $arr;
    }

    public function getEvents($offset, $filter, $limit)
    {
        if (!is_numeric($offset)) {
            return array();
        }
        $params = array();
        if (!empty($filter)) {
            $where = "WHERE";
            foreach ($filter as $key => $value) {
                $where .= " $key LIKE ? AND";
                $params[] = $value;
            }
            $where = rtrim($where, " AND");
        } else {
            $where = "";
        }
        $sql = "SELECT event.sid, event.cid, sig_name, DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
                DATE_FORMAT(timestamp, '%H:%i') AS time, sig_priority,
                inet_ntoa(ip_src) as ip_src, inet_ntoa(ip_dst) as ip_dst
                FROM event
                INNER JOIN signature on event.signature = signature.sig_id
                INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
                $where
                ORDER BY date DESC, time DESC
                LIMIT ?
                OFFSET ?";
        array_push($params, $limit, $offset);
        $res = $this->db->executeFetchAll($sql, $params);
        return $res;
    }

    public function getSingleEvent($sid, $cid)
    {
        $sql = "SELECT * FROM event
                LEFT JOIN data on event.sid = data.sid AND event.cid = data.cid
                INNER JOIN signature on event.signature = signature.sig_id
                INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
                LEFT JOIN tcphdr on event.sid = tcphdr.sid AND event.cid = tcphdr.cid
                LEFT JOIN udphdr on event.sid = udphdr.sid AND event.cid = udphdr.cid
                LEFT JOIN icmphdr on event.sid = icmphdr.sid AND event.cid = icmphdr.cid
                LEFT JOIN sig_class on signature.sig_class_id = sig_class.sig_class_id
                WHERE event.sid = ? AND event.cid = ?";
        $res = $this->db->executeFetchAll($sql, array($sid, $cid));
        return $res[0];
    }

    public function countEvents()
    {
        $sql = "SELECT COUNT(*) as amount FROM event";
        $res = $this->db->executeFetchAll($sql);
        return $res[0]->amount;
    }

    public function getCommonEvents($days)
    {
        $firstDay = $this->firstDay($days);
        $sql = "SELECT sig_name, COUNT(sig_name) AS amount FROM event
                INNER JOIN signature on event.signature = signature.sig_id
                WHERE DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') >= ?
                GROUP BY sig_name
                ORDER BY amount DESC
                LIMIT 5";
        $res = $this->db->executeFetchAll($sql, array($firstDay));
        return $res;
    }

    public function getFrequentIP($days)
    {
        $firstDay = $this->firstDay($days);
        $sql = "SELECT inet_ntoa(ip_src) as ip_src, COUNT(inet_ntoa(ip_src)) as amount
                FROM event
                INNER JOIN signature on event.signature = signature.sig_id
                INNER JOIN iphdr on event.sid = iphdr.sid AND event.cid = iphdr.cid
                WHERE DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:%s') >= ?
                GROUP BY ip_src
                ORDER BY amount DESC
                LIMIT 5";
        $res = $this->db->executeFetchAll($sql, array($firstDay));
        return $res;
    }
}
