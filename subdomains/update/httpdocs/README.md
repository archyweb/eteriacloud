# Repository aggiornamenti ArchyArt

Questo sottodominio contiene solo file pubblici di aggiornamento:

- `manifest.json`
- `sql/*.sql`
- `packages/*.zip`

Non deve contenere credenziali, `customers.csv`, `.env`, `config-local.php`,
backup, log o file privati.

## Manifest

Esempio:

```json
{
  "version": "2026.07.16.1",
  "requires": "2026.07.13.1",
  "package": "packages/archyart-update-2026.07.16.1.zip",
  "sha256": "HASH_SHA256_DELLO_ZIP",
  "sql": "sql/archyart-update-2026.07.16.1.sql",
  "sql_sha256": "HASH_SHA256_DEL_SQL",
  "changelog": "Descrizione sintetica dell'aggiornamento",
  "created_at": "2026-07-16"
}
```

`requires` blocca l'aggiornamento se sul database cliente non risulta installata
con successo la versione indicata.

Per il primo aggiornamento della catena `requires` puo restare vuoto.

## SQL

I file SQL devono essere idempotenti quando possibile, usando ad esempio:

- `CREATE TABLE IF NOT EXISTS`
- controlli su `INFORMATION_SCHEMA` prima di `ALTER TABLE`

## Packages

Gli zip in `packages/` devono contenere i file con la stessa gerarchia della
root gestionale cliente. Non includere mai file sensibili.

I percorsi dentro lo zip non possono essere assoluti e non possono usare `../`.

## Comandi master

Il file clienti resta privato sul master:

```text
private/_common/update/customers.csv
```

Test DB:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=db --dry-run=1
```

Aggiornamento DB:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --mode=db --dry-run=0
```

Per aggiornare file, il CSV deve includere `main_root` o `subdomain_root`:

```csv
customer;db_host;db_port;db_name;db_user;db_pass;enabled;main_root
cliente-demo;localhost;3306;nome_database;utente;password;1;/var/www/vhosts/cliente.it
```
