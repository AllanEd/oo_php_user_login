<?php

  require_once('config/database.php');
  require_once('classes/class.user.php');

  $database = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
  
  if ($database->connect_errno) {
    echo 'Database connection problem: ' . $database->connect_errno;
    exit();
  }
  
  $user = new User($database);

  $userIsNotLoggedIn = !$user->isLoggedIn();

  if ($userIsNotLoggedIn) {
    if ($user->isDatabaseEmpty()) {
      include('registrieren.php');
    } elseif (isset($_GET['registrieren'])) {
      include('registrieren.php');
    } else {
      include('login.php');
    }
    exit();
  }
?>