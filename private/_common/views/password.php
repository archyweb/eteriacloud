<?php
$u = $app->currentUser();
?>

    <form method="post" class="row g-3 needs-validation" novalidate="" action="/archy/app.php?do_action=changePassword">
        <div class="col-12 mb-3">
            <div class="row justify-content-center justify-content-sm-between">
                <div class="col-sm-auto text-center">
                    <h5 class="d-inline-block p-2">Cambio password</h5>
                </div>
            </div>
        </div>
        
        
      <div class="col-sm-7">
        <label class="form-label" for="oldPassword">Vecchia password</label>
        <input type="password" class="form-control" name="old_password" id="oldPassword" placeholder="vecchia password">
      </div>

      <div class="col-sm-7">
        <label class="form-label" for="newPassword">Digita la nuova password</label>
        <input type="password" class="form-control" name="new_password" id="newPassword" placeholder="nuova password">
        <div class="form-text">(Minimo 8 caratteri alfanumerici, usa caratteri speciali come @,#,!)</div>
        <div id="user-form-text"></div>
      </div>

      <div class="col-sm-7">
        <label class="form-label" for="newPasswordRepeat">Ripeti nuova password</label>
        <input type="password" class="form-control" name="new_password_repeat" id="newPasswordRepeat" placeholder="ripeti nuova password">
      </div>
      
        <div class="col-sm-7">
      <button type="submit" id="changeButton" class="btn btn-outline-secondary" disabled>Cambia password</button>
      </div>
    </form>

<script>
  //selectors
  let sel_password_field = document.getElementById("newPassword");
  let sel_login_button = document.getElementById("changeButton");
  let sel_user_info_paragraph = document.getElementById("user-form-text");

  //add event listener for changes of password field
  sel_password_field.addEventListener("keyup", function() {
    //read current password
    let password = sel_password_field.value;

    //evaluate password strength
    let result = zxcvbn(password);

    //set minimum_score via php
      <?php echo 'let minimum_score =' . $app->auth->config->password_min_score; ?>

    //enable submit button
    if (result.score >= minimum_score) {
      sel_login_button.disabled = false;
      sel_user_info_paragraph.style.color = "green";
    } else {
      sel_login_button.disabled = true;
      sel_user_info_paragraph.style.color = "red";
    }

    //change user info paragraph text
    switch (result.score) {
      case 0:
        sel_user_info_paragraph.innerHTML = "Troppo debole";
        break;
      case 1:
        sel_user_info_paragraph.innerHTML = "Non sicura";
        break;
      case 2:
        sel_user_info_paragraph.innerHTML = "Non abbastanza sicura";
        break;
      case 3:
        sel_user_info_paragraph.innerHTML = "Buona";
        break;
      case 4:
        sel_user_info_paragraph.innerHTML = "Eccellente";
        break;
      default:
        sel_user_info_paragraph.innerHTML = "Troppo debole";
        break;
    }
  })
</script>
