<?php
session_start();
require 'dbAccess.php';

// Se l'utente non è loggato, reindirizza
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Recupera info utente (id)
try {
    $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
    $stmt = $pdo->prepare("SELECT username, email FROM utenti WHERE id = :id");
    $stmt->bindValue(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore database: " . $e->getMessage());
}

// Recupera tutte le sale
$sale = [];
try {
    $stmt = $pdo->query("SELECT id, nome, immagine, posti_totali, attrezzature FROM sale");
    $sale = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Errore recupero sale: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Prenotazione Sale Studio</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <script src="../js/home.js"></script>
</head>
<body>
    <div id="page">
        <!-- Header con benvenuto -->
        <header id="header">
            <h1>Bentornato, <?= htmlspecialchars($user['username']) ?></h1>
        </header>

        <div id="container">
            <!-- Sezione laterale sinistra -->
            <aside id="sidebar">
                <div class="profile-card">
                    <h3>Il tuo profilo</h3>
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <a href="mie_prenotazioni.php" class="btn">Le mie prenotazioni</a>
                    <a href="recensioni.php" class="btn">Recensioni</a> <br>
                    <a href="logout.php" class="btn logout">Esci</a>
                </div>
            </aside>

            <!-- Sezione centrale -->
            <main id="main-content">

                <!-- Filtri -->
                <div class="filters">
                <h2>Filtra le sale</h2>
                <form id="filtri-form">
                    <div class="filter-row">
                        <!-- Data -->
                    <div class="filter-group">
                        <label for="data">Data*</label>
                        <input type="date" id="data" name="data" required>
                    </div>
                        <!-- Ora -->
                    <div class="filter-group">
                        <label for="orario">Orario*</label>
                        <select id="orario" name="orario" required>
                            <option value="">Seleziona orario</option>
                            <option value="mattina">Mattina (8-13)</option>
                            <option value="pomeriggio">Pomeriggio (13-18)</option>
                            <option value="sera">Sera (18-23)</option>
                        </select>
                    </div>
                    
                    <div class="filter-row">
                        <!-- Attrezzature -->
                        <div class="filter-group">
                            <label for="attrezzature">Attrezzature</label>
                            <select id="attrezzature" name="attrezzature">
                                <option value="">Tutte</option>
                                <option value="proiettore">Proiettore</option>
                                <option value="wi-fi">Wi-Fi</option>
                                <option value="prese">Prese elettriche</option>
                            </select>
                        </div>
                        
                        <div class="filter-group" style="justify-content: center;">
                        <!-- Date disponibili -->
                            <label>
                                <input type="checkbox" name="disponibili" id="disponibili">
                                Solo sale disponibili
                            </label>
                        </div>
                    </div>
                        
                        <button type="submit" class="btn">Applica filtri</button>
                    </form>
                </div>

                <!-- Lista sale -->
                <div class="sale-list">
                    <h2>Sale disponibili</h2>
                    <div class="saleGrid">
                        <?php foreach ($sale as $sala): ?>
                        <div class="sala-card">
                            <img src="../img/sale/<?= htmlspecialchars($sala['immagine']) ?>" 
                                alt="<?= htmlspecialchars($sala['nome']) ?>" 
                                class="sala-img">
                            <h3><?= htmlspecialchars($sala['nome']) ?></h3>
                            <p>Posti totali: <?= $sala['posti_totali'] ?></p>
                            <p>Attrezzature: <?= htmlspecialchars($sala['attrezzature']) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
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
