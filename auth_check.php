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
                
                // Re-hidrata os dados do perfil do usuário
                $stmt_u = $pdo->prepare("SELECT id, nome, nivel FROM usuarios WHERE email = ? AND status = 1");
                $stmt_u->execute([$user['email']]);
                $u_data = $stmt_u->fetch();
                
                if ($u_data) {
                    $_SESSION['user_id']    = $u_data['id'];
                    $_SESSION['user_name']  = $u_data['nome'];
                    $_SESSION['user_level'] = trim($u_data['nivel']);
                } else {
                    // Fallback para usuários SEFA que não estão na tabela 'usuarios'
                    $_SESSION['user_id']    = 0;
                    $_SESSION['user_name']  = explode('@', $user['email'])[0];
                    $_SESSION['user_level'] = 'Consultor';
                }
            } else {
                setcookie('gestorgov_session', '', time() - 3600, "/");
                // Determina o caminho para a raiz (index.php)
                $redirect_root = (basename(dirname($_SERVER['PHP_SELF'])) === 'app-contratos') ? '../index.php' : 'index.php';
                header("Location: $redirect_root?error=expired");
                exit;
            }
        } catch (PDOException $e) {
            $redirect_root = (basename(dirname($_SERVER['PHP_SELF'])) === 'app-contratos') ? '../index.php' : 'index.php';
            header("Location: $redirect_root?error=db");
            exit;
        }
    } else {
        $redirect_root = (basename(dirname($_SERVER['PHP_SELF'])) === 'app-contratos') ? '../index.php' : 'index.php';
        header("Location: $redirect_root?error=unauthorized");
        exit;
    }
}
?>
