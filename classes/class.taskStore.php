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
      $this->database = $database;
      $this->userId = $userId;

      $taskText = isset($_POST[self::TEXT]) ? $_POST[self::TEXT] : NULL;
      $taskDeleteId = isset($_GET[self::DELETE]) ? (int) $_GET[self::DELETE] : NULL;
      $taskDone = isset($_GET[self::DONE]) ? (int) $_GET[self::DONE] : NULL;
      $taskDoneId = isset($_GET[self::TASK_ID]) ? (int) $_GET[self::TASK_ID] : NULL;
      
      if (isset($taskText)) {
        $this->addTask($taskText);

      } elseif (isset($taskDeleteId)) {
        $this->deleteTask($taskDeleteId);
      
      } elseif (isset($taskDone) && isset($taskDoneId)) {
        $this->updateTask($taskDone, $taskDoneId);
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
    
    private function createTask(int $id = null, int $owner, int $done, string $text): Task {
      return new Task($id, $owner, $done, $text);
    }

    private function addTask(string $text) {
      $newTask = $this->createTask(null, $this->userId, 0, $text);

      $query = '
        INSERT INTO ' . self::TASK_STORE_TABLE . ' (' . self::OWNER . ', ' . self::DONE . ', '  . self::TEXT . ') ' . ' 
        VALUES ("' . $newTask->getOwner() . '", "' . $newTask->getDone() . '", "' . $newTask->getText() . '")';

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
