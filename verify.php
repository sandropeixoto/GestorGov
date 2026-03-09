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
    // Busca token não usado e não expirado
    $stmt = $pdo->prepare("SELECT * FROM login_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$token]);
    $result = $stmt->fetch();

    if ($result) {
        // Marca como usado
        $update = $pdo->prepare("UPDATE login_tokens SET used = 1 WHERE id = ?");
        $update->execute([$result['id']]);

        // Busca o nível do usuário na tabela usuarios
        $stmt_user = $pdo->prepare("SELECT nivel, nome FROM usuarios WHERE email = ? AND status = 1");
        $stmt_user->execute([$result['email']]);
        $user_data = $stmt_user->fetch();

        // Define Sessão
        $_SESSION['user_email'] = $result['email'];
        $_SESSION['user_name'] = $user_data['nome'] ?? explode('@', $result['email'])[0];
        $_SESSION['user_level'] = $user_data['nivel'] ?? 'Consultor'; // Default para quem não está no banco
        
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
