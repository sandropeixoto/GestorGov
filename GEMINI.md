# 📜 GestorGov - Manual de Verdades Absolutas

Este documento define os padrões inegociáveis do sistema. Qualquer nova implementação deve respeitar rigorosamente estas definições.

## 1. Identidade Visual e Layout
- **Layout Base**: Fullscreen utilizando **Flexbox**. O conteúdo deve ocupar 100% da largura restante da tela.
- **Sidebar (Menu Lateral)**:
    - Cor de Fundo: Deep Navy Blue `#0f172a`.
    - Cores de Fonte: Branco puro ou com opacidade para hierarquia.
    - Comportamento: Colapsável no desktop (estado salvo em `$_SESSION['sidebar_collapsed']`).
    - Mobile: Drawer lateral oculto por padrão.
- **Frameworks**: PHP 8.x (Vanilla), TailwindCSS (CDN para dev, PostCSS para prod), DaisyUI (Tema: `corporate`).
- **Ícones**: Sempre utilizar **Phosphor Icons**.

## 2. Regras de Negócio de Contratos
- **Identificação**: O número oficial de um contrato é a composição de `SeqContrato` / `AnoContrato`. O campo `NumeroContrato` é obsoleto e não deve ser usado.
- **Hierarquia**:
    - **Contrato Principal**: Registro onde `PaiId = 0`.
    - **Termos Vinculados (Aditivos, Apostilamentos, etc)**: Registros onde `PaiId = ID_DO_CONTRATO_PAI`.
- **Vigência Efetiva**: A data de vencimento real de um contrato é o `MAX(VigenciaFim)` entre o contrato principal e todos os seus termos vinculados.
- **Tipos de Documentos**: Definidos na tabela `TiposDocumentos`. ID 1 é reservado para "Contrato".

## 3. Padrões de Desenvolvimento
- **Banco de Dados**: Acesso via **PDO** com prepared statements (proteção contra SQL Injection).
- **Persistência de UI**: Filtros de pesquisa e estado da sidebar devem ser mantidos via `$_SESSION`.
- **Dossiê**: O centro de gestão é a `contract_view.php`. Ações de edição/exclusão em termos devem redirecionar o usuário de volta ao dossiê pai.
- **Componentização**: Partes repetitivas do menu devem estar em `sidebar_content.php`.

## 4. Estrutura de Diretórios
- `/app-contratos/`: Módulo principal de gestão contratual.
- `/SUGGESTIONS.md`: Registro de melhorias evolutivas propostas.
- `/database_update.sql`: Histórico de migrações estruturais do banco.
