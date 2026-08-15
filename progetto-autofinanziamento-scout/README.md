# Autofinanziamento scout — dashboard prodotti

Progetto didattico per mostrare come costruire un'applicazione SSR con PHP,
Slim, Plates e PDO, usando Docker per avviare Apache/PHP e MariaDB.

La prima iterazione è una dashboard CRUD pubblica per i prodotti venduti dal
gruppo scout. Non è ancora presente autenticazione: chiunque raggiunga il sito
può creare, modificare ed eliminare prodotti. Questa è una scelta intenzionale
per introdurre un concetto alla volta.

## Avvio rapido

1. Copia `.env.example` in `.env` e, se serve, modifica le porte.
2. Assicurati di avere costruito l'immagine condivisa `didattica-php:latest`.
3. Avvia i servizi:

   ```bash
   docker compose up -d
   docker exec scout-prodotti_web composer install
   ```

4. Apri [http://localhost:9081](http://localhost:9081). Adminer è disponibile
   su [http://localhost:8081](http://localhost:8081).

Durante l'avvio il container assegna automaticamente la cartella degli upload
all'utente `www-data`, cioè l'utente con cui Apache esegue PHP. Questo passaggio
è necessario perché `storage_data` è un volume Docker nominato e, alla prima
creazione, può appartenere a `root`. Senza questo allineamento un upload genera
`Upload target path is not writable`.

Se il container era già in esecuzione dopo una modifica al Compose, ricrealo:

```bash
docker compose up -d --force-recreate web
```

Per controllare i permessi dall'interno del container:

```bash
docker exec scout-prodotti_web ls -ld /var/www/html/public/uploads
```

Il proprietario atteso è `www-data www-data` e la cartella dovrebbe avere
permessi almeno `drwxrwxr-x`.

Il database viene creato dagli script in `db-init/` solo quando il volume è
vuoto. Per ripartire dai dati iniziali: `docker compose down -v` e poi
`docker compose up -d`.

## Struttura didattica

La cartella `www/public/` è il DocumentRoot e contiene il front-controller.
Le route Slim chiamano `ProductController`, che usa `ProductRepository` per
le query PDO e `View` per renderizzare i template Plates. Le immagini vengono
salvate nel volume Docker montato in `public/uploads/`.

Gli step progettuali sono tracciati in [`docs/step.md`](docs/step.md), così il
repository può diventare la base per il documento finale rivolto agli studenti.
