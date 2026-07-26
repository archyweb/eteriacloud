<?php
$mainRoot = isset($main_root) ? $main_root : dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$envPath = $mainRoot . '/private/_common/.env';
$turnstileSiteKey = getenv('CLOUDFLARE_TURNSTILE_SITEKEY');

if ($turnstileSiteKey === false) {
    $turnstileSiteKey = '';
}

if ($turnstileSiteKey === '' && is_readable($envPath)) {
    $envLines = file($envPath);

    if (is_array($envLines)) {
        foreach ($envLines as $envLine) {
            $envLine = trim($envLine);

            if ($envLine === '' || strpos($envLine, '#') === 0) {
                continue;
            }

            $separatorPosition = strpos($envLine, '=');
            if ($separatorPosition === false) {
                continue;
            }

            $key = trim(substr($envLine, 0, $separatorPosition));
            if ($key !== 'CLOUDFLARE_TURNSTILE_SITEKEY') {
                continue;
            }

            $turnstileSiteKey = trim(substr($envLine, $separatorPosition + 1), " \t\n\r\0\x0B\"'");
            break;
        }
    }
}

$loginMode = isset($login_mode) ? $login_mode : 'login';
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- ===============================================-->
        <!--    Document Title-->
        <!-- ===============================================-->
        <title>Archy Art | Login</title>

        <!-- ===============================================-->
        <!--    Favicons-->
        <!-- ===============================================-->
        <link rel="apple-touch-icon" sizes="180x180" href="/admin_template_child/img/favicons/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/admin_template_child/img/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/admin_template_child/img/favicons/favicon-16x16.png">
        <link rel="shortcut icon" type="image/x-icon" href="/admin_template_child/img/favicons/favicon.ico">
        <link rel="manifest" href="/admin_template_child/img/favicons/manifest.json">
        <meta name="msapplication-TileImage" content="/admin_template_child/img/favicons/ms-icon-150x150.png">
        <meta name="theme-color" content="#ffffff">
        <script src="/admin_template_child/js/config.js"></script>
        <script src="/admin_template/vendors/simplebar/simplebar.min.js"></script>
        
        <!-- ===============================================-->
        <!--    Stylesheets-->
        <!-- ===============================================-->
        <link href="/admin_template/vendors/prism/prism-okaidia.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
        <link href="/admin_template/vendors/simplebar/simplebar.min.css" rel="stylesheet">
        <link href="/admin_template/assets/css/theme-rtl.css" rel="stylesheet" id="style-rtl">
        <link href="/admin_template/assets/css/theme.css" rel="stylesheet" id="style-default">
        <link href="/admin_template/assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
        <link href="/admin_template/assets/css/user.css" rel="stylesheet" id="user-style-default">
        <link href="/admin_template_child/css/custom.css" rel="stylesheet" id="user-style-default">
        <link rel="preconnect" href="https://challenges.cloudflare.com" />
        
        <script>
            var isRTL = JSON.parse(localStorage.getItem('isRTL'));
            if (isRTL) {
              var linkDefault = document.getElementById('style-default');
              var userLinkDefault = document.getElementById('user-style-default');
              linkDefault.setAttribute('disabled', true);
              userLinkDefault.setAttribute('disabled', true);
              document.querySelector('html').setAttribute('dir', 'rtl');
            } else {
              var linkRTL = document.getElementById('style-rtl');
              var userLinkRTL = document.getElementById('user-style-rtl');
              linkRTL.setAttribute('disabled', true);
              userLinkRTL.setAttribute('disabled', true);
            }
        </script>
        <script
            src="https://challenges.cloudflare.com/turnstile/v0/api.js"
            async
            defer
        ></script>
    </head>
    <body> 
        
        <!-- ===============================================-->
        <!--    Main Content-->
        <!-- ===============================================-->
        <main class="main" id="top">
          <div class="container-fluid">
            <div class="row min-vh-100 flex-center g-0">
              <div class="col-lg-8 col-xxl-5 py-3 position-relative">
                  <img class="bg-auth-circle-shape" src="/admin_template/assets/img/icons/spot-illustrations/bg-shape-archyart.png" alt="" width="250">
                  <img class="bg-auth-circle-shape-2" src="/admin_template/assets/img/icons/spot-illustrations/shape-1-archyart.png" alt="" width="150">
                <div class="card overflow-hidden z-1">
                  <div class="card-body p-0">
                    <div class="row g-0 h-100">
                      <div class="col-md-5 text-center bg-card-gradient">
                        <div class="position-relative mb-4" data-bs-theme="light" style="height: 350px;">
                          
                          <!--/.bg-holder-->

                              <div class="p-3 position-absolute top-50 start-50 translate-middle"><img src="/admin_template_child/img/logo/logo-white.png" class="img-fluid"></div>
                          
                        </div>
                   <!--     <div class="mt-3 mb-4 mt-md-4 mb-md-5" data-bs-theme="light">
                          <p class="text-white"></p>
                          <p class="mb-0 mt-4 mt-md-5 fs-10 fw-semi-bold text-white opacity-75"></p>
                        </div> -->
                      </div>
                      <div class="col-md-7 d-flex flex-center">
                        <div class="p-4 p-md-5 flex-grow-1">
                          <div class="row flex-between-center">
                            <div class="col-auto">
                              <?php
                                $loginTitles = [
                                  'forgot_password' => 'Password dimenticata',
                                  'reset_password' => 'Reimposta password',
                                  'login_otp' => 'Codice di accesso',
                                  'login' => 'Account Login'
                                ];
                              ?>
                              <h3><?php echo htmlspecialchars($loginTitles[$loginMode] ?? $loginTitles['login'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            </div>
                          </div>
                          <?php if ($loginMode === 'forgot_password'): ?>
                            <form name="forgot-password-form" method="post" id="forgot-password-form">
                              <input type="hidden" name="form_action" value="forgot_password">
                              <div class="mb-3">
                                <label class="form-label" for="forgot-email">Email</label>
                                <input class="form-control" id="forgot-email" name="forgot_email" type="email" value="<?php echo htmlspecialchars($forgot_password_email ?? '', ENT_QUOTES, 'UTF-8'); ?>" required/>
                              </div>
                              <div class="mb-3">
                                <button class="btn btn-primary d-block w-100 mt-3" type="submit">Invia istruzioni</button>
                                <?php if (!empty($forgot_password_message)): ?>
                                  <div class="<?php echo !empty($forgot_password_success) ? 'alert alert-success' : 'error'; ?> mt-2" style="padding:6px;"><?php echo htmlspecialchars($forgot_password_message, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                              </div>
                              <div class="text-center">
                                <a class="fs-10" href="/archy/login.php">Torna al login</a>
                              </div>
                            </form>
                          <?php elseif ($loginMode === 'reset_password'): ?>
                            <form name="reset-password-form" method="post" id="change-password-form">
                              <input type="hidden" name="form_action" value="reset_password">
                              <div class="mb-3">
                                <label class="form-label" for="reset-token">Codice reset</label>
                                <input class="form-control" id="reset-token" name="reset_token" type="text" value="<?php echo htmlspecialchars($reset_password_token ?? '', ENT_QUOTES, 'UTF-8'); ?>" required/>
                              </div>
                              <input id="old-password" type="hidden" value=""/>
                              <div class="mb-3">
                                <label class="form-label" for="new-password">Nuova password</label>
                                <input class="form-control" id="new-password" name="new_password" type="password" autocomplete="new-password" required/>
                              </div>
                              <div class="mb-3">
                                <label class="form-label" for="new-password-repeat">Ripeti nuova password</label>
                                <input class="form-control" id="new-password-repeat" name="new_password_repeat" type="password" autocomplete="new-password" required/>
                              </div>
                              <ul class="list-unstyled fs-10 mb-3" id="password-rules">
                                <li data-rule="length">Almeno 12 caratteri</li>
                                <li data-rule="lowercase">Almeno una lettera minuscola</li>
                                <li data-rule="uppercase">Almeno una lettera maiuscola</li>
                                <li data-rule="number">Almeno un numero</li>
                                <li data-rule="special">Almeno un carattere speciale</li>
                                <li data-rule="spaces">Nessuno spazio</li>
                                <li data-rule="match">Le password coincidono</li>
                              </ul>
                              <div class="mb-3">
                                <button class="btn btn-primary d-block w-100 mt-3" type="submit" id="change-password-btn" disabled>Reimposta password</button>
                                <?php if (!empty($reset_password_message)): ?>
                                  <div class="<?php echo !empty($reset_password_success) ? 'alert alert-success' : 'error'; ?> mt-2" style="padding:6px;"><?php echo htmlspecialchars($reset_password_message, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                              </div>
                              <div class="text-center">
                                <a class="fs-10" href="/archy/login.php">Torna al login</a>
                              </div>
                            </form>
                          <?php elseif ($loginMode === 'login_otp'): ?>
                            <form name="login-otp-form" method="post" id="login-otp-form">
                              <input type="hidden" name="form_action" value="login_otp">
                              <input type="hidden" name="otp_token" value="<?php echo htmlspecialchars($login_otp_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                              <input type="hidden" name="otp_email" value="<?php echo htmlspecialchars($login_otp_email ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                              <div class="mb-3">
                                <label class="form-label" for="otp-code">Codice ricevuto via email</label>
                                <input class="form-control" id="otp-code" name="otp_code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus/>
                              </div>
                              <div class="mb-3">
                                <button class="btn btn-primary d-block w-100 mt-3" type="submit">Verifica codice</button>
                                <?php if (!empty($login_otp_message)): ?>
                                  <div class="<?php echo !empty($login_otp_success) ? 'alert alert-success' : 'error'; ?> mt-2" style="padding:6px;"><?php echo htmlspecialchars($login_otp_message, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                              </div>
                              <div class="text-center">
                                <a class="fs-10" href="/archy/login.php">Torna al login</a>
                              </div>
                            </form>
                          <?php else: ?>
                            <form name="login-form" method="post">
                              <input type="hidden" name="form_action" value="login">
                              <div class="mb-3">
                                <label class="form-label" for="card-email">Email</label>
                                <input class="form-control" id="card-email" name="username" type="email" required/>
                              </div>
                              <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                  <label class="form-label" for="card-password">Password</label>
                                </div>
                                <input class="form-control" id="card-password" name="password" type="password" required/>
                              </div>
                              <!--<div class="row flex-between-center">
                                <div class="col-auto">
                                  <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="card-checkbox" checked="checked" />
                                    <label class="form-check-label mb-0" for="card-checkbox">Remember me</label>
                                  </div>
                                </div>
                                <div class="col-auto"><a class="fs-10" href="/admin_template/pages/authentication/card/forgot-password.html">Forgot Password?</a></div>
                              </div>-->
                              
                              <div
                                  class="cf-turnstile"
                                  data-sitekey="<?php echo htmlspecialchars($turnstileSiteKey, ENT_QUOTES, 'UTF-8'); ?>"
                                  data-theme="auto"
                                  data-size="flexible"
                                  data-callback="onTurnstileSuccess"
                                  data-error-callback="onTurnstileError"
                                  data-expired-callback="onTurnstileExpired"
                                ></div>
                              
                              <div class="mb-3">
                                <button class="btn btn-primary d-block w-100 mt-3" type="submit" id="submit-btn" name="submit" disabled>Log in</button>
                                <?php if (!empty($login_error_message)): ?>
                                  <div class="error mt-2" style="padding:6px;"><?php echo htmlspecialchars($login_error_message, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                              </div>
                              <div class="text-center">
                                <a class="fs-10" href="/archy/login.php?mode=forgot-password">Password dimenticata</a>
                              </div>
                            </form>
                          <?php endif; ?>
                         <!-- <div class="position-relative mt-4">
                            <hr />
                            <div class="divider-content-center">or log in with</div>
                          </div>
                          <div class="row g-2 mt-2">
                            <div class="col-sm-6"><a class="btn btn-outline-google-plus btn-sm d-block w-100" href="#"><span class="fab fa-google-plus-g me-2" data-fa-transform="grow-8"></span> google</a></div>
                            <div class="col-sm-6"><a class="btn btn-outline-facebook btn-sm d-block w-100" href="#"><span class="fab fa-facebook-square me-2" data-fa-transform="grow-8"></span> facebook</a></div>
                          </div>-->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
        <!-- ===============================================-->
        <!--    End of Main Content-->
        <!-- ===============================================-->
        
        <!-- ===============================================-->
        <!--    JavaScripts-->
        <!-- ===============================================-->
        <script src="/admin_template/vendors/popper/popper.min.js"></script>
        <script src="/admin_template/vendors/bootstrap/bootstrap.min.js"></script>
        <script src="/admin_template/vendors/anchorjs/anchor.min.js"></script>
        <script src="/admin_template/vendors/is/is.min.js"></script>
        <script src="/admin_template/vendors/echarts/echarts.min.js"></script>
        <script src="/admin_template/vendors/prism/prism.js"></script>
        <script src="/admin_template/vendors/fontawesome/all.min.js"></script>
        <script src="/admin_template/vendors/lodash/lodash.min.js"></script>
        <script src="/admin_template/vendors/list.js/list.min.js"></script>
        <script src="/admin_template/assets/js/theme.js"></script>
        <script>
        function onTurnstileSuccess(token) {
          console.log("Turnstile success:", token);
          var submitButton = document.getElementById("submit-btn");
          if (submitButton) {
            submitButton.disabled = false;
          }
        }
        function onTurnstileError(errorCode) {
          console.error("Turnstile error:", errorCode);
          var submitButton = document.getElementById("submit-btn");
          if (submitButton) {
            submitButton.disabled = true;
          }
        }
        function onTurnstileExpired() {
          console.warn("Turnstile token expired");
          var submitButton = document.getElementById("submit-btn");
          if (submitButton) {
            submitButton.disabled = true;
          }
        }

        (function () {
          var form = document.getElementById("change-password-form");
          if (!form) {
            return;
          }

          var oldPassword = document.getElementById("old-password");
          var newPassword = document.getElementById("new-password");
          var repeatPassword = document.getElementById("new-password-repeat");
          var submitButton = document.getElementById("change-password-btn");
          var ruleItems = document.querySelectorAll("#password-rules [data-rule]");

          function setRuleState(name, passed) {
            var item = document.querySelector('#password-rules [data-rule="' + name + '"]');
            if (!item) {
              return;
            }

            item.classList.toggle("text-success", passed);
            item.classList.toggle("text-danger", !passed);
            item.dataset.passed = passed ? "1" : "0";
          }

          function validatePassword() {
            var current = oldPassword.value;
            var password = newPassword.value;
            var repeated = repeatPassword.value;
            var rules = {
              length: password.length >= 12,
              lowercase: /[a-z]/.test(password),
              uppercase: /[A-Z]/.test(password),
              number: /[0-9]/.test(password),
              special: /[^A-Za-z0-9\s]/.test(password),
              spaces: !/\s/.test(password),
              different: current === "" || current !== password,
              match: password !== "" && password === repeated
            };
            var valid = true;

            Object.keys(rules).forEach(function (name) {
              setRuleState(name, rules[name]);
              valid = valid && rules[name];
            });

            submitButton.disabled = !valid;
          }

          ruleItems.forEach(function (item) {
            item.classList.add("text-danger");
          });

          oldPassword.addEventListener("input", validatePassword);
          newPassword.addEventListener("input", validatePassword);
          repeatPassword.addEventListener("input", validatePassword);
          form.addEventListener("submit", function (event) {
            validatePassword();
            if (submitButton.disabled) {
              event.preventDefault();
            }
          });
        })();
      </script>
        
    </body>

</html>
