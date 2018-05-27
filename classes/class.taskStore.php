<?php
  declare(strict_types=1);
  
  include_once('classes/class.task.php');

  class TaskStore {
    const TASK_STORE_TABLE = 'EA3_TODO';
    const TASK_ID = 'id';
    const OWNER = 'owner';
    const DELETE = 'delete';
    const DONE = 'done';
    const TEXT = 'text';

    private $database;
    private $userId;

    public function __construct(mysqli $database, int $userId) {
      $postText = isset($_POST[self::TEXT]) ? $_POST[self::TEXT] : NULL;
      $getDelete = isset($_GET[self::DELETE]) ? (int) $_GET[self::DELETE] : NULL;
      $getDone = isset($_GET[self::DONE]) ? (int) $_GET[self::DONE] : NULL;
      $getId = isset($_GET[self::TASK_ID]) ? (int) $_GET[self::TASK_ID] : NULL;

      $this->database = $database;
      $this->userId = (int) $userId;

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

    public function getTaskStore(): array {
      $query = '
        SELECT ' . self::TASK_ID . ', ' . self::OWNER . ', ' . self::DONE . ', ' . self::TEXT . ' 
        FROM ' . self::TASK_STORE_TABLE . ' 
        WHERE ' . self::OWNER . ' = "' . $this->userId . '"';

      $result = $this->database->query($query);

      $tasks = [];

      while ($task = $result->fetch_object()) {
        $taskId = (int) $task->id;
        $taskOwner = (int) $task->owner;
        $taskDone = (int) $task->done;
        $taskText = $task->text;

        $currentTask = $this->createTask($taskId, $taskOwner, $taskDone, $taskText);

        array_push($tasks, $currentTask);
      }

      return $tasks;
    }
    
    private function createTask(?int $id, int $owner, int $done, string $text): Task {
      return new Task($id, $owner, $done, $text);
    }

    private function addTask(Task $task) {
      $query = '
        INSERT INTO ' . self::TASK_STORE_TABLE . ' (' . self::OWNER . ', ' . self::DONE . ', '  . self::TEXT . ') ' . ' 
        VALUES ("' . $task->getOwner() . '", "' . $task->getDone() . '", "' . $task->getText() . '")';

      $this->database->query($query);
    }

    private function deleteTask(int $taskId) {
      $query = '
        DELETE FROM ' . self::TASK_STORE_TABLE . ' 
        WHERE ' . self::TASK_ID . ' = ' . $taskId;

      $this->database->query($query);
    }

    private function updateTask(int $done, int $taskId) {
      $query = '
        UPDATE ' . self::TASK_STORE_TABLE . ' 
        SET ' . self::DONE . ' = ' . $done . ' 
        WHERE ' . self::TASK_ID . ' = ' . $taskId;

      $this->database->query($query);
    }
  }
