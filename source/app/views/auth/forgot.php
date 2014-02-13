<script type="text/javascript">
    $(document).ready(function() {
        $("#submit").click(function(ev) {
            ev.preventDefault();
            var stage = $("#stage").val();
            if (parseInt(stage) === 1) {
                var data = {
                    'email': $("#email").val(),
                    'submitted': true,
                    'form-token': $("#form-token").val()
                };
            } else {
                var data = {
                    'user_id': $("#user_id").val(),
                    'answer': $("#secret-answer").val(),
                    'submitted': true,
                    'form-token': $("#form-token").val()
                }
            }
            $.ajax({
                'url': '/auth/forgot/'+stage,
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
                            $("#forgot-secret-question").html(i.question);
                            $(".forgot_hidden").show();
                            $("#errors").html('<p>We have found your secret question, answer it below to receive an automatically generated password to your registered email address.</p>');
                        } else {
                            $("#errors").html('<p style="color: #cc0202;">'+i.errors+'</p>');
                        }
                    } else {
                        if (i.success) {
                            $("#errors").html('<p>An email has been sent to your registered email address.</p>');
                        } else {
                            $("#errors").html('<p style="color: #cc0202;">'+i.errors+'</p>');
                        }
                    }
                }
            });
        });
    });
</script>
<form action="/auth/forgot" method="post">
    <input type="hidden" name="submitted" value="TRUE" />
    <input type="hidden" name="form-token" id="form-token" value="<?php echo $csrf; ?>" />
    <input type="hidden" name="stage" id="stage" value="1" />
    <input type="hidden" name="user_id" id="user_id" value="" />
    <h1>Forgotten your password?</h1>
    
    <hr class="greenline" />
    
    <p class="margin-top-30" style="font-size:18px"><strong>Please enter your email address below, then answer your secret question chosen when you registered. After submitting, you will then receive an email with a new temporary password.</strong></p>
    
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
        <p class="register-full-width"><label for="email">Email address:<span class="green">*</span></label> <input id="email" name="email" type="text" /> <span class="nb" style="width: 309px;"><span class="green">*</span> This is your Reckitt Benckiser email address.<br />&nbsp;&nbsp;Either @RB.com or @ReckittBenckiser.com.</span></p>
        <div class="forgot_hidden">
            <label for="secret-question">Your secret question:</label> <div id="forgot-secret-question"></div>
            <label for="secret-answer">Answer to your secret question:</label> <input id="secret-answer" name="secret-answer" type="text" />
        </div>
        
        <p class="register-submit"><input id="submit" type="submit" name="submit" value="Continue" /></p>
    </div>
    
    <hr class="greenline margin-top-30" />

</form>
