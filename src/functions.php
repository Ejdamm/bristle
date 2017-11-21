<?php

function paging($offset, $totalEvents) {
	include 'src/conf.php';
	$html = "";
	$next = $offset + $LIMIT;
	$prev = $offset - $LIMIT;
	if ($prev >= 0)
		$html .= "<div id='paging'><a href='?offset=$prev' id='prev'>Previous</a>";
	if ($next <= $totalEvents)
		$html .= "<a href='?offset=$next' id='next'>Next</a></div>";
	return $html;
}

function showSingleEvent($db, $sid, $cid)
{
	$singleEvent = $db->getSingleEvent($sid, $cid);
	$payload = $singleEvent['data_payload'];
	$html = "";
	$html .= "<div id='eventInfo'>";
	$html .= "<div class='eventInfoBlock'><span class='eventInfoField'><span class='fieldLabel'>Protocol: </span><span>";
	$sport = "<span class='eventInfoField'><span class='fieldLabel'>Source port: </span><span>";
	$dport = "<span class='eventInfoField'><span class='fieldLabel'>Destination port: </span><span>";

	if ($singleEvent['tcp_dport']) {
		$html .= "TCP</span></span>";
		$html .= $sport . $singleEvent['tcp_sport'] . "</span></span>";
		$html .= $dport . $singleEvent['tcp_dport'] . "</span></span></div>";
	} else if ($singleEvent['udp_dport']) {
		$html .= "UDP</span></span>";
		$html .= $sport . $singleEvent['udp_sport'] . "</span></span>";
		$html .= $dport . $singleEvent['udp_dport'] . "</span></span></div>";
	} else {
		$html .= "ICMP</span></span></div>";
	}

	$formattedPayload = hex2ascii($payload);
	$html .= "<div class='eventInfoBlock'><div class='fieldLabel'>Payload:</div><div>$formattedPayload</div></div>";
	$html .= "</div>";
	return $html;
}

function hex2ascii($hex) {
	$hex = chunk_split($hex, 2, " ");
	$arr = explode(" ", $hex);
	$html = "";
	$asciicells = "";
	$asciirows = "";
	$hexcells = "";
	$hexrows = "";
	$i = 1;
	unset($arr[sizeof($arr)-1]); //remove terminating null
	foreach($arr as $char) {
		$hexcells .= "<td>$char</td>";
		if (hexdec($char) < 32 || hexdec($char) > 126) {
			$ascii = ".";
		} else {
			$ascii = hex2bin($char);
		}
		$asciicells .= "<td>$ascii</td>";
		if ($i++ % 15 == 0) {
			$hexrows .= "<tr>$hexcells</tr>";
			$hexcells = "";
			$asciirows .= "<tr>$asciicells</tr>";
			$asciicells = "";
		}
	}
	if ($hexcells != "") {
		$hexrows .= "<tr>$hexcells</tr>";
		$asciirows .= "<tr>$asciicells</tr>";
	}
	$html .= "<table class='payloadTable'>$hexrows</table><table class='payloadTable'>$asciirows</table>";
	return $html;
}

?>
