<?php
include 'src/header.php';

$html = "";
$db = new DB_connect;
$db->connect();
$severityCount = $db->countSeverities();


//Create chart
require 'lib/ChartJS.php';

$days = isset($_GET['days']) ? $_GET['days'] : 1;
$keys = array();

switch ($days) {
case 7:
    for ($i = 6; $i >= 0; $i--) {
        $labels[] = date("l", time() - $i * 24 * 3600);
    }
    break;
case 30:
    for ($i = 29; $i >= 0; $i--) {
        $labels[] = date("d/m", time() - $i * 24 * 3600);
    }
    break;
case 365:
    $months = array('January', 'February', 'March',
                    'April', 'May', 'June',
                    'July', 'August', 'September',
                    'October', 'November', 'December');
    $m = date("m", time() - 364 * 24 * 3600);
    for ($i = 0; $i < 12; $i++) {
        $m %= 12;
        $labels[] = $months[$m++];
    }
    break;
case 1:
default:
    for ($i = 23; $i >= 0; $i--) {
        $labels[] = date("H:00", time() - $i * 3600);
    }
    $days = 1;
}

$chartdata = $db->countEventsPerDay($days);
foreach ($labels as $label) {
    $data[1][] = !empty($chartdata[$label][1]) ? $chartdata[$label][1] : 0;
    $data[2][] = !empty($chartdata[$label][2]) ? $chartdata[$label][2] : 0;
    $data[3][] = !empty($chartdata[$label][3]) ? $chartdata[$label][3] : 0;
}

$dataset[1]['data'] = $data[1];
$dataset[1]['backgroundColor'] = 'rgba(252, 75, 75, 0.5)';
$dataset[1]['borderColor'] = 'rgba(252, 75, 75, 0.5)';
$dataset[1]['label'] = 'High severity';
$dataset[2]['data'] = $data[2];
$dataset[2]['backgroundColor'] = 'rgba(255, 171, 46, 0.5)';
$dataset[2]['borderColor'] = 'rgba(255, 171, 46, 0.5)';
$dataset[2]['label'] = 'Medium severity';
$dataset[3]['data'] = $data[3];
$dataset[3]['backgroundColor'] = 'rgba(151, 255, 48, 0.5)';
$dataset[3]['borderColor'] = 'rgba(151, 255, 48, 0.5)';
$dataset[3]['label'] = 'Low severity';
$options = array(
               'scales' => array(
                   'yAxes' => array(
                       array('stacked' => true, 'ticks' => array('min' => 0))
                   )
               )
           );
$attributes = array('id' => 'chart');
$chart = new ChartJS('line', $labels, $options, $attributes);
$chart->addDataset($dataset[1]);
$chart->addDataset($dataset[2]);
$chart->addDataset($dataset[3]);
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
