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

?>
