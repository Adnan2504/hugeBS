<div class="container">

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="login-page-box">
        <div class="table-wrapper">

            <!-- login box on left side -->
            <div class="login-box">
                <h2>Login here</h2>
                <form id="loginForm" action="<?php echo Config::get('URL'); ?>login/login" method="post" onsubmit="return onSubmitForm(event)">
                    <input type="text" name="user_name" placeholder="Username or email" required />
                    <input type="password" name="user_password" placeholder="Password" required />
                    <label for="set_remember_me_cookie" class="remember-me-label">
                        <input type="checkbox" name="set_remember_me_cookie" class="remember-me-checkbox" />
                        Remember me for 2 weeks
                    </label>

                    <!-- Include reCAPTCHA JavaScript -->
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    <div class="g-recaptcha" id="captchaV"  data-sitekey="6LdEV7kqAAAAANEy6mVAH-fg_bGVxw1Jn0Vi7-db"></div>

                    <!-- Attach reCAPTCHA token -->
                    <input type="hidden" id="recaptchaToken" name="recaptchaToken" />

                    <!-- Handle redirects -->
                    <?php if (!empty($this->redirect)) { ?>
                        <input type="hidden" name="redirect" value="<?php echo $this->encodeHTML($this->redirect); ?>" />
                    <?php } ?>

                    <!-- Set CSRF token -->
                    <input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />

                    <input type="submit" class="login-submit-button" value="Log in"/>
                </form>
                <div class="link-forgot-my-password">
                    <a href="<?php echo Config::get('URL'); ?>login/requestPasswordReset">I forgot my password</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        function onSubmitForm(event) {
            event.preventDefault();

            const captchaResponse = grecaptcha.getResponse();
            const captchaWidget = document.getElementById('captchaV');
            if (captchaResponse.length === 0) {
            captchaWidget.style.border = '2px solid red';
            alert("Please complete the reCAPTCHA to proceed.");
            return false;
            } else {
                captchaWidget.style.border = '';
                document.getElementById('loginForm').submit();
            }
        }

</script>
