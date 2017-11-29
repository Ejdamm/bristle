<?php
include 'src/header.php';

$html = "";
$db = new DB_connect;
$db->connect();
$days = isset($_GET['days']) ? $_GET['days'] : 1;
$severityLabels = array("High severity", "Medium severity", "Low severity");

//Create period navbar
$periods = array(1 => "href='?days=1'>24 hours</a></li>",
                 7 => "href='?days=7'>7 days</a></li>",
                 30 => "href='?days=30'>30 days</a></li>",
                 365 => "href='?days=365'>12 months</a></li>");
$lihtml = "";
foreach ($periods as $key => $li) {
    $lihtml .= "<li><a ";
    if ($days == $key) {
        $lihtml .= "id='active'";
    }
    $lihtml .= " $li";
}
$html .= "<nav id='period_navbar'><ul id='period_navbar-list'>$lihtml</ul></nav>";

//Collect data for chart
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
    for ($i = 0; $i <= 2; $i++) {
        $data[$i][] = !empty($chartdata[$label][$i+1]) ? $chartdata[$label][$i+1] : 0;
    }
}

//Create datasets, options and attributes for chart
$chartColors = array('rgba(252, 75, 75, 0.5)',
                     'rgba(255, 171, 46, 0.5)',
                     'rgba(151, 255, 48, 0.5)');
for ($i = 0; $i < 3; $i++) {
    $dataset[$i]['data'] = $data[$i];
    $dataset[$i]['backgroundColor'] = $chartColors[$i];
    $dataset[$i]['borderColor'] = $chartColors[$i];
    $dataset[$i]['label'] = $severityLabels[$i];
}
$options = array('scales' => array('yAxes' => array(array(
                 'stacked' => true, 'ticks' => array('min' => 0)))));
$attributes = array('id' => 'chart');

//Create chart
require 'lib/ChartJS.php';
$chart = new ChartJS('line', $labels, $options, $attributes);
for ($i = 0; $i < 3; $i++) {
    $chart->addDataset($dataset[$i]);
}
$html .= "<section id='container'>" . $chart;

//Prepare the severity boxes
$severityIds = array("high", "medium", "low");
for ($i = 0; $i < 3; $i++) {
    $severities[$i+1] = array('count' => 0,
                              'id' => $severityIds[$i],
                              'description' => $severityLabels[$i]);
}

//Update the count from the database result
foreach ($chartdata as $day) {
    foreach ($day as $key => $priority) {
        $severities[$key]['count'] += $priority;
    }
}

//Create the html for severity boxes
$html .= "<div id='severities'>";
foreach ($severities as $severity) {
    $html .= "<span class='outer_severity_box'>
              <span class='inner_severity_box' id='".$severity['id']."'>
              <div class='severity_number red'>".$severity['count']."</div>
              <div class='severity_caption'>".$severity['description']."</div>
              </span></span>";
}
$html .= "</div></section>";

//Get statistics
$htmlCE = "";
$commonEvents = $db->getCommonEvents($days);
foreach ($commonEvents as $event) {
    $htmlCE .= "<div class='comp_entry'>".$event['sig_name']." (".$event['amount'].")"."</div>";
}

$htmlFIP = "";
$frequentIP = $db->getFrequentIP($days);
foreach ($frequentIP as $ip) {
    $htmlFIP .= "<div class='comp_entry'>".$ip['ip_src']." (".$ip['amount'].")"."</div>";
}

$html .= "<aside id='compilation'>
          <div><h4>Most common events</h4>$htmlCE</div>
          <div><h4>Most frequent source IP</h4>$htmlFIP</div>
          </aside>
          <script src='lib/Chart.js'></script>
          <script src='lib/driver.js'></script>
          <script>(function() {loadChartJsPhp();})();</script>";

echo $html;
include 'src/footer.php';
