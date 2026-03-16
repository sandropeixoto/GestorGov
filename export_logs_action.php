<?php
// export_logs_action.php na raiz
require_once 'auth_check.php';
require_once 'config.php';

// Segurança: Apenas Administradores podem exportar
if (strtolower($_SESSION['user_level'] ?? '') !== 'administrador') {
    die("Acesso negado.");
}

// Parâmetros de Filtro
$modulo   = $_GET['modulo'] ?? '';
$acao     = $_GET['acao'] ?? '';
$usuario  = $_GET['usuario'] ?? '';
$data_ini = $_GET['data_ini'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

try {
    $where = ["1=1"];
    $params = [];

    if ($modulo) {
        $where[] = "modulo = ?";
        $params[] = $modulo;
    }
    if ($acao) {
        $where[] = "acao = ?";
        $params[] = $acao;
    }
    if ($usuario) {
        $where[] = "usuario_email LIKE ?";
        $params[] = "%$usuario%";
    }
    if ($data_ini) {
        $where[] = "data_hora >= ?";
        $params[] = $data_ini . " 00:00:00";
    }
    if ($data_fim) {
        $where[] = "data_hora <= ?";
        $params[] = $data_fim . " 23:59:59";
    }

    $where_sql = implode(" AND ", $where);
    $sql = "SELECT data_hora, usuario_email, modulo, acao, tabela, registro_id, detalhes, ip FROM sistema_logs WHERE $where_sql ORDER BY data_hora DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Configura Headers para Download do CSV
    $filename = "logs_auditoria_" . date('Y-m-d_H-i') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    
    // Adiciona BOM para Excel ler UTF-8 corretamente
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Cabeçalho do CSV
    fputcsv($output, ['Data/Hora', 'Usuário', 'Módulo', 'Ação', 'Tabela', 'ID Registro', 'Detalhes', 'IP']);

    foreach ($logs as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    die("Erro ao exportar: " . $e->getMessage());
}
?>
