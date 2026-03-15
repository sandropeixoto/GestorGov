<?php
/**
 * sso_redirect.php - Handler de Redirecionamento Seguro para Módulos Externos
 * Atua como o Provedor de Identidade (IdP) do ecossistema GestorGov.
 */
require_once 'auth_check.php';
require_once 'config.php';

$module_id = $_GET['id'] ?? null;

if (!$module_id) {
    header("Location: home.php?error=module_not_found");
    exit;
}

try {
    // Busca o módulo no banco
    $stmt = $pdo->prepare("SELECT * FROM launcher_modules WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$module_id]);
    $module = $stmt->fetch();

    if (!$module) {
        header("Location: home.php?error=invalid_module");
        exit;
    }

    // Se não for externo, redireciona direto
    if (!$module['is_external']) {
        header("Location: " . $module['url']);
        exit;
    }

    // --- GERAÇÃO DO TOKEN SSO ---
    // Dados que serão passados para o outro sistema
    $payload = [
        'user_id'    => $_SESSION['user_id']    ?? 0,
        'user_name'  => $_SESSION['user_name']  ?? 'Usuário',
        'user_email' => $_SESSION['user_email'] ?? '',
        'user_level' => $_SESSION['user_level'] ?? 0,
        'iat'        => time(),                 // Issued At
        'exp'        => time() + 60             // Expira em 60 segundos (tempo para o handshake)
    ];

    // Serializa e codifica em Base64
    $payload_json = json_encode($payload);
    $payload_base64 = base64_encode($payload_json);

    // Gera a assinatura HMAC usando a chave secreta compartilhada
    $signature = hash_hmac('sha256', $payload_base64, SSO_SECRET_KEY);

    // Monta a URL de destino com o token e a assinatura
    $sep = (strpos($module['url'], '?') === false) ? '?' : '&';
    $redirect_url = $module['url'] . $sep . "sso_payload=" . urlencode($payload_base64) . "&sso_sig=" . $signature;

    // Log do redirecionamento
    logSistema($pdo, 'SSO', 'Redirect to External Module', 'launcher_modules', $module_id, ['target' => $module['title']]);

    // Redireciona
    header("Location: " . $redirect_url);
    exit;

} catch (PDOException $e) {
    die("Erro no sistema de SSO: " . $e->getMessage());
}
