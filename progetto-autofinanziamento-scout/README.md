# Autofinanziamento scout — dashboard prodotti

Progetto didattico per mostrare come costruire un'applicazione SSR con PHP,
Slim, Plates e PDO, usando Docker per avviare Apache/PHP e MariaDB.

La prima iterazione comprende un catalogo pubblico e una dashboard CRUD
riservata agli amministratori per i prodotti venduti dal gruppo scout.
Gli utenti anonimi possono consultare i prodotti, mentre solo un utente con
ruolo amministratore può modificare il catalogo e il magazzino.

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

Il catalogo pubblico è disponibile su /prodotti. La dashboard è protetta e
si raggiunge da /login. L'account amministratore di prova è:

    username: admin
    password: admin123

La password è presente solo per le esercitazioni locali: deve essere cambiata
prima di qualsiasi utilizzo reale.

Sono disponibili anche due clienti di prova, con password `admin123`:
`mario.rossi` e `anna.bianchi`. Un cliente autenticato può prenotare una
quantità disponibile di un prodotto e consultare o annullare le proprie
prenotazioni da /prenotazioni. L'annullamento ripristina il magazzino.

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

Se il progetto era già stato avviato prima dell'aggiunta della tabella utenti,
è necessario ricreare il volume del database con docker compose down -v;
gli script di inizializzazione non vengono rieseguiti su un volume già popolato.

Lo stesso vale per le tabelle e i dati dello step 2: dopo aver modificato gli
script SQL, usare `docker compose down -v` e poi `docker compose up -d` in un
ambiente didattico locale.

Gli utenti e i ruoli sono gestiti da UserRepository; AuthMiddleware protegge le
route amministrative sotto /admin.

### Aggiornare l'autoload dopo aver aggiunto nuove classi

Quando si aggiunge una nuova classe o un nuovo namespace al file
www/composer.json, bisogna rigenerare l'autoloader Composer:

    docker exec scout-prodotti_web composer dump-autoload

In alternativa, al primo avvio o dopo aver modificato le dipendenze:

    docker exec scout-prodotti_web composer install

Se compare un errore come Class Middleware\AuthMiddleware not found, eseguire
uno dei due comandi sopra e ricaricare la pagina.

La cartella `www/public/` è il DocumentRoot e contiene il front-controller.
Le route Slim chiamano `ProductController`, che usa `ProductRepository` per
le query PDO e `View` per renderizzare i template Plates. Le immagini vengono
salvate nel volume Docker montato in `public/uploads/`.

Gli step progettuali sono tracciati in [`docs/step.md`](docs/step.md), così il
repository può diventare la base per il documento finale rivolto agli studenti.
