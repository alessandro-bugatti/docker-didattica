# docker-didattica

Struttura per la gestione di progetti didattici PHP/MariaDB con Docker.

L'obiettivo è avere **una sola immagine Docker** condivisa da tutti i progetti,
invece di ricostruire un'immagine quasi identica per ogni progetto.
Ogni progetto ha il proprio codice, il proprio database e le proprie porte,
ma condivide la stessa base PHP+Apache+Composer.

## Struttura generale

```
docker-didattica/
├── docker-base-image/     # Immagine condivisa (costruisci una volta sola)
│   ├── Dockerfile
│   ├── apache-php/
│   │   ├── php.ini
│   │   └── php-production.ini
│   └── apache-config/
│       ├── 000-default.conf
│       └── ssl-default.conf
│
├── progetto-template/     # Stampino per ogni nuovo progetto
│   ├── docker-compose.yml
│   ├── docker-compose.override.yml
│   ├── .env.example
│   ├── .gitignore
│   ├── README.md
│   ├── db-init/
│   ├── db-data/
│   ├── docs/
│   └── www/                 # Codice applicativo; Apache espone solo www/public/
│
├── progetto-a/            # Ogni progetto è una copia del template
├── progetto-b/
└── ...
```

## Flusso di lavoro

### 1. Prima volta (una volta sola)

Entra nella cartella `docker-base-image` e costruisci l'immagine:

```bash
cd docker-base-image
docker build -t didattica-php:latest .
```

### 2. Nuovo progetto

Copia la cartella `progetto-template` con un nuovo nome.

**Linux/Mac:**
```bash
cp -r progetto-template progetto-nuovo
```

**Windows (Prompt dei comandi):**
```cmd
xcopy progetto-template progetto-nuovo /E /I
```

**Windows (PowerShell):**
```powershell
Copy-Item -Recurse progetto-template progetto-nuovo
```

Poi, apri il file `.env.example` con l'editor preferito, compilalo e salvalo
come `.env` nella stessa cartella. Infine:

```bash
cd progetto-nuovo
docker compose up -d
docker exec <PROJECT_NAME>_web composer install
```

### 3. Aggiornare l'immagine base

Se aggiungi un'estensione PHP o modifichi la configurazione Apache:

```bash
cd docker-base-image
docker build -t didattica-php:latest .
```

Poi riavvia i progetti che vuoi aggiornare:

```bash
cd ../progetto-x
docker compose up -d
```

## Vantaggi rispetto al vecchio approccio

| | Prima | Ora |
|---|---|---|
| Immagine per progetto | ~1 GB cadauna | 0 (usa l'immagine base) |
| Immagine base | 1 (~1 GB) | 1 (~1 GB) |
| Spazio totale con N progetti | N × 1 GB | ~1 GB fisso |
| Nuovo progetto | `docker compose up` (con build) | copia template → `docker compose up` |
