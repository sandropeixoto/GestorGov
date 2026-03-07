<?php
// auth_check.php na raiz
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

// Verifica Sessão
if (!isset($_SESSION['user_email'])) {
    
    // Verifica se existe Cookie de Longa Duração (30 dias)
    if (isset($_COOKIE['gestorgov_session'])) {
        $cookie_token = $_COOKIE['gestorgov_session'];
        
        try {
            // Re-valida o token no banco (mesmo usado para login, ou poderia ser um refresh token dedicado)
            // Aqui buscamos o registro mais recente para aquele e-mail que ainda não expirou
            $stmt = $pdo->prepare("SELECT email FROM login_tokens WHERE token = ? AND expires_at > NOW() LIMIT 1");
            $stmt->execute([$cookie_token]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_email'] = $user['email'];
            } else {
                setcookie('gestorgov_session', '', time() - 3600, "/");
                header("Location: /index.php?error=expired");
                exit;
            }
        } catch (PDOException $e) {
            header("Location: /index.php?error=db");
            exit;
        }
    } else {
        header("Location: /index.php?error=unauthorized");
        exit;
    }
}
?>
