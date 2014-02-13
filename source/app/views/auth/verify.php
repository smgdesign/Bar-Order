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
    <script type="text/javascript">
    $(document).ready(function() {
       $("input[name='deny']").click(function() {
           $.ajax({
               'url': '/auth/verify',
               'type': 'post',
               'data': {'submitted': true, 'id': $("input[name='id']").val(), 'deny': true},
               'success': function() {
                   $("form").html('<p>You have Denied access for this user. This action can be undone by visiting this page again at any time.</p>')
               }
           });
       });
    });
    </script>
    <form action="/auth/verify" method="post">
        <?php if (isset($user)) {
            ?>
        <p>Would you like to verify the user registered with email address <?php echo $user['email']; ?></p>
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>" />
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="form-token" value="<?php echo $csrf; ?>" />
        <input type="submit" name="verify" value="Verify" id="verify" />&nbsp;
        <input type="button" name="deny" value="Deny" />
        <?php
        } else {
        ?>
        <p>You have successfully verified this user.</p>
        <?php
        }
        ?>
        
    </form>
</div>