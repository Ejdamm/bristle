<?php 
include 'src/header.php';

$html = "";
$db = new DB_connect;
$db->connect();	
$severityCount = $db->countSeverities();

//prepare the severites
$severities = array(
	1 => array('count' => 0, 'id' => "high", 'description' => "High severity"),
	2 => array('count' => 0, 'id' => "medium", 'description' => "Medium severity"),
	3 => array('count' => 0, 'id' => "low", 'description' => "Low severity")
);

//update the count from the database result
foreach ($severityCount as $sCount) {
	$severities[$sCount['sig_priority']]['count'] = $sCount['count'];
}

//create the html for severity boxes
$html .= "<section id='severities'>";
foreach ($severities as $severity) {
	$html .= " 
  <span class='severity_box' id='".$severity['id']."'>
    <div class='severity_number red'>".$severity['count']."</div>
    <div class='severity_caption'>".$severity['description']."</div>
  </span>";
}
$html .= "</section>";

//okejokej lite idéer: 5 mest vanliga klasser senaste 30 dagar, 5 mest vanliga IP-adresser source, 3 vanligaste protokoll

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
    <h4>Last unique events</h3>
    $htmlLE
  </div>

  <div>
    <h4>Most common events (last 30 days)</h3>
    $htmlCE
  </div>

  <div>
    <h4>Most frequent source IP (last 30 days)</h3>
    $htmlFIP
  </div>
</aside>
";




echo $html;
include 'src/footer.php';
