<?php
// includes/db.php — Database connection

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Change to your MySQL username
define('DB_PASS', '');            // Change to your MySQL password
define('DB_NAME', 'naturedrop');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="padding:2rem;text-align:center;font-family:sans-serif;">
                    <h2>⚠️ Database Connection Failed</h2>
                    <p>Please check your <code>includes/db.php</code> credentials.</p>
                    <small>' . htmlspecialchars($e->getMessage()) . '</small>
                 </div>');
        }
    }
    return $pdo;
}

// Session start (safe — only starts if not already active)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: is user logged in?
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

// Helper: get current user id
function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

// Helper: get current user name
function currentUserName(): string {
    return $_SESSION['user_name'] ?? 'Guest';
}