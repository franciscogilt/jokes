<?php include  $_SERVER['DOCUMENT_ROOT'] . 'jokes/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php htmlout($pagetitle); ?></title>
</head>

<body>
    <h1><?php htmlout($pagetitle); ?></h1>
    <form action="?<?php htmlout($action); ?>" method="post">
        <div>
            <label for="name">Name: <input type="text" name="name" id="name" value="<?php htmlout($name); ?>"></label>
        </div>
        <div>
            <input type="hidden" name="id" value="<?php htmlout($id); ?>">
            <input type="submit" value="<?php htmlout($button); ?>">
        </div>
    </form>
</body>

</html>