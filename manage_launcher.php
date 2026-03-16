<?php
// manage_launcher.php na raiz
require_once 'auth_check.php';
require_once 'config.php';

// Segurança: Apenas Administradores podem gerenciar o launcher
if (($_SESSION['user_level'] ?? '') !== 'Administrador') {
    header("Location: home.php?error=unauthorized");
    exit;
}

$id = $_GET['id'] ?? null;
$module = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM launcher_modules WHERE id = ?");
    $stmt->execute([$id]);
    $module = $stmt->fetch();
}

$icons = [
    'ph-file-text' => 'Contrato / Documento',
    'ph-gear' => 'Engrenagem / Config',
    'ph-users-three' => 'Usuários / Pessoas',
    'ph-chart-bar' => 'Gráfico / PCA',
    'ph-airplane-tilt' => 'Viagens / Avião',
    'ph-student' => 'Estagiários / Chapéu',
    'ph-briefcase' => 'Maleta / Trabalho',
    'ph-currency-dollar' => 'Financeiro',
    'ph-shield-check' => 'Segurança',
    'ph-truck' => 'Logística / Frota',
    'ph-buildings' => 'Prédios / Órgãos',
    'ph-user-switch' => 'Movimentação / RH'
];

try {
    $modules = $pdo->query("SELECT * FROM launcher_modules ORDER BY display_order ASC, title ASC")->fetchAll();
} catch (PDOException $e) {
    $error = "Erro ao carregar módulos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestorGov - Gerir Launcher</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .sortable-ghost { opacity: 0.4; background: #e2e8f0 !important; }
        .drag-handle { cursor: grab; }
        .drag-handle:active { cursor: grabbing; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="h-16 flex items-center justify-between px-8 bg-[#0f172a] text-white shadow-lg">
        <div class="flex items-center gap-4">
            <a href="admin_settings.php" class="btn btn-square btn-ghost btn-sm"><i class="ph ph-arrow-left text-xl"></i></a>
            <h1 class="text-xl font-bold tracking-tight">Gerenciar Launcher</h1>
        </div>
    </header>

    <main class="flex-1 p-8">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulário -->
            <div class="card bg-white shadow-xl border border-base-200 h-fit">
                <div class="card-body">
                    <h3 class="card-title mb-4"><?php echo $id ? 'Editar Módulo' : 'Novo Módulo'; ?></h3>
                    <form action="launcher_action.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="<?php echo $id ? 'update' : 'create'; ?>">
                        <?php if ($id): ?> <input type="hidden" name="id" value="<?php echo $id; ?>"> <?php endif; ?>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">Título</span></label>
                            <input type="text" name="title" required class="input input-bordered" value="<?php echo htmlspecialchars($module['title'] ?? ''); ?>" placeholder="Ex: Gestão de Viagens">
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">Descrição Curta</span></label>
                            <textarea name="description" class="textarea textarea-bordered h-20" placeholder="Resumo da funcionalidade..."><?php echo htmlspecialchars($module['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold">Ordem</span></label>
                                <input type="number" name="display_order" class="input input-bordered" value="<?php echo $module['display_order'] ?? 0; ?>">
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold">Status</span></label>
                                <select name="is_active" class="select select-bordered">
                                    <option value="1" <?php echo ($module['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>Ativo</option>
                                    <option value="0" <?php echo ($module['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>Em Breve (Desfocado)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">URL ou Caminho</span></label>
                            <input type="text" name="url" required class="input input-bordered" value="<?php echo htmlspecialchars($module['url'] ?? ''); ?>" placeholder="pasta/index.php ou https://...">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="is_external" value="1" class="checkbox checkbox-xs" <?php echo ($module['is_external'] ?? 0) ? 'checked' : ''; ?>>
                                <span class="label-text text-xs">Módulo Externo (Gerar Token SSO)</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="open_in_new_tab" value="1" class="checkbox checkbox-xs checkbox-primary" <?php echo ($module['open_in_new_tab'] ?? 0) ? 'checked' : ''; ?>>
                                <span class="label-text text-xs font-bold text-primary">Abrir em Nova Aba / Janela</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">Ícone Visual</span></label>
                            <div class="grid grid-cols-4 gap-2 border border-base-200 p-3 rounded-lg max-h-40 overflow-y-auto bg-base-50">
                                <?php foreach ($icons as $class => $label): ?>
                                    <label class="flex flex-col items-center gap-1 cursor-pointer group">
                                        <input type="radio" name="icon" value="<?php echo $class; ?>" class="radio radio-xs radio-primary hidden" <?php echo ($module['icon'] ?? 'ph-cube') === $class ? 'checked' : ''; ?>>
                                        <div class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-transparent group-hover:bg-primary/10 peer-checked:border-primary peer-checked:bg-primary/10 transition-all icon-box">
                                            <i class="ph <?php echo $class; ?> text-xl"></i>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-6">
                            <?php if ($id): ?> <a href="manage_launcher.php" class="btn btn-ghost btn-sm">Cancelar</a> <?php endif; ?>
                            <button type="submit" class="btn btn-primary w-full shadow-lg">Salvar Módulo</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listagem -->
            <div class="lg:col-span-2 card bg-white shadow-xl border border-base-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="bg-base-200/50">
                                <th class="w-10"></th>
                                <th class="w-16">Ordem</th>
                                <th>Módulo</th>
                                <th>Tipo/URL</th>
                                <th class="text-right w-24">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-list">
                            <?php foreach ($modules as $m): ?>
                                <tr class="hover group" data-id="<?php echo $m['id']; ?>">
                                    <td class="drag-handle text-slate-300 hover:text-primary transition-colors">
                                        <i class="ph ph-dots-six-vertical text-xl"></i>
                                    </td>
                                    <td class="font-bold text-center row-order"><?php echo $m['display_order']; ?></td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-base-200 rounded-lg flex items-center justify-center text-primary">
                                                <i class="ph <?php echo $m['icon']; ?> text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold"><?php echo htmlspecialchars($m['title']); ?></div>
                                                <div class="text-[10px] opacity-50 <?php echo $m['is_active'] ? 'text-success' : 'text-warning'; ?>">
                                                    <?php echo $m['is_active'] ? 'ATIVO' : 'EM BREVE / DESFOCADO'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-xs">
                                        <div class="opacity-60"><?php echo htmlspecialchars($m['url']); ?></div>
                                        <div class="flex gap-1 mt-1">
                                            <?php if ($m['is_external'] ?? 0): ?> <span class="badge badge-outline badge-xs">EXTERNO (SSO)</span> <?php endif; ?>
                                            <?php if ($m['open_in_new_tab'] ?? 0): ?> <span class="badge badge-primary badge-xs"><i class="ph ph-arrow-square-out mr-1"></i> NOVA ABA</span> <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="?id=<?php echo $m['id']; ?>" class="btn btn-square btn-sm btn-ghost text-info"><i class="ph ph-pencil-simple text-lg"></i></a>
                                            <button onclick="confirmDelete(<?php echo $m['id']; ?>, '<?php echo htmlspecialchars($m['title']); ?>')" class="btn btn-square btn-sm btn-ghost text-error"><i class="ph ph-trash text-lg"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <dialog id="delete_modal" class="modal">
      <div class="modal-box">
        <h3 class="font-bold text-lg text-error flex items-center gap-2"><i class="ph ph-warning"></i> Remover Módulo</h3>
        <p class="py-4 text-sm">Deseja remover <strong><span id="del_name"></span></strong> do Launcher?</p>
        <div class="modal-action">
          <form method="POST" action="launcher_action.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="del_id">
            <button type="submit" class="btn btn-error text-white">Sim, Remover</button>
            <button type="button" class="btn" onclick="delete_modal.close()">Cancelar</button>
          </form>
        </div>
      </div>
    </dialog>

    <style>
        input[type="radio"]:checked + .icon-box { border-color: #570df8 !important; background-color: rgba(87, 13, 248, 0.1) !important; }
    </style>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('del_id').value = id;
            document.getElementById('del_name').innerText = name;
            delete_modal.showModal();
        }

        // Inicializa o Drag & Drop com SortableJS
        const el = document.getElementById('sortable-list');
        Sortable.create(el, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const rows = el.querySelectorAll('tr');
                const orderData = [];
                
                rows.forEach((row, index) => {
                    const newOrder = index + 1;
                    // Atualiza visualmente o número da ordem na tabela
                    row.querySelector('.row-order').innerText = newOrder;
                    
                    orderData.push({
                        id: row.getAttribute('data-id'),
                        order: newOrder
                    });
                });

                // Envia a nova ordem para o servidor via AJAX para atualização em massa
                const formData = new URLSearchParams();
                formData.append('action', 'reorder');
                formData.append('data', JSON.stringify(orderData));

                fetch('launcher_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Erro ao salvar nova ordem: ' + (data.error || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Erro AJAX:', error);
                });
            }
        });
    </script>
</body>
</html>
