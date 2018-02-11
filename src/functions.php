<?php

function dump($arr)
{
	echo "<pre>" . print_r($arr, true) . "</pre>";
}

function createURL(array $gets)
{
    $url = "";
    if (!empty($gets)) {
        $url = "?";
        foreach ($gets as $key => $value) {
            if ($value) {
                $url .= "$key=$value&";
            }
        }
        $url = rtrim($url, "&");
    }
    return $url;
}

function paging($offset, $totalEvents)
{
    include 'src/conf.php';
    $html = "";
    $next = $offset + $LIMIT;
    $prev = $offset - $LIMIT;
    if ($prev >= 0) {
        $html .= "<div id='paging'><a href='?offset=$prev' id='prev'>Previous</a>";
    }
    if ($next <= $totalEvents) {
        $html .= "<a href='?offset=$next' id='next'>Next</a></div>";
    }
    return $html;
}

function showSingleEvent($db, $sid, $cid)
{
    $singleEvent = $db->getSingleEvent($sid, $cid);
    $html = "";
    $html .= "<div id='eventInfo'>";
	$html .= createIPheader($singleEvent);

	switch ($singleEvent->ip_proto) {
	case 1:
		$html .= createICMPheader($singleEvent);
		break;
	case 6:
		$html .= createTCPheader($singleEvent);
		break;
	case 17:
		$html .= createUDPheader($singleEvent);
		break;
	}

	$html .= createSignatureInfo($singleEvent);
	$html .= createPayloadInfo($singleEvent->data_payload);
    $html .= "</div>";
    return $html;
}

function createIPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'>
				<h4>IP Header<h4>
				<table class='eventTable'>
					<tr>
						<th>Source</th>
						<th>Destination</th>
						<th>Version</th>
						<th>Header Length</th>
						<th>ToS</th>
						<th>Total Length</th>
						<th>Identification</th>
						<th>Flags</th>
						<th>Fragment Offset</th>
						<th>TTL</th>
						<th>Protocol</th>
						<th>Header Checksum</th>
					</tr>
					<tr>
						<td>" . long2ip($singleEvent->ip_src) . "</td>
						<td>" . long2ip($singleEvent->ip_dst) . "</td>
						<td>$singleEvent->ip_ver</td>
						<td>$singleEvent->ip_hlen</td>
						<td>$singleEvent->ip_tos</td>
						<td>$singleEvent->ip_len</td>
						<td>$singleEvent->ip_id</td>
						<td>$singleEvent->ip_flags</td>
						<td>$singleEvent->ip_off</td>
						<td>$singleEvent->ip_ttl</td>
						<td>$singleEvent->ip_proto</td>
						<td>$singleEvent->ip_csum</td>
					</tr>
				</table>
			</div>";
	return $html;
}

function createICMPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'>
				<h4>ICMP Header<h4>
				<table class='eventTable'>
					<tr>
						<th>Type</th>
						<th>Code</th>
						<th>Checksum</th>
						<th>Identifier</th>
						<th>Sequence Nr</th>
					</tr>
					<tr>
						<td>$singleEvent->icmp_type</td>
						<td>$singleEvent->icmp_code</td>
						<td>$singleEvent->icmp_csum</td>
						<td>$singleEvent->icmp_id</td>
						<td>$singleEvent->icmp_seq</td>
					</tr>
				</table>
			</div>";
	return $html;
}

function createTCPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'>
				<h4>TCP Header<h4>
				<table class='eventTable'>
					<tr>
						<th>Source Port</th>
						<th>Destination Port</th>
						<th>Sequence Nr</th>
						<th>Acknowledgment Nr</th>
						<th>Data Offset</th>
						<th>Reserved</th>
						<th>Flags</th>
						<th>Window Size</th>
						<th>Checksum</th>
						<th>Urgent Pointer</th>
					</tr>
					<tr>
						<td>$singleEvent->tcp_sport</td>
						<td>$singleEvent->tcp_dport</td>
						<td>$singleEvent->tcp_seq</td>
						<td>$singleEvent->tcp_ack</td>
						<td>$singleEvent->tcp_off</td>
						<td>$singleEvent->tcp_res</td>
						<td>$singleEvent->tcp_flags</td>
						<td>$singleEvent->tcp_win</td>
						<td>$singleEvent->tcp_csum</td>
						<td>$singleEvent->tcp_urp</td>
					</tr>
				</table>
			</div>";
	return $html;
}

function createUDPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'>
				<h4>UDP Header<h4>
				<table class='eventTable'>
					<tr>
						<th>Source Port</th>
						<th>Destination Port</th>
						<th>Length</th>
						<th>Cheksum</th>
					</tr>
					<tr>
						<td>$singleEvent->udp_sport</td>
						<td>$singleEvent->udp_dport</td>
						<td>$singleEvent->udp_len</td>
						<td>$singleEvent->udp_csum</td>
					</tr>
				</table>
			  </div>";
	return $html;
}

function createSignatureInfo($singleEvent)
{
	$class_name = $singleEvent->sig_class_name ? $singleEvent->sig_class_name : "none";
	$html = "<div class='eventInfoBlock'>
				<h4>Signature<h4>
				<table class='eventTable'>
					<tr>
						<th>Generator Id</th>
						<th>Signature Id</th>
						<th>Revision</th>
						<th>Priority</th>
						<th>Class</th>
					</tr>
					<tr>
						<td>$singleEvent->sig_gid</td>
						<td>$singleEvent->sig_sid</td>
						<td>$singleEvent->sig_rev</td>
						<td>$singleEvent->sig_priority</td>
						<td>$class_name</td>
					</tr>
				</table>
			</div>";
	return $html;
}

function createPayloadInfo($payload)
{
	$formattedPayload = $payload ? hex2ascii($payload) : "none";
	$html = "<div class='eventInfoBlock'><h4>Payload</h4>
			 <div>$formattedPayload</div></div>";
	return $html;
}

function hex2ascii($hex)
{
    $hex = chunk_split($hex, 2, " ");
    $arr = explode(" ", $hex);
    $html = "";
    $asciicells = "";
    $asciirows = "";
    $hexcells = "";
    $hexrows = "";
    $i = 1;
    unset($arr[sizeof($arr)-1]); //remove terminating null character
    foreach ($arr as $char) {
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
