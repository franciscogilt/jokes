<?php
function userIsLoggedIn()
{
  if (isset($_POST['action']) and $_POST['action'] == 'login') {
    if (
      !isset($_POST['email']) or $_POST['email'] == '' or
      !isset($_POST['password']) or $_POST['password'] == ''
    ) {
      $GLOBALS['loginError'] = 'Please fill in both fields';
      return FALSE;
    }
    $password = md5($_POST['password'] . 'ijdb');
    $email = $_POST['email'];
    if (databaseContainsAuthor($email, $password)) {
      session_start();
      $_SESSION['loggedIn'] = TRUE;
      $_SESSION['email'] = $email;
      $_SESSION['password'] = $password;
      return TRUE;
    } else {
      session_start();
      unset($_SESSION['loggedIn']);
      unset($_SESSION['email']);
      unset($_SESSION['password']);
      $GLOBALS['loginError'] =
        'The specified email address or password was incorrect.' . $email . ' ' . $password;
      return FALSE;
    }
  }
  if (isset($_POST['action']) and $_POST['action'] == 'logout') {
    session_start();
    unset($_SESSION['loggedIn']);
    unset($_SESSION['email']);
    unset($_SESSION['password']);
    header('Location: ' . $_POST['goto']);
    exit();
  }
  session_start();
  if (isset($_SESSION['loggedIn'])) {
    return databaseContainsAuthor(
      $_SESSION['email'],
      $_SESSION['password']
    );
  } else {
    return FALSE;
  }
}
function databaseContainsAuthor($email, $password)
{
  include 'db.inc.php';
  $sql = "SELECT COUNT(*) FROM author WHERE email = ? AND password = ?";
  $result = $pdo->prepare($sql);
  try {
    $result->execute([$email, $password]);
  } catch (PDOException $e) {
    $error = 'Error searching for author.' . $e->getMessage();
    include 'error.html.php';
    exit();
  }
  $row = $result->fetch();
  if ($row[0] > 0) {
    return TRUE;
  } else {
    return FALSE;
  }
}
function userHasRole($role)
{
  include 'db.inc.php';
  $email = $_SESSION['email'];
  $sql = "SELECT COUNT(*) FROM author  
      INNER JOIN authorrole ON author.id = authorid  
      INNER JOIN role ON roleid = role.id  
      WHERE email = ? AND role.id=?";
  $result = $pdo->prepare($sql);
  try {
    $result->execute([$email, $role]);
  } catch (PDOException $e) {
    $error = 'Error searching for author roles.';
    include 'error.html.php';
    exit();
  }
  $row = $result->fetch();
  if ($row[0] > 0) {
    return TRUE;
  } else {
    return FALSE;
  }
}
