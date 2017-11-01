<?php
include 'src/header.php';

$html = "";
$db = new DB_connect;
$db->connect();
$severityCount = $db->countSeverities();

//Create chart
require 'lib/ChartJS.php';

$labels = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
$options = array();
$attributes = array('id' => 'chart');
$chart = new ChartJS('line', $labels, $options, $attributes);
$chart->addDataset(array());
$html .= "<section id='container'>" . $chart;

//Prepare the severity boxes
$severities = array(
    1 => array('count' => 0, 'id' => "high", 'description' => "High severity"),
    2 => array('count' => 0, 'id' => "medium", 'description' => "Medium severity"),
    3 => array('count' => 0, 'id' => "low", 'description' => "Low severity")
);

//Update the count from the database result
foreach ($severityCount as $sCount) {
    $severities[$sCount['sig_priority']]['count'] = $sCount['count'];
}

//Create the html for severity boxes
$html .= "<div id='severities'>";
foreach ($severities as $severity) {
    $html .= "<span class='outer_severity_box'>
                <span class='inner_severity_box' id='".$severity['id']."'>
                  <div class='severity_number red'>".$severity['count']."</div>
                  <div class='severity_caption'>".$severity['description']."</div>
                </span>
              </span>";
}
$html .= "</div></section>";

//Get statistics
$htmlLE = "";
$lastEvents = $db->getLastEvents();
foreach($lastEvents as $event)
    $htmlLE .= "<div class='comp_entry'>".$event['sig_name']."</div>";

$htmlCE = "";
$commonEvents = $db->getCommonEvents();
foreach($commonEvents as $event)
    $htmlCE .= "<div class='comp_entry'>".$event['sig_name']." (".$event['amount'].")"."</div>";

$htmlFIP = "";
$frequentIP = $db->getFrequentIP();
foreach($frequentIP as $ip)
    $htmlFIP .= "<div class='comp_entry'>".$ip['ip_src']." (".$ip['amount'].")"."</div>";


$html .= "
<aside id='compilation'>
  <div>
    <h4>Last unique events</h4>
    $htmlLE
  </div>

  <div>
    <h4>Most common events (last 30 days)</h4>
    $htmlCE
  </div>

  <div>
    <h4>Most frequent source IP (last 30 days)</h4>
    $htmlFIP
  </div>
</aside>

<script src='lib/Chart.js'></script>
<script src='lib/driver.js'></script>
<script>(function() {loadChartJsPhp();})();</script>
";

echo $html;
include 'src/footer.php';
