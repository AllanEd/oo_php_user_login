<?php
  //TODO: Check if User exists
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

      $this->handleHttpRequest();

      $this->logInUserIfStoredInSession();

      return $this;
    }

    private function handleHttpRequest() {
      $postLogin = isset($_POST[self::LOGIN]);
      $getLogout = isset($_GET[self::LOGOUT]);
      $postRegister = isset($_POST[self::REGISTER]);

      if ($postLogin) {
        $this->login();
      
      } elseif ($getLogout) {
        $this->logout();
      
      } elseif ($postRegister) {
        $this->register();
      }
    }

    private function logInUserIfStoredInSession() {
      $isUsernameInSession = !empty($_SESSION[self::USERNAME]);
      $isUserLoggedIn = isset($_SESSION[self::IS_LOGGED_IN]);

      if ($isUsernameInSession && $isUserLoggedIn)  {
        $this->isLoggedIn = true;
        $this->username = $_SESSION[self::USERNAME];
      }
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
      $postUsername = $_POST[self::USERNAME];
      $postPassword = $_POST[self::PASSWORD];
      $postUsernameGiven = !empty($postUsername);
      $postPasswordGiven = !empty($postPassword);

      if ($postUsernameGiven && $postPasswordGiven) {
        $this->username = $this->escapeString($postUsername);
        $this->password = $this->createSha1Hash($postPassword);

        if ($row = $this->verifyPassword()) {
          $this->userId = $this->getUserIdFromDatabase();

          $this->logInUser($this->username, $this->userId);

          $this->redirectToRequestUri();
        };
      };
    }

    private function getUserIdFromDatabase() {
      $query  = '
        SELECT ' . self::USERID . ' FROM ' . self::USER_TABLE . ' 
        WHERE ' . self::USERNAME . ' = "' . $this->username . '"' . ' 
        AND ' . self::PASSWORD . ' = "' . $this->password . '"';

      return ($this->database->query($query)->fetch_object()->userId);
    }

    private function verifyPassword() {
      $query  = '
        SELECT * 
        FROM ' . self::USER_TABLE . ' 
        WHERE ' . self::USERNAME . ' = "' . $this->username . '"' . ' 
        AND ' . self::PASSWORD . ' = "' . $this->password . '"';

      return ($this->database->query($query)->fetch_object());
    }

    public function logout() {
      session_unset();
      session_destroy();

      $this->isLoggedIn = false;
      
      header('Location: index.php');
      exit();
    }

    public function register() {
      $postUsername = $_POST[self::USERNAME];
      $postPassword = $_POST[self::PASSWORD];
      $postConfirm = $_POST[self::CONFIRM];

      $allPostDataGiven = !empty($postUsername) && !empty($postPassword) && !empty($postConfirm);

      $passwordEqualsConfirmation = $postPassword === $postConfirm;

      if ($allPostDataGiven && $passwordEqualsConfirmation) {
        $isInitialUser = $this->isDatabaseEmpty();

        $isCreateUserSuccessful = $this->createUser($postUsername, $postPassword);

        if ($isCreateUserSuccessful) {
          if ($isInitialUser) {
            $this->logInUser($username, $userId);
          }

          $this->redirectToRequestUri();
        };
      }
    }

    private function createUser($username, $password) {
      $username = $this->escapeString($username);
      $password = $this->createSha1Hash($password);
      $query  = '
        INSERT INTO ' . self::USER_TABLE . ' (' . self::USERNAME . ', ' . self::PASSWORD . ') ' . '
        VALUES ("' . $username . '", "' . $password . '")';

      return $this->database->query($query);
    }

    private function logInUser($username, $userId) {
      session_regenerate_id(true);
      $_SESSION[self::SESSION_ID] = session_id();
      $_SESSION[self::USERNAME] = $username;
      $_SESSION[self::USERID] = $userId;
      $_SESSION[self::IS_LOGGED_IN] = true;
      $this->isLoggedIn = true;
    }

    public function isDatabaseEmpty() {
      $query = 'SELECT * FROM ' . self::USER_TABLE;
      $result = $this->database->query($query);

      return ($result->num_rows === 0);
    }

    private function escapeString($string) {
      return $this->database->real_escape_string($string);
    }

    private function createSha1Hash($string) {
      return sha1($this->escapeString($string));
    }

    private function redirectToRequestUri() {
      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit();
    }
  }

?>