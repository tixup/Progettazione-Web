<?php
session_start();
require 'dbAccess.php';

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Recupera info utente
try {
    $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
    $stmt = $pdo->prepare("SELECT username, email FROM utenti WHERE id = :id");
    $stmt->bindValue(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore database: " . $e->getMessage());
}

// Recupera tutte le sale con media voti
try {
    $query = "SELECT s.id, s.nome, s.immagine, s.attrezzature, 
              AVG(r.voto) as media_voti, COUNT(r.id) as numero_voti
              FROM sale s
              LEFT JOIN recensioni r ON s.id = r.id_sala
              GROUP BY s.id";
    $stmt = $pdo->query($query);
    $sale = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Errore recupero sale: " . $e->getMessage());
    $sale = [];
}

// Gestione invio voto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['voto'])) {
    try {
        // Verifica se l'utente ha già votato questa sala
        $stmt = $pdo->prepare("SELECT id FROM recensioni 
                              WHERE id_utente = :user_id AND id_sala = :sala_id");
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':sala_id', $_POST['sala_id']);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            // Aggiorna voto esistente
            $stmt = $pdo->prepare("UPDATE recensioni SET voto = :voto 
                                  WHERE id_utente = :user_id AND id_sala = :sala_id");
        } else {
            // Inserisci nuovo voto
            $stmt = $pdo->prepare("INSERT INTO recensioni 
                                 (id_utente, id_sala, voto) 
                                 VALUES (:user_id, :sala_id, :voto)");
        }
        
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':sala_id', $_POST['sala_id']);
        $stmt->bindValue(':voto', $_POST['voto']);
        $stmt->execute();
        
        header("Location: recensioni.php?success=1");
        exit();
    } catch (PDOException $e) {
        error_log("Errore salvataggio voto: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <title>Recensioni - Prenotazione Sale Studio</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/recensioni.css">
</head>
<body>
    <div id="page">
        <header id="header">
            <h1>Recensioni delle sale, <?= htmlspecialchars($user['username']) ?></h1>
        </header>

        <div id="container">
            <!-- Sidebar a sinistra -->
            <aside id="sidebar">
                <div class="profile-card">
                    <h3>Il tuo profilo</h3>
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <a href="home.php" class="btn">Torna alla home</a>
                    <a href="recensioni.php" class="btn">Recensioni</a> <br>
                    <a href="logout.php" class="btn logout">Esci</a>
                </div>
            </aside>

            <main id="main-content">
                <!-- Messaggio -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message">Voto registrato con successo!</div>
                <?php endif; ?>

                <div class="recensioni-container">
                    <!-- Sale con valutazioni -->
                    <h2>Lascia la tua valutazione</h2>
                    
                    <?php if (empty($sale)): ?>
                        <p class="no-sale">Nessuna sala disponibile per la valutazione.</p>
                    <?php else: ?>
                        <div class="saleGrid">
                            <?php foreach ($sale as $sala): ?>
                                <div class="sala-card">
                                    <img src="../img/sale/<?= htmlspecialchars($sala['immagine']) ?>" 
                                         alt="<?= htmlspecialchars($sala['nome']) ?>" 
                                         class="sala-img">
                                    <h3><?= htmlspecialchars($sala['nome']) ?></h3>
                                    <p><?= htmlspecialchars($sala['attrezzature']) ?></p>
                                    
                                    <div class="valutazione">
                                        <?php if ($sala['media_voti']): ?>
                                            <div class="media-voti">
                                                Valutazione media: 
                                                <span class="voto"><?= number_format($sala['media_voti'], 1) ?></span>/5
                                                (<?= $sala['numero_voti'] ?> voti)
                                            </div>
                                        <?php else: ?>
                                            <div class="media-voti">Nessuna valutazione</div>
                                        <?php endif; ?>
                                        
                                        <form method="POST" class="form-voto">
                                            <input type="hidden" name="sala_id" value="<?= $sala['id'] ?>">
                                            <select name="voto" required>
                                                <option value="">Seleziona voto</option>
                                                <option value="1">1 ★</option>
                                                <option value="2">2 ★★</option>
                                                <option value="3">3 ★★★</option>
                                                <option value="4">4 ★★★★</option>
                                                <option value="5">5 ★★★★★</option>
                                            </select>
                                            <button type="submit" class="btn btn-vota">Vota</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <footer>
        <small>
            Corso Progettazione Web - Anno Accademico 2024/2025
        </small>
    </footer>
</body>
</html>
