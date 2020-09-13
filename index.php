<?php

try {
    include __DIR__ . '/includes/autoload.php';

    $route = isset($_GET['route']) ? $_GET['route'] : '';
    // $route = ltrim(strtok($_SERVER['REQUEST_URI'], '?'), 'jokes/');

    $entryPoint = new \Ninja\EntryPoint($route, $_SERVER['REQUEST_METHOD'], new \Ijdb\IjdbRoutes());
    $entryPoint->run();
} catch (PDOException $e) {
    $title = 'An error has occurred';
    $output = 'Database error: ' . $e->getMessage() . ' in '
        . $e->getFile() . ':' . $e->getLine();
}
// include __DIR__ . '/templates/layout.html.php';
