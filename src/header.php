<?php
include 'src/conf.php';
include 'src/db.php';
include 'src/functions.php';
include 'src/common.php';

?>
<!DOCTYPE html>
<html>
  <head>
    <title>bristle</title>
    <link rel="stylesheet" href="<?php echo $PATH;?>style/common.css">	
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oxygen"> 
  </head>
  <body>
    <header class="topheader">
      <span class="logo">BRISTLE</span>

      <nav class="navbar">
        <ul class="navbar-list">
          <li><a href="index.php">Dashboard</a></li>
          <li><a href="events.php">Events</a></li>
        </ul>
      </nav>
    </header>
    <main>
