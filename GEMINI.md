# 📜 GestorGov - Manual de Verdades Absolutas (SSOT)

Este documento é a **Única Fonte de Verdade** para o layout, UX e Copywriting do sistema. Qualquer alteração nestes padrões exige instrução explícita e atualização deste manual.

## 1. Identidade Visual e UI
### 🎨 Cores e Estilos
- **Fundo da Sidebar/Drawer**: Deep Navy Blue `#0f172a`.
- **Fundo do Conteúdo Principal**: Off-White/Blueish `#f8fafc`.
- **Cor Primária (DaisyUI Primary)**: Indigo/Purple `#570df8`.
- **Cores de Gráficos (Analytics)**: `['#570df8', '#f000b8', '#37cdbe', '#3d4451', '#fbbd23', '#ef4f4f']`.
- **Bordas e Divisores**: `border-white/5` (Sidebar) e `border-base-200` (Cards).
- **Glassmorphism**: Header com `backdrop-filter: blur(10px)` e fundo `rgba(255, 255, 255, 0.85)`.

### 🔡 Tipografia e Espaçamentos
- **Fonte Principal**: `Inter` (Google Fonts), sans-serif.
- **Títulos de Página**: `text-3xl font-bold text-base-content`.
- **Títulos de Seção**: `text-lg font-bold border-b pb-2 mb-4`.
- **Padding do Viewport**: `p-4 md:p-8`.
- **Gaps**: `gap-6` (Grids de Stats), `gap-8` (Grids de Conteúdo Principal).

## 2. UX e Fluxo de Navegação
### 🔄 Caminhos Definidos
- **Dashboard ➔ Listagem**: Clicar nos cards de KPI (Total, Vigentes, A Vencer) redireciona para `contratos.php` com filtros aplicados.
- **Listagem ➔ Dossiê**: O clique na linha da tabela (`tr`) redireciona para `contract_view.php?id={id}`.
- **Dossiê ➔ Edição**: Botão "Editar Contrato" leva para `contract_form.php?id={id}`.
- **Dossiê ➔ Novo Termo**: Botão "Adicionar Termo" leva para `contract_form.php?parent_id={id}`.
- **Persistência**: Filtros de pesquisa e estado da sidebar (`$_SESSION['sidebar_collapsed']`) são mantidos durante a sessão.

### 🛡️ Regras de Interação
- **Modais de Exclusão**: Sempre exigir confirmação via `<dialog>` antes de processar `DELETE`.
- **Botão de Voltar**: Presente em formulários e dossiês, sempre retornando à listagem principal.
- **Sidebar Retrátil**: Salva estado no backend para consistência entre páginas.

## 3. Copywriting (Textos Exatos)
### 🔘 Botões e Ações
- **Principal**: `Novo Contrato`, `Salvar Contrato`, `Adicionar Termo`.
- **Secundário**: `Ver todos`, `Limpar`, `Remover todos`, `Cancelar`.
- **Crítico**: `Sim, Excluir`.

### 🏷️ Labels e Cabeçalhos
- **Dashboard**: "Dashboard de Contratos", "Total de Contratos", "Contratos Vigentes", "A vencer (30 dias)", "Valor Global Acumulado".
- **Listagem**: "Lista de Contratos", "Número/Ano", "Objeto", "Fornecedor", "Vencimento", "Valor Global".
- **Breadcrumbs**: "Painel Geral", "Início", "Módulo de Contratos".

## 4. Diretriz de Imutabilidade
> [!IMPORTANT]
> **As definições acima são imutáveis.** Não alterar cores, espaçamentos, fontes ou textos de interface sem ordem específica. Em caso de dúvida, a implementação deve seguir rigorosamente o código atual mapeado neste manual.
