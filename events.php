<?php 
include 'src/header.php';
$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
$html = "";
$db = new DB_connect;
$db->connect();	

$html .= "<section id='events'>";


$events = $db->getEvents($offset, $LIMIT);
$runningDate = null;
foreach($events as $event) {
	if ($event['date'] != $runningDate) {
		$html .= "<h2>".$event['date']."</h2>";
		$runningDate = $event['date'];
		$html .= "<div class='eventHeader'>
			<span class='eventAttr' id='eventPrio'>Sev.
			</span><span class='eventAttr' id='eventSrc'>Source IP
			</span><span class='eventAttr' id='eventDest'>Destination IP
			</span><span class='eventAttr' id='eventName'>Event Signature
			</span><span class='eventAttr' id='eventTime'>Time</span>
			</div>";
	}
	$html .= "<div class='event'>
	<span class='eventAttr' id='eventPrio".$event['sig_priority']."'>".$event['sig_priority']."
	</span><span class='eventAttr' id='eventSrc'>".$event['ip_src']."
	</span><span class='eventAttr' id='eventDest'>".$event['ip_dst']."
	</span><span class='eventAttr' id='eventName'>".$event['sig_name']."
	</span><span class='eventAttr' id='eventTime'>".$event['time']."</span>
	</div>";
}
$html .= "</section>";
$totalEvents = $db->countEvents();
$html .= paging($offset, $totalEvents['amount']);

echo $html;
include 'src/footer.php';
