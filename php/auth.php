<?php
require 'dbAccess.php';
session_start();

$error = false;
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $connectionString = "mysql:host=".DBHOST.";dbname=".DBNAME;
        $pdo = new PDO($connectionString, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerca l'utente per username
        $sql = "SELECT id, username, password_hash, is_admin FROM utenti WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica credenziali
        if ($result && password_verify($password, $result['password_hash'])) {
            // Login riuscito
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['is_admin'] = $result['is_admin'];
            
            header("Location: home.php");
            exit();
        } else {
            // Login fallito - reindirizza alla login con errore
            $_SESSION['login_error'] = true;
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Errore durante il login: " . $e->getMessage());
        $_SESSION['login_error'] = 'Errore di sistema. Riprova più tardi.';
        header("Location: index.php");
        exit();
    }
}

// Se si arriva qui senza POST, reindirizza
header("Location: index.php");
exit();
?>