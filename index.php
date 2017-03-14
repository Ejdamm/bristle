<?php 
include 'src/conf.php';
include 'src/header.php';

include 'src/db.php';
$db = new DB_connect;
$db->connect();
echo $db->getLastId("signature");
	





include 'src/footer.php';
