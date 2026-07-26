# Mini guida aggiornamenti ArchyArt

Questa guida descrive il flusso operativo per preparare e lanciare un
aggiornamento automatico sui clienti.

## Struttura

File pubblici, da mettere nel sottodominio `update`:

```text
subdomains/update/httpdocs/manifest.json
subdomains/update/httpdocs/sql/*.sql
subdomains/update/httpdocs/packages/*.zip
```

File privati, solo sul master:

```text
private/_common/update/customers.csv
private/_common/update/logs/
private/_common/update/runtime/
```

Il file `customers.csv` non va mai pubblicato e non va mai messo su Git.

## 1. Scegliere la versione

Usare il formato:

```text
YYYY.MM.DD.N
```

Esempi:

```text
2026.07.17.1
2026.07.17.2
2026.07.18.1
```

Nel manifest:

```json
"version": "2026.07.17.1",
"requires": "2026.07.13.1"
```

`requires` deve contenere l'ultima versione gia installata con successo. Per il
primo aggiornamento della catena puo restare vuoto.

## 2. Preparare il file SQL

Creare il file:

```text
subdomains/update/httpdocs/sql/archyart-update-2026.07.17.1.sql
```

Quando possibile, rendere lo SQL idempotente:

```sql
CREATE TABLE IF NOT EXISTS `nome_tabella` (...);
```

Per aggiungere colonne usare controlli su `INFORMATION_SCHEMA`, cosi se una
colonna esiste gia l'update non si blocca.

## 3. Calcolare hash SQL

Da PowerShell locale:

```powershell
Get-FileHash C:\wamp64\www\archyart-staging\subdomains\update\httpdocs\sql\archyart-update-2026.07.17.1.sql -Algorithm SHA256
```

Copiare l'hash in minuscolo dentro `manifest.json`:

```json
"sql": "sql/archyart-update-2026.07.17.1.sql",
"sql_sha256": "hash_sha256_del_file_sql"
```

Se il file SQL cambia anche solo di uno spazio, l'hash va ricalcolato.

## 4. Preparare eventuale pacchetto files

Creare uno zip in:

```text
subdomains/update/httpdocs/packages/archyart-update-2026.07.17.1.zip
```

Lo zip deve contenere i file con la stessa gerarchia della root gestionale.

Non includere mai:

```text
config-local.php
.env
private/_common/config.php
customers.csv
backup
log
upload/documenti cliente
```

Calcolare hash zip:

```powershell
Get-FileHash C:\wamp64\www\archyart-staging\subdomains\update\httpdocs\packages\archyart-update-2026.07.17.1.zip -Algorithm SHA256
```

Nel manifest:

```json
"package": "packages/archyart-update-2026.07.17.1.zip",
"sha256": "hash_sha256_dello_zip"
```

Se non ci sono file da aggiornare, `package` e `sha256` possono restare vuoti.

## 5. Aggiornare manifest

Esempio completo:

```json
{
  "version": "2026.07.17.1",
  "requires": "2026.07.13.1",
  "package": "packages/archyart-update-2026.07.17.1.zip",
  "sha256": "hash_sha256_dello_zip",
  "sql": "sql/archyart-update-2026.07.17.1.sql",
  "sql_sha256": "hash_sha256_del_file_sql",
  "changelog": "Descrizione sintetica dell'aggiornamento",
  "created_at": "2026-07-17"
}
```

## 6. Caricare sul server

Caricare sul sottodominio `update`:

```text
manifest.json
sql/archyart-update-2026.07.17.1.sql
packages/archyart-update-2026.07.17.1.zip
```

Sul master devono essere presenti:

```text
private/_common/update/customers.csv
private/_common/.env
```

Nel `.env` del master deve esserci:

```env
CORE_UPDATE_WEB_TOKEN=token_lungo_casuale
```

## 7. Test aggiornamento DB

Da menu master usare:

```text
Aggiornamenti > Test aggiornamento DB
```

Oppure da browser:

```text
https://master.archyart.it/srv/core-update/web-run.php?token=TOKEN&manifest=https://update.archyart.it/manifest.json&customers=private/_common/update/customers.csv&mode=db&dry_run=1&debug=1
```

Oppure da SSH:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=db --dry-run=1
```

Il dry-run controlla manifest, hash, CSV, connessioni DB e `requires`, ma non
modifica i database.

## 8. Eseguire aggiornamento DB

Da menu master:

```text
Aggiornamenti > Esegui aggiornamento DB
```

Oppure da SSH:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=db --dry-run=0
```

## 9. Test aggiornamento files

Per aggiornare file, nel CSV clienti serve anche `main_root` o `subdomain_root`:

```csv
customer;db_host;db_port;db_name;db_user;db_pass;enabled;main_root
cliente-demo;localhost;3306;nome_database;utente;password;1;/var/www/vhosts/cliente.it
```

Test:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=files --dry-run=1
```

Esecuzione:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=files --dry-run=0
```

## 10. Aggiornamento DB + files insieme

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=all --dry-run=1
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=all --dry-run=0
```

## 11. Controlli dopo update

Su ogni DB cliente verificare:

```sql
SELECT *
FROM archy_core_updates
ORDER BY id DESC;
```

La versione deve risultare con:

```text
status = success
dry_run = 0
```

## 12. Errori comuni

Hash SQL non valido:

```text
Il file SQL e diverso da quello indicato nel manifest.
Ricalcolare sql_sha256 e ricaricare manifest.json.
```

Versione richiesta non installata:

```text
Il cliente non ha ancora applicato la versione indicata in requires.
Applicare prima la versione precedente.
```

Errore SSL sul manifest:

```text
Il certificato di update.archyart.it non e valido per quel sottodominio.
Sistemare SSL oppure usare temporaneamente http solo per test.
```

File clienti mancante:

```text
customers.csv deve stare sul master in private/_common/update/customers.csv.
```
