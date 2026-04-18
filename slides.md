---
title: "Progetti didattici PHP con Docker e PHPStorm"
author: "Alessandro Bugatti"
date: "30-03-2026"
theme: solarized
---

# Progetti didattici PHP  
con Docker e PHPStorm

---

## Immagine base condivisa

Al posto di avere un'immagine per ogni progetto, si crea un'immagine
base da condividere in ogni progetto.

```
docker-didattica/
├── docker-base-image/   ← costruita una volta sola
├── progetto-a/          ← usa l'immagine base
├── progetto-b/          ← usa l'immagine base
└── progetto-c/          ← usa l'immagine base
```

Il codice di ogni progetto entra nel container  
tramite **bind mount**, non costruendo una nuova immagine

---

## Struttura: docker-base-image

```
docker-base-image/
├── Dockerfile
├── apache-php/
│   ├── php.ini
│   └── php-production.ini
└── apache-config/
    ├── 000-default.conf
    └── ssl-default.conf
```

Contiene tutto ciò che è comune:  
PHP 8.2, Apache, Composer, estensioni, config SSL

---

## Cosa include l'immagine base

- PHP 8.2 con Apache
- Estensioni: `mysqli`, `pdo`, `pdo_mysql`, `zip`, `gd`
- Composer
- `mod_rewrite` e `mod_ssl`
- Certificato SSL self-signed per lo sviluppo locale
- Configurazione Apache
- Configurazione PHP (`display_errors`, timezone, limiti upload…)

---

## Costruire l'immagine base

Si fa **una volta sola** (o quando si modifica qualcosa)

```bash
cd docker-base-image
docker build -t didattica-php:latest .
```

Tutti i progetti useranno questa immagine  
senza doverla ricostruire

---

## Struttura: progetto-template

```
progetto-template/
├── docker-compose.yml
├── docker-compose.override.yml
├── .env.example
├── .gitignore
├── db-init/
│   ├── 01_schema.sql
│   └── 02_seed.sql
├── db-data/
│   └── load.sh
└── www/
    ├── conf/config.php
    ├── public/
    ├── src/
    ├── templates/
    ├── index.php
    └── composer.json
```

---

## docker-compose.yml

Usa l'immagine condivisa definita in precedenza, senza doverla ricostruire
per ogni progetto:

```yaml
web:
  image: didattica-php:latest
  volumes:
    - ./www:/var/www/html   # bind mount
```

---

## Il file .env

Ogni progetto ha il suo `.env` con configurazione isolata

```ini
PROJECT_NAME=biblioteca

PORT_HTTP=9080
PORT_HTTPS=9443
PORT_DB=3306
PORT_ADMINER=8080

MYSQL_DATABASE=biblioteca_db
MYSQL_USER=utente
MYSQL_PASSWORD=password
```

Con più progetti attivi contemporaneamente:  
basta usare **porte diverse** in ogni `.env`

---

## Inizializzazione del database

Gli script in `db-init/` vengono eseguiti **automaticamente**  
al primo avvio del container, se il volume è vuoto

```
db-init/
├── 01_schema.sql   ← CREATE TABLE ...
└── 02_seed.sql     ← INSERT INTO ...
```

Per reinizializzare da zero:

```bash
docker compose down -v
docker compose up -d
```

---

## Creare un nuovo progetto

**Linux/Mac:**
```bash
cp -r progetto-template progetto-nuovo
```

**Windows (PowerShell):**
```powershell
Copy-Item -Recurse progetto-template progetto-nuovo
```

**Windows (Prompt dei comandi):**
```cmd
xcopy progetto-template progetto-nuovo /E /I
```

---

## Avviare un nuovo progetto

```bash
cd progetto-nuovo

# 1. Configura l'ambiente (editor preferito)
cp .env.example .env

# 2. Scrivi lo schema del database
#    modifica db-init/01_schema.sql
#    modifica db-init/02_seed.sql

# 3. Avvia i container
docker compose up -d

# 4. Installa le dipendenze PHP
docker exec <PROJECT_NAME>_web composer install
```

---

## Comandi utili

```bash
# Fermare i container
docker compose down

# Fermare e cancellare i dati del DB
docker compose down -v

# Log in tempo reale
docker compose logs -f <PROJECT_NAME>_web

# Shell nel container
docker exec -it <PROJECT_NAME>_web bash

# Generare hash password
docker exec <PROJECT_NAME>_web php \
  -r "echo password_hash('password', PASSWORD_DEFAULT);"
```

---

# PHPStorm  
Un progetto per ogni cartella

---

## Strategia: un progetto PHPStorm per cartella

Ogni cartella-progetto (`progetto-a/`, `progetto-b/`, …)  
diventa un **progetto PHPStorm separato**

Vantaggi:
- Configurazione PHP, database e server isolata per progetto
- Nessuna interferenza tra progetti diversi
- La cartella `.idea/` rimane dentro la cartella del progetto

---

## Piano di lavoro: panoramica

1. Copiare `progetto-template/`
2. Aprire la cartella in PHPStorm
3. Creare e compilare il file `.env` e scrivere schema/seed SQL
4. Avviare i container
5. Configurare l'interprete PHP remoto (via Docker)
6. Configurare la connessione al database
7. Installare le dipendenze con Composer
8. Configurare il server locale (opzionale)

---

## Passo 1 — Copiare il template

Copia `progetto-template/` con il nome del nuovo progetto  
(vedi slide precedenti per il comando corretto su Windows/Linux)

---

## Passo 2 — Aprire il progetto in PHPStorm

**File → Open**  
Seleziona la cartella del nuovo progetto (es. `progetto-nuovo/`)  
Conferma con **Open** (non la cartella parent)

PHPStorm creerà automaticamente la cartella `.idea/`  
con la configurazione del progetto

---

## Passo 3 - Configurazione variabili d'ambiente e database

Aprire il file `.env.example` con l'editor preferito,  
compilarlo con nome, porte e credenziali, e salvarlo come `.env`

Se si utilizza il database, modificare opportunamente i file `db-init\01_schema.sql`
e `db-init\02_seed.sql`.

---

## Passo 4 — Avviare i container

Prima di configurare PHPStorm, avvia i container:

```bash
docker compose up -d
```

PHPStorm deve poter raggiungere il container  
per configurare l'interprete PHP

---

## Passo 5 — Configurare l'interprete PHP

**File → Settings → PHP**

In **CLI Interpreter**, clicca `...` poi `+`  
Scegli **From Docker, Vagrant, VM, WSL, Remote…**

Seleziona **Docker** o **Docker Compose**, dipende da cosa si sta utilizzando, e si scelga l'immagine corretta, quella che contiene `web` nel nome.

PHPStorm rileverà automaticamente la versione di PHP  
e il percorso dell'eseguibile

---

## Passo 5 — Interprete PHP (continua)

Dopo aver aggiunto l'interprete, torna in **File → Settings → PHP**  
e seleziona il nuovo interprete come quello attivo per il progetto

Verifica che la versione rilevata sia **PHP 8.2**

---

## Passo 6 — Configurare il database

**View → Tool Windows → Database**  
Nel pannello Database, clicca `+` → **Data Source → MariaDB**

Compila:
- **Host:** `localhost`
- **Port:** il valore di `PORT_DB` dal tuo `.env` (default: `3306`)
- **Database:** il valore di `MYSQL_DATABASE`
- **User / Password:** i valori di `MYSQL_USER` / `MYSQL_PASSWORD`

Clicca **Test Connection** per verificare

---

## Passo 6 — Database (continua)

Se PHPStorm segnala che manca il driver MariaDB,  
clicca **Download** nel banner che appare in basso nella finestra

Dopo il download, ripeti **Test Connection**:  
dovrebbe comparire il messaggio **Successful**

---

## Passo 7 — Installare le dipendenze PHP

Dal terminale (interno a PHPStorm o esterno):

```bash
docker exec <PROJECT_NAME>_web composer install
```

Dopo l'installazione, PHPStorm dovrebbe riconoscere  
automaticamente le classi in `vendor/`  
(Slim, PHP-DI, Plates…) e offrire autocompletamento

---

## Passo 8 — Configurare il server locale (opzionale)

Per usare **Run/Debug** di PHPStorm direttamente:

**File → Settings → PHP → Servers**  
Clicca `+` e compila:
- **Name:** nome del progetto
- **Host:** `localhost`
- **Port:** il valore di `PORT_HTTP` dal `.env`

Spunta **Use path mappings** e mappa  
la cartella locale `www/` → `/var/www/html` nel container

---

## Struttura finale in PHPStorm

```
progetto-nuovo/          ← radice del progetto PHPStorm
├── .idea/               ← configurazione PHPStorm (non committare)
├── docker-compose.yml
├── .env
├── db-init/
└── www/                 ← tutto il codice PHP
    ├── src/
    ├── templates/
    └── index.php
```

L'autocompletamento, la navigazione tra classi  
e il controllo degli errori funzionano su `www/`

---

## Aggiornare .gitignore per PHPStorm

La cartella `.idea/` è già nel `.gitignore` del template

Verifica che ci sia questa riga:

```
.idea/
```

La configurazione PHPStorm rimane locale  
e non viene condivisa nel repository

---

## Flusso completo: riepilogo

| Passo | Azione |
|---|---|
| 1 | Copiare `progetto-template/` |
| 2 | Aprire la cartella in PHPStorm |
| 3 | Creare `.env` e scrivere schema SQL e seed  |
| 4 | eseguire `docker compose up -d` |
| 5 | Configurare interprete PHP (Docker) |
| 6 | Configurare connessione database |
| 7 | `composer install` nel container |
| 8 | Sviluppare in `www/` |

---

## Riferimenti

- Documentazione Docker: [docs.docker.com](https://docs.docker.com)
- PHPStorm + Docker: [jetbrains.com/help/phpstorm](https://www.jetbrains.com/help/phpstorm/)
- Slim Framework: [slimframework.com](https://www.slimframework.com)
- Composer: [getcomposer.org](https://getcomposer.org)

---

# Fine

