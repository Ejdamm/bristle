<?php 
include 'src/header.php';

$db = new DB_connect;
$db->connect();	
$events = $db->fetchAll("event");
$signatures = $db->fetchAll("signature");
$severities = array(
	1 => 0,
	2 => 0,
	3 => 0
);

foreach ($events as $event) {
	$sig_id = $event['signature'] - 1;
	$sig_priority = $signatures[$sig_id]['sig_priority'];
	$severities[$sig_priority]++;
}

$html = "
<section id='severities'>
  <span class='severity_box' id='high'>
    <div class='severity_number red'>$severities[1]</div>
    <div class='severity_caption'>High severity</div>
  </span>
  <span class='severity_box' id='medium'>
    <div class='severity_number yellow'>$severities[2]</div>
    <div class='severity_caption'>Medium severity</div>
  </span>
  <span class='severity_box' id='low'>
    <div class='severity_number green'>$severities[3]</div>
    <div class='severity_caption'>Low severity</div>
  </span>
</section>";

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
