<?php
// logger.php - Função global de log de auditoria

/**
 * Registra uma ação no log do sistema para auditoria.
 * 
 * @param PDO $pdo Instância do banco de dados
 * @param string $modulo Onde ocorreu (ex: Portal, Contratos)
 * @param string $acao O que foi feito (ex: Login, Insert, Update, Delete)
 * @param string|null $tabela Nome da tabela afetada
 * @param int|null $registro_id ID do registro afetado
 * @param mixed|null $detalhes Dados extras (array ou string) para salvar como JSON
 */
function logSistema($pdo, $modulo, $acao, $tabela = null, $registro_id = null, $detalhes = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $usuario_id    = $_SESSION['user_id'] ?? null;
    $usuario_email = $_SESSION['user_email'] ?? null;
    $ip            = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent    = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // Converte detalhes para JSON se for array, ou mantém como string/null
    $detalhes_json = (is_array($detalhes) || is_object($detalhes)) 
        ? json_encode($detalhes, JSON_UNESCAPED_UNICODE) 
        : $detalhes;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO sistema_logs (usuario_id, usuario_email, modulo, acao, tabela, registro_id, detalhes, ip, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $usuario_id, 
            $usuario_email, 
            $modulo, 
            $acao, 
            $tabela, 
            $registro_id, 
            $detalhes_json, 
            $ip, 
            $user_agent
        ]);
    } catch (PDOException $e) {
        // Silencioso em caso de erro de log para não travar a aplicação principal
        error_log("Erro de logSistema: " . $e->getMessage());
    }
}
?>
