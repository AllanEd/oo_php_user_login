<?php
  require_once('classes/class.task.php');

  class TaskStore {
    const TASKS_STORE = 'ea3_todo';
    const TASK_ID = 'taskId';
    const OWNER = 'owner';
    const DONE = 'done';
    const TEXT = 'text';

    private $database;
    private $userId;
    private $tasks = [];

    public function __construct($database, $userId) {
      $POST_TEXT = isset($_POST['text']) ? $_POST['text'] : NULL;

      $this->database = $database;
      $this->userId = $userId;

      /**
       * initial tasks
       */
      $this->tasks = $this->getTasksStore();

      /**
       * conditions
       */
      $isPostMethod = $_SERVER['REQUEST_METHOD'] === 'POST';
      $isGetMethod = $_SERVER['REQUEST_METHOD'] === 'GET';
      $isPostText = $isPostMethod && isset($POST_TEXT) && !empty($POST_TEXT);
      $isGetDelete = $isGetMethod && isset($GET_DELETE);
      $isGetDone = $isGetMethod && isset($GET_DONE);

      if ($isPostText) {
        $newTask = $this->createTask($POST_TEXT);

        $this->addTask($newTask);
      }
    }

    
    private function createTask($text) {
      return new Task(null, 0, $text);
    }

    private function addTask($task) {
      $query = 'INSERT INTO ' . self::TASKS_STORE .
        ' (' . self::TASK_ID . ', ' . self::OWNER . ', ' . self::DONE . ', '  . self::TEXT . ') ' .
        ' VALUES ("' . $task->getId() . '", "' . $this->userId . '", "' . $task->getDone() . '", "' . $task->getText() . '")';

      $result = $this->database->query($query);
    }

    private function getTasksStore() {
      $query = 'SELECT ' . self::OWNER . ', ' . self::DONE . ', ' . self::TEXT . 
        ' FROM ' . self::TASKS_STORE . 
        ' WHERE ' . self::OWNER . ' = "' . $this->userId . '"';

      $result = $this->database->query($query);

      return $result->fetch_object();
    }
  }

?>