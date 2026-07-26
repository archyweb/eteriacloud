# Area privata aggiornamenti

Questa cartella resta sul master e contiene solo file privati del processo di
aggiornamento:

- `customers.csv`
- `customers.xlsx`
- `runtime/`
- `backups/`
- `logs/`

I file pubblici di aggiornamento stanno invece nel sottodominio:

```text
subdomains/update/httpdocs
```

Quindi `manifest.json`, `sql/*.sql` e `packages/*.zip` non devono essere presi
da questa cartella privata nel flusso finale.
