<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/access.inc.php';  
if (!userIsLoggedIn())  
{  
  include '../login.html.php';  
  exit();  
}  
if (!userHasRole('Site Administrator'))  
{  
  $error = 'Only Account Administrators may access this page.';  
  include '../accessdenied.html.php';  
  exit();  
} 

//Delete Category
if (isset($_POST['action']) && $_POST['action'] == 'Delete') {
    //Delete Jokes for Category
    $id = $_POST['id'];
    include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $sql = "DELETE FROM jokecategory WHERE categoryid=?";
    try {
        $pdo->prepare($sql)->execute([$id]);
    } catch (PDOException $e) {
        $error = 'Error deleting category entries for joke.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }

    //Delete category
    $sql = "DELETE FROM category WHERE id=?";
    try {
        $pdo->prepare($sql)->execute([$id]);
    } catch (PDOException $e) {
        $error = 'Error deleting jokes for category.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
}

//Display form for adding a new category
if (isset($_GET['add'])) {
    $pagetitle = 'New Category';
    $action = 'addform';
    $name = '';
    $id = '';
    $button = 'Add category';
    include 'form.html.php';
    exit();
}

//Adding the new category
if (isset($_GET['addform'])) {
    include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $name = $_POST['name'];
    $sql = "INSERT INTO category (name) VALUES (?)";
    try {
        $pdo->prepare($sql)->execute([$name]);
    } catch (PDOException $e) {
        $error = 'Error adding submitted category.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
    header('Location: .');
    exit();
}

//Selecting the category to edit
if (isset($_POST['action']) && $_POST['action'] == 'Edit') {
    include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $id = $_POST['id'];
    $sql = "SELECT id, `name` FROM category WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Error fetching category details.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
    $pagetitle = 'Edit category';
    $action = 'editform';
    $name = $result['name'];
    $id = $result['id'];
    $button = 'Update category';
    include 'form.html.php';
    exit();
}

//Updating category
if (isset($_GET['editform'])) {
    include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $id = $_POST['id'];
    $name = $_POST['name'];
    $sql = "UPDATE category SET `name` = ? WHERE id= ?";
    try {
        $pdo->prepare($sql)->execute([$name, $id]);
    } catch (PDOException $e) {
        $error = 'Error updating submitted category.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
    header('Location: .');
    exit();
}

//Display list of categorys
include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
try {
    $sql = 'SELECT id, `name` FROM category';
    $categories = $pdo->query($sql);
    $title = 'Category List';
    include 'categories.html.php';
} catch (PDOException $e) {
    $error = 'Error displaying list.' . $e->getMessage();
    include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
    exit();
}
