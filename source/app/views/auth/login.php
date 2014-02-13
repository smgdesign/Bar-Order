<?php


?>
<div id="login_box">
    <form action="/auth/login" method="post">
        <?php
        if (isset($msg)) {
            if ($msg == 'timeout') {
                echo '<p style="color: #cc0202;">Unfortunately you were inactive for more than 30 minutes so you have been logged out for security.</p>';
            } else if ($msg == 'permission') {
                echo '<p style="color: #cc0202;">You must be logged in to access this resource.</p>';
            }
        }
        if (isset($errors)) {
            echo '<p style="color:#cc0202;">';
            foreach ($errors as $err) {
                if (is_array($err)) {
                    foreach ($err as $item) {
                        echo $item.'<br />';
                    }
                } else {
                    echo $err.'<br />';
                }
            }
            echo '</p>';
        }
        ?>
        <input type="text" name="username" id="username" placeholder="Username" />
        <br />
        <input type="password" name="password" id="password" placeholder="Password" />
        <br />
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="submit" name="login" value="Login" id="login_btn" />
    </form>
</div>