<?php
if (isset($error)) :
    echo '<div class="errors">' . $error . '</div>';
endif;
?>
<form method="post" action="">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    <br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <br><br>
    <input type="submit" name="login" value="Log in">
</form>
<p>
    Don't have an account?<br>
    Click <a href="index.php?route=author/register">here</a> to register an account
</p>