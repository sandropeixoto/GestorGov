<?php
// app-contratos/contract_form.php
require_once 'config.php';
require_once 'header.php';

if (!CONTRATOS_CONSULTOR) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;
$parent_id = $_GET['parent_id'] ?? null;
$contract = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM Contratos WHERE Id = ?");
    $stmt->execute([$id]);
    $contract = $stmt->fetch();
}

// Se não houver contrato mas houver parent_id, define o PaiId inicial
if (!$contract && $parent_id) {
    $contract = ['PaiId' => $parent_id];
}

// Fetch Prestadors for dropdown
$prestadors = $pdo->query("SELECT Id, Nome FROM Prestador ORDER BY Nome ASC")->fetchAll();

// Fetch Categories for dropdown
$categories = $pdo->query("SELECT Id, Descricao FROM CategoriaContrato ORDER BY Descricao ASC")->fetchAll();

// Fetch Modalidades for dropdown
$modalidades = $pdo->query("SELECT Id, Descricao FROM Modalidade ORDER BY Descricao ASC")->fetchAll();

// Fetch Diretorias for dropdown
$diretorias = $pdo->query("SELECT IdDiretoria, NomeDiretoria, SiglaDiretoria FROM Diretorias ORDER BY NomeDiretoria ASC")->fetchAll();

// Fetch Fontes for dropdown
$fontes = $pdo->query("SELECT IdFonte, NomeFonte FROM FontesRecursos ORDER BY NomeFonte ASC")->fetchAll();
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-base-content"><?php echo $id ? 'Editar Contrato' : 'Novo Contrato'; ?></h2>
            <p class="text-base-content/60">Preencha os campos abaixo com as informações do contrato.</p>
        </div>
        <a href="contratos.php" class="btn btn-ghost gap-2">
            <i class="ph ph-arrow-left"></i> Voltar
        </a>
    </div>

    <form action="contracts_action.php" method="POST" class="card bg-base-100 shadow-xl border border-base-200">
        <input type="hidden" name="action" value="<?php echo $id ? 'update' : 'create'; ?>">
        <input type="hidden" name="PaiId" value="0">
        <input type="hidden" name="TipoDocumentoId" value="1">
        <?php if ($id): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <div class="card-body space-y-8">
            <!-- Informações Básicas -->
            <section>
                <h3 class="text-lg font-bold border-b pb-2 mb-4 flex items-center gap-2">
                    <i class="ph ph-info text-primary"></i> Informações Básicas
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="form-control md:col-span-1">
                        <label class="label"><span class="label-text font-semibold">Número</span></label>
                        <input type="number" name="SeqContrato" required class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['SeqContrato'] ?? ''); ?>" placeholder="Ex: 45">
                    </div>
                    <div class="form-control md:col-span-1">
                        <label class="label"><span class="label-text font-semibold">Ano</span></label>
                        <input type="number" name="AnoContrato" required class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['AnoContrato'] ?? date('Y')); ?>">
                    </div>
                    <div class="form-control md:col-span-4">
                        <label class="label"><span class="label-text font-semibold">Objeto</span></label>
                        <textarea name="Objeto" required class="textarea textarea-bordered h-24" 
                                  placeholder="Descrição detalhada do contrato..."><?php echo htmlspecialchars($contract['Objeto'] ?? ''); ?></textarea>
                    </div>
                </div>
            </section>

            <!-- Datas e Vigência -->
            <section>
                <h3 class="text-lg font-bold border-b pb-2 mb-4 flex items-center gap-2">
                    <i class="ph ph-calendar text-primary"></i> Datas e Vigência
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="form-control md:col-span-1">
                        <label class="label"><span class="label-text font-semibold">Data Assinatura</span></label>
                        <input type="date" name="DataAssinatura" required class="input input-bordered w-full max-w-[180px]" 
                               value="<?php echo $contract['DataAssinatura'] ?? ''; ?>">
                    </div>
                    <div class="form-control md:col-span-1">
                        <label class="label"><span class="label-text font-semibold">Início Vigência</span></label>
                        <input type="date" name="VigenciaInicio" required class="input input-bordered w-full max-w-[180px]" 
                               value="<?php echo $contract['VigenciaInicio'] ?? ''; ?>">
                    </div>
                    <div class="form-control md:col-span-1">
                        <label class="label"><span class="label-text font-semibold">Fim Vigência</span></label>
                        <input type="date" name="VigenciaFim" required class="input input-bordered w-full max-w-[180px]" 
                               value="<?php echo $contract['VigenciaFim'] ?? ''; ?>">
                    </div>
                </div>
            </section>

            <!-- Fornecedor e Valores -->
            <section>
                <h3 class="text-lg font-bold border-b pb-2 mb-4 flex items-center gap-2">
                    <i class="ph ph-briefcase text-primary"></i> Fornecedor e Valores
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text font-semibold">Fornecedor / Prestador</span></label>
                        <select name="PrestadorId" required class="select select-bordered w-full">
                            <option value="">Selecione um fornecedor</option>
                            <?php foreach($prestadors as $p): ?>
                                <option value="<?php echo $p['Id']; ?>" <?php echo ($contract['PrestadorId'] ?? '') == $p['Id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['Nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Valor Mensal (R$)</span></label>
                        <input type="number" step="0.01" name="ValorMensalContrato" class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['ValorMensalContrato'] ?? ''); ?>" placeholder="0.00">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Valor Global (R$)</span></label>
                        <input type="number" step="0.01" name="ValorGlobalContrato" required class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['ValorGlobalContrato'] ?? ''); ?>" placeholder="0.00">
                    </div>
                </div>
            </section>

            <!-- Fiscalização -->
            <section>
                <h3 class="text-lg font-bold border-b pb-2 mb-4 flex items-center gap-2">
                    <i class="ph ph-user-focus text-primary"></i> Fiscalização
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text font-semibold">Diretoria Responsável</span></label>
                        <select name="DiretoriaId" class="select select-bordered w-full">
                            <option value="">Selecione...</option>
                            <?php foreach($diretorias as $d): ?>
                                <option value="<?php echo $d['IdDiretoria']; ?>" <?php echo ($contract['DiretoriaId'] ?? '') == $d['IdDiretoria'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($d['SiglaDiretoria']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Fiscal Titular</span></label>
                        <input type="text" name="FiscalContrato" class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['FiscalContrato'] ?? ''); ?>">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">E-mail do Fiscal</span></label>
                        <input type="email" name="EmailFiscal" class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['EmailFiscal'] ?? ''); ?>">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Fiscal Substituto</span></label>
                        <input type="text" name="FiscalSubstituto" class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['FiscalSubstituto'] ?? ''); ?>">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">E-mail do Substituto</span></label>
                        <input type="email" name="EmailFiscalSubstituto" class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['EmailFiscalSubstituto'] ?? ''); ?>">
                    </div>
                </div>
            </section>

            <!-- Outros Detalhes -->
            <section>
                <h3 class="text-lg font-bold border-b pb-2 mb-4 flex items-center gap-2">
                    <i class="ph ph-plus text-primary"></i> Outros Detalhes
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Categoria</span></label>
                        <select name="CategoriaContratoId" class="select select-bordered w-full">
                            <option value="">Selecione...</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['Id']; ?>" <?php echo ($contract['CategoriaContratoId'] ?? '') == $cat['Id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['Descricao']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Fonte de Recurso</span></label>
                        <select name="FonteRecursosId" class="select select-bordered w-full">
                            <option value="">Selecione...</option>
                            <?php foreach($fontes as $f): ?>
                                <option value="<?php echo $f['IdFonte']; ?>" <?php echo ($contract['FonteRecursosId'] ?? '') == $f['IdFonte'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($f['NomeFonte']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Número do Processo</span></label>
                        <input type="text" name="NProcesso" class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['NProcesso'] ?? ''); ?>">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Modalidade</span></label>
                        <select name="ModalidadeId" class="select select-bordered w-full">
                            <option value="">Selecione...</option>
                            <?php foreach($modalidades as $m): ?>
                                <option value="<?php echo $m['Id']; ?>" <?php echo ($contract['ModalidadeId'] ?? '') == $m['Id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($m['Descricao']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Número da Modalidade</span></label>
                        <input type="text" name="NumeroModalidade" class="input input-bordered" 
                               value="<?php echo htmlspecialchars($contract['NumeroModalidade'] ?? ''); ?>" placeholder="Ex: 002/2021">
                    </div>
                </div>
            </section>
        </div>

        <div class="card-footer p-8 bg-base-200/50 flex justify-end gap-3">
            <a href="contratos.php" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary px-8 shadow-lg">
                <i class="ph ph-floppy-disk text-xl"></i> Salvar Contrato
            </button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
