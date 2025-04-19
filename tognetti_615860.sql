-- Elimina il database se esiste già
DROP DATABASE IF EXISTS tognetti_615860;

-- Crea il database
CREATE DATABASE tognetti_615860;
USE tognetti_615860;

-- Tabella Utenti
CREATE TABLE utenti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    data_registrazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT FALSE
);

-- Tabella Sale
CREATE TABLE sale (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    posti_totali INT NOT NULL,
    attrezzature TEXT,
    immagine VARCHAR(255)
);

-- Tabella Prenotazioni
CREATE TABLE prenotazioni (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_utente INT NOT NULL,
    id_sala INT NOT NULL,
    data_prenotazione DATE NOT NULL,
    orario VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_utente) REFERENCES utenti(id),
    FOREIGN KEY (id_sala) REFERENCES sale(id)
);

-- Tabella Recensioni
CREATE TABLE recensioni (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_utente INT NOT NULL,
    id_sala INT NOT NULL,
    voto INT CHECK (voto BETWEEN 1 AND 5),
    commento TEXT,
    data_recensione DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utente) REFERENCES utenti(id),
    FOREIGN KEY (id_sala) REFERENCES sale(id)
);