<?php include('header.php'); ?>
<?php
  $MAX_INPUT_LENGTH = 500;

  require_once('classes/class.taskStore.php');

  $taskStore = new TaskStore($database, $_SESSION['userId']);
?>
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
  <header><span>Angemeldet als {{Login}} (<a href="./login.html"><span>logout</span></a>)</span></header>
  <h2>Aufgabenliste</h2>
  <ul id="todolist">
    <?php
      $tasks = $taskStore->getTasks();

      for ($i = 0; $i < count($tasks); $i++) {
        $taskId = $tasks[$i]->getId();
        $taskDone = $tasks[$i]->getDone();
        $taskDoneCssClass = '';
        $taskText = $tasks[$i]->getText();

        if ($taskDone == 1) {
          $taskDoneCssClass = 'checked';
          $taskDone = 0;
        } else {
          $taskDone = 1;
        }

        echo "<li>
                <a href='?done={$taskDone}&id={$taskId}'class='done {$taskDoneCssClass}'></a>
                <span>{$taskText}</span>
                <a href='?delete={$taskId}' class='delete'>löschen</a>
              </li>";
      }
    ?>
  </ul>
  <div class="spacer"></div>
  <form id="add-todo" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <?php
      echo "<input type='text' placeholder='Text für neue Aufgabe' name='text' maxlength='{$MAX_INPUT_LENGTH}'>"
    ?>
    <input type="submit" value="hinzufügen">
  </form>
</body>
</html>
