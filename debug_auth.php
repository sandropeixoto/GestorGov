<?php
require_once 'config.php';
session_start();

echo "<h3>🔍 Diagnóstico de Autenticação</h3>";

echo "<h4>1. Dados da Sessão Atual:</h4>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['user_email'])) {
    echo "<h4>2. Verificação no Banco de Dados:</h4>";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$_SESSION['user_email']]);
    $user = $stmt->fetch();

    if ($user) {
        echo "<p>Usuário encontrado na tabela 'usuarios'.</p>";
        echo "<pre>";
        var_dump($user);
        echo "</pre>";
        
        echo "<p>Comparação de Nível:</p>";
        echo "Valor no banco: [" . $user['nivel'] . "]<br>";
        echo "Tipo: " . gettype($user['nivel']) . "<br>";
        echo "Tamanho: " . strlen($user['nivel']) . "<br>";
        echo "É igual a 'Administrador'? " . ($user['nivel'] === 'Administrador' ? "SIM" : "NÃO") . "<br>";
        echo "É igual a 'Administrador' (case-insensitive)? " . (strcasecmp(trim($user['nivel']), 'Administrador') === 0 ? "SIM" : "NÃO") . "<br>";
    } else {
        echo "<p style='color:red;'>ERRO: E-mail [" . $_SESSION['user_email'] . "] NÃO encontrado na tabela 'usuarios'.</p>";
        echo "<p>Por isso você está caindo no nível 'Consultor'.</p>";
    }
} else {
    echo "<p style='color:orange;'>Nenhuma sessão ativa encontrada.</p>";
}
?>
