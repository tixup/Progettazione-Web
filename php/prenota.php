<?php
session_start();
require 'dbAccess.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

/* Validazione input di base */
// Verifica che tutti i parametri necessari siano presenti prima di procedere
if (empty($input['sala_id']) || empty($input['data']) || empty($input['orario'])) {
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* Verifica prenotazioni esistenti */
    // Controlla numero di prenotazioni esistenti per quella data e orario di quell'utente
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM prenotazioni 
        WHERE id_utente = :user_id 
        AND id_sala = :sala_id 
        AND data_prenotazione = :data 
        AND orario = :orario
    ");
    
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':sala_id' => $input['sala_id'],
        ':data' => $input['data'],
        ':orario' => $input['orario']
    ]);
    
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing['count'] > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Hai già una prenotazione per questa sala nella stessa fascia oraria'
        ]);
        exit();
    }
    
    /*  Verifica disponibilità con transazione */
    $pdo->beginTransaction();   // inizia transazione nel database

    // Tutte le operazioni successive saranno atomiche (o tutte riescono o tutte falliscono)
    
    try {
        // Lock della sala per evitare race conditions (prenotazioni temporanee)
        // Garantisce consistenza dei dati
        // Risolve il problema dei posti che non si aggiornavano correttamente
        $stmt = $pdo->prepare("
            SELECT posti_totali 
            FROM sale 
            WHERE id = :sala_id 
            FOR UPDATE
        ");
        $stmt->execute([':sala_id' => $input['sala_id']]);
        $sala = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sala) {
            throw new Exception('Sala non trovata');
        }
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as prenotati
            FROM prenotazioni
            WHERE id_sala = :sala_id
            AND data_prenotazione = :data
            AND orario = :orario
        ");
        $stmt->execute([
            ':sala_id' => $input['sala_id'],
            ':data' => $input['data'],
            ':orario' => $input['orario']
        ]);
        $prenotati = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $posti_disponibili = $sala['posti_totali'] - $prenotati['prenotati'];
        
        if ($posti_disponibili <= 0) {
            throw new Exception('Posti esauriti per questa data/orario');
        }
        
        // 3. Crea prenotazione
        $stmt = $pdo->prepare("
            INSERT INTO prenotazioni (id_utente, id_sala, data_prenotazione, orario)
            VALUES (:user_id, :sala_id, :data, :orario)
        ");
        
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':sala_id' => $input['sala_id'],
            ':data' => $input['data'],
            ':orario' => $input['orario']
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Prenotazione effettuata con successo',
            'updated_availability' => $posti_disponibili - 1
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Errore database: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/* 
Esempio di Flusso:

1. Riceve i dati (es. sala 11, 2025-04-14, pomeriggio)
2. Verifica se esiste già una prenotazione per quell'utente

Se non esiste:
- Blocca la sala (FOR UPDATE)
- Calcola i posti disponibili

Se disponibili:
- Inserisce la prenotazione
- Rilascia il lock (commit)
- Restituisce successo -> messaggio di prenotazione confermata

Se non disponibili:
- Annulla (rollback)
- Restituisce errore -> messaggio di prenotazione annullata

*/

?>