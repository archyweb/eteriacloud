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

## Tabella aggiornamenti

La tabella `archy_core_updates` va creata nel database di ogni installazione
cliente, perche registra la versione e l'esito degli aggiornamenti di quello
specifico sito.

Il pannello master potra avere in seguito tabelle separate per elenco clienti,
lanci aggiornamento e log centralizzati, ma `archy_core_updates` resta locale
al database che viene aggiornato.

Schema iniziale:

```sql
private/_common/update/sql/archyart-update-2026.07.13.1.sql
```

## File clienti

Salvare il file clienti come `private/_common/update/customers.xlsx`.
Il file resta privato e viene ignorato da Git perche contiene credenziali DB.

In alternativa e possibile usare un CSV come `private/_common/update/customers.csv`.

Colonne consigliate per Excel o CSV:

```csv
customer,db_host,db_port,db_name,db_user,db_pass,enabled
cliente-demo,localhost,3306,nome_database,utente,password,1
```

Sono accettate anche intestazioni italiane equivalenti:

```csv
cliente,host,porta,database,utente,password,attivo
cliente-demo,localhost,3306,nome_database,utente,password,1
```

Per aggiornare file su root diverse, aggiungere anche `main_root` e avviare
il comando con `--customer-roots=1`.

## Comando

Simulazione:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=private/_common/update/manifest.json --customers=private/_common/update/customers.xlsx --dry-run=1
```

Aggiornamento live:

```bash
php subdomains/master/httpdocs/srv/core-update/run.php --manifest=private/_common/update/manifest.json --customers=private/_common/update/customers.xlsx --dry-run=0
```

## Test da browser

Il comando principale `run.php` resta solo CLI. Per testare da browser usare
`subdomains/master/httpdocs/srv/core-update/web-run.php`, protetto da token.

Nel `config-local.php` del master aggiungere:

```php
'core_update_web_token' => 'TOKEN_LUNGO_CASUALE',
```

URL di test:

```text
https://master.dominio.it/srv/core-update/web-run.php?token=TOKEN_LUNGO_CASUALE&dry_run=1
```

Da browser l'aggiornamento live e disabilitato di default. Per abilitarlo
esplicitamente:

```php
'core_update_web_allow_live' => true,
```
