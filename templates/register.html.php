<?php if (!empty($errors)) : ?>
    <div class="errors">
        <p>Your account could not be created, please check the following:</p>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form action="" method="post">
    <label for="email">Email:</label>
    <input type="text" name="author[email]" id="email" value="<?=isset($author) ? $author['email']: ''?>">
    <br><br>
    <label for="name">Name:</label>
    <input type="text" name="author[name]" id="name" value="<?=isset($author) ? $author['name']: ''?>">
    <br><br>
    <label for="password">Password:</label>
    <input type="password" name="author[password]" id="password" value="<?=isset($author) ? $author['password']: ''?>">
    <br><br>
    <input type="submit" name="submit" value="Register Account">
</form>