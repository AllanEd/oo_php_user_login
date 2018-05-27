<?php include('header.php'); ?>
<?php
  $MAX_INPUT_LENGTH = 500;

  require_once('classes/class.taskStore.php');

  $taskStore = new TaskStore($database, $_SESSION['id']);
?>
  <header><span>Angemeldet als <?php echo $_SESSION['username'] ?> (<a href="?logout"><span>logout</span></a>)</span></header>
  <h2>Aufgabenliste</h2>
  <ul id="todolist">
    <?php
      $tasks = $taskStore->getTaskStore();

      for ($i = 0; $i < count($tasks); $i++) {
        $taskId = $tasks[$i]->getId();
        $taskDone = $tasks[$i]->getDone();
        $taskDoneCssClass = '';
        $taskText = htmlspecialchars_decode($tasks[$i]->getText());

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
