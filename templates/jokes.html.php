<?php include __DIR__ . './../includes/helpers.inc.php' ?>

<div class="jokelist">
    <ul class="categories">
        <?php foreach ($categories as $category) : ?>
            <li><a href="index.php?route=joke/list&category=<?= $category->id ?>"><?= $category->name ?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="jokes">
        <p><?= $totalJokes ?> jokes have been submitted to the Internet Joke Database.</p>
        <br>

        <table width="600px">
            <thead>
                <th>Joke</th>
                <th>Author</th>
                <th>Date</th>
                <?php if ($user) : ?>
                    <th>Edit</th>
                    <th>Delete</th>
                <?php endif; ?>
            </thead>
            <tbody>
                <?php foreach ($jokes as $joke) : ?>
                    <tr>
                        <td width="200px">
                            <?php
                            $markdown = new \Ninja\Markdown($joke->joketext);
                            echo $markdown->toHtml();
                            ?>
                        </td>
                        <td>
                            <a href="mailto:<?php htmlout($joke->getAuthor()->email); ?>"><?php htmlout($joke->getAuthor()->name); ?></a>
                        </td>
                        <td>
                            <?php
                            $date = new DateTime($joke->jokedate);
                            echo $date->format('jS F Y');
                            ?>
                        </td>
                        <?php if ($user) : ?>
                            <?php if (
                                $user->id == $joke->authorid ||
                                $user->hasPermission(\Ijdb\Entity\Author::EDIT_JOKES)
                            ) : ?>
                                <td>
                                    <a href="index.php?route=joke/edit&id=<?= $joke->id ?>">Edit</a>
                                </td>
                            <?php endif; ?>
                            <?php if (
                                $user->id == $joke->authorid ||
                                $user->hasPermission(\Ijdb\Entity\Author::DELETE_JOKES)
                            ) : ?>
                                <td>
                                    <form action="index.php?route=joke/delete" method="post">
                                        <input type="hidden" name="id" value="<?= $joke->id ?>">
                                        <input type="submit" value="Delete">
                                    </form>
                                </td>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        Select Page:
        <?php
        $numPages = ceil($totalJokes / 10);
        for ($i = 1; $i <= $numPages; $i++) :
            if ($i == $currentPage) :
        ?>
                <a class="currentpage" href="index.php?route=joke/list&page=<?= $i ?><?= !empty($categoryId) ? '&category=' . $categoryId : '' ?>"><?= $i ?></a>
            <?php else : ?>
                <a href="index.php?route=joke/list&page=<?= $i ?><?= !empty($categoryId) ? '&category=' . $categoryId : '' ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

    </div>
</div>