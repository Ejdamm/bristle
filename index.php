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


$html .= "
<aside id='compilation'>
  <div>
    <h4>Top 5 most common events</h3>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
  </div>
  <div>
    <h4>Last 5 unique events</h3>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
  </div>
  <div>
    <h4>Protocol distribution</h3>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
    <div class='comp_entry'>1234</div>
  </div>

</aside>
";




echo $html;
include 'src/footer.php';
