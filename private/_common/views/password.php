<?php
$u = $app->currentUser();
$userEmail = $u['email'] ?? '';
$emailLocalPart = '';
$passwordForbiddenTerms = [];

if ($userEmail !== '' && strpos($userEmail, '@') !== false) {
    $emailLocalPart = strtolower(substr($userEmail, 0, strpos($userEmail, '@')));
}

$siteName = $app->config['customer_name'] ?? $app->auth->config->site_name ?? '';
foreach (preg_split('/[^A-Za-z0-9]+/', $siteName, -1, PREG_SPLIT_NO_EMPTY) as $term) {
    $term = strtolower($term);
    if (strlen($term) >= 4) {
        $passwordForbiddenTerms[] = $term;
    }
}
?>

<form method="post" class="row g-3 needs-validation" novalidate action="/archy/app.php?do_action=changePassword" id="change-password-form">
    <div class="col-12 mb-3">
        <div class="row justify-content-center justify-content-sm-between">
            <div class="col-sm-auto text-center">
                <h5 class="d-inline-block p-2">Cambio password</h5>
            </div>
        </div>
    </div>

    <div class="col-sm-7">
        <label class="form-label" for="old-password">Password attuale</label>
        <input type="password" class="form-control" name="old_password" id="old-password" autocomplete="current-password" required>
    </div>

    <div class="col-sm-7">
        <label class="form-label" for="new-password">Nuova password</label>
        <input type="password" class="form-control" name="new_password" id="new-password" autocomplete="new-password" required>
    </div>

    <div class="col-sm-7">
        <label class="form-label" for="new-password-repeat">Ripeti nuova password</label>
        <input type="password" class="form-control" name="new_password_repeat" id="new-password-repeat" autocomplete="new-password" required>
    </div>

    <div class="col-sm-7">
        <ul class="list-unstyled fs-10 mb-3" id="password-rules" data-email-local="<?php echo htmlspecialchars($emailLocalPart, ENT_QUOTES, 'UTF-8'); ?>" data-forbidden-terms="<?php echo htmlspecialchars(json_encode(array_values(array_unique($passwordForbiddenTerms))), ENT_QUOTES, 'UTF-8'); ?>">
            <li data-rule="length">Almeno 12 caratteri</li>
            <li data-rule="lowercase">Almeno una lettera minuscola</li>
            <li data-rule="uppercase">Almeno una lettera maiuscola</li>
            <li data-rule="number">Almeno un numero</li>
            <li data-rule="special">Almeno un carattere speciale</li>
            <li data-rule="spaces">Nessuno spazio</li>
            <li data-rule="pattern">Non usa parole con simbolo e numeri finali</li>
            <li data-rule="different">Diversa dalla password attuale</li>
            <li data-rule="email">Non contiene la parte iniziale dell email</li>
            <li data-rule="site">Non contiene il nome del cliente</li>
            <li data-rule="strength">Complessita adeguata</li>
            <li data-rule="match">Le password coincidono</li>
        </ul>
    </div>

    <div class="col-sm-7">
        <button type="submit" id="change-password-btn" class="btn btn-primary" disabled>Cambia password</button>
    </div>
</form>

<script>
(function () {
    var form = document.getElementById("change-password-form");
    if (!form) {
        return;
    }

    var oldPassword = document.getElementById("old-password");
    var newPassword = document.getElementById("new-password");
    var repeatPassword = document.getElementById("new-password-repeat");
    var submitButton = document.getElementById("change-password-btn");
    var passwordRules = document.getElementById("password-rules");
    var ruleItems = document.querySelectorAll("#password-rules [data-rule]");
    var emailLocalPart = (passwordRules.dataset.emailLocal || "").toLowerCase();
    var forbiddenTerms = [];
    var minimumScore = <?php echo max(3, (int) $app->auth->config->password_min_score); ?>;

    try {
        forbiddenTerms = JSON.parse(passwordRules.dataset.forbiddenTerms || "[]");
    } catch (error) {
        forbiddenTerms = [];
    }

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
        var passwordLower = password.toLowerCase();
        var strength = typeof zxcvbn === "function" ? zxcvbn(password).score : minimumScore;
        var rules = {
            length: password.length >= 12,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9\s]/.test(password),
            spaces: !/\s/.test(password),
            pattern: !/^[A-Za-z]{4,}[^A-Za-z0-9\s][0-9]{2,4}$/.test(password),
            different: current === "" || current !== password,
            email: emailLocalPart === "" || emailLocalPart.length < 4 || passwordLower.indexOf(emailLocalPart) === -1,
            site: !forbiddenTerms.some(function (term) {
                return term.length >= 4 && passwordLower.indexOf(term.toLowerCase()) !== -1;
            }),
            strength: typeof zxcvbn === "function" && strength >= minimumScore,
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
