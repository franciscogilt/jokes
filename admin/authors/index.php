<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/access.inc.php';
if (!userIsLoggedIn()) {
    include '../login.html.php';
    exit();
}
if (!userHasRole('Account Administrator')) {
    $error = 'Only Account Administrators may access this page.';
    include '../accessdenied.html.php';
    exit();
}

//Display form for adding a new author
if (isset($_GET['add'])) {
    $pagetitle = 'New Author';
    $action = 'addform';
    $name = '';
    $email = '';
    $id = '';
    $button = 'Add author';

    // Build the list of roles  
    $sql = "SELECT id, description FROM role";
    try {
        $result = $pdo->query($sql);
    } catch (PDOException $e) {
        $error = 'Error fetching list of roles.';
        include 'error.html.php';
        exit();
    }
    foreach ($result as $row) {
        $roles[] = array(
            'id' => $row['id'],
            'description' => $row['description'],
            'selected' => FALSE
        );
    }

    include 'form.html.php';
    exit();
}

//Adding the new author
if (isset($_GET['addform'])) {
    include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "INSERT INTO author (`name`, `email`) VALUES (?, ?)";
    try {
        $pdo->prepare($sql)->execute([$name, $email]);
        $authorid = $pdo->lastInsertId();
    } catch (PDOException $e) {
        $error = 'Error adding submitted author.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
    if ($_POST['password'] != '') {
        $password = md5($_POST['password'] . 'ijdb');
        $sql = "UPDATE author SET  
        password = ?  
        WHERE id = ?";
        $result = $pdo->prepare($sql);
        try {
            $result->execute([$password, $authorid]);
        } catch (PDOException $e) {
            $error = 'Error setting author password.';
            include 'error.html.php';
            exit();
        }
    }
    if (isset($_POST['roles'])) {
        foreach ($_POST['roles'] as $roleid) {
            $sql = "INSERT INTO authorrole SET  
          authorid='$authorid',  
          roleid='$roleid'";
            $result = $pdo->prepare($sql);
            try {
                $result->execute([$authorid, $roleid]);
            } catch (PDOException $e) {
                $error = 'Error assigning selected role to author.';
                include 'error.html.php';
                exit();
            }
        }
    }
    header('Location: .');
    exit();
}

//Selecting the author to edit
if (isset($_POST['action']) and $_POST['action'] == 'Edit') {
    include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $id = $_POST['id'];
    $sql = "SELECT id, name, email FROM author WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Error fetching author details.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
    $pagetitle = 'Edit Author';
    $action = 'editform';
    $name = $result['name'];
    $email = $result['email'];
    $id = $result['id'];
    $button = 'Update author';
    // Get list of roles assigned to this author  
    $sql = "SELECT roleid FROM authorrole WHERE authorid=?";
    $result = $pdo->prepare($sql);
    try {
        $result->execute([$id]);
    } catch (PDOException $e) {
        $error = 'Error fetching list of assigned roles.';
        include 'error.html.php';
        exit();
    }
    $selectedRoles = array();
    foreach ($result as $row) {
        $selectedRoles[] = $row['roleid'];
    }
    // Build the list of all roles  
    $sql = "SELECT id, description FROM role";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        $error = 'Error fetching list of roles.';
        include 'error.html.php';
        exit();
    }
    while ($row = mysqli_fetch_array($result)) {
        $roles[] = array(
            'id' => $row['id'],
            'description' => $row['description'],
            'selected' => in_array($row['id'], $selectedRoles)
        );
    }

    include 'form.html.php';
    exit();
}

//Updating author
if (isset($_GET['editform'])) {
    include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "UPDATE author SET name = ?, email= ? WHERE id= ?";
    try {
        $pdo->prepare($sql)->execute([$name, $email, $id]);
    } catch (PDOException $e) {
        $error = 'Error updating submitted author.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
    if ($_POST['password'] != '') {
        $password = md5($_POST['password'] . 'ijdb');
        $password = mysqli_real_escape_string($link, $password);
        $sql = "UPDATE author SET  
        password = '$password'  
        WHERE id = '$id'";
        if (!mysqli_query($link, $sql)) {
            $error = 'Error setting author password.';
            include 'error.html.php';
            exit();
        }
    }
    $sql = "DELETE FROM authorrole WHERE authorid='$id'";
    if (!mysqli_query($link, $sql)) {
        $error = 'Error removing obsolete author role entries.';
        include 'error.html.php';
        exit();
    }
    if (isset($_POST['roles'])) {
        foreach ($_POST['roles'] as $role) {
            $roleid = mysqli_real_escape_string($link, $role);
            $sql = "INSERT INTO authorrole SET  
          authorid='$id',  
          roleid='$roleid'";
            if (!mysqli_query($link, $sql)) {
                $error = 'Error assigning selected role to author.';
                include 'error.html.php';
                exit();
            }
        }
    }
    header('Location: .');
    exit();
}

//Delete Author
if (isset($_POST['action']) && $_POST['action'] == 'Delete') {
    //Get Jokes of author
    include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
    $id = $_POST['id'];

    // Delete role assignments for this author  
    $sql = "DELETE FROM authorrole WHERE authorid='$id'";
    if (!mysqli_query($link, $sql)) {
        $error = 'Error removing author from roles.';
        include 'error.html.php';
        exit();
    }

    //Get jokes of the author
    $sql = "SELECT id FROM joke WHERE authorid = ?";
    try {
        $result = $pdo->prepare($sql)->execute([$id]);
    } catch (PDOException $e) {
        $error = 'Error getting list of jokes to delete.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }

    foreach ($result as $joke) {
        $jokeid = $joke[0];
        //Delete category for each joke
        $sql = "DELETE FROM jokecategory WHERE jokeid=?";
        try {
            $pdo->prepare($sql)->execute([$jokeid]);
        } catch (PDOException $e) {
            $error = 'Error deleting category entries for joke.' . $e->getMessage();
            include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
            exit();
        }
    }

    //Delete jokes for author
    $sql = "DELETE FROM joke WHERE authorid=?";
    try {
        $pdo->prepare($sql)->execute([$id]);
    } catch (PDOException $e) {
        $error = 'Error deleting jokes for author.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }

    //Delete author
    $sql = "DELETE FROM author WHERE id=?";
    try {
        $pdo->prepare($sql)->execute([$id]);
    } catch (PDOException $e) {
        $error = 'Error deleting author.' . $e->getMessage();
        include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
        exit();
    }
    header('location: .');
    exit();
}

//Display list of authors
include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
$sql = 'SELECT id, `name` FROM author';
try {
    $authors = $pdo->query($sql);
    $title = 'Author List';
    include 'authors.html.php';
} catch (PDOException $e) {
    $error = 'Error displaying list.' . $e->getMessage();
    include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/templates/error.html.php';
    exit();
}
