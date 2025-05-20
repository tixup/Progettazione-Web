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

// Recupera prenotazioni utente con info sala
try {
    $query = "SELECT p.id, s.nome as sala, p.data_prenotazione, p.orario 
              FROM prenotazioni p 
              JOIN sale s ON p.id_sala = s.id 
              WHERE p.id_utente = :id 
              ORDER BY p.data_prenotazione DESC";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':id', $_SESSION['user_id']);
    $stmt->execute();
    $prenotazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Errore recupero prenotazioni: " . $e->getMessage());
    $prenotazioni = [];
}

// Gestione cancellazione prenotazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancella_prenotazione'])) {
    try {
        // Verifica che la prenotazione sia futura
        $stmt = $pdo->prepare("SELECT data_prenotazione FROM prenotazioni WHERE id = :id AND id_utente = :user_id");
        $stmt->bindValue(':id', $_POST['prenotazione_id']);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $prenotazione = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prenotazione && strtotime($prenotazione['data_prenotazione']) > time()) {
            $stmt = $pdo->prepare("DELETE FROM prenotazioni WHERE id = :id");
            $stmt->bindValue(':id', $_POST['prenotazione_id']);
            $stmt->execute();
            header("Location: mie_prenotazioni.php?success=1");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Errore cancellazione prenotazione: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <title>Le mie prenotazioni</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/prenotazioni.css">
</head>
<body>
    <div id="page">
        <header id="header">
            <h1>Le tue prenotazioni, <?= htmlspecialchars($user['username']) ?></h1>
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
                    <div class="success-message">Prenotazione cancellata con successo!</div>
                <?php endif; ?>

                <div class="prenotazioni-container">
                    <!-- Lista prenotazioni sale -->
                    <h2>Storico prenotazioni</h2>
                    
                    <?php if (empty($prenotazioni)): ?>
                        <p class="no-prenotazioni">Non hai ancora effettuato prenotazioni.</p>

                    <?php else: ?>
                        <div class="prenotazioni-list">
                            <?php foreach ($prenotazioni as $prenotazione): ?>
                                <div class="prenotazione-card <?= strtotime($prenotazione['data_prenotazione']) < time() ? 'passata' : '' ?>">
                                    <div class="prenotazione-info">
                                        <h3><?= htmlspecialchars($prenotazione['sala']) ?></h3>
                                        <p>Data: <?= date('d/m/Y', strtotime($prenotazione['data_prenotazione'])) ?></p>
                                        <p>Orario: <?= htmlspecialchars($prenotazione['orario']) ?></p>
                                    </div>
                                    
                                    <?php if (strtotime($prenotazione['data_prenotazione']) > time()): ?>
                                        <form method="POST" class="prenotazione-actions">
                                            <input type="hidden" name="prenotazione_id" value="<?= $prenotazione['id'] ?>">
                                            <button type="submit" name="cancella_prenotazione" class="btn btn-cancella">Cancella</button>
                                        </form>
                                    <?php endif; ?>
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
