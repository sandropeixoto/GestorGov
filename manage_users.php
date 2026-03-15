<?php
// manage_users.php na raiz
require_once 'auth_check.php';
require_once 'config.php';

// Segurança: Apenas Administradores podem gerenciar usuários
if (($_SESSION['user_level'] ?? '') !== 'Administrador') {
    header("Location: home.php?error=unauthorized");
    exit;
}

$id = $_GET['id'] ?? null;
$user_to_edit = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $user_to_edit = $stmt->fetch();
}

try {
    $search = $_GET['search'] ?? '';
    $sql = "SELECT * FROM usuarios WHERE 1=1";
    $params = [];
    if ($search) {
        $sql .= " AND (nome LIKE ? OR email LIKE ?)";
        $params = ["%$search%", "%$search%"];
    }
    $sql .= " ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erro ao carregar usuários: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestorGov - Gerir Usuários</title>
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
            <h1 class="text-xl font-bold tracking-tight">Gestão de Usuários</h1>
        </div>
    </header>

    <main class="flex-1 p-8">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulário -->
            <div class="card bg-white shadow-xl border border-base-200 h-fit">
                <div class="card-body">
                    <h3 class="card-title mb-4"><?php echo $id ? 'Editar Usuário' : 'Novo Usuário'; ?></h3>
                    <form action="users_action.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="<?php echo $id ? 'update' : 'create'; ?>">
                        <?php if ($id): ?> <input type="hidden" name="id" value="<?php echo $id; ?>"> <?php endif; ?>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">Nome Completo</span></label>
                            <input type="text" name="nome" required class="input input-bordered" value="<?php echo htmlspecialchars($user_to_edit['nome'] ?? ''); ?>" placeholder="Ex: Sandro Peixoto">
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">E-mail</span></label>
                            <input type="email" name="email" required class="input input-bordered" value="<?php echo htmlspecialchars($user_to_edit['email'] ?? ''); ?>" placeholder="usuario@sefa.pa.gov.br">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold">Nível</span></label>
                                <select name="nivel" class="select select-bordered">
                                    <option value="Consultor" <?php echo ($user_to_edit['nivel'] ?? '') == 'Consultor' ? 'selected' : ''; ?>>Consultor</option>
                                    <option value="Gestor" <?php echo ($user_to_edit['nivel'] ?? '') == 'Gestor' ? 'selected' : ''; ?>>Gestor</option>
                                    <option value="Administrador" <?php echo ($user_to_edit['nivel'] ?? '') == 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-bold">Status</span></label>
                                <select name="status" class="select select-bordered">
                                    <option value="1" <?php echo ($user_to_edit['status'] ?? 1) == 1 ? 'selected' : ''; ?>>Ativo</option>
                                    <option value="0" <?php echo ($user_to_edit['status'] ?? 1) == 0 ? 'selected' : ''; ?>>Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-6">
                            <?php if ($id): ?> <a href="manage_users.php" class="btn btn-ghost btn-sm">Cancelar</a> <?php endif; ?>
                            <button type="submit" class="btn btn-primary w-full shadow-lg text-white">Salvar Usuário</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listagem -->
            <div class="lg:col-span-2 space-y-4">
                <div class="card bg-white shadow-md border border-base-200">
                    <div class="card-body p-4">
                        <form method="GET" class="join w-full">
                            <input type="text" name="search" placeholder="Buscar por nome ou e-mail..." class="input input-bordered join-item w-full" value="<?php echo htmlspecialchars($search); ?>" />
                            <button type="submit" class="btn btn-primary join-item text-white"><i class="ph ph-magnifying-glass"></i></button>
                        </form>
                    </div>
                </div>

                <div class="card bg-white shadow-xl border border-base-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr class="bg-base-200/50 text-[10px] uppercase font-bold tracking-widest">
                                    <th>Usuário</th>
                                    <th>Nível</th>
                                    <th>Status</th>
                                    <th class="text-right w-24">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr class="hover group">
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-neutral text-neutral-content rounded-lg w-10">
                                                        <span class="text-xs"><?php echo substr($u['nome'], 0, 2); ?></span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold"><?php echo htmlspecialchars($u['nome']); ?></div>
                                                    <div class="text-[10px] opacity-50"><?php echo htmlspecialchars($u['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $badge_class = 'badge-ghost';
                                                if($u['nivel'] === 'Administrador') $badge_class = 'badge-primary text-white';
                                                if($u['nivel'] === 'Gestor') $badge_class = 'badge-secondary text-white';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> badge-sm font-bold uppercase text-[9px]"><?php echo $u['nivel']; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($u['status']): ?>
                                                <span class="badge badge-success badge-sm text-white font-bold">ATIVO</span>
                                            <?php else: ?>
                                                <span class="badge badge-error badge-sm text-white font-bold">INATIVO</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button onclick="confirmWelcome(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nome']); ?>')" class="btn btn-square btn-sm btn-ghost text-primary" title="Enviar e-mail de Boas-vindas"><i class="ph ph-paper-plane-tilt text-lg"></i></button>
                                                <a href="?id=<?php echo $u['id']; ?>" class="btn btn-square btn-sm btn-ghost text-info"><i class="ph ph-pencil-simple text-lg"></i></a>
                                                <button onclick="confirmDelete(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nome']); ?>')" class="btn btn-square btn-sm btn-ghost text-error"><i class="ph ph-trash text-lg"></i></button>
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
    </main>

    <dialog id="welcome_modal" class="modal">
      <div class="modal-box">
        <h3 class="font-bold text-lg text-primary flex items-center gap-2"><i class="ph ph-paper-plane-tilt"></i> Boas-vindas ao GestorGov</h3>
        <p class="py-4 text-sm">Deseja enviar o e-mail de instruções e boas-vindas para <strong><span id="welcome_name"></span></strong>?</p>
        <div class="modal-action">
          <form method="POST" action="users_action.php">
            <input type="hidden" name="action" value="send_welcome">
            <input type="hidden" name="id" id="welcome_id">
            <button type="submit" class="btn btn-primary text-white">Sim, Enviar Agora</button>
            <button type="button" class="btn" onclick="welcome_modal.close()">Cancelar</button>
          </form>
        </div>
      </div>
    </dialog>

    <dialog id="delete_modal" class="modal">
      <div class="modal-box">
        <h3 class="font-bold text-lg text-error flex items-center gap-2"><i class="ph ph-warning"></i> Remover Usuário</h3>
        <p class="py-4 text-sm">Deseja remover o acesso de <strong><span id="del_name"></span></strong>?</p>
        <div class="modal-action">
          <form method="POST" action="users_action.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="del_id">
            <button type="submit" class="btn btn-error text-white">Sim, Remover</button>
            <button type="button" class="btn" onclick="delete_modal.close()">Cancelar</button>
          </form>
        </div>
      </div>
    </dialog>

    <?php if (isset($_GET['msg'])): ?>
    <div class="toast toast-top toast-end mt-16">
        <?php if ($_GET['msg'] === 'email_sent'): ?>
            <div class="alert alert-success text-white shadow-lg">
                <i class="ph ph-check-circle text-xl"></i>
                <span>E-mail enviado com sucesso!</span>
            </div>
        <?php elseif ($_GET['msg'] === 'success'): ?>
            <div class="alert alert-success text-white shadow-lg">
                <i class="ph ph-check-circle text-xl"></i>
                <span>Operação realizada com sucesso!</span>
            </div>
        <?php endif; ?>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.toast').classList.add('hidden');
        }, 3000);
    </script>
    <?php endif; ?>

    <script>
        function confirmWelcome(id, name) {
            document.getElementById('welcome_id').value = id;
            document.getElementById('welcome_name').innerText = name;
            welcome_modal.showModal();
        }

        function confirmDelete(id, name) {
            document.getElementById('del_id').value = id;
            document.getElementById('del_name').innerText = name;
            delete_modal.showModal();
        }
    </script>
</body>
</html>
