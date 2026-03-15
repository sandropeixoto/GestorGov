<?php
// view_logs.php na raiz
require_once 'auth_check.php';
require_once 'config.php';

// Segurança: Apenas Administradores acessam logs globais
if (($_SESSION['user_level'] ?? '') !== 'Administrador') {
    header("Location: home.php?error=unauthorized");
    exit;
}

// Parâmetros de Filtro
$modulo   = $_GET['modulo'] ?? '';
$acao     = $_GET['acao'] ?? '';
$usuario  = $_GET['usuario'] ?? '';
$data_ini = $_GET['data_ini'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 20;
$offset = ($page - 1) * $limit;

try {
    // 1. Buscar Opções Únicas para Filtros
    $modulos_list = $pdo->query("SELECT DISTINCT modulo FROM sistema_logs ORDER BY modulo ASC")->fetchAll(PDO::FETCH_COLUMN);
    $acoes_list   = $pdo->query("SELECT DISTINCT acao FROM sistema_logs ORDER BY acao ASC")->fetchAll(PDO::FETCH_COLUMN);

    // 2. Construir Query de Logs
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

    // Contagem Total
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM sistema_logs WHERE $where_sql");
    $stmt_count->execute($params);
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Dados Paginados
    $sql = "SELECT * FROM sistema_logs WHERE $where_sql ORDER BY data_hora DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Erro ao carregar logs: " . $e->getMessage();
}

// Helper para gerar URL de exportação
$export_query = http_build_query([
    'modulo' => $modulo,
    'acao' => $acao,
    'usuario' => $usuario,
    'data_ini' => $data_ini,
    'data_fim' => $data_fim,
    'export' => 'csv'
]);
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Auditoria - GestorGov</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="h-16 flex items-center justify-between px-8 bg-[#0f172a] text-white shadow-lg">
        <div class="flex items-center gap-4">
            <a href="admin_settings.php" class="btn btn-square btn-ghost btn-sm"><i class="ph ph-arrow-left text-xl"></i></a>
            <h1 class="text-xl font-bold tracking-tight">Logs de Auditoria</h1>
        </div>
        <div class="flex gap-2">
            <a href="export_logs_action.php?<?php echo $export_query; ?>" class="btn btn-success btn-sm text-white gap-2 shadow-lg">
                <i class="ph ph-file-csv text-xl"></i> Exportar CSV
            </a>
        </div>
    </header>

    <main class="flex-1 p-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Filtros -->
            <div class="card bg-white shadow-md border border-base-200">
                <div class="card-body p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase opacity-50">Módulo</span></label>
                            <select name="modulo" class="select select-bordered select-sm">
                                <option value="">Todos</option>
                                <?php foreach($modulos_list as $m): ?>
                                    <option value="<?php echo $m; ?>" <?php echo $modulo == $m ? 'selected' : ''; ?>><?php echo $m; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase opacity-50">Ação</span></label>
                            <select name="acao" class="select select-bordered select-sm">
                                <option value="">Todas</option>
                                <?php foreach($acoes_list as $a): ?>
                                    <option value="<?php echo $a; ?>" <?php echo $acao == $a ? 'selected' : ''; ?>><?php echo $a; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase opacity-50">Usuário</span></label>
                            <input type="text" name="usuario" class="input input-bordered input-sm" value="<?php echo htmlspecialchars($usuario); ?>" placeholder="E-mail ou ID">
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-[10px] uppercase opacity-50">Período</span></label>
                            <div class="flex items-center gap-2">
                                <input type="date" name="data_ini" class="input input-bordered input-sm w-full" value="<?php echo $data_ini; ?>">
                                <span class="opacity-30">/</span>
                                <input type="date" name="data_fim" class="input input-bordered input-sm w-full" value="<?php echo $data_fim; ?>">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-1"><i class="ph ph-funnel"></i> Filtrar</button>
                            <?php if ($modulo || $acao || $usuario || $data_ini || $data_fim): ?>
                                <a href="view_logs.php" class="btn btn-ghost btn-sm text-error"><i class="ph ph-x"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela de Logs -->
            <div class="card bg-white shadow-xl border border-base-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm w-full">
                        <thead>
                            <tr class="bg-base-200/50 text-[10px] uppercase font-bold tracking-widest">
                                <th>Data/Hora</th>
                                <th>Usuário</th>
                                <th>Módulo</th>
                                <th>Ação</th>
                                <th>Tabela/ID</th>
                                <th>IP</th>
                                <th class="text-right">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $l): ?>
                                <tr class="hover group">
                                    <td class="whitespace-nowrap font-mono text-xs opacity-70">
                                        <?php echo date('d/m/Y H:i:s', strtotime($l['data_hora'])); ?>
                                    </td>
                                    <td>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-xs"><?php echo $l['usuario_email'] ?: 'Sistema'; ?></span>
                                            <span class="text-[9px] opacity-40">ID: <?php echo $l['usuario_id'] ?: '-'; ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-outline badge-sm text-[9px] font-bold"><?php echo $l['modulo']; ?></span></td>
                                    <td>
                                        <?php 
                                            $action_class = 'badge-ghost';
                                            if($l['acao'] === 'Delete') $action_class = 'badge-error text-white';
                                            if($l['acao'] === 'Create' || $l['acao'] === 'Upload') $action_class = 'badge-success text-white';
                                            if($l['acao'] === 'Update') $action_class = 'badge-info text-white';
                                        ?>
                                        <span class="badge <?php echo $action_class; ?> badge-sm font-bold uppercase text-[9px]"><?php echo $l['acao']; ?></span>
                                    </td>
                                    <td>
                                        <div class="flex flex-col">
                                            <span class="text-xs"><?php echo $l['tabela'] ?: '-'; ?></span>
                                            <span class="text-[9px] opacity-50">Reg: <?php echo $l['registro_id'] ?: 'N/A'; ?></span>
                                        </div>
                                    </td>
                                    <td class="text-xs font-mono opacity-50"><?php echo $l['ip']; ?></td>
                                    <td class="text-right">
                                        <button onclick="viewDetails(<?php echo htmlspecialchars(json_encode($l['detalhes'])); ?>)" class="btn btn-circle btn-xs btn-ghost text-primary" <?php echo empty($l['detalhes']) ? 'disabled' : ''; ?>>
                                            <i class="ph ph-eye text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-12 opacity-40 italic">Nenhum log encontrado para os critérios selecionados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="p-4 bg-base-200/30 flex justify-between items-center border-t border-base-200">
                    <span class="text-xs opacity-50">Total: <?php echo $total_records; ?> registros</span>
                    <div class="join">
                        <?php 
                        $range = 2;
                        for ($i = 1; $i <= $total_pages; $i++): 
                            if ($i == 1 || $i == $total_pages || ($i >= $page - $range && $i <= $page + $range)):
                                $q = $_GET;
                                $q['page'] = $i;
                        ?>
                            <a href="?<?php echo http_build_query($q); ?>" class="join-item btn btn-xs <?php echo $page === $i ? 'btn-primary text-white' : ''; ?>"><?php echo $i; ?></a>
                        <?php 
                            elseif ($i == $page - $range - 1 || $i == $page + $range + 1):
                        ?>
                            <span class="join-item btn btn-xs btn-disabled">...</span>
                        <?php 
                            endif;
                        endfor; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Detalhes -->
    <dialog id="details_modal" class="modal">
      <div class="modal-box w-11/12 max-w-2xl bg-[#0f172a] text-success font-mono text-sm p-0 overflow-hidden shadow-2xl">
        <div class="bg-slate-800 px-6 py-3 flex justify-between items-center border-b border-slate-700">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"><i class="ph ph-code"></i> Payload do Registro</span>
            <form method="dialog"><button class="btn btn-square btn-ghost btn-sm text-slate-400"><i class="ph ph-x"></i></button></form>
        </div>
        <div class="p-8 max-h-[70vh] overflow-y-auto">
            <pre id="payload_view" class="whitespace-pre-wrap"></pre>
        </div>
      </div>
    </dialog>

    <script>
    function viewDetails(data) {
        const payloadView = document.getElementById('payload_view');
        try {
            const parsed = JSON.parse(data);
            payloadView.innerText = JSON.stringify(parsed, null, 4);
        } catch (e) {
            payloadView.innerText = data;
        }
        details_modal.showModal();
    }
    </script>

    <footer class="p-8 text-center text-slate-400 text-xs">
        &copy; 2026 GestorGov - Auditoria do Sistema
    </footer>
</body>
</html>
