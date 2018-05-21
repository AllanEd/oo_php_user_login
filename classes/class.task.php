<?php

  class Task {
    const MAX_INPUT_LENGTH = 500;

    private $id;
    private $owner;
    private $done;
    private $text;

    public function __construct($id, $owner, $done, $text) {
      $this->id = $id;
      $this->owner = $owner;
      $this->done = $done;
      $this->text = $this->validateInput($text);
    }

    //validates a given input
    private function validateInput($input) {
      $input = trim($input);
      $input = stripslashes($input);
      $input = htmlspecialchars($input);

      if (strlen($input) >= self::MAX_INPUT_LENGTH) {
        $input = substr($input, 0, self::MAX_INPUT_LENGTH);
      }
      
      return $input;
    }

    public function getId() {
      return $this->id;
    }

    public function getOwner() {
      return $this->owner;
    }

    public function getDone() {
      return $this->done;
    }

    public function getText() {
      return $this->text;
    }
  }

?>