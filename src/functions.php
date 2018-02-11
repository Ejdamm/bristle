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
						<th>src</th>
						<th>dest</th>
						<th>ver</th>
						<th>hlen</th>
						<th>tos</th>
						<th>len</th>
						<th>id</th>
						<th>flags</th>
						<th>off</th>
						<th>ttl</th>
						<th>proto</th>
						<th>csum</th>
					</tr>
					<tr>
						<td>$singleEvent->ip_src</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</div>";
	return $html;
}

function createICMPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>ICMP Header<h4>
			</div>";
	return $html;
}

function createTCPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>TCP Header<h4>
			  <span class='eventInfoField'>
			  	  <span class='fieldLabel'>Source port: </span>
			  	  <span>" . $singleEvent->tcp_sport . "</span>
			  </span>
			  	  <span class='eventInfoField'>
			  	  <span class='fieldLabel'>Destination port: </span>
			  <span>" . $singleEvent->tcp_dport . "</span></span>
			  </div>";
	return $html;
}

function createUDPheader($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>UDP Header<h4>
			  <span class='eventInfoField'>
			  	  <span class='fieldLabel'>Source port: </span>
			  	  <span>" . $singleEvent->udp_sport . "</span>
			  </span>
			  	  <span class='eventInfoField'>
			  	  <span class='fieldLabel'>Destination port: </span>
			  <span>" . $singleEvent->udp_dport . "</span></span>
			  </div>";
	return $html;
}

function createSignatureInfo($singleEvent)
{
	$html = "<div class='eventInfoBlock'><h4>Signature<h4>
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
