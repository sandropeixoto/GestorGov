<?php
// app-contratos/settings.php
require_once 'config.php';
require_once 'header.php';

$tab = $_GET['tab'] ?? 'diretorias';

// Configuração das tabelas suportadas nesta página
$tables = [
    'diretorias' => [
        'title' => 'Diretorias',
        'table' => 'Diretorias',
        'pk' => 'IdDiretoria',
        'fields' => [
            'SiglaDiretoria' => ['label' => 'Sigla', 'type' => 'text'],
            'NomeDiretoria' => ['label' => 'Nome da Diretoria', 'type' => 'text']
        ]
    ],
    'fontes' => [
        'title' => 'Fontes de Recursos',
        'table' => 'FontesRecursos',
        'pk' => 'IdFonte',
        'fields' => [
            'NomeFonte' => ['label' => 'Nome da Fonte', 'type' => 'text']
        ]
    ],
    'categorias' => [
        'title' => 'Categorias de Contrato',
        'table' => 'CategoriaContrato',
        'pk' => 'Id',
        'fields' => [
            'Codigo' => ['label' => 'Código', 'type' => 'text'],
            'Descricao' => ['label' => 'Descrição', 'type' => 'text']
        ]
    ],
    'modalidades' => [
        'title' => 'Modalidades',
        'table' => 'Modalidade',
        'pk' => 'Id',
        'fields' => [
            'Codigo' => ['label' => 'Código', 'type' => 'text'],
            'Descricao' => ['label' => 'Descrição', 'type' => 'text']
        ]
    ]
];

if (!isset($tables[$tab])) {
    $tab = 'diretorias';
}

$current = $tables[$tab];
$pk = $current['pk'];

try {
    $data = $pdo->query("SELECT * FROM {$current['table']} ORDER BY {$pk} DESC")->fetchAll();
} catch (PDOException $e) {
    $error = "Erro ao carregar dados: " . $e->getMessage();
}
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-base-content">Configurações do Sistema</h2>
            <p class="text-base-content/60">Gerencie as tabelas auxiliares e parâmetros dos contratos.</p>
        </div>
        <div class="flex gap-2">
            <a href="prestadores.php" class="btn btn-outline btn-primary">
                <i class="ph ph-buildings text-xl"></i> Gerenciar Fornecedores
            </a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs tabs-boxed bg-base-100 p-2 shadow-sm border border-base-200">
        <?php foreach ($tables as $key => $t): ?>
            <a href="?tab=<?php echo $key; ?>" class="tab tab-lg <?php echo $tab === $key ? 'tab-active' : ''; ?> transition-all">
                <?php echo $t['title']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Column -->
        <div class="card bg-base-100 shadow-xl border border-base-200 h-fit">
            <div class="card-body">
                <h3 class="card-title mb-4" id="form-title">Adicionar <?php echo rtrim($current['title'], 's'); ?></h3>
                <form action="settings_action.php" method="POST" id="settings-form" class="space-y-4">
                    <input type="hidden" name="action" id="form-action" value="create">
                    <input type="hidden" name="tab" value="<?php echo $tab; ?>">
                    <input type="hidden" name="pk_value" id="pk_value" value="">

                    <?php foreach ($current['fields'] as $name => $field): ?>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold"><?php echo $field['label']; ?></span></label>
                            <input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" id="field_<?php echo $name; ?>" required class="input input-bordered w-full">
                        </div>
                    <?php endforeach; ?>

                    <div class="card-actions justify-end mt-6">
                        <button type="button" onclick="resetForm()" class="btn btn-ghost btn-sm hidden" id="btn-cancel">Cancelar</button>
                        <button type="submit" class="btn btn-primary w-full shadow-lg">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Column -->
        <div class="lg:col-span-2 card bg-base-100 shadow-xl border border-base-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200/50">
                            <th class="w-16">ID</th>
                            <?php foreach ($current['fields'] as $field): ?>
                                <th><?php echo $field['label']; ?></th>
                            <?php endforeach; ?>
                            <th class="text-right w-24">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr class="hover group">
                                <td class="opacity-50 text-xs"><?php echo $row[$pk]; ?></td>
                                <?php foreach ($current['fields'] as $name => $field): ?>
                                    <td class="font-medium"><?php echo htmlspecialchars($row[$name]); ?></td>
                                <?php endforeach; ?>
                                <td class="text-right">
                                    <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick='editRow(<?php echo json_encode($row); ?>)' class="btn btn-square btn-sm btn-ghost text-info">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $row[$pk]; ?>)" class="btn btn-square btn-sm btn-ghost text-error">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="<?php echo count($current['fields']) + 2; ?>" class="text-center py-8 opacity-50 italic">
                                    Nenhum registro encontrado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<dialog id="delete_modal" class="modal">
  <div class="modal-box">
    <h3 class="font-bold text-lg text-error flex items-center gap-2">
        <i class="ph ph-warning"></i> Confirmar Exclusão
    </h3>
    <p class="py-4">Tem certeza que deseja excluir este registro? Esta ação pode afetar contratos vinculados.</p>
    <div class="modal-action">
      <form method="POST" action="settings_action.php">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="tab" value="<?php echo $tab; ?>">
        <input type="hidden" name="pk_value" id="delete_pk_value">
        <button type="submit" class="btn btn-error text-white">Sim, Excluir</button>
        <button type="button" class="btn" onclick="delete_modal.close()">Cancelar</button>
      </form>
    </div>
  </div>
</dialog>

<script>
function editRow(data) {
    document.getElementById('form-title').innerText = 'Editar <?php echo rtrim($current['title'], 's'); ?>';
    document.getElementById('form-action').value = 'update';
    document.getElementById('btn-cancel').classList.remove('hidden');
    
    const pkField = '<?php echo $pk; ?>';
    document.getElementById('pk_value').value = data[pkField];

    <?php foreach ($current['fields'] as $name => $field): ?>
        document.getElementById('field_<?php echo $name; ?>').value = data['<?php echo $name; ?>'];
    <?php endforeach; ?>

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('settings-form').reset();
    document.getElementById('form-title').innerText = 'Adicionar <?php echo rtrim($current['title'], 's'); ?>';
    document.getElementById('form-action').value = 'create';
    document.getElementById('pk_value').value = '';
    document.getElementById('btn-cancel').classList.add('hidden');
}

function confirmDelete(id) {
    document.getElementById('delete_pk_value').value = id;
    delete_modal.showModal();
}
</script>

<?php require_once 'footer.php'; ?>
