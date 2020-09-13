<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/access.inc.php';  
if (!userIsLoggedIn())  
{  
  include '../login.html.php';  
  exit();  
}  
if (!userHasRole('Content Editor'))  
{  
  $error = 'Only Account Administrators may access this page.';  
  include '../accessdenied.html.php';  
  exit();  
}  

if (isset($_GET['add'])) {
	$pagetitle = 'New Joke';
	$action = 'addform';
	$text = '';
	$authorid = '';
	$id = '';
	$button = 'Add joke';

	include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';

	// Build the list of authors
	$sql = "SELECT id, name FROM author";
	try {
		$result = $pdo->query($sql);
	} catch (PDOexception $e) {
		$error = 'Error fetching list of authors.' . $e->getMessage();
		include 'error.html.php';
		exit();
	}
	foreach ($result as $row) {
		$authors[] = [
			'id' => $row['id'],
			'name' => $row['name']
		];
	}

	// Build the list of categories
	$sql = "SELECT id, name FROM category";
	try {
		$result = $pdo->query($sql);
	} catch (PDOexception $e) {
		$error = 'Error fetching list of categories.' . $e->getMessage();
		include 'error.html.php';
		exit();
	}
	foreach ($result as $row) {
		$categories[] = [
			'id' => $row['id'],
			'name' => $row['name'],
			'selected' => FALSE
		];
	}

	include 'form.html.php';
	exit();
}

if (isset($_GET['addform'])) {
	include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';

	$text = $_POST['text'];
	$author = $_POST['author'];
	
	if ($author == '') {
		$error = 'You must choose an author for this joke.
				Click &lsquo;back&rsquo; and try again.';
		include 'error.html.php';
		exit();
	}

	if (!isset($_POST['categories'])) {
		$error = 'You must choose a category for this joke.
				Click &lsquo;back&rsquo; and try again.';
		include 'error.html.php';
		exit();
	}

	$sql = "INSERT INTO joke SET joketext=?, jokedate=CURDATE(), authorid=?";

	try {
		$pdo->prepare($sql)->execute([$text, $author]);
		$jokeid = $pdo->lastInsertId();
	} catch (PDOException $e) {
		$error = 'Error adding submitted joke.' . $e->getMessage();
		include 'error.html.php';
		exit();
	}

	foreach ($_POST['categories'] as $categoryid) {
		$sql = "INSERT INTO jokecategory SET jokeid=?, categoryid=?";
		try {
			$pdo->prepare($sql)->execute([$jokeid, $categoryid]);
		} catch (PDOException $e) {
			$error = 'Error inserting joke into selected category.';
			include 'error.html.php';
			exit();
		}
	}


	header('Location: .');
	exit();
}

if (isset($_POST['action']) and $_POST['action'] == 'Edit') {
	include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';

	$id = $_POST['id'];
	$sql = "SELECT id, joketext, authorid FROM joke WHERE id='$id'";
	try {
		$result = $pdo->query($sql);
		$row = $result->fetch();
	} catch (PDOException $e) {
		$error = 'Error fetching joke details.';
		include 'error.html.php';
		exit();
	}

	$pagetitle = 'Edit Joke';
	$action = 'editform';
	$text = $row['joketext'];
	$authorid = $row['authorid'];
	$id = $row['id'];
	$button = 'Update joke';

	// Build the list of authors
	$sql = "SELECT id, name FROM author";
	try {
		$result = $pdo->query($sql);
	} catch (PDOException $e) {
		$error = 'Error fetching list of authors.';
		include 'error.html.php';
		exit();
	}

	foreach ($result as $row) {
		$authors[] = ['id' => $row['id'], 'name' => $row['name']];
	}

	// Get list of categories containing this joke
	$sql = "SELECT categoryid FROM jokecategory WHERE jokeid = '$id'";
	try {
		$result = $pdo->query($sql);
	} catch (PDOException $e) {
		$error = 'Error fetching list of selected categories.';
		include 'error.html.php';
		exit();
	}

	foreach ($result as $row) {
		$selectedCategories[] = $row['categoryid'];
	}

	// Build the list of all categories
	$sql = "SELECT id, name FROM category";
	try {
		$result = $pdo->query($sql);
	} catch (PDOException $e) {
		$error = 'Error fetching list of categories.';
		include 'error.html.php';
		exit();
	}

	foreach ($result as $row) {
		$categories[] = [
			'id' => $row['id'],
			'name' => $row['name'],
			'selected' => in_array($row['id'], $selectedCategories)
		];
	}

	include 'form.html.php';
	exit();
}

if (isset($_GET['editform'])) {
	include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';

	$text = $_POST['text'];
	$author = $_POST['author'];
	$id = $_POST['id'];

	if ($author == '') {
		$error = 'You must choose an author for this joke.
				Click &lsquo;back&rsquo; and try again.';
		include 'error.html.php';
		exit();
	}

	$sql = "UPDATE joke SET joketext=?, authorid=? WHERE id=?";
	try {
		$pdo->prepare($sql)->execute([$text, $author, $id]);
	} catch (PDOException $e) {
		$error = 'Error updating submitted joke.' . $e->getMessage();
		include 'error.html.php';
		exit();
	}

	$sql = "DELETE FROM jokecategory WHERE jokeid = '$id'";
	try {
		$pdo->query($sql);
	} catch (PDOException $e) {
		$error = 'Error removing obsolete joke category entries.';
		include 'error.html.php';
		exit();
	}

	if (isset($_POST['categories'])) {
		foreach ($_POST['categories'] as $categoryid) {
			$sql = "INSERT INTO jokecategory SET jokeid=?, categoryid=?";
			try {
				$pdo->prepare($sql)->execute([$id, $categoryid]);
			} catch (PDOException $e) {
				$error = 'Error inserting joke into selected category.';
				include 'error.html.php';
				exit();
			}
		}
	}

	header('Location: .');
	exit();
}

if (isset($_POST['action']) and $_POST['action'] == 'Delete') {
	include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
	$id = $_POST['id'];

	// Delete category assignments for this joke
	$sql = "DELETE FROM jokecategory WHERE jokeid='$id'";
	try {
		$pdo->query($sql);
	} catch (PDOException $e) {
		$error = 'Error removing joke from categories.';
		include 'error.html.php';
		exit();
	}

	// Delete the joke
	$sql = "DELETE FROM joke WHERE id='$id'";
	try {
		$pdo->query($sql);
	} catch (PDOException $e) {
		$error = 'Error deleting joke.';
		include 'error.html.php';
		exit();
	}

	header('Location: .');
	exit();
}

if (isset($_GET['action']) and $_GET['action'] == 'search') {
	include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';

	// The basic SELECT statement
	$select = 'SELECT id, joketext';
	$from   = ' FROM joke';
	$where  = ' WHERE TRUE';

	$authorid =  $_GET['author'];
	if ($authorid != '') // An author is selected
	{
		$where .= " AND authorid='$authorid'";
	}

	$categoryid =  $_GET['category'];
	if ($categoryid != '') // A category is selected
	{
		$from  .= ' INNER JOIN jokecategory ON id = jokeid';
		$where .= " AND categoryid='$categoryid'";
	}

	$text = $_GET['text'];
	if ($text != '') // Some search text was specified
	{
		$where .= " AND joketext LIKE '%$text%'";
	}
	try {
		$result = $pdo->query($select . $from . $where);
	} catch (PDOException $e) {
		$error = 'Error fetching jokes.';
		include 'error.html.php';
		exit();
	}
	foreach ($result as $row) {
		$jokes[] = ['id' => $row['id'], 'text' => $row['joketext']];
	}

	include 'jokes.html.php';
	exit();
}

// Display search form
include $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/db.inc.php';
$sql = 'SELECT id, name FROM author';
try {
	$authors = $pdo->query($sql);
} catch (PDOException $e) {
	$error = 'Error fetching authors from database!';
	include 'error.html.php';
	exit();
}

$sql = 'SELECT id, name FROM category';
try {
	$categories = $pdo->query($sql);
} catch (PDOException $e) {
	$error = 'Error fetching categories from database!';
	include 'error.html.php';
	exit();
}

include 'searchform.html.php';
