<?php
function paging($offset, $limit = 10) {
	$html = "";
	$next = $offset + $limit;
	$previous = $offset - $limit;
	$html .= "<a href='?offset=$next'>Next</a>";
	return $html;
}

?>
