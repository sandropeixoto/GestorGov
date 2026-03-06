<?php
// app-contratos/contract_view.php
require_once 'config.php';
require_once 'header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: contratos.php");
    exit;
}

try {
    // Busca Contrato Principal com nomes de tabelas auxiliares e Vigência Efetiva
    $stmt = $pdo->prepare("
        SELECT c.*, p.Nome as PrestadorNome, p.CNPJ as PrestadorCNPJ, p.Email as PrestadorEmail,
               m.Descricao as ModalidadeNome, cat.Descricao as CategoriaNome, f.NomeFonte as FonteNome,
               d.SiglaDiretoria as DiretoriaSigla,
               GREATEST(c.VigenciaFim, COALESCE((SELECT MAX(VigenciaFim) FROM Contratos WHERE PaiId = c.Id), '0000-00-00')) as VigenciaEfetiva
        FROM Contratos c
        LEFT JOIN Prestador p ON c.PrestadorId = p.Id
        LEFT JOIN Modalidade m ON c.ModalidadeId = m.Id
        LEFT JOIN CategoriaContrato cat ON c.CategoriaContratoId = cat.Id
        LEFT JOIN FontesRecursos f ON c.FonteRecursosId = f.IdFonte
        LEFT JOIN Diretorias d ON c.DiretoriaId = d.IdDiretoria
        WHERE c.Id = ? AND c.PaiId = 0
    ");
    $stmt->execute([$id]);
    $contract = $stmt->fetch();

    if (!$contract) {
        die("Contrato não encontrado.");
    }

    // Busca Termos Vinculados (Aditivos, Apostilamentos, etc)
    $stmt_terms = $pdo->prepare("
        SELECT c.*, td.Nome as TipoNome
        FROM Contratos c
        LEFT JOIN TiposDocumentos td ON c.TipoDocumentoId = td.Id
        WHERE c.PaiId = ?
        ORDER BY c.DataAssinatura ASC, c.Id ASC
    ");
    $stmt_terms->execute([$id]);
    $terms = $stmt_terms->fetchAll();

    // Cálculo do Valor Total (Contrato + Aditivos)
    $total_value = $contract['ValorGlobalContrato'];
    foreach($terms as $t) {
        $total_value += $t['ValorGlobalContrato'];
    }

} catch (PDOException $e) {
    die("Erro ao carregar dossiê: " . $e->getMessage());
}

// Lógica de Status
$vencimento = new DateTime($contract['VigenciaEfetiva']);
$hoje = new DateTime();
$diff = $hoje->diff($vencimento);
$is_expired = ($vencimento < $hoje);
$is_warning = (!$is_expired && $diff->days <= 30);
?>

<div class="space-y-6">
    <!-- Header de Ações -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="contratos.php" class="btn btn-circle btn-ghost">
                <i class="ph ph-arrow-left text-2xl"></i>
            </a>
            <div>
                <h2 class="text-3xl font-bold">Contrato <?php echo $contract['SeqContrato'] . '/' . $contract['AnoContrato']; ?></h2>
                <div class="flex items-center gap-2 mt-1">
                    <?php if ($is_expired): ?>
                        <span class="badge badge-error gap-1"><i class="ph ph-x-circle"></i> Vencido</span>
                    <?php elseif ($is_warning): ?>
                        <span class="badge badge-warning gap-1"><i class="ph ph-warning"></i> Vence em <?php echo $diff->days; ?> dias</span>
                    <?php else: ?>
                        <span class="badge badge-success text-white gap-1"><i class="ph ph-check-circle"></i> Vigente</span>
                    <?php endif; ?>
                    <span class="text-base-content/50 text-sm">• Fornecedor: <?php echo htmlspecialchars($contract['PrestadorNome']); ?></span>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="contract_form.php?id=<?php echo $id; ?>" class="btn btn-outline btn-info gap-2">
                <i class="ph ph-pencil-simple"></i> Editar Contrato
            </a>
            <button onclick="confirmDelete(<?php echo $id; ?>, '<?php echo $contract['SeqContrato'] . '/' . $contract['AnoContrato']; ?>')" class="btn btn-outline btn-error gap-2">
                <i class="ph ph-trash"></i> Excluir
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Coluna Principal (Informações e Termos) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Card de Resumo Principal -->
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h3 class="font-bold text-lg border-b pb-2 mb-4">Resumo do Objeto</h3>
                    <p class="text-base-content/80 leading-relaxed italic">
                        "<?php echo nl2br(htmlspecialchars($contract['Objeto'])); ?>"
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <div class="stat p-0">
                            <div class="stat-title text-xs uppercase font-bold">Vigência Efetiva</div>
                            <div class="stat-value text-2xl text-primary"><?php echo date('d/m/Y', strtotime($contract['VigenciaEfetiva'])); ?></div>
                            <div class="stat-desc font-medium">Início: <?php echo date('d/m/Y', strtotime($contract['VigenciaInicio'])); ?></div>
                        </div>
                        <div class="stat p-0">
                            <div class="stat-title text-xs uppercase font-bold">Valor Mensal Original</div>
                            <div class="stat-value text-2xl">R$ <?php echo number_format($contract['ValorMensalContrato'], 2, ',', '.'); ?></div>
                        </div>
                        <div class="stat p-0">
                            <div class="stat-title text-xs uppercase font-bold text-secondary">Valor Total Acumulado</div>
                            <div class="stat-value text-2xl text-secondary">R$ <?php echo number_format($total_value, 2, ',', '.'); ?></div>
                            <div class="stat-desc font-bold text-secondary/70">Contrato + Aditivos</div>
                        </div>
                    </div>

                    <!-- Seção Ver Mais -->
                    <div class="collapse collapse-arrow mt-6 bg-base-200/50 rounded-lg">
                        <input type="checkbox" /> 
                        <div class="collapse-title text-sm font-bold flex items-center gap-2">
                            <i class="ph ph-plus-circle text-primary"></i> Clique para visualizar detalhes administrativos (Fiscal, Licitação, Recursos)
                        </div>
                        <div class="collapse-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                                <div class="space-y-3">
                                    <p class="text-sm"><strong>Diretoria:</strong> <?php echo htmlspecialchars($contract['DiretoriaSigla'] ?? 'Não informada'); ?></p>
                                    <p class="text-sm"><strong>Fiscal Titular:</strong> <?php echo htmlspecialchars($contract['FiscalContrato'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($contract['EmailFiscal'] ?? '-'); ?>)</p>
                                    <p class="text-sm"><strong>Fiscal Substituto:</strong> <?php echo htmlspecialchars($contract['FiscalSubstituto'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="space-y-3">
                                    <p class="text-sm"><strong>Modalidade:</strong> <?php echo htmlspecialchars($contract['ModalidadeNome'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($contract['NumeroModalidade'] ?? '-'); ?>)</p>
                                    <p class="text-sm"><strong>Nº Processo:</strong> <?php echo htmlspecialchars($contract['NProcesso'] ?? 'N/A'); ?></p>
                                    <p class="text-sm"><strong>Fonte de Recurso:</strong> <?php echo htmlspecialchars($contract['FonteNome'] ?? 'N/A'); ?></p>
                                    <p class="text-sm"><strong>Categoria:</strong> <?php echo htmlspecialchars($contract['CategoriaNome'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Listagem de Termos Vinculados -->
            <div class="card bg-base-100 shadow-xl border border-base-200 overflow-hidden">
                <div class="p-6 border-b border-base-200 flex justify-between items-center bg-base-50">
                    <h3 class="text-xl font-bold flex items-center gap-2">
                        <i class="ph ph-stack text-primary"></i> Termos e Aditivos
                    </h3>
                    <a href="contract_form.php?parent_id=<?php echo $id; ?>" class="btn btn-primary btn-sm gap-2">
                        <i class="ph ph-plus-circle"></i> Adicionar Termo
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="bg-base-200/50">
                                <th>Tipo</th>
                                <th>Nº/Ano</th>
                                <th>Data Assinatura</th>
                                <th>Nova Vigência</th>
                                <th class="text-right">Valor</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($terms as $t): ?>
                                <tr class="hover group">
                                    <td><span class="badge badge-outline badge-sm uppercase font-bold text-[10px]"><?php echo htmlspecialchars($t['TipoNome']); ?></span></td>
                                    <td class="font-bold"><?php echo $t['SeqContrato'] . '/' . $t['AnoContrato']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($t['DataAssinatura'])); ?></td>
                                    <td class="text-primary font-medium"><?php echo date('d/m/Y', strtotime($t['VigenciaFim'])); ?></td>
                                    <td class="text-right font-semibold">R$ <?php echo number_format($t['ValorGlobalContrato'], 2, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <div class="flex justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="contract_form.php?id=<?php echo $t['Id']; ?>" class="btn btn-square btn-sm btn-ghost text-info"><i class="ph ph-pencil-simple text-lg"></i></a>
                                            <button onclick="confirmDelete(<?php echo $t['Id']; ?>, '<?php echo $t['SeqContrato'] . '/' . $t['AnoContrato']; ?>')" class="btn btn-square btn-sm btn-ghost text-error"><i class="ph ph-trash text-lg"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($terms)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-12 opacity-50 italic">Nenhum termo ou aditivo vinculado a este contrato.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Coluna Lateral (Dossiê do Fornecedor e Atalhos) -->
        <div class="space-y-6">
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h3 class="card-title text-sm uppercase opacity-50"><i class="ph ph-buildings"></i> Dados do Fornecedor</h3>
                    <p class="font-bold text-lg mt-2"><?php echo htmlspecialchars($contract['PrestadorNome']); ?></p>
                    <p class="text-sm font-mono opacity-70"><?php echo htmlspecialchars($contract['PrestadorCNPJ']); ?></p>
                    
                    <div class="divider my-2"></div>
                    
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="ph ph-envelope text-primary"></i>
                            <span><?php echo htmlspecialchars($contract['PrestadorEmail'] ?? 'E-mail não cadastrado'); ?></span>
                        </div>
                        <a href="prestadores.php?id=<?php echo $contract['PrestadorId']; ?>" class="btn btn-ghost btn-xs text-primary justify-start px-0">
                            <i class="ph ph-arrow-square-out"></i> Ver cadastro completo
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-primary text-primary-content shadow-xl">
                <div class="card-body">
                    <h3 class="card-title"><i class="ph ph-info"></i> Notas do Sistema</h3>
                    <p class="text-sm opacity-90">Este dossiê consolida todas as informações desde a assinatura original até o último termo de apostilamento ou aditivo registrado.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exclusão (Reutilizado) -->
<dialog id="delete_modal" class="modal">
  <div class="modal-box">
    <h3 class="font-bold text-lg text-error flex items-center gap-2"><i class="ph ph-warning"></i> Confirmar Exclusão</h3>
    <p class="py-4">Deseja excluir o documento <span id="del_name" class="font-bold"></span>?</p>
    <div class="modal-action">
      <form method="POST" action="contracts_action.php">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="del_id">
        <input type="hidden" name="redirect" value="contract_view.php?id=<?php echo $id; ?>">
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
