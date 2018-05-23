<?php
  require_once('classes/class.task.php');

  class TaskStore {
    const TASKS_STORE = 'EA3_TODO';
    const TASK_ID = 'id';
    const OWNER = 'owner';
    const DELETE = 'delete';
    const DONE = 'done';
    const TEXT = 'text';

    private $database;
    private $userId;

    public function __construct($database, $userId) {
      $postText = isset($_POST[self::TEXT]) ? $_POST[self::TEXT] : NULL;
      $getDelete = isset($_GET[self::DELETE]) ? $_GET[self::DELETE] : NULL;
      $getDone = isset($_GET[self::DONE]) ? $_GET[self::DONE] : NULL;
      $getId = isset($_GET[self::TASK_ID]) ? $_GET[self::TASK_ID] : NULL;

      $this->database = $database;
      $this->userId = $userId;

      /**
       * conditions
       */
      $isPostMethod = $_SERVER['REQUEST_METHOD'] === 'POST';
      $isGetMethod = $_SERVER['REQUEST_METHOD'] === 'GET';
      $isPostText = $isPostMethod && isset($postText) && !empty($postText);
      $isGetDelete = $isGetMethod && isset($getDelete);
      $isGetDone = $isGetMethod && isset($getDone);
      $isGetId = $isGetMethod && isset($getId);

      if ($isPostText) {
        $newTask = $this->createTask(null, $this->userId, 0, $postText);

        $this->addTask($newTask);

      } elseif ($isGetDelete) {
        $this->deleteTask($getDelete);
      
      } elseif ($isGetDone && $isGetId) {
        $this->updateTask($getDone, $getId);
      }
    }

    public function getTaskStore() {
      $query = '
        SELECT ' . self::TASK_ID . ', ' . self::OWNER . ', ' . self::DONE . ', ' . self::TEXT . ' 
        FROM ' . self::TASKS_STORE . ' 
        WHERE ' . self::OWNER . ' = "' . $this->userId . '"';

      $result = $this->database->query($query);

      $tasks = [];

      while ($task = $result->fetch_object()) {
        $currentTask = $this->createTask($task->id, $task->owner, $task->done, $task->text);

        array_push($tasks, $currentTask);
      }

      return $tasks;
    }
    
    private function createTask($id, $owner, $done, $text) {
      return new Task($id, $owner, $done, $text);
    }

    private function addTask($task) {
      $query = '
        INSERT INTO ' . self::TASKS_STORE . ' (' . self::TASK_ID . ', ' . self::OWNER . ', ' . self::DONE . ', '  . self::TEXT . ') ' . ' 
        VALUES ("' . $task->getId() . '", "' . $task->getOwner() . '", "' . $task->getDone() . '", "' . $task->getText() . '")';

      $this->database->query($query);
    }

    private function deleteTask($taskId) {
      $query = '
        DELETE FROM ' . self::TASKS_STORE . ' 
        WHERE ' . self::TASK_ID . ' = ' . $taskId;

      $this->database->query($query);
    }

    private function updateTask($done, $taskId) {
      $query = '
        UPDATE ' . self::TASKS_STORE . ' 
        SET ' . self::DONE . ' = ' . $done . ' 
        WHERE ' . self::TASK_ID . ' = ' . $taskId;

      $this->database->query($query);
    }
  }

?>