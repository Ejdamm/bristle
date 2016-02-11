<?php
function paging($offset, $totalEvents, $limit = 10) {
	$html = "";
	$next = $offset + $limit;
	$prev = $offset - $limit;
	if ($prev >= 0)
		$html .= "<div id='paging'><a href='?offset=$prev' id='prev'>Previous</a>";
	if ($next <= $totalEvents)
		$html .= "<a href='?offset=$next' id='next'>Next</a></div>";
	return $html;
}

?>
