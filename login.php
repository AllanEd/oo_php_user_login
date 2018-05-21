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
  <h2>Willkommen bei der EA3 Aufgaben-Web-App</h2>
  <div id="login">
    <p>
      Melden Sie sich mit ihren Login-Daten ein oder erstelllen Sie 
      <a href="./registrieren.php">hier</a> einen neuen Account.
    </p>
    <form id="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
      <input type="text" placeholder="Login" name="username">
      <input type="password" placeholder="Passwort" name="password">
      <input type="submit" value="anmelden" name="login">
    </form>
  </div>
</body>
</html>
