<?php
require 'dbAccess.php';
session_start();

// Regex per validazione
$regex_username = '/^(?=.*[a-zA-Z])[a-zA-Z0-9_]{5,}$/';
$regex_password = '/^(?=.*\d).{7,}$/';
$regexEmail = '/^[A-z0-9\.\+_-]+@[A-z0-9\._-]+\.[A-z]{2,6}$/';

// Inizializza gli errori
$errors = [
    'username' => '',
    'email' => '',
    'password' => '',
    'conferma_password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validazione campi vuoti
    $campi_richiesti = ['username', 'email', 'password', 'conferma_password'];
    foreach ($campi_richiesti as $campo) {
        if (empty($_POST[$campo])) {
            $errors[$campo] = 'Campo obbligatorio';
        }
    }

    // Validazione formato username
    if (empty($errors['username']) && !preg_match($regex_username, $_POST['username'])) {
        $errors['username'] = 'Min 5 caratteri di cui una lettera';
    }

    // Validazione email
    if (empty($errors['email']) && !preg_match($regexEmail, $_POST['email'])) {
        $errors['email'] = 'Inserisci un\'email valida';
    }

    // Validazione password
    if (empty($errors['password']) && !preg_match($regex_password, $_POST['password'])) {
        $errors['password'] = 'Min 7 caratteri con almeno 1 numero';
    }

    // Verifica corrispondenza password
    if (empty($errors['password']) && $_POST['password'] !== $_POST['conferma_password']) {
        $errors['conferma_password'] = 'Le password non coincidono';
    }

    // Se non ci sono errori di formato, controlla username/email esistenti
    if (empty(array_filter($errors))) {
        try {
            $pdo = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Controlla username esistente
            $stmt = $pdo->prepare("SELECT id FROM utenti WHERE username = :user");
            $stmt->bindValue(':user', $_POST['username']);
            $stmt->execute();
            if ($stmt->fetch()) {
                $errors['username'] = 'Username già in uso';
            }

            // Controlla email esistente
            $stmt = $pdo->prepare("SELECT id FROM utenti WHERE email = :email");
            $stmt->bindValue(':email', $_POST['email']);
            $stmt->execute();
            if ($stmt->fetch()) {
                $errors['email'] = 'Email già registrata';
            }

            // Se tutto ok, registra l'utente
            if (empty(array_filter($errors))) {
                $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO utenti (username, email, password_hash) VALUES (:user, :email, :pw)");
                $stmt->bindValue(':user', $_POST['username']);
                $stmt->bindValue(':email', $_POST['email']);
                $stmt->bindValue(':pw', $password_hash);
                $stmt->execute();

                $_SESSION['show_success'] = true;
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Errore database: " . $e->getMessage());
            $errors['password'] = 'Errore durante la registrazione';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Prenotazione Sale Studio</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/registrazione.css">
    <script src="../js/registrazione.js" defer></script>
</head>
<body>
    <div class="registrazione-container">
        <h1>Registrazione</h1>
        
        <form id="form-registrazione" method="POST" novalidate>
  
            <!-- === USERNAME === -->
            <?php $err = $errors['username'] ?? ''; ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input
                type="text"
                id="username"
                name="username"
                required
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                <?php if ($err): // se c’è errore, bordo rosso ?>
                    class="error"
                <?php endif; ?>
                >
                <!--
                Il 'small' è SEMPRE display:block (dal CSS).
                Se $err è vuoto, dentro ci sarà solo stringa vuota.
                Se c’è testo, verrà mostrato.
                -->
                <small class="error-message" id="username-error">
                <?= htmlspecialchars($err) ?>
                </small>
            </div>

            <!-- === EMAIL === -->
            <?php $err = $errors['email'] ?? ''; ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input
                type="email"
                id="email"
                name="email"
                required
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                <?php if ($err): ?>
                    class="error"
                <?php endif; ?>
                >
                <small class="error-message" id="email-error">
                <?= htmlspecialchars($err) ?>
                </small>
            </div>

            <!-- === PASSWORD === -->
            <?php $err = $errors['password'] ?? ''; ?>
            <div class="form-group">
                <label for="password">Password</label>
                <input
                type="password"
                id="password"
                name="password"
                required
                <?php if ($err): ?>
                    class="error"
                <?php endif; ?>
                >
                <small class="error-message" id="password-error">
                <?= htmlspecialchars($err) ?>
                </small>
            </div>

            <!-- === CONFERMA PASSWORD === -->
            <?php $err = $errors['conferma_password'] ?? ''; ?>
            <div class="form-group">
                <label for="conferma_password">Conferma Password</label>
                <input
                type="password"
                id="conferma_password"
                name="conferma_password"
                required
                <?php if ($err): ?>
                    class="error"
                <?php endif; ?>
                >
                <small class="error-message" id="conferma_password-error">
                <?= htmlspecialchars($err) ?>
                </small>
            </div>

            <button type="submit" class="btn-registrati">Registrati</button>
        </form>
        
        <div class="login-link">
            Hai già un account? <a href="index.php">Accedi</a>
        </div>
    </div>

    <footer>
        <small>
            Corso Progettazione Web - Anno Accademico 2024/2025
        </small>
    </footer>


    <!-- Spiegazione flusso 
    Quando viene inviato il form (POST):
- Fase di validazione dei campi, tramite query al DB,
  in caso mostro errori (vuoto, non rispetta la forma, già presente...).
  La pagine viene ricaricata mostrando gli errori, mantenendo i valori inseriti e rimuovendo la password. (rimosso 'value').
    
    $err = $errors['campo'] ?? '';
– Prendo il messaggio d’errore (oppure stringa vuota se non esiste).

    php if ($err): ?> class="error" ...
– Se $err non è vuoto, aggiungo la classe error all’<input>, che col bordo rosso è già gestita dal CSS.

    <small class="error-message">…< htmlspecialchars($err) ?></small>
– Il tag <small> è sempre display:block (dal CSS).
– Se $err è vuoto, non c’è alcun testo e quindi non si vede niente.
– Se c’è testo, lo vediamo in rosso sotto l’<input>.

    Nel frattempo JS, durante la digitazione (input):
- rimuove la class='error' e pulisce messaggio di errore presente.

    Se tutto ok:
- Imposta token 'show_success' che mostra messaggio nella pagina di login.
-->

</body>
</html>
