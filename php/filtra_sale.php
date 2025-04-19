<?php
require 'dbAccess.php';

header('Content-Type: application/json');

// Leggi l'input JSON
$json = file_get_contents('php://input');
// Decodifica il JSON in array PHP
$filters = json_decode($json, true);

try {
    $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);

    // Validazione input obbligatori (data e orario)
    if (empty($filters['data'])) {
        throw new Exception("La data è obbligatoria");
    }
    
    if (empty($filters['orario'])) {
        throw new Exception("L'orario è obbligatorio");
    }
    
    $data = $filters['data'];
    $orario = $filters['orario'];
    $attrezzature = isset($filters['attrezzature']) ? $filters['attrezzature'] : '';
    $soloDisponibili = !empty($filters['disponibili']);

    // Query che calcola i posti disponibili per la data e orario specifici
    $query = "SELECT 
                s.id, 
                s.nome, 
                s.immagine, 
                s.attrezzature, 
                s.posti_totali,
                (s.posti_totali - IFNULL((
                    SELECT COUNT(*) 
                    FROM prenotazioni p 
                    WHERE p.id_sala = s.id 
                    AND p.data_prenotazione = :data 
                    AND p.orario = :orario
                ), 0)) AS posti_disponibili
              FROM sale s
              WHERE 1=1";
    
    $params = [
        ':data' => $data,
        ':orario' => $orario
    ];

    // Filtro per attrezzature
    if (!empty($attrezzature)) {
        $query .= " AND s.attrezzature LIKE :attrezzature";
        $params[':attrezzature'] = "%$attrezzature%";
    }

    // mostra tutti gli id delle sale trovate
    $query .= " GROUP BY s.id";

    // Filtro per disponibilità
    if ($soloDisponibili) {
        $query .= " HAVING posti_disponibili > 0";
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sale = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sale);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['errore' => 'Errore DB: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['errore' => $e->getMessage()]);
}
?>