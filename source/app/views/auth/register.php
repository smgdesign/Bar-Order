<?php

if (isset($locationID)) {
?>
<div id="login_box">
    <form action="/auth/register" method="post">
        <input type="text" name="username" id="username" placeholder="Username" />
        <br />
        <input type="password" name="password" id="password" placeholder="Password" />
        <br />
        <input type="password" name="conf-password" id="confpassword" placeholder="Confirm Password" />
        <br />
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="location_id" value="<?php echo $locationID; ?>" />
        <input type="submit" name="register" value="Register" id="login_btn" />
    </form>
</div>
<?php
}
?>