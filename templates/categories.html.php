<?php include __DIR__ . './../includes/helpers.inc.php'; ?>
<h2>Categories</h2>
<br><a href="index.php?route=category/edit">New Category</a><br>

<?php foreach ($categories as $category) : ?>
    <blockquote>
        <p>
            <?= html($category->name) ?>
            <a href="index.php?route=category/edit&id=<?= $category->id ?>">  Edit</a>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?= $category->id ?>">
                <input type="submit" name="submit" value="Delete">
            </form>
        </p>
    </blockquote>
<?php endforeach; ?>