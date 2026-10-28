# Sicurezza account

Eseguire prima `subdomains/update/httpdocs/sql/archyart-update-2026.10.26.1.sql`
nel database di ciascun sottodominio. La migrazione aggiunge solo
`archy_user_security`; richiede gli update TOTP precedenti.

Distribuire insieme:

- `private/_common/classes/ArchyPHPAuth.php`
- `private/_common/classes/App.php`
- `private/_common/views/security.php`
- `private/_common/views/password.php`
- `subdomains/master/httpdocs/archy/app.php`
- `subdomains/master/httpdocs/archy/login.php`
- `subdomains/master/httpdocs/admin_template_child/html/navbar_right.php`

Nel `config-local.php` di ogni sottodominio:

```php
'login_email_otp' => true,
'login_google_authenticator_otp' => true,
'login_otp_required' => false,
```

I primi due flag definiscono i valori iniziali per gli utenti senza preferenze.
Email e Authenticator possono essere attivati personalmente anche con questi
flag spenti. Il terzo rende obbligatorio
almeno un secondo fattore; con `false` l'utente puo disattivarli entrambi.
Gli utenti senza preferenze ereditano i metodi precedentemente disponibili.
Se viene successivamente imposta l'obbligatorieta, un precedente opt-out
non permette di saltare il secondo fattore.

Disattivare il metodo Authenticator conserva il collegamento; Scollega
Authenticator cancella il segreto dopo la verifica del codice corrente.
Le preferenze richiedono una sessione attiva e il token CSRF; con un'app gia
collegata richiedono anche un codice Authenticator valido e non ancora usato.
La password attuale viene richiesta solo nel blocco cambio password.
Il numero WhatsApp e solo un recapito non verificato e non abilita OTP.

Test automatico, senza database applicativo:

```text
php private/_common/tests/security_preferences.php
```

Verificare nel gestionale: apertura scheda Sicurezza, cambio password,
salvataggio con token CSRF errato, collegamento e conferma QR, scelta di un
solo metodo al login, opt-out quando consentito, rifiuto opt-out quando
obbligatorio, scollegamento con e senza un metodo alternativo disponibile.
