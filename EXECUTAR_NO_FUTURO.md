# ⚠️ Ação Pendente: Limpeza de Tabelas Legadas (Contratos)

Este documento contém o plano de remoção das tabelas que foram migradas para o banco `eventoss_contratos`. 

**NÃO EXECUTAR** até que todos os outros módulos (PCA, Viagens, Estagiários, etc.) sejam validados e confirmados que não possuem dependências cruzadas com estas tabelas no banco `eventoss_vocegov`.

## Tabelas para Remoção (DROP)
Caso confirmada a independência dos módulos, as seguintes tabelas podem ser removidas do banco `eventoss_vocegov`:

### 📦 Módulo de Contratos & Fornecedores
- `Contratos`
- `Prestador`
- `prestador_contatos`
- `SituacoesContratos`
- `contratos_anexos`
- `contratos_anexos_categorias`
- `contratos_configuracoes`
- `contratos_coordenacoes`
- `contratos_permissoes`
- `contratos_fiscais_setoriais`

### 🤝 Módulo de Convênios & Parcerias
- `Convenio`
- `Convenio_Atividade`
- `Convenio_Desembolso`
- `Convenio_Documento`
- `Convenio_Execucao`
- `Convenio_Participante`
- `Convenio_PlanoTrabalho`
- `Convenio_Publicacao`

### ⚙️ Tabelas de Apoio (Checar dependências com outros módulos antes)
- `TiposDocumentos`
- `Modalidade`
- `CategoriaContrato`
- `Diretorias`
- `FontesRecursos`
- `Acompanhamento`
- `AcompanhamentoItens`
- `Lei`
- `Inciso`
- `Artigo`
- `adsefa` (Tabela de apoio legada)

## Notas de Segurança
1. **NUNCA REMOVER** a tabela `usuarios`, `launcher_modules`, `login_tokens` ou `sistema_logs`, pois são o coração do Portal.
2. Antes de qualquer DROP, realize um **Backup Completo (Dump)** do banco `eventoss_vocegov`.
3. Verifique se as Views (`vw_...`) que referenciam estas tabelas também devem ser removidas ou atualizadas.

---
*Plano arquivado em 16/03/2026 para validação futura.*
