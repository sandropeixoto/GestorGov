# 📜 GestorGov - Manual de Verdades Absolutas

Este documento define os padrões inegociáveis do sistema. Qualquer nova implementação deve respeitar rigorosamente estas definições.

## 1. Identidade Visual e Layout
- **Layout Base**: Fullscreen utilizando **Flexbox**. O conteúdo ocupa 100% da largura restante.
- **Sidebar (Menu Lateral)**:
    - Cor de Fundo: Deep Navy Blue `#0f172a`.
    - Cores de Fonte: Branco puro ou com opacidade para hierarquia.
    - Comportamento: Colapsável no desktop (estado em `$_SESSION['sidebar_collapsed']`).
    - Mobile: Drawer lateral oculto por padrão.
    - **Rodapé da Sidebar**: Exibe nome do usuário e Perfil Efetivo no módulo.
- **Topbar**: Glassmorphism (`backdrop-filter: blur`), fixo no topo, contém breadcrumbs e menu de perfil.
- **Frameworks**: PHP 8.x (Vanilla), TailwindCSS (CDN), DaisyUI (Tema: `corporate`).
- **Ícones**: Sempre utilizar **Phosphor Icons** (`ph` ou `ph-fill`).

## 2. Sistema de Permissões (Módulo Contratos)
O acesso é controlado por constantes definidas em `auth_module.php`:
- **Administrador (Global)**: Acesso total, incluindo aba "Permissões" em Configurações.
- **Gestor (Módulo)**: Pode Incluir, Editar e Excluir contratos e fornecedores. Não acessa Configurações.
- **Consultor (Módulo)**: Pode Incluir e Editar contratos e fornecedores. **Não pode excluir**.
- **Leitor (Global)**: Acesso apenas de visualização se a chave `acesso_leitura_global` estiver ativa.
- **Bloqueado**: Sem perfil definido e com leitura global desligada, o acesso ao módulo é negado.

## 3. Regras de Negócio de Contratos
- **Identificação**: O número oficial é `SeqContrato` / `AnoContrato`.
- **Hierarquia**:
    - **Contrato Principal**: `PaiId = 0`.
    - **Termos/Aditivos**: `PaiId = ID_DO_CONTRATO_PAI`.
- **Vigência Efetiva**: Calculada pelo `MAX(VigenciaFim)` entre o principal e todos os seus termos vinculados.
- **Dossiê**: A `contract_view.php` é o centro de gestão, consolidando todo o histórico do contrato.

## 4. Padrões de Desenvolvimento
- **Banco de Dados**: PDO com Prepared Statements.
- **Segurança**: Proteção de backend em todos os arquivos `_action.php` validando permissões específicas.
- **Componentização**: Partes repetitivas da sidebar em `sidebar_content.php`.
- **Persistência**: Filtros e estados de UI mantidos via `$_SESSION`.

## 5. Estrutura de Diretórios
- `/app-contratos/`: Módulo de gestão contratual.
- `auth_check.php` & `verify.php`: Núcleo de autenticação global.
- `auth_module.php`: Lógica de permissões específica do módulo.
