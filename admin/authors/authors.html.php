<?php include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Authors</title>
</head>

<body>
    <h1>Manage Authors</h1>
    <p><a href="?add">New Author</a></p>
    <ul>
        <?php foreach ($authors as $author) : ?>
            <li>
                <form action="" method="post">
                    <div>
                        <?php htmlout($author['name']) ?>
                        <input type="hidden" name="id" value="<?php htmlout($author['id']); ?>">
                        <input type="submit" name="action" value="Edit">
                        <input type="submit" name="action" value="Delete">
                    </div>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <p><a href="..">Home</a></p>
    <?php include './admin/logout.inc.html.php'?>
</body>

</html>