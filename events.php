<?php
include 'src/header.php';
$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
$sid = isset($_GET['sid']) && is_numeric($_GET['sid']) ? $_GET['sid'] : NULL;
$cid = isset($_GET['cid']) && is_numeric($_GET['cid']) ? $_GET['cid'] : NULL;
$filterMode = isset($_GET['filter']) && $_GET['filter'] == "-" ? $_GET['filter'] : "+";
$inv = $filterMode == "+" ? "-" : "+";
$filter = filterHandler();
$gets = array('offset' => $offset, 'filter' => $inv, 'sid' => $sid, 'cid' => $cid);
$url = createURL($gets);
$html = "";
$db = new DB_query;

$html .= "<section id=filter><a href='$url'>".$filterMode."Filter</a>";
$gets['filter'] = $filterMode;
if ($filterMode == "-") {
    $html .= "<form method='GET' id='filterForm'>
              <input type='text' name='ip_src' placeholder='Source IP'>
              <input type='text' name='ip_dst' placeholder='Destination IP'>
              <input type='text' name='sig_name' placeholder='Event signature'>
              <input type='submit' name='submit' value='Filter'>
              <input type='hidden' name='filter' value='$filterMode'>
              </form>";
}
$html .= "</section>";

$html .= "<section id='events'>";
$events = $db->getEvents($offset, $filter, $LIMIT);
$runningDate = NULL;
foreach($events as $event) {
    if ($event->date != $runningDate) {
        $html .= "<h2>".$event->date."</h2>";
        $runningDate = $event->date;
        $html .= "<div class='eventHeader'>
                  <span class='eventAttr eventPrio'>Sev.
                  </span><span class='eventAttr eventSrc'>Source IP
                  </span><span class='eventAttr eventDest'>Destination IP
                  </span><span class='eventAttr eventName'>Event Signature
                  </span><span class='eventAttr eventTime'>Time</span></div>";
    }
    $gets['sid'] = $event->sid;
    $gets['cid'] = $event->cid;
    $url = createURL($gets);
    $html .= "<a href='$url'>
              <div class='event'>
              <span class='eventAttr eventPrio".$event->sig_priority."'>".$event->sig_priority."
              </span><span class='eventAttr eventSrc'>".$event->ip_src."
              </span><span class='eventAttr eventDest'>".$event->ip_dst."
              </span><span class='eventAttr eventName'>".$event->sig_name."
              </span><span class='eventAttr eventTime'>".$event->time."</span></div></a>";
    if ($sid == $event->sid && $cid == $event->cid) {
        $html .= showSingleEvent($db, $sid, $cid);
    }
}
$html .= "</section>";
$totalEvents = $db->countEvents();
$html .= paging($offset, $totalEvents);

echo $html;
include 'src/footer.php';
