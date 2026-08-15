-- Account didattico: username admin, password admin123.
-- Cambiare la password prima di usare il progetto in un ambiente reale.
INSERT INTO utenti (username, password, ruolo, nome) VALUES
    ('admin', '$2y$10$OJdqC5xuz09fJK3WrV7wZOsbgLMrHT2iqaKEPHOmH6li/Hu6wYm/.', 'amministratore', 'Amministratore');

INSERT INTO prodotti (nome, descrizione, prezzo, quantita) VALUES
    ('Biscotti fatti in casa', 'Confezione di biscotti preparati dal gruppo scout.', 4.50, 18),
    ('Calendario scout', 'Calendario annuale con le fotografie delle attività.', 8.00, 12),
    ('Tazza del gruppo', 'Tazza in ceramica con il simbolo del gruppo.', 7.50, 6);
