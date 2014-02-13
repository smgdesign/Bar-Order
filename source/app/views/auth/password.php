<?php
echo '<h1>'.$title.'</h1>';
?>
<hr class="greenline" />
<div class="password_blurb">
    <?php
    echo $content;
    ?>
</div>
<hr class="greenline" />
<div class="change_password">
    <?php
if (isset($errors)) {
    echo '<p style="color: #cc0202;">';
    foreach ($errors as $err) {
        if (is_array($err)) {
            foreach ($err as $sub) {
                echo $sub.'<br />';
            }
        } else {
            echo $err.'<br />';
        }
    }
    echo '</p>';
}
?>
    <form action="/auth/password" method="post">
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="nextURL" value="<?php echo $prevURL; ?>" />
        <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
        <p><label for="current-password">Current password:</label> <input id="current-password" type="password" name="current-password" /></p>
        <p><label for="new-password">New password:</label> <input id="new-password" type="password" name="new-password" /></p>
        <p><label for="conf-password">Confirm new password:</label> <input id="conf-password" type="password" name="conf-password" /></p>
        <input type="submit" name="change" value="Submit" id="change" />
    </form>
</div>