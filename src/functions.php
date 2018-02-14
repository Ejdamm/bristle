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
	$html = "<div class='eventInfoBlock'><h4>IP Header</h4>";
	$src = long2ip($singleEvent->ip_src);
	$dst = long2ip($singleEvent->ip_dst);
	$srcAbuse = "<a href='https://www.abuseipdb.com/check/$src'>$src</a>";
	$destAbuse = "<a href='https://www.abuseipdb.com/check/$dst'>$dst</a>";
	$columns = array(
		array('header' => 'Source', 'value' => $srcAbuse),
		array('header' => 'Destination', 'value' => $destAbuse),
		array('header' => 'Version', 'value' => $singleEvent->ip_ver),
		array('header' => 'Header Length', 'value' => $singleEvent->ip_hlen),
		array('header' => 'ToS', 'value' => $singleEvent->ip_tos),
		array('header' => 'Total Length', 'value' => $singleEvent->ip_len),
		array('header' => 'Identification', 'value' => $singleEvent->ip_id),
		array('header' => 'Flags', 'value' => $singleEvent->ip_flags),
		array('header' => 'Fragment Offset', 'value' => $singleEvent->ip_off),
		array('header' => 'TTL', 'value' => $singleEvent->ip_ttl),
		array('header' => 'Protocol', 'value' => $singleEvent->ip_proto),
		array('header' => 'Header Checksum', 'value' => $singleEvent->ip_csum)
	);
	$html .= tableCreator($columns) . "</div>";
	return $html;
}

function createICMPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>ICMP Header</h4>";
	$columns = array(
		array('header' => 'Type', 'value' => $singleEvent->icmp_type),
		array('header' => 'Code', 'value' => $singleEvent->icmp_code),
		array('header' => 'Checksum', 'value' => $singleEvent->icmp_csum),
		array('header' => 'Identifier', 'value' => $singleEvent->icmp_id),
		array('header' => 'Sequence Nr', 'value' => $singleEvent->icmp_seq)
	);
	$html .= tableCreator($columns) . "</div>";
	return $html;
}

function createTCPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>TCP Header</h4>";
	$columns = array(
		array('header' => 'Source Port', 'value' => $singleEvent->tcp_sport),
		array('header' => 'Destination Port', 'value' => $singleEvent->tcp_dport),
		array('header' => 'Sequence Nr', 'value' => $singleEvent->tcp_seq),
		array('header' => 'Acknowledgment Nr', 'value' => $singleEvent->tcp_ack),
		array('header' => 'Data Offset', 'value' => $singleEvent->tcp_off),
		array('header' => 'Reserved', 'value' => $singleEvent->tcp_res),
		array('header' => 'Flags', 'value' => $singleEvent->tcp_flags),
		array('header' => 'Window Size', 'value' => $singleEvent->tcp_win),
		array('header' => 'Checksum', 'value' => $singleEvent->tcp_csum),
		array('header' => 'Urgent Pointer', 'value' => $singleEvent->tcp_urp)
	);
	$html .= tableCreator($columns) . "</div>";
	return $html;
}

function createUDPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>UDP Header</h4>";
	$columns = array(
		array('header' => 'Source Port', 'value' => $singleEvent->udp_sport),
		array('header' => 'Destination Port', 'value' => $singleEvent->udp_dport),
		array('header' => 'Length', 'value' => $singleEvent->udp_len),
		array('header' => 'Cheksum', 'value' => $singleEvent->udp_csum)
	);
	$html .= tableCreator($columns) . "</div>";
	return $html;
}

function createSignatureInfo($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>Signature</h4>";
	$class_name = $singleEvent->sig_class_name ? $singleEvent->sig_class_name : "none";
	$rule = "<a href='https://www.snort.org/rule_docs/
			$singleEvent->sig_gid-$singleEvent->sig_sid'>
			$singleEvent->sig_gid-$singleEvent->sig_sid</a>";
	$columns = array(
		array('header' => 'Generator Id', 'value' => $singleEvent->sig_gid),
		array('header' => 'Signature Id', 'value' => $singleEvent->sig_sid),
		array('header' => 'Revision', 'value' => $singleEvent->sig_rev),
		array('header' => 'Priority', 'value' => $singleEvent->sig_priority),
		array('header' => 'Class', 'value' => $class_name),
		array('header' => 'Snort Rule Docs', 'value' => $rule)
	);
	$html .= tableCreator($columns) . "</div>";
	return $html;
}

function createPayloadInfo($payload)
{
	$formattedPayload = $payload ? hex2ascii($payload) : "none";
	$html = "<div class='eventInfoBlock'><h4>Payload</h4>
			 <div>$formattedPayload</div></div>";
	return $html;
}

function tableCreator($columns)
{
	$html = "<div class='eventInfoTable'>";
	foreach ($columns as $column) {
		$html .= "<div class='eventInfoTableCell'>
				 	<div class='eventInfoTableHeader'>" . $column['header'] . "</div>
				 	<div class='eventInfoTableValue'>" . $column['value'] . "</div>
				 </div>";
	}
	$html .= "</div>";
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
