# Percorso didattico

Questo file tiene traccia delle iterazioni del progetto. Ogni step dovrebbe
introdurre un concetto circoscritto e lasciare l'applicazione funzionante.

## Step 0 — Infrastruttura e template

- [x] Copiare la struttura del progetto-template.
- [x] Avviare Apache/PHP tramite l'immagine condivisa Docker.
- [x] Avviare MariaDB e Adminer tramite Docker Compose.
- [x] Separare il codice pubblico (www/public) dal resto dell'applicazione.

## Step 1 — Autenticazione amministratore e catalogo pubblico

- [x] Definire la tabella prodotti e alcuni dati iniziali.
- [x] Configurare PDO tramite variabili d'ambiente.
- [x] Definire route Slim e controller.
- [x] Implementare il CRUD dei prodotti.
- [x] Aggiungere validazione server-side dei dati.
- [x] Aggiungere upload opzionale di JPG, PNG o WebP.
- [x] Configurare i permessi del volume upload per l'utente Apache www-data.
- [x] Renderizzare l'interfaccia con Plates e Pico.css classless.
- [x] Creare la tabella utenti con ruoli amministratore e cliente.
- [x] Inserire un amministratore di prova con password hashata.
- [x] Implementare login, logout e sessione PHP.
- [x] Proteggere la dashboard con un middleware Slim.
- [x] Spostare la dashboard sotto /admin/prodotti.
- [x] Lasciare pubblico /prodotti, senza esporre la quantità in magazzino.
- [x] Aggiungere protezione CSRF ai form di login, prodotti e logout.
- [ ] Spiegare e aggiungere test automatici.

### Sottopassaggi suggeriti per il materiale didattico

1. Modello utenti: spiegare perché la password viene salvata con un hash e
   perché il ruolo non deve essere scelto dall'utente.
2. Login: leggere i dati, cercare l'utente con PDO, verificare la password con
   password_verify e rigenerare l'identificativo della sessione.
3. Middleware: intercettare le richieste alle route amministrative e
   reindirizzare al login chi non possiede il ruolo corretto.
4. Separazione delle viste: mostrare il catalogo pubblico senza quantita e la
   dashboard completa solo all'amministratore.
5. CSRF: inserire un token nei form e verificarlo prima di ogni scrittura.
6. Verifica manuale: provare catalogo anonimo, login errato, login corretto,
   logout e accesso diretto a /admin/prodotti.

### Dati di prova

- Username amministratore: admin
- Password amministratore: admin123
- Ruolo: amministratore

La password è didattica e deve essere sostituita in un ambiente reale.

## Step 2 — Clienti, ordini e prenotazioni

- [ ] Aggiungere utenti con ruolo cliente e alcuni account di prova.
- [ ] Modellare ordini e righe d'ordine.
- [ ] Definire le regole di disponibilità e prenotazione.
- [ ] Permettere a un cliente autenticato di prenotare prodotti.

## Step 3 — Gestione amministrativa degli ordini

- [ ] Visualizzare gli ordini nella dashboard.
- [ ] Aggiungere filtri, stati e stampe riepilogative.

## Step 4 — Registrazione autonoma del cliente

- [ ] Permettere la registrazione autonoma con ruolo cliente assegnato dal server.
- [ ] Validare username/email e password.
- [ ] Aggiungere eventuale conferma email e recupero password.

## Note per il docente

Prima di passare allo step successivo, si può chiedere agli studenti di
riconoscere il percorso di una richiesta:

browser → Apache → index.php → route Slim → middleware → controller →
repository PDO → database → template Plates.
