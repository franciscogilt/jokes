<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/css/jokes.css">
    <title><?= $title ?></title>
</head>

<body>
    <header>
        <h1>Internet Joke Database</h1>
    </header>
    <nav>
        <ul>
            <li><a href="./index.php">Home</a></li>
            <li><a href="./index.php?route=joke/list">Jokes List</a></li>
            <li><a href="./index.php?route=joke/edit">Add New Joke</a></li>
            <?php if ($loggedIn) : ?>
                <li><a href="./index.php?route=logout">Logout</a></li>
            <?php else : ?>
                <li><a href="./index.php?route=login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
        <?= $output ?>
    </main>
    <footer>
        &copy; IJDB 2020
    </footer>
</body>

</html>