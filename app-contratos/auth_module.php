<?php
// app-contratos/auth_module.php
require_once 'config.php'; // Inicia sessão globalmente

// A re-hidratação agora é feita centralmente no auth_check.php da raiz,
// que é incluído no header.php antes deste arquivo.

$user_id = $_SESSION['user_id'] ?? 0;
$user_level = $_SESSION['user_level'] ?? '';
$is_admin = $user_level === 'Administrador';

// Busca perfil específico do módulo (apenas se tiver um user_id válido)
$perfil_modulo = null;
if ($user_id > 0) {
    $stmt_p = $pdo->prepare("SELECT perfil FROM contratos_permissoes WHERE usuario_id = ?");
    $stmt_p->execute([$user_id]);
    $perfil_modulo = $stmt_p->fetchColumn();
}

// Busca configuração de leitura global
$stmt_c = $pdo->prepare("SELECT valor FROM contratos_configuracoes WHERE chave = 'acesso_leitura_global'");
$stmt_c->execute();
$leitura_global = $stmt_c->fetchColumn() === '1';

// Definição de permissões
define('CONTRATOS_ADMIN', $is_admin);
define('CONTRATOS_GESTOR', $is_admin || $perfil_modulo === 'Gestor');
define('CONTRATOS_CONSULTOR', $is_admin || $perfil_modulo === 'Gestor' || $perfil_modulo === 'Consultor');
define('CONTRATOS_LEITOR', CONTRATOS_CONSULTOR || $leitura_global);

// Se não houver sessão ativa de e-mail, algo está errado (auth_check.php deveria ter pego)
if (!isset($_SESSION['user_email'])) {
    header("Location: ../index.php?error=unauthorized");
    exit;
}

// Bloqueio de acesso básico (se não for leitor, não entra no módulo)
if (!CONTRATOS_LEITOR && basename($_SERVER['PHP_SELF']) !== 'index.php') {
    header("Location: ../home.php?error=no_access_contratos");
    exit;
}
?>