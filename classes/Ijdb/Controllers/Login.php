<?php

namespace Ijdb\Controllers;

class Login
{
    private $authentication;

    public function __construct($authentication)
    {
        $this->authentication = $authentication;
    }

    public function error()
    {
        return [
            'template' => 'loginerror.html.php',
            'title' => 'You are not logged in'
        ];
    }

    public function loginForm()
    {
        return [
            'template' => 'login.html.php',
            'title' => 'Log in'
        ];
    }

    public function processLogin()
    {
        if ($this->authentication->login($_POST['email'], $_POST['password'])) {
            header('location: index.php?route=login/success');
        } else {
            return [
                'template' => 'login.html.php',
                'title' => 'Log in',
                'variables' => [
                    'error' => 'Invalid username or password.'
                ]
            ];
        }
    }

    public function success()
    {
        return [
            'template' => 'loginsuccess.html.php',
            'title' => 'Login Successful'
        ];
    }

    public function logout()
    {
        $_SESSION = [];
        unset($_SESSION);
        return [
            'template' => 'logout.html.php',
            'title' => 'You have been logged out.'
        ];
    }
}
