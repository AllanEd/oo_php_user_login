<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ISP - Gerüst der Einsendeaufgabe 3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="./css/main.css" />
</head>
<body>

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
  
  $hasErrors = $user->hasErrors();

  if ($hasErrors) {
    echo $user->getErrorsAsElement();
  }

  if ($userIsNotLoggedIn) {
    if (isset($_GET['registrieren'])) {
      include('registrieren.php');
    } else {
      include('login.php');
    }
    exit();
  }
?>