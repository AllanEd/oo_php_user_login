<?php
  //TODO: Check if User exists
  class User {
    const USER_TABLE = 'EA3_USER';
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const USERNAME = 'username';
    const USERID = 'id';
    const PASSWORD = 'password';
    const CONFIRMATION = 'confirmation';
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
        $this->handlePostLogin();      
      } elseif ($getLogout) {
        $this->logout();
      
      } elseif ($postRegister) {
        $this->handlePostRegister();
      }
    }

    private function handlePostLogin() {
      $isPostUsernameSet = !empty($_POST[self::USERNAME]);
      $isPostPasswordSet = !empty($_POST[self::PASSWORD]);

      if ($isPostUsernameSet && $isPostPasswordSet) {
        $postUsername = $_POST[self::USERNAME];
        $postPassword = $_POST[self::PASSWORD];

        $this->login($postUsername, $postPassword);
      }
    }

    private function handlePostRegister() {
      $isPostUsernameSet = !empty($_POST[self::USERNAME]);
      $isPostPasswordSet = !empty($_POST[self::PASSWORD]);
      $isPostConfirmationSet = !empty($_POST[self::CONFIRMATION]);

      $allPostDataGiven = $isPostUsernameSet && $isPostPasswordSet && $isPostConfirmationSet;

      if ($allPostDataGiven) {
        $postUsername = $_POST[self::USERNAME];
        $postPassword = $_POST[self::PASSWORD];
        $postConfirmation = $_POST[self::CONFIRMATION];

        $this->register($postUsername, $postPassword, $postConfirmation);
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

    public function login($username, $password) {
      $isUsernameSet = !empty($username);
      $isPasswordSet = !empty($password);

      if ($isUsernameSet && $isPasswordSet) {
        $this->username = $this->escapeString($username);
        $this->password = $this->createSha1Hash($password);

        if ($row = $this->verifyPassword()) {
          $this->userId = $this->getUserIdFromDatabase();

          $this->setUserLoggedIn($this->username, $this->userId);

          $this->redirectTo($_SERVER['REQUEST_URI']);
        };
      };
    }

    private function getUserIdFromDatabase() {
      $query  = '
        SELECT ' . self::USERID . ' FROM ' . self::USER_TABLE . ' 
        WHERE ' . self::USERNAME . ' = "' . $this->username . '"' . ' 
        AND ' . self::PASSWORD . ' = "' . $this->password . '"';

      return ($this->database->query($query)->fetch_object()->id);
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
      $this->removeLoggedInUser();
      
      $this->redirectTo('index.php');
    }

    public function register($username, $password, $confirmation) {
      if ($password === $confirmation) {
        $isCreateUserSuccessful = $this->createUser($username, $password);

        if ($isCreateUserSuccessful) {
          $this->login($username, $password);
          $this->redirectTo('aufgabenliste.php');
        };
      }
    }

    private function createUser($username, $password) {
      $username = $this->escapeString($username);
      $password = $this->createSha1Hash($password);
      $query  = '
        INSERT INTO ' . self::USER_TABLE . ' (' . self::USERNAME . ', ' . self::PASSWORD . ') ' . '
        VALUES ("' . $username . '", "' . $password . '")';

      $isInsertSuccesful = $this->database->query($query);

      return $isInsertSuccesful;
    }

    private function setUserLoggedIn($username, $userId) {
      session_regenerate_id(true);

      $this->setSessionValues($username, $userId);

      $this->isLoggedIn = true;
    }

    private function removeLoggedInUser() {
      session_unset();
      session_destroy();

      $this->isLoggedIn = false;
    }

    private function setSessionValues($username, $userId) {
      $_SESSION[self::SESSION_ID] = session_id();
      $_SESSION[self::USERNAME] = $username;
      $_SESSION[self::USERID] = $userId;
      $_SESSION[self::IS_LOGGED_IN] = true;
    }

    private function escapeString($string) {
      return $this->database->real_escape_string($string);
    }

    private function createSha1Hash($string) {
      return sha1($this->escapeString($string));
    }

    private function redirectTo($uri) {
      header('Location: ' . $uri);
      exit();
    }
  }

?>