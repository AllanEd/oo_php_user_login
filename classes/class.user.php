<?php
  declare(strict_types=1);

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

    public function __construct(mysqli $database) {
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
        $this->isLoggedIn = 1;
        $this->username = $_SESSION[self::USERNAME];
      }
    }

    public function getUsername(): string {
      return $this->username;
    }

    public function getUserId(): int {
      return $this->userId;
    }

    public function isLoggedIn(): int {
      if ($this->isLoggedIn === NULL) {
        return 0;
      } else {
        return $this->isLoggedIn;
      }
    }

    public function login(string $username, string $password) {
      $isUsernameSet = !empty($username);
      $isPasswordSet = !empty($password);

      if ($isUsernameSet && $isPasswordSet) {
        $this->username = $this->escapeString($username);
        $this->password = $this->createSha1Hash($password);

        if ($this->doesPasswordAndUserMatch()) {
          $this->userId = $this->getUserIdFromDatabase();

          $this->setUserLoggedIn($this->username, $this->userId);

          $this->redirectTo($_SERVER['REQUEST_URI']);
        };
      };
    }

    private function getUserIdFromDatabase(): int {
      $query  = '
        SELECT ' . self::USERID . ' FROM ' . self::USER_TABLE . ' 
        WHERE ' . self::USERNAME . ' = "' . $this->username . '"' . ' 
        AND ' . self::PASSWORD . ' = "' . $this->password . '"';

      $userId = (int) $this->database->query($query)->fetch_object()->id;

      return $userId;
    }

    private function doesPasswordAndUserMatch(): bool {
      $query  = '
        SELECT * 
        FROM ' . self::USER_TABLE . ' 
        WHERE ' . self::USERNAME . ' = "' . $this->username . '"' . ' 
        AND ' . self::PASSWORD . ' = "' . $this->password . '"';

      $doesPasswordAndUserMatch =
        $this->database->query($query)->num_rows > 0;

      return $doesPasswordAndUserMatch;
    }

    public function logout() {
      $this->removeLoggedInUser();
      
      $this->redirectTo('index.php');
    }

    public function register(string $username, string $password, string $confirmation) {
      if ($password === $confirmation) {
        $isCreateUserSuccessful = $this->createUser($username, $password);

        if ($isCreateUserSuccessful) {
          $this->login($username, $password);
          $this->redirectTo('aufgabenliste.php');
        };
      }
    }

    private function createUser(string $username, string $password): bool {
      $username = $this->escapeString($username);
      $password = $this->createSha1Hash($password);
      $query  = '
        INSERT INTO ' . self::USER_TABLE . ' (' . self::USERNAME . ', ' . self::PASSWORD . ') ' . '
        VALUES ("' . $username . '", "' . $password . '")';

      $isInsertSuccesful = $this->database->query($query);

      return $isInsertSuccesful;
    }

    private function setUserLoggedIn(string $username, int $userId) {
      session_regenerate_id(true);

      $this->setSessionValues($username, $userId);

      $this->isLoggedIn = 1;
    }

    private function removeLoggedInUser() {
      session_unset();
      session_destroy();

      $this->isLoggedIn = 0;
    }

    private function setSessionValues(string $username, int $userId) {
      $_SESSION[self::SESSION_ID] = session_id();
      $_SESSION[self::USERNAME] = $username;
      $_SESSION[self::USERID] = $userId;
      $_SESSION[self::IS_LOGGED_IN] = true;
    }

    private function escapeString(string $string): string {
      return $this->database->real_escape_string($string);
    }

    private function createSha1Hash(string $string): string {
      return sha1($this->escapeString($string));
    }

    private function redirectTo(string $uri) {
      header('Location: ' . $uri);
      exit();
    }
  }
