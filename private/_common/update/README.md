# Aggiornamenti ArchyArt

Questa cartella contiene solo file privati del processo di aggiornamento.
Non deve essere pubblicata dentro una root web.

## Manifest sul sottodominio update

Esempio `https://update.archyart.it/manifest.json`:

```json
{
  "version": "2026.06.13.1",
  "package": "archyart-update-2026.06.13.1.zip",
  "sha256": "HASH_SHA256_DELLO_ZIP",
  "sql": "archyart-update-2026.06.13.1.sql",
  "sql_sha256": "HASH_SHA256_DEL_SQL",
  "changelog": "Descrizione sintetica dell'aggiornamento"
}
```

Lo zip deve contenere i file con la stessa gerarchia della root del gestionale.
I file sensibili come `config-local.php`, `private/_common/config.php`, `.env`,
upload, documenti, backup e runtime vengono esclusi dal processo.

## CSV clienti

Salvare il CSV come `private/_common/update/customers.csv`.

Colonne consigliate:

```csv
customer,db_host,db_port,db_name,db_user,db_pass,enabled
cliente-demo,localhost,3306,nome_database,utente,password,1
```

Per aggiornare file su root diverse, aggiungere anche `main_root` e avviare
il comando con `--customer-roots=1`.

## Comando

Simulazione:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --dry-run=1
```

Aggiornamento live:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=https://update.archyart.it/manifest.json --customers=private/_common/update/customers.csv --dry-run=0
```
