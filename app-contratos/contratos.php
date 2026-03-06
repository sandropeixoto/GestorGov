<?php
// app-contratos/contratos.php
require_once 'config.php';
require_once 'header.php';

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';

try {
    $sql = "SELECT c.*, p.Nome as PrestadorNome 
            FROM Contratos c 
            LEFT JOIN Prestador p ON c.PrestadorId = p.Id 
            WHERE c.PaiId = 0";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (c.SeqContrato LIKE ? OR c.NumeroContrato LIKE ? OR c.Objeto LIKE ? OR p.Nome LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if ($status === 'active') {
        $sql .= " AND c.VigenciaFim >= CURDATE()";
    } elseif ($status === 'expired') {
        $sql .= " AND c.VigenciaFim < CURDATE()";
    }

    $sql .= " ORDER BY c.Id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $contracts = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Erro ao buscar contratos: " . $e->getMessage();
}
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-base-content">Lista de Contratos</h2>
            <p class="text-base-content/60">Gerencie todos os contratos registrados.</p>
        </div>
        <a href="contract_form.php" class="btn btn-primary shadow-lg">
            <i class="ph ph-plus-circle text-xl"></i> Novo Contrato
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="card bg-base-100 shadow-md border border-base-200">
        <div class="card-body p-4">
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="form-control flex-1">
                    <label class="label"><span class="label-text font-semibold">Pesquisar</span></label>
                    <div class="join w-full">
                        <input type="text" name="search" placeholder="Número, objeto ou fornecedor..." 
                               class="input input-bordered join-item w-full" value="<?php echo htmlspecialchars($search); ?>" />
                        <button type="submit" class="btn btn-primary join-item">
                            <i class="ph ph-magnifying-glass text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-control w-full md:w-48">
                    <label class="label"><span class="label-text font-semibold">Status</span></label>
                    <select name="status" class="select select-bordered w-full" onchange="this.form.submit()">
                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>Todos</option>
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Vigentes</option>
                        <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>Vencidos</option>
                    </select>
                </div>

                <?php if (!empty($search) || $status !== 'all'): ?>
                <div class="form-control">
                    <a href="contratos.php" class="btn btn-ghost">Limpar</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error shadow-lg">
            <i class="ph ph-warning-circle text-2xl"></i>
            <span><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <!-- Contracts Table -->
    <div class="card bg-base-100 shadow-xl overflow-hidden border border-base-200">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="w-16">ID</th>
                        <th>Número/Ano</th>
                        <th>Objeto</th>
                        <th>Fornecedor</th>
                        <th>Período</th>
                        <th>Status</th>
                        <th class="text-right">Valor Global</th>
                        <th class="text-center w-24">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($contracts as $c): ?>
                    <tr class="hover group">
                        <td class="text-xs opacity-50"><?php echo $c['Id']; ?></td>
                        <td class="font-bold text-primary">
                            <?php echo $c['SeqContrato'] . '/' . $c['AnoContrato']; ?>
                        </td>
                        <td class="max-w-xs overflow-hidden text-ellipsis whitespace-nowrap" title="<?php echo htmlspecialchars($c['Objeto']); ?>">
                            <?php echo htmlspecialchars($c['Objeto']); ?>
                        </td>
                        <td class="text-sm">
                            <div class="font-medium"><?php echo htmlspecialchars($c['PrestadorNome'] ?? 'N/A'); ?></div>
                            <?php if (!empty($c['NProcesso'])): ?>
                                <div class="text-[10px] opacity-50">Proc: <?php echo $c['NProcesso']; ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-xs whitespace-nowrap">
                            <div class="flex flex-col">
                                <span>Ini: <?php echo date('d/m/Y', strtotime($c['VigenciaInicio'])); ?></span>
                                <span>Fim: <?php echo date('d/m/Y', strtotime($c['VigenciaFim'])); ?></span>
                            </div>
                        </td>
                        <td>
                            <?php 
                                $vencimento = new DateTime($c['VigenciaFim']);
                                $hoje = new DateTime();
                                $diff = $hoje->diff($vencimento);
                                $is_expired = ($vencimento < $hoje);
                                $is_warning = (!$is_expired && $diff->days <= 30);
                                
                                if ($is_expired) {
                                    echo '<span class="badge badge-error badge-sm">Vencido</span>';
                                } elseif ($is_warning) {
                                    echo '<span class="badge badge-warning badge-sm">Vence em ' . $diff->days . 'd</span>';
                                } else {
                                    echo '<span class="badge badge-success badge-sm text-white">Vigente</span>';
                                }
                            ?>
                        </td>
                        <td class="text-right font-semibold">
                            R$ <?php echo number_format($c['ValorGlobalContrato'], 2, ',', '.'); ?>
                        </td>
                        <td>
                            <div class="flex justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="contract_form.php?id=<?php echo $c['Id']; ?>" class="btn btn-square btn-sm btn-ghost text-info" title="Editar">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </a>
                                <button onclick="confirmDelete(<?php echo $c['Id']; ?>, '<?php echo $c['SeqContrato'] . '/' . $c['AnoContrato']; ?>')" class="btn btn-square btn-sm btn-ghost text-error" title="Excluir">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($contracts)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <i class="ph ph-folder-open text-6xl opacity-10 mb-2 block mx-auto"></i>
                                <p class="opacity-50 italic">Nenhum contrato encontrado com os filtros aplicados.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<dialog id="delete_modal" class="modal">
  <div class="modal-box">
    <h3 class="font-bold text-lg text-error flex items-center gap-2">
        <i class="ph ph-warning"></i> Confirmar Exclusão
    </h3>
    <p class="py-4">Tem certeza que deseja excluir o contrato <span id="delete_contract_num" class="font-bold"></span>? Esta ação não pode ser desfeita.</p>
    <div class="modal-action">
      <form method="POST" action="contracts_action.php">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_contract_id">
        <button type="submit" class="btn btn-error text-white">Sim, Excluir</button>
        <button type="button" class="btn" onclick="delete_modal.close()">Cancelar</button>
      </form>
    </div>
  </div>
</dialog>

<script>
function confirmDelete(id, num) {
    document.getElementById('delete_contract_id').value = id;
    document.getElementById('delete_contract_num').innerText = num;
    delete_modal.showModal();
}
</script>

<?php require_once 'footer.php'; ?>
