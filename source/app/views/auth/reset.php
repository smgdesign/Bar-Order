<script type="text/javascript">
    $(document).ready(function() {
        $("#submit").click(function(ev) {
            ev.preventDefault();
            var stage = $("#stage").val();
            if (parseInt(stage) === 1) {
                var data = {
                    'username': $("#username").val(),
                    'password': $("#temp-password").val(),
                    'submitted': true,
                    'form-token': $("#form-token").val()
                };
            } else {
                var data = {
                    'user_id': $("#user_id").val(),
                    'password': $("#password").val(),
                    'conf-password': $("#conf-password").val(),
                    'submitted': true,
                    'form-token': $("#form-token").val()
                }
            }
            $.ajax({
                'url': '/auth/reset/false/'+stage,
                'type': 'post',
                'data': data,
                'dataType': 'json',
                'success': function(i) {
                    $("#form-token").val(i.csrf);
                    if (parseInt(stage) === 1) {
                        if (i.success) {
                            stage = 2;
                            $("#stage").val(stage);
                            $("#user_id").val(i.user_id);
                            $(".forgot_hidden").show();
                            $("#errors").html('<p>We have found your temporary password. Please enter a new password below to continue.</p>');
                        } else {
                            $("#errors").html('<p style="color: #cc0202;">'+i.errors+'</p>');
                        }
                    } else {
                        if (i.success) {
                            window.location.href = '/';
                        } else {
                            $("#errors").html('<p style="color: #cc0202;">'+i.errors+'</p>');
                        }
                    }
                }
            });
        });
    });
</script>
<form action="/auth/reset" method="post">
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" id="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="stage" id="stage" value="1" />
    <input type="hidden" name="user_id" id="user_id" value="" />
    <h1>Reset your password</h1>
    
    <hr class="greenline" />
    
    <p class="margin-top-30" style="font-size:18px"><strong>Welcome to the Dettol Lysol learning resource.</strong></p>
    <p>[Reset password text here]</p>
    <?php
    if (isset($msg)) {
        echo '<p>'.$msg.'</p>';
    }
    ?>
    <hr class="greenline margin-top-30" />
  <div id="errors">
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
  </div>
    <div class="register">
        <p class="register-full-width"><label for="username">Username:<span class="green">*</span></label> <input id="username" name="username" type="text" /> <span class="nb" style="width: 309px;"><span class="green">*</span> Between 8-15 characters and/or numbers.</span></p>
        <p class="register-full-width"><label for="temp-password">Temporary Password:</label> <input id="temp-password" name="temp-password" type="password" /></p>
        <div class="forgot_hidden">
            <p><label for="password">New Password:</label> <input id="password" type="password" name="password" /></p>
            <p><label for="conf-password">Confirm Password:</label> <input id="conf-password" type="password" name="conf-password" /></p>
        </div>
        
        <p class="register-submit"><input id="submit" type="submit" name="submit" value="Continue" /></p>
    </div>
    
    <hr class="greenline margin-top-30" />

</form>
