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
$contatos = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM Prestador WHERE Id = ?");
    $stmt->execute([$id]);
    $prestador = $stmt->fetch();
    
    if ($prestador) {
        $stmt_c = $pdo->prepare("SELECT * FROM prestador_contatos WHERE PrestadorId = ? ORDER BY Id ASC");
        $stmt_c->execute([$id]);
        $contatos = $stmt_c->fetchAll();
    }
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

<!-- Scripts para Máscaras e CEP -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-base-content">Gestão de Fornecedores</h2>
            <p class="text-base-content/60">Cadastre e gerencie os prestadores de serviço e fornecedores.</p>
        </div>
        <div class="flex gap-2">
            <?php if (CONTRATOS_ADMIN): ?>
            <a href="settings.php" class="btn btn-ghost gap-2">
                <i class="ph ph-gear"></i> Configurações
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Form Column -->
        <?php if (CONTRATOS_CONSULTOR): ?>
        <div class="lg:col-span-5">
            <div class="card bg-base-100 shadow-xl border border-base-200 h-fit sticky top-24">
                <div class="card-body">
                    <h3 class="card-title mb-4"><?php echo $id ? 'Editar Fornecedor' : 'Novo Fornecedor'; ?></h3>
                    <form action="prestadores_action.php" method="POST" class="space-y-6" id="prestadorForm">
                        <input type="hidden" name="action" value="<?php echo $id ? 'update' : 'create'; ?>">
                        <?php if ($id): ?>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php endif; ?>

                        <!-- Dados Principais -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold uppercase opacity-50 border-b pb-1">Identificação</h4>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Nome/Razão Social</span></label>
                                <input type="text" name="Nome" required class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Nome'] ?? ''); ?>">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Tipo</span></label>
                                    <select name="Tipo" id="tipo_pessoa" class="select select-bordered" onchange="toggleMask()">
                                        <option value="PJ" <?php echo ($prestador['Tipo'] ?? '') == 'PJ' ? 'selected' : ''; ?>>Pessoa Jurídica (CNPJ)</option>
                                        <option value="PF" <?php echo ($prestador['Tipo'] ?? '') == 'PF' ? 'selected' : ''; ?>>Pessoa Física (CPF)</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Documento</span></label>
                                    <input type="text" name="CNPJ" id="documento" required class="input input-bordered" value="<?php echo htmlspecialchars($prestador['CNPJ'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Endereço Detalhado -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold uppercase opacity-50 border-b pb-1">Endereço</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="form-control col-span-1">
                                    <label class="label"><span class="label-text font-semibold">CEP</span></label>
                                    <input type="text" name="CEP" id="cep" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['CEP'] ?? ''); ?>" onblur="buscarCEP(this.value)">
                                </div>
                                <div class="form-control col-span-2">
                                    <label class="label"><span class="label-text font-semibold">Cidade</span></label>
                                    <input type="text" name="Cidade" id="cidade" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Cidade'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-4">
                                <div class="form-control col-span-3">
                                    <label class="label"><span class="label-text font-semibold">Logradouro</span></label>
                                    <input type="text" name="Logradouro" id="logradouro" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Logradouro'] ?? ''); ?>">
                                </div>
                                <div class="form-control col-span-1">
                                    <label class="label"><span class="label-text font-semibold">Número</span></label>
                                    <input type="text" name="Numero" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Numero'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Bairro</span></label>
                                    <input type="text" name="Bairro" id="bairro" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Bairro'] ?? ''); ?>">
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">UF</span></label>
                                    <input type="text" name="UF" id="uf" maxlength="2" class="input input-bordered uppercase" value="<?php echo htmlspecialchars($prestador['UF'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Complemento</span></label>
                                <input type="text" name="Complemento" class="input input-bordered" value="<?php echo htmlspecialchars($prestador['Complemento'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Contatos (1-N) -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b pb-1">
                                <h4 class="text-sm font-bold uppercase opacity-50">Contatos</h4>
                                <button type="button" onclick="addContatoRow()" class="btn btn-xs btn-outline btn-primary gap-1">
                                    <i class="ph ph-plus"></i> Novo
                                </button>
                            </div>
                            
                            <div id="contatosList" class="space-y-4">
                                <?php if (empty($contatos)): ?>
                                    <div class="contato-row p-4 bg-base-200/50 rounded-lg relative grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="form-control">
                                            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Tipo</span></label>
                                            <select name="contato_tipo[]" class="select select-bordered select-sm">
                                                <option value="Comercial">Comercial</option>
                                                <option value="Financeiro">Financeiro</option>
                                                <option value="Suporte">Suporte</option>
                                                <option value="Gestão">Gestão</option>
                                            </select>
                                        </div>
                                        <div class="form-control">
                                            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Nome</span></label>
                                            <input type="text" name="contato_nome[]" class="input input-bordered input-sm" placeholder="Nome do contato">
                                        </div>
                                        <div class="form-control">
                                            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">E-mail</span></label>
                                            <input type="email" name="contato_email[]" class="input input-bordered input-sm" placeholder="email@exemplo.com">
                                        </div>
                                        <div class="form-control">
                                            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Telefone</span></label>
                                            <input type="text" name="contato_tel[]" class="input input-bordered input-sm tel-mask" placeholder="(00) 00000-0000">
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($contatos as $c): ?>
                                        <div class="contato-row p-4 bg-base-200/50 rounded-lg relative grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <button type="button" onclick="this.parentElement.remove()" class="btn btn-circle btn-xs btn-error absolute -top-2 -right-2 text-white"><i class="ph ph-x"></i></button>
                                            <div class="form-control">
                                                <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Tipo</span></label>
                                                <select name="contato_tipo[]" class="select select-bordered select-sm">
                                                    <option value="Comercial" <?php echo $c['Tipo'] == 'Comercial' ? 'selected' : ''; ?>>Comercial</option>
                                                    <option value="Financeiro" <?php echo $c['Tipo'] == 'Financeiro' ? 'selected' : ''; ?>>Financeiro</option>
                                                    <option value="Suporte" <?php echo $c['Tipo'] == 'Suporte' ? 'selected' : ''; ?>>Suporte</option>
                                                    <option value="Gestão" <?php echo $c['Tipo'] == 'Gestão' ? 'selected' : ''; ?>>Gestão</option>
                                                </select>
                                            </div>
                                            <div class="form-control">
                                                <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Nome</span></label>
                                                <input type="text" name="contato_nome[]" class="input input-bordered input-sm" value="<?php echo htmlspecialchars($c['Nome']); ?>">
                                            </div>
                                            <div class="form-control">
                                                <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">E-mail</span></label>
                                                <input type="email" name="contato_email[]" class="input input-bordered input-sm" value="<?php echo htmlspecialchars($c['Email']); ?>">
                                            </div>
                                            <div class="form-control">
                                                <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Telefone</span></label>
                                                <input type="text" name="contato_tel[]" class="input input-bordered input-sm tel-mask" value="<?php echo htmlspecialchars($c['Telefone']); ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-8 border-t pt-6">
                            <?php if ($id): ?>
                                <a href="prestadores.php" class="btn btn-ghost">Cancelar</a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary px-8 shadow-lg">
                                <i class="ph ph-floppy-disk text-xl"></i> Salvar Fornecedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Table Column -->
        <div class="<?php echo CONTRATOS_CONSULTOR ? 'lg:col-span-7' : 'lg:col-span-12'; ?> space-y-4">
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body p-4">
                    <form method="GET" class="join w-full">
                        <input type="text" name="search" placeholder="Buscar por nome, CNPJ ou e-mail..." class="input input-bordered join-item w-full" value="<?php echo htmlspecialchars($search); ?>" />
                        <button type="submit" class="btn btn-primary join-item"><i class="ph ph-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'error_has_contracts'): ?>
                    <div class="alert alert-error text-white shadow-lg">
                        <i class="ph ph-warning-circle text-xl"></i>
                        <span>Não é possível excluir este fornecedor pois ele possui contratos atrelados.</span>
                    </div>
                <?php elseif ($_GET['msg'] === 'success'): ?>
                    <div class="alert alert-success text-white shadow-lg">
                        <i class="ph ph-check-circle text-xl"></i>
                        <span>Operação realizada com sucesso!</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card bg-base-100 shadow-xl border border-base-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="bg-base-200/50">
                                <th>Fornecedor</th>
                                <th>Documento</th>
                                <th>Localização</th>
                                <th class="text-right w-24">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestadores as $p): ?>
                                <tr class="hover group">
                                    <td>
                                        <div class="font-bold text-primary"><?php echo htmlspecialchars($p['Nome']); ?></div>
                                        <div class="text-[9px] badge badge-ghost badge-sm font-bold uppercase"><?php echo $p['Tipo']; ?></div>
                                    </td>
                                    <td class="text-xs font-mono"><?php echo htmlspecialchars($p['CNPJ']); ?></td>
                                    <td class="text-[11px] opacity-70">
                                        <?php echo htmlspecialchars($p['Cidade']) . ' / ' . htmlspecialchars($p['UF']); ?>
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
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($prestadores)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-12 opacity-50 italic">Nenhum fornecedor encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="contatoTemplate">
    <div class="contato-row p-4 bg-base-200/50 rounded-lg relative grid grid-cols-1 md:grid-cols-2 gap-3">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-circle btn-xs btn-error absolute -top-2 -right-2 text-white"><i class="ph ph-x"></i></button>
        <div class="form-control">
            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Tipo</span></label>
            <select name="contato_tipo[]" class="select select-bordered select-sm">
                <option value="Comercial">Comercial</option>
                <option value="Financeiro">Financeiro</option>
                <option value="Suporte">Suporte</option>
                <option value="Gestão">Gestão</option>
            </select>
        </div>
        <div class="form-control">
            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Nome</span></label>
            <input type="text" name="contato_nome[]" class="input input-bordered input-sm" placeholder="Nome do contato">
        </div>
        <div class="form-control">
            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">E-mail</span></label>
            <input type="email" name="contato_email[]" class="input input-bordered input-sm" placeholder="email@exemplo.com">
        </div>
        <div class="form-control">
            <label class="label p-1"><span class="label-text text-[10px] font-bold uppercase">Telefone</span></label>
            <input type="text" name="contato_tel[]" class="input input-bordered input-sm tel-mask" placeholder="(00) 00000-0000">
        </div>
    </div>
</template>

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
$(document).ready(function(){
    $('#cep').mask('00000-000');
    $('.tel-mask').mask('(00) 00000-0000');
    toggleMask();
});

function toggleMask() {
    const tipo = $('#tipo_pessoa').val();
    const doc = $('#documento');
    doc.val('');
    if (tipo === 'PJ') {
        doc.mask('00.000.000/0000-00');
        doc.attr('placeholder', '00.000.000/0000-00');
    } else {
        doc.mask('000.000.000-00');
        doc.attr('placeholder', '000.000.000-00');
    }
}

function addContatoRow() {
    const template = document.getElementById('contatoTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById('contatosList').appendChild(clone);
    $('.tel-mask').mask('(00) 00000-0000');
}

function buscarCEP(cep) {
    cep = cep.replace(/\D/g, '');
    if (cep.length !== 8) return;

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res => res.json())
        .then(data => {
            if (!data.erro) {
                $('#logradouro').val(data.logradouro);
                $('#bairro').val(data.bairro);
                $('#cidade').val(data.localidade);
                $('#uf').val(data.uf);
            }
        });
}

function confirmDelete(id, name) {
    document.getElementById('del_id').value = id;
    document.getElementById('del_name').innerText = name;
    delete_modal.showModal();
}
</script>

<?php require_once 'footer.php'; ?>
