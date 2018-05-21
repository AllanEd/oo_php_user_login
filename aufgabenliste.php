<?php include('header.php'); ?>
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
  <header><span>Angemeldet als {{Login}} (<a href="?logout"><span>logout</span></a>)</span></header>
  <h2>Aufgabenliste</h2>
  <ul id="todolist">
    <li>
      <a href="#" class="done"></a>
      <span>Aufgabentext 1</span>
      <a href="#" class="delete">löschen</a>
    </li>
    <li>
      <a href="#" class="done checked"></a>
      <span>Aufgabentext 2</span>
      <a href="#" class="delete">löschen</a>
    </li>
  </ul>
  <div class="spacer"></div>
  <form id="add-todo">
    <input type="text" placeholder="Text für neue Aufgabe" name="text">
    <input type="submit" value="hinzufügen">
  </form>
</body>
</html>
