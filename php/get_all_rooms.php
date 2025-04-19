<?php

// Restituisce tutte le sale senza filtri (Home iniziale)

require 'dbAccess.php';

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
    
    $stmt = $pdo->query("SELECT id, nome, immagine, posti_totali, attrezzature FROM sale");
    $sale = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($sale);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['errore' => 'Errore DB: ' . $e->getMessage()]);
}
?>