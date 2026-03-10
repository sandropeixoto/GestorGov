<?php
// app-contratos/auth_module.php
require_once 'config.php';

// Se por algum motivo o ID não estiver na sessão, tenta recuperar pelo e-mail
if (!isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$_SESSION['user_email']]);
    $u = $stmt->fetch();
    if ($u) $_SESSION['user_id'] = $u['id'];
}

$user_id = $_SESSION['user_id'] ?? 0;
$is_admin = ($_SESSION['user_level'] ?? '') === 'Administrador';

// Busca perfil específico do módulo
$stmt_p = $pdo->prepare("SELECT perfil FROM contratos_permissoes WHERE usuario_id = ?");
$stmt_p->execute([$user_id]);
$perfil_modulo = $stmt_p->fetchColumn();

// Busca configuração de leitura global
$stmt_c = $pdo->prepare("SELECT valor FROM contratos_configuracoes WHERE chave = 'acesso_leitura_global'");
$stmt_c->execute();
$leitura_global = $stmt_c->fetchColumn() === '1';

// Definição de permissões
define('CONTRATOS_ADMIN', $is_admin);
define('CONTRATOS_GESTOR', $is_admin || $perfil_modulo === 'Gestor');
define('CONTRATOS_CONSULTOR', $is_admin || $perfil_modulo === 'Gestor' || $perfil_modulo === 'Consultor');
define('CONTRATOS_LEITOR', CONTRATOS_CONSULTOR || $leitura_global);

// Bloqueio de acesso básico (se não for leitor, não entra no módulo)
if (!CONTRATOS_LEITOR && basename($_SERVER['PHP_SELF']) !== 'index.php') {
    // Permitir index.php para mostrar mensagem de erro amigável se necessário, 
    // mas por enquanto vamos apenas redirecionar para a home geral se não tiver acesso.
    header("Location: ../home.php?error=no_access_contratos");
    exit;
}
?>
