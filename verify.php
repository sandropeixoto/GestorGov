<?php
// verify.php na raiz
require_once 'config.php';
session_start();

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: index.php?error=invalid_token");
    exit;
}

try {
    // Busca token que não expirou (mesmo que já usado recentemente, para lidar com pre-fetch de antivírus)
    // Se used=1 mas created_at (ou um campo de last_used) for muito recente, podemos permitir.
    // Como não temos um last_used, vamos permitir tokens não expirados que foram criados há menos de 10 minutos mesmo se used=1.
    
    $stmt = $pdo->prepare("
        SELECT * FROM login_tokens 
        WHERE token = ? 
        AND expires_at > NOW()
        AND (used = 0 OR (used = 1 AND created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)))
    ");
    $stmt->execute([$token]);
    $result = $stmt->fetch();

    if ($result) {
        // Marca como usado (se ainda não estava)
        if ($result['used'] == 0) {
            $update = $pdo->prepare("UPDATE login_tokens SET used = 1 WHERE id = ?");
            $update->execute([$result['id']]);
        }

        // Busca o nível do usuário na tabela usuarios
        $stmt_user = $pdo->prepare("SELECT id, nivel, nome FROM usuarios WHERE email = ? AND status = 1");
        $stmt_user->execute([$result['email']]);
        $user_data = $stmt_user->fetch();

        // Define Sessão
        $_SESSION['user_id'] = $user_data['id'] ?? null;
        $_SESSION['user_email'] = $result['email'];
        $_SESSION['user_name'] = $user_data['nome'] ?? explode('@', $result['email'])[0];
        
        // Mapeamento Robusto: Suporta tanto o Nome quanto o ID numérico do nível
        $raw_level = strtolower(trim($user_data['nivel'] ?? 'Consultor'));

        // DEBUG: Log raw level to console
        echo "<script>console.log('DEBUG: Raw Level from DB:', '" . ($user_data['nivel'] ?? 'NULL') . "'); console.log('DEBUG: Sanitized Level:', '" . $raw_level . "');</script>";

        if ($raw_level === 'administrador' || $raw_level === '1') {
            $_SESSION['user_level'] = 'Administrador';
        } elseif ($raw_level === 'gestor' || $raw_level === '2') {
            $_SESSION['user_level'] = 'Gestor';
        } else {
            $_SESSION['user_level'] = 'Consultor';
        }
        logSistema($pdo, 'Portal', 'Login Success', 'usuarios', $_SESSION['user_id']);
        
        // Define Cookie de 30 dias para persistência (Segurança: HttpOnly)
        setcookie('gestorgov_session', $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);

        header("Location: home.php");
        exit;
    } else {
        header("Location: index.php?error=expired_or_invalid");
        exit;
    }

} catch (PDOException $e) {
    die("Erro de autenticação.");
}
?>
