<?php
  declare(strict_types=1);

  class Task {
    const MAX_INPUT_LENGTH = 500;

    private $id;
    private $owner;
    private $done;
    private $text;

    public function __construct(int $id = null, int $owner, int $done, string $text) {
      $this->id = $id;
      $this->owner = $owner;
      $this->done = $done;
      $this->text = $this->validateInput($text);
    }

    //validates a given input
    private function validateInput(string $input): string {
      $input = trim($input);
      $input = stripslashes($input);
      $input = htmlspecialchars($input);

      if (strlen($input) >= self::MAX_INPUT_LENGTH) {
        $input = substr($input, 0, self::MAX_INPUT_LENGTH);
      }
      
      return $input;
    }

    public function getId(): int {
      return $this->id;
    }

    public function getOwner(): int {
      return $this->owner;
    }

    public function getDone(): int {
      return $this->done;
    }

    public function getText(): string {
      return $this->text;
    }
  }
