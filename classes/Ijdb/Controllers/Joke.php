<?php

namespace Ijdb\Controllers;

use Ninja\DatabaseTable;
use Ninja\Authentication;

class Joke
{
    private $jokesTable;
    private $authorsTable;
    private $categoriesTable;
    private $authentication;

    public function __construct(DatabaseTable $jokesTable, DatabaseTable $authorsTable, DatabaseTable $categoriesTable, Authentication $authentication)
    {
        $this->jokesTable = $jokesTable;
        $this->authorsTable = $authorsTable;
        $this->categoriesTable = $categoriesTable;
        $this->authentication = $authentication;
    }

    public function listJokes()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($page-1)*10;

        if (isset($_GET['category'])) {
            $category = $this->categoriesTable->findById($_GET['category']);
            $jokes = $category->getJokes(10, $offset);
            $totalJokes = $category->getNumJokes();
        } else {
            $jokes = $this->jokesTable->findAll('jokedate DESC', 10, $offset);
            $totalJokes = $this->jokesTable->total();
        }

        $author = $this->authentication->getUser();
        $title = 'Joke List';

        return [
            'template' => 'jokes.html.php',
            'title' => $title,
            'variables' => [
                'totalJokes' => $totalJokes,
                'jokes' => $jokes,
                'user' => $author,
                'categories' => $this->categoriesTable->findAll(),
                'currentPage' => $page,
                'category' => isset($_GET['category']) ? $_GET['category'] : null
            ]
        ];
    }

    public function home()
    {
        $title = 'Internet Joke Database';
        return [
            'title' => $title,
            'template' => 'home.html.php'
        ];
    }

    public function delete()
    {
        $author = $this->authentication->getUser();
        $joke = $this->jokesTable->findById($_POST['id']);

        if ($joke->authorId != $author->id && !$author->hasPermission(\Ijdb\Entity\Author::DELETE_JOKES)) {
            return;
        }

        $this->jokesTable->delete($_POST['id']);
        header('location: index.php?route=joke/list');
    }

    public function saveEdit()
    {
        $author = $this->authentication->getUser();

        $joke = $_POST['joke'];
        $joke['jokedate'] = new \DateTime();

        $jokeEntity = $author->addJoke($joke);
        $jokeEntity->clearCategories();
        

        foreach ($_POST['category'] as $categoryid) {
            $jokeEntity->addCategory($categoryid);
        }

        header('location: index.php?route=joke/list');
    }

    public function edit()
    {
        $author = $this->authentication->getUser();
        $categories = $this->categoriesTable->findAll();

        if (isset($_GET['id'])) {
            $joke = $this->jokesTable->findById($_GET['id']);
        }

        $title = 'Edit joke';
        return [
            'template' => 'editjoke.html.php',
            'title' => $title,
            'variables' => [
                'joke' => isset($joke) ? $joke : null,
                'user' => $author,
                'categories' => $categories
            ]
        ];
    }
}
