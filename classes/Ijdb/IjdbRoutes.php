<?php

namespace Ijdb;

class IjdbRoutes implements \Ninja\Routes
{
    private $authorsTable;
    private $jokesTable;
    private $categoriesTable;
    private $jokeCategoriesTable;
    private $authentication;

    public function __construct()
    {
        include __DIR__ . '/../../includes/DatabaseConnection.php';

        $this->jokesTable = new \Ninja\DatabaseTable($pdo, 'joke', 'id', 'Ijdb\Entity\Joke', [&$this->authorsTable, &$this->jokeCategoriesTable]);
        $this->authorsTable = new \Ninja\DatabaseTable($pdo, 'author', 'id', 'Ijdb\Entity\Author', [&$this->jokesTable]);
        $this->categoriesTable = new \Ninja\DatabaseTable($pdo, 'category', 'id', 'Ijdb\Entity\Category', [&$this->jokesTable, &$this->jokeCategoriesTable]);
        $this->jokeCategoriesTable = new \Ninja\DatabaseTable($pdo, 'jokecategory', 'categoryid');
        $this->authentication = new \Ninja\Authentication($this->authorsTable, 'email', 'password');
    }

    public function getRoutes()
    {

        $jokeController = new \Ijdb\Controllers\Joke($this->jokesTable, $this->authorsTable, $this->categoriesTable, $this->authentication);
        $authorController = new \Ijdb\Controllers\Register($this->authorsTable);
        $categoryController = new \ijdb\Controllers\Category($this->categoriesTable);
        $loginController = new \Ijdb\Controllers\Login($this->authentication);

        $routes = [
            'author/register' => [
                'GET' => [
                    'controller' => $authorController,
                    'action' => 'registrationForm'
                ],
                'POST' => [
                    'controller' => $authorController,
                    'action' => 'registerUser'
                ]
            ],
            'author/success' => [
                'GET' => [
                    'controller' => $authorController,
                    'action' => 'success'
                ]
            ],
            'joke/edit' => [
                'POST' => [
                    'controller' => $jokeController,
                    'action' => 'saveEdit'
                ],
                'GET' => [
                    'controller' => $jokeController,
                    'action' => 'edit'
                ],
                'login' => true
            ],
            'joke/delete' => [
                'POST' => [
                    'controller' => $jokeController,
                    'action' => 'delete'
                ],
                'login' => true
            ],
            'joke/list' => [
                'GET' => [
                    'controller' => $jokeController,
                    'action' => 'listJokes'
                ]
            ],
            '' => [
                'GET' => [
                    'controller' => $jokeController,
                    'action' => 'home'
                ]
            ],
            'category/edit' => [
                'POST' => [
                    'controller' => $categoryController,
                    'action' => 'saveEdit'
                ],
                'GET' => [
                    'controller' => $categoryController,
                    'action' => 'edit'
                ],
                'login' => true,
                'permissions' => \Ijdb\Entity\Author::EDIT_CATEGORIES
            ],
            'category/list' => [
                'GET' => [
                    'controller' => $categoryController,
                    'action' => 'listCategories'
                ],
                'login' => true,
                'permissions' => \Ijdb\Entity\Author::LIST_CATEGORIES
            ],
            'category/delete' => [
                'POST' => [
                    'controller' => $categoryController,
                    'action' => 'delete'
                ],
                'login' => true,
                'permissions' => \Ijdb\Entity\Author::REMOVE_CATEGORIES
            ],
            'login/error' => [
                'GET' => [
                    'controller' => $loginController,
                    'action' => 'error'
                ]
            ],
            'login' => [
                'GET' => [
                    'controller' => $loginController,
                    'action' => 'loginForm'
                ],
                'POST' => [
                    'controller' => $loginController,
                    'action' => 'processLogin'
                ]
            ],
            'login/success' => [
                'GET' => [
                    'controller' => $loginController,
                    'action' => 'success'
                ],
                'login' => 'true'
            ],
            'logout' => [
                'GET' => [
                    'controller' => $loginController,
                    'action' => 'logout'
                ]
            ],
            'author/permissions' => [
                'GET' => [
                    'controller' => $authorController,
                    'action' => 'permissions'
                ],
                'POST' => [
                    'controller' => $authorController,
                    'action' => 'savePermissions'
                ],
                'login' => true,
                'permissions' => \Ijdb\Entity\Author::EDIT_USER_ACCESS
            ],
            'author/list' => [
                'GET' => [
                    'controller' => $authorController,
                    'action' => 'authorList'
                ],
                'login' => true,
                'permissions' => \Ijdb\Entity\Author::EDIT_USER_ACCESS
            ]
        ];

        return $routes;
    }

    public function getAuthentication()
    {
        return $this->authentication;
    }

    public function checkPermission($permission)
    {
        $user = $this->authentication->getUser();

        if ($user && $user->hasPermission($permission)) {
            return true;
        } else {
            return false;
        }
    }
}
