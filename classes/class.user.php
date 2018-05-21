<?php

  class User {
    const USER_TABLE = 'ea3_user';
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const USERNAME = 'username';
    const USERID = 'userId';
    const PASSWORD = 'password';
    const CONFIRM = 'confirm';
    const IS_LOGGED_IN = 'isLoggedIn';
    const REGISTER = 'register';
    const SESSION_ID = 'sessionId';

    private $database;
    private $username;
    private $userId;
    private $isLoggedIn;

    public function __construct($database) {
      session_start();

      $this->database = $database;

      $isCookieSet = isset($_COOKIE[self::USERNAME]);
      $hasUsername = !empty($_SESSION[self::USERNAME]);
      $isLoggedIn = isset($_SESSION[self::IS_LOGGED_IN]);

      $loginUser = isset($_POST[self::LOGIN]);
      $logoutUser = isset($_GET[self::LOGOUT]);
      $registerUser = isset($_POST[self::REGISTER]);

      $databaseIsEmpty = $this->emptyDatabase();

      if ($loginUser) {
        $this->login();
      
      } elseif ($logoutUser) {
        $this->logout();
      
      } elseif ($registerUser) {
        $this->register();

      } elseif ($isCookieSet || ($hasUsername && $isLoggedIn))  {
        $this->isLoggedIn = true;
        $this->username = $_SESSION[self::USERNAME];

      } elseif ($databaseIsEmpty && $registerUser) {
        $this->register();
      };

      return $this;
    }

    public function getUsername() {
      return $this->username;
    }
    public function getUserId() {
      return $this->userId;
    }

    public function isLoggedIn() {
      if ($this->isLoggedIn === NULL) {
        return false;
      } else {
        return $this->isLoggedIn;
      }
    }

    public function login() {
      $usernameGiven = !empty($_POST[self::USERNAME]);
      $passwordGiven = !empty($_POST[self::PASSWORD]);

      if ($usernameGiven && $passwordGiven) {
        $this->username = $this->database->real_escape_string($_POST[self::USERNAME]);
        $this->password = sha1($this->database->real_escape_string($_POST[self::PASSWORD]));

        if ($row = $this->verifyPassword()) {
          $this->userId = $this->getUserIdFromDatabase();

          session_regenerate_id(true);
          $_SESSION[self::SESSION_ID] = session_id();
          $_SESSION[self::USERNAME] = $this->username;
          $_SESSION[self::USERID] = $this->userId;
          $_SESSION[self::IS_LOGGED_IN] = true;
          $this->isLoggedIn = true;

          // avoid resending form on refresh
          header('Location: ' . $_SERVER['REQUEST_URI']);
          exit();

        };
      };
    }

    private function getUserIdFromDatabase() {
      $query  = 'SELECT ' . self::USERID . ' FROM ' . self::USER_TABLE
          . ' WHERE ' . self::USERNAME . ' = "' . $this->username . '"'
          . ' AND ' . self::PASSWORD . ' = "' . $this->password . '"';

      return ($this->database->query($query)->fetch_object()->userId);
    }

    private function verifyPassword() {
      $query  = 'SELECT * FROM ' . self::USER_TABLE
          . ' WHERE ' . self::USERNAME . ' = "' . $this->username . '"'
          . ' AND ' . self::PASSWORD . ' = "' . $this->password . '"';

      return ($this->database->query($query)->fetch_object());
    }

    public function logout() {
      session_unset();
      session_destroy();

      $this->isLoggedIn = false;
      setcookie(self::USERNAME, '', time()-3600);
      
      header('Location: index.php');
      exit();
    }

    public function register() {
      $usernameGiven = !empty($_POST[self::USERNAME]);
      $passwordGiven = !empty($_POST[self::PASSWORD]);
      $confirmationGiven = !empty($_POST[self::PASSWORD]);

      if ($usernameGiven && $passwordGiven && $confirmationGiven) {
        if ($_POST[self::PASSWORD] === $_POST[self::PASSWORD]) {
          $initialUser = $this->emptyDatabase();
          $username = $this->database->real_escape_string($_POST[self::USERNAME]);
          $password = sha1($this->database->real_escape_string($_POST[self::PASSWORD]));
          $query  = 'INSERT INTO ' . self::USER_TABLE . ' (' . self::USERNAME . ', ' . self::PASSWORD . ') '
              . 'VALUES ("' . $username . '", "' . $password . '")';

          if ($this->database->query($query)) {
            if ($initialUser) {
              session_regenerate_id(true);
              $_SESSION[self::SESSION_ID] = session_id();
              $_SESSION[self::USERNAME] = $username;
              $_SESSION[self::USERID] = $userId;
              $_SESSION[self::IS_LOGGED_IN] = true;
              $this->isLoggedIn = true;
            }

            // avoid resending form on refresh
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
          };
        };
      }
    }

    public function emptyDatabase() {
      $query = 'SELECT * FROM ' . self::USER_TABLE;
      $result = $this->database->query($query);

      return ($result->num_rows === 0);
    }

  }

?>