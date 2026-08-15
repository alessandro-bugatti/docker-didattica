# Percorso didattico

Questo file tiene traccia delle iterazioni del progetto. Ogni step dovrebbe
introdurre un concetto circoscritto e lasciare l'applicazione funzionante.

## Step 0 — Infrastruttura e template

- [x] Copiare la struttura del progetto-template.
- [x] Avviare Apache/PHP tramite l'immagine condivisa Docker.
- [x] Avviare MariaDB e Adminer tramite Docker Compose.
- [x] Separare il codice pubblico (`www/public`) dal resto dell'applicazione.

## Step 1 — CRUD pubblico dei prodotti (corrente)

- [x] Definire la tabella `prodotti` e alcuni dati iniziali.
- [x] Configurare PDO tramite variabili d'ambiente.
- [x] Definire route Slim e un controller.
- [x] Implementare elenco, inserimento, modifica e cancellazione.
- [x] Aggiungere validazione server-side dei dati.
- [x] Aggiungere upload opzionale di JPG, PNG o WebP.
- [x] Configurare i permessi del volume upload per l'utente Apache `www-data`.
- [x] Renderizzare l'interfaccia con Plates e Pico.css classless.
- [ ] Spiegare e aggiungere test automatici.

## Step 2 — Qualità e sicurezza

- [ ] Aggiungere autenticazione per la dashboard.
- [ ] Aggiungere protezione CSRF ai form.
- [ ] Validare dimensione e contenuto reale delle immagini.
- [ ] Gestire correttamente la cancellazione delle immagini non più usate.
- [ ] Centralizzare messaggi flash e gestione degli errori.

## Step 3 — Area pubblica

- [ ] Creare il catalogo pubblico dei prodotti.
- [ ] Distinguere dashboard e sito per gli acquirenti.
- [ ] Nascondere o evidenziare i prodotti esauriti.

## Step 4 — Ordini

- [ ] Modellare clienti, ordini e righe d'ordine.
- [ ] Implementare il carrello e l'invio dell'ordine.
- [ ] Aggiungere aggiornamento del magazzino durante la conferma.
- [ ] Aggiungere una vista amministrativa degli ordini.

## Note per il docente

Prima di passare allo step successivo, si può chiedere agli studenti di
riconoscere il percorso di una richiesta: browser → Apache → `index.php` →
route Slim → controller → repository PDO → database → template Plates.
