<?php
// auth_check.php na raiz
require_once __DIR__ . '/config.php'; // config.php já inicia a sessão com parâmetros globais

// Verifica Sessão
if (!isset($_SESSION['user_email'])) {
    
    // Verifica se existe Cookie de Longa Duração (30 dias) para auto-login
    if (isset($_COOKIE['gestorgov_session'])) {
        $cookie_token = $_COOKIE['gestorgov_session'];
        
        try {
            // Re-valida o token no banco de dados
            $stmt = $pdo->prepare("SELECT email FROM login_tokens WHERE token = ? AND expires_at > NOW() LIMIT 1");
            $stmt->execute([$cookie_token]);
            $token_data = $stmt->fetch();
            
            if ($token_data) {
                $user_email = $token_data['email'];
                
                // Re-hidrata os dados do perfil do usuário
                $stmt_u = $pdo->prepare("SELECT id, nome, nivel FROM usuarios WHERE email = ? AND status = 1");
                $stmt_u->execute([$user_email]);
                $u_data = $stmt_u->fetch();
                
                if ($u_data) {
                    $_SESSION['user_email'] = $user_email;
                    $_SESSION['user_id']    = $u_data['id'];
                    $_SESSION['user_name']  = $u_data['nome'];
                    
                    // Normaliza para 'Administrador', 'Gestor' ou 'Consultor'
                    $raw_level = strtolower(trim($u_data['nivel']));
                    if ($raw_level === 'administrador') $_SESSION['user_level'] = 'Administrador';
                    elseif ($raw_level === 'gestor') $_SESSION['user_level'] = 'Gestor';
                    else $_SESSION['user_level'] = 'Consultor';
                } else {
                    // Usuário SEFA (externo) - Re-hidratação mínima
                    $_SESSION['user_email'] = $user_email;
                    $_SESSION['user_id']    = 0;
                    $_SESSION['user_name']  = explode('@', $user_email)[0];
                    $_SESSION['user_level'] = 'Consultor';
                }
            } else {
                // Token inválido ou expirado no banco
                setcookie('gestorgov_session', '', time() - 3600, "/");
                $redirect_root = (basename(dirname($_SERVER['PHP_SELF'])) === 'app-contratos') ? '../index.php' : 'index.php';
                header("Location: $redirect_root?error=session_lost");
                exit;
            }
        } catch (PDOException $e) {
            $redirect_root = (basename(dirname($_SERVER['PHP_SELF'])) === 'app-contratos') ? '../index.php' : 'index.php';
            header("Location: $redirect_root?error=db");
            exit;
        }
    } else {
        // Nenhuma sessão nem cookie -> Login necessário
        $redirect_root = (basename(dirname($_SERVER['PHP_SELF'])) === 'app-contratos') ? '../index.php' : 'index.php';
        header("Location: $redirect_root?error=unauthorized");
        exit;
    }
}
?>
