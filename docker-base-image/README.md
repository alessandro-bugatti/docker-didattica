# docker-base-image

Contiene il **Dockerfile dell'immagine PHP/Apache condivisa** da tutti i progetti didattici.

Incluso nell'immagine:
- PHP 8.2 con Apache
- Estensioni: `mysqli`, `pdo`, `pdo_mysql`, `zip`, `gd`
- Composer (ultima versione)
- `mod_rewrite` e `mod_ssl` abilitati
- Certificato SSL self-signed per lo sviluppo locale (valido 10 anni)
- Configurazione Apache (`000-default.conf`, `ssl-default.conf`)
- Configurazione PHP (`php.ini`)

## Costruire l'immagine (una volta sola)

Il comando va eseguito dalla cartella `docker-base-image/`, perché il
build context include sia `apache-php/` che `apache-config/`:

```bash
cd docker-base-image
docker build -t didattica-php:latest .
```

Per reti con proxy (es. rete scolastica):

```bash
cd docker-base-image
docker build \
  --build-arg PROXY_HOST=proxy.istituto.it \
  --build-arg PROXY_PORT=3128 \
  -t didattica-php:latest .
```

## Aggiornare l'immagine

Se modifichi il Dockerfile, la config Apache o php.ini, ricostruisci:

```bash
cd docker-base-image
docker build -t didattica-php:latest .
```

I progetti già avviati continueranno a usare la versione precedente fino al
prossimo `docker compose up`.

## Struttura

```
docker-base-image/
├── Dockerfile               ← build context = questa cartella
├── apache-php/
│   ├── php.ini              # PHP sviluppo (display_errors On, ecc.)
│   └── php-production.ini   # Riferimento produzione (non copiato nell'immagine)
└── apache-config/
    ├── 000-default.conf     # VirtualHost HTTP + HTTPS
    └── ssl-default.conf     # VirtualHost HTTPS standalone
```
