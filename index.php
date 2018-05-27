<?php include('header.php'); ?>
<?php
  $aufgabenlisteUrl = 'http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . '/aufgabenliste.php';

  header('Location: ' . $aufgabenlisteUrl);
  exit();
?>