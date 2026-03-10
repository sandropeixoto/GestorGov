<?php
// app-contratos/prestadores.php
require_once 'config.php';
require_once 'header.php';

// Bloqueio de leitura
if (!CONTRATOS_LEITOR) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$search = $_GET['search'] ?? '';
$id = $_GET['id'] ?? null;
$prestador = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM Prestador WHERE Id = ?");
    $stmt->execute([$id]);
    $prestador = $stmt->fetch();
}

try {
    $sql = "SELECT * FROM Prestador WHERE 1=1";
    $params = [];
    if (!empty($search)) {
        $sql .= " AND (Nome LIKE ? OR CNPJ LIKE ? OR Email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    $sql .= " ORDER BY Nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $prestadores = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erro: " . $e->getMessage();
}
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-base-content">Gestão de Fornecedores</h2>
            <p class="text-base-content/60">Cadastre e gerencie os prestadores de serviço e fornecedores.</p>
        </div>
        <?php if (CONTRATOS_ADMIN): ?>
        <a href="settings.php" class="btn btn-ghost gap-2">
            <i class="ph ph-gear"></i> Configurações
        </a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Column -->
        <?php if (CONTRATOS_CONSULTOR): ?>
        <div class="card bg-base-100 shadow-xl border border-base-200 h-fit">
            <div class="card-body">
                <h3 class="card-title mb-4"><?php echo $id ? 'Editar Fornecedor' : 'Novo Fornecedor'; ?></h3>
                <form action="prestadores_action.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="<?php echo $id ? 'update' : 'create'; ?>">
                    <?php if ($id): ?>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <?php endif; ?>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Nome/Razão Social</span></label>
                        <input type="text" name="Nome" required class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Nome'] ?? ''); ?>">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Tipo</span></label>
                            <select name="Tipo" class="select select-bordered">
                                <option value="PJ" <?php echo ($prestador['Tipo'] ?? '') == 'PJ' ? 'selected' : ''; ?>>PJ</option>
                                <option value="PF" <?php echo ($prestador['Tipo'] ?? '') == 'PF' ? 'selected' : ''; ?>>PF</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">CNPJ/CPF</span></label>
                            <input type="text" name="CNPJ" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['CNPJ'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">E-mail</span></label>
                        <input type="email" name="Email" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Email'] ?? ''); ?>">
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Telefone</span></label>
                        <input type="text" name="Telefone" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Telefone'] ?? ''); ?>">
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Endereço</span></label>
                        <textarea name="Endereco" class="textarea textarea-bordered h-20"><?php echo htmlspecialchars($prestador['Endereco'] ?? ''); ?></textarea>
                    </div>

                    <div class="card-actions justify-end mt-6">
                        <?php if ($id): ?>
                            <a href="prestadores.php" class="btn btn-ghost btn-sm">Cancelar</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary w-full shadow-lg">Salvar Fornecedor</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Table Column -->
        <div class="<?php echo CONTRATOS_CONSULTOR ? 'lg:col-span-2' : 'lg:col-span-3'; ?> space-y-4">
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body p-4">
                    <form method="GET" class="join w-full">
                        <input type="text" name="search" placeholder="Buscar por nome, CNPJ ou e-mail..." class="input input-bordered join-item w-full" value="<?php echo htmlspecialchars($search); ?>" />
                        <button type="submit" class="btn btn-primary join-item"><i class="ph ph-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl border border-base-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="bg-base-200/50">
                                <th>Nome</th>
                                <th>Documento</th>
                                <th>Contato</th>
                                <th class="text-right w-24">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestadores as $p): ?>
                                <tr class="hover group">
                                    <td>
                                        <div class="font-bold text-primary"><?php echo htmlspecialchars($p['Nome']); ?></div>
                                        <div class="text-[10px] opacity-50 uppercase"><?php echo $p['Tipo']; ?></div>
                                    </td>
                                    <td class="text-sm font-mono"><?php echo htmlspecialchars($p['CNPJ']); ?></td>
                                    <td class="text-xs">
                                        <div class="flex items-center gap-1"><i class="ph ph-envelope"></i> <?php echo htmlspecialchars($p['Email']); ?></div>
                                        <div class="flex items-center gap-1"><i class="ph ph-phone"></i> <?php echo htmlspecialchars($p['Telefone']); ?></div>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <?php if (CONTRATOS_CONSULTOR): ?>
                                            <a href="?id=<?php echo $p['Id']; ?>" class="btn btn-square btn-sm btn-ghost text-info" title="Editar">
                                                <i class="ph ph-pencil-simple text-lg"></i>
                                            </a>
                                            <?php endif; ?>

                                            <?php if (CONTRATOS_GESTOR): ?>
                                            <button onclick="confirmDelete(<?php echo $p['Id']; ?>, '<?php echo htmlspecialchars($p['Nome']); ?>')" class="btn btn-square btn-sm btn-ghost text-error" title="Excluir">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                            <?php endif; ?>

                                            <?php if (!CONTRATOS_CONSULTOR): ?>
                                            <i class="ph ph-eye text-lg opacity-30" title="Apenas Visualização"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<dialog id="delete_modal" class="modal">
  <div class="modal-box">
    <h3 class="font-bold text-lg text-error flex items-center gap-2"><i class="ph ph-warning"></i> Excluir Fornecedor</h3>
    <p class="py-4">Deseja excluir <span id="del_name" class="font-bold"></span>?</p>
    <div class="modal-action">
      <form method="POST" action="prestadores_action.php">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="del_id">
        <button type="submit" class="btn btn-error text-white">Sim, Excluir</button>
        <button type="button" class="btn" onclick="delete_modal.close()">Cancelar</button>
      </form>
    </div>
  </div>
</dialog>

<script>
function confirmDelete(id, name) {
    document.getElementById('del_id').value = id;
    document.getElementById('del_name').innerText = name;
    delete_modal.showModal();
}
</script>

<?php require_once 'footer.php'; ?>
