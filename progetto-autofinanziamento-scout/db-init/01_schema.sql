-- Utenti dell'applicazione.
CREATE TABLE IF NOT EXISTS utenti
(
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(190) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    ruolo      VARCHAR(30) NOT NULL DEFAULT 'cliente',
    nome       VARCHAR(120) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ruolo_valido CHECK (ruolo IN ('amministratore', 'cliente'))
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Prodotti venduti per l'autofinanziamento del gruppo scout.
CREATE TABLE IF NOT EXISTS prodotti
(
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(120) NOT NULL,
    descrizione     VARCHAR(255) NOT NULL,
    immagine        VARCHAR(255) NULL,
    prezzo          DECIMAL(10, 2) NOT NULL,
    quantita        INT UNSIGNED NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT prezzo_non_negativo
        CHECK (prezzo >= 0),
    CONSTRAINT quantita_non_negativa
        CHECK (quantita >= 0)
) CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Ogni riga rappresenta la prenotazione di un solo prodotto.
CREATE TABLE IF NOT EXISTS prenotazioni
(
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id      INT UNSIGNED NOT NULL,
    prodotto_id     INT UNSIGNED NOT NULL,
    quantita        INT UNSIGNED NOT NULL,
    stato           VARCHAR(20) NOT NULL DEFAULT 'in_attesa',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    annullata_at    DATETIME NULL,
    CONSTRAINT fk_cliente
        FOREIGN KEY (cliente_id) REFERENCES utenti(id),
    CONSTRAINT fk_prodotto
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    CONSTRAINT quantita_positiva
        CHECK (quantita > 0),
    CONSTRAINT stato_valido
        CHECK (stato IN ('in_attesa', 'consegnato', 'annullato'))) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
