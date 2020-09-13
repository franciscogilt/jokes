<form action="" method="post">
    <input type="hidden" name="category[id]" value="<?= isset($category) ? $category->id : '' ?>">
    <label for="categoryname">Category name:</label>
    <input type="text" name="category[name]" value="<?= isset($category) ? $category->name : '' ?>">
    <input type="submit" name="submit" value="Save">
</form>