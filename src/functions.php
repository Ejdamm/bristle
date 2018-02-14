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
				<h4>IP Header</h4>
				<div class='eventInfoTable'>
					<div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Source</div>
						<div class='eventInfoTableValue'>" . long2ip($singleEvent->ip_src) . "</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Destination</div>
						<div class='eventInfoTableValue'>" . long2ip($singleEvent->ip_dst) . "</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Version</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_ver</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Header Length</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_hlen</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>ToS</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_tos</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Total Length</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_len</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Identification</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_id</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Flags</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_flags</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Fragment Offset</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_off</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>TTL</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_ttl</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Protocol</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_proto</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Header Checksum</div>
						<div class='eventInfoTableValue'>$singleEvent->ip_csum</div>
					</div>
				</div>
			</div>";
	return $html;
}

function createICMPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'>
				<h4>ICMP Header</h4>
				<div class='eventInfoTable'>
					<div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Type</div>
						<div class='eventInfoTableValue'>$singleEvent->icmp_type</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Code</div>
						<div class='eventInfoTableValue'>$singleEvent->icmp_code</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Checksum</div>
						<div class='eventInfoTableValue'>$singleEvent->icmp_csum</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Identifier</div>
						<div class='eventInfoTableValue'>$singleEvent->icmp_id</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Sequence Nr</div>
						<div class='eventInfoTableValue'>$singleEvent->icmp_seq</div>
					</div>
				</div>
			</div>";
	return $html;
}

function createTCPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'>
				<h4>TCP Header</h4>
				<div class='eventInfoTable'>
					<div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Source Port</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_sport</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Destination Port</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_dport</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Sequence Nr</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_seq</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Acknowledgment Nr</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_ack</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Data Offset</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_off</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Reserved</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_res</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Flags</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_flags</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Window Size</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_win</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Checksum</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_csum</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Urgent Pointer</div>
						<div class='eventInfoTableValue'>$singleEvent->tcp_urp</div>
					</div>
				</div>
			</div>";
	return $html;
}

function createUDPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'>
				<h4>UDP Header</h4>
				<div class='eventInfoTable'>
					<div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Source Port</div>
						<div class='eventInfoTableValue'>$singleEvent->udp_sport</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Destination Port</div>
						<div class='eventInfoTableValue'>$singleEvent->udp_dport</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Length</div>
						<div class='eventInfoTableValue'>$singleEvent->udp_len</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Cheksum</div>
						<div class='eventInfoTableValue'>$singleEvent->udp_csum</div>
					</div>
				</div>
			</div>";
	return $html;
}

function createSignatureInfo($singleEvent)
{
	$class_name = $singleEvent->sig_class_name ? $singleEvent->sig_class_name : "none";
	$html = "<div class='eventInfoBlock'>
				<h4>Signature</h4>
				<div class='eventInfoTable'>
					<div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Generator Id</div>
						<div class='eventInfoTableValue'>$singleEvent->sig_gid</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Signature Id</div>
						<div class='eventInfoTableValue'>$singleEvent->sig_sid</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Revision</div>
						<div class='eventInfoTableValue'>$singleEvent->sig_rev</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Priority</div>
						<div class='eventInfoTableValue'>$singleEvent->sig_priority</div>
					</div><div class='eventInfoTableCell'>
						<div class='eventInfoTableHeader'>Class</div>
						<div class='eventInfoTableValue'>$class_name</div>
					</div>
				</div>
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
