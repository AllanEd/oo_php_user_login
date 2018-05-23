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
  <h2>Registrieren</h2>
  <div id="register">
  <p>Registieren Sie sich, um Zugriff auf Ihre persönliche Aufgabenliste zu bekommen:</p>
  <p>Hinweise zu Eingabe-Mustern</p>
  <form id="register-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <input type="text" placeholder="Benutzername" name="username">
    <input type="password" placeholder="Passwort" name="password">
    <input type="password" placeholder="Passwort wiederholen" name="confirmation">
    <input type="submit" value="registieren" name="register">
  </form>
</div>
</body>
</html>
