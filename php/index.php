<?php
session_start();

// Mostra messaggio 'autentication success' solo una volta
$showSuccess = false;
if (isset($_SESSION['show_success'])) {
    $showSuccess = true;
    // Rimuove il flag dopo l'uso
    // Per non mostrare messaggio ogni aggiornamento pagina
    unset($_SESSION['show_success']); 
}

// Gestione errori di login
$login_error = false;
if (isset($_SESSION['login_error'])) {
    $login_error = true;
    // Cancella il flag dopo averlo mostrato
    unset($_SESSION['login_error']); 
}

// prendo username
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <title>Prenotazione Sale Studio</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">
    <script src="../js/auth.js" defer></script>
</head>
<body>
    <div id="center">
        <div class="login-container">

            <?php if ($showSuccess): ?>
                <div class="success-message">
                    Registrazione avvenuta con successo!
                </div>
            <?php endif; ?>

            <?php if ($login_error): ?>
                <div class="error-login">
                    Username o Password non validi
                </div>
            <?php endif; ?>

            <h2>Prenotazione Sale Studio</h2>
            <form id="form-login" action="auth.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Accedi</button>
            </form>
            <div class="links">
                <p>Non hai un account? 
                <a href="registrazione.php">Fai click qui per crearne uno</a> </p>
                <a href="../Guida.html">Guida</a>
            </div>
        </div>
    </div>
    <footer>
        <small>
            Corso Progettazione Web - Anno Accademico 2024/2025
        </small>
    </footer>
</body>
</html>
