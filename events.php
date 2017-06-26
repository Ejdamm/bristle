<?php 
include 'src/header.php';

$html = "";
$db = new DB_connect;
$db->connect();	

$html .= "<section id='events'>";
$html .= "<div class='eventHeader'>
	<span class='eventAttr' id='eventPrio'>Sev.
	</span><span class='eventAttr' id='eventSrc'>Source IP
	</span><span class='eventAttr' id='eventDest'>Destination IP
	</span><span class='eventAttr' id='eventName'>Event Signature
	</span><span class='eventAttr' id='eventTime'>Timestamp</span>
	</div>";


$events = $db->getEvents();
foreach($events as $event) {
	$html .= "<div class='event'>
	<span class='eventAttr' id='eventPrio".$event['sig_priority']."'>".$event['sig_priority']."
	</span><span class='eventAttr' id='eventSrc'>".$event['ip_src']."
	</span><span class='eventAttr' id='eventDest'>".$event['ip_dst']."
	</span><span class='eventAttr' id='eventName'>".$event['sig_name']."
	</span><span class='eventAttr' id='eventTime'>".$event['timestamp']."</span>
	</div>";
}
$html .= "</section>";



echo $html;
include 'src/footer.php';
