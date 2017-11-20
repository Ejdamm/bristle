<?php
include 'src/header.php';
$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
$sid = isset($_GET['sid']) && is_numeric($_GET['sid']) ? $_GET['sid'] : NULL;
$cid = isset($_GET['cid']) && is_numeric($_GET['cid']) ? $_GET['cid'] : NULL;
$html = "";
$db = new DB_connect;
$db->connect();

$html .= "<section id='events'>";


$events = $db->getEvents($offset, $LIMIT);
$runningDate = NULL;
foreach($events as $event) {
	if ($event['date'] != $runningDate) {
		$html .= "<h2>".$event['date']."</h2>";
		$runningDate = $event['date'];
		$html .= "<div class='eventHeader'>
			<span class='eventAttr eventPrio'>Sev.
			</span><span class='eventAttr eventSrc'>Source IP
			</span><span class='eventAttr eventDest'>Destination IP
			</span><span class='eventAttr eventName'>Event Signature
			</span><span class='eventAttr eventTime'>Time</span>
			</div>";
	}
	$html .= "<a href='?offset=$offset&sid=".$event['sid']."&cid=".$event['cid']."'><div class='event'>
	<span class='eventAttr eventPrio".$event['sig_priority']."'>".$event['sig_priority']."
	</span><span class='eventAttr eventSrc'>".$event['ip_src']."
	</span><span class='eventAttr eventDest'>".$event['ip_dst']."
	</span><span class='eventAttr eventName'>".$event['sig_name']."
	</span><span class='eventAttr eventTime'>".$event['time']."</span>
	</div></a>";
	if ($sid == $event['sid'] && $cid == $event['cid']) {
		$html .= showSingleEvent($db, $sid, $cid);
	}
}
$html .= "</section>";
$totalEvents = $db->countEvents();
$html .= paging($offset, $totalEvents['amount']);

echo $html;
include 'src/footer.php';
