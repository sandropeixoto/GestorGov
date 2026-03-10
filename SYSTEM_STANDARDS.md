# 🛠️ Padrões Técnicos do Sistema

## 1. Backend (PHP 8.x)
- **Prepared Statements**: SEMPRE utilizar `PDO::prepare` e `execute` para evitar SQL Injection.
- **Segurança de Acesso**: Todo arquivo de ação (`_action.php`) deve carregar `auth_check.php` e `auth_module.php` e validar as permissões (`CONTRATOS_GESTOR`, `CONTRATOS_CONSULTOR`).
- **Gestão de Erros**: Utilizar blocos `try-catch` em todas as operações de banco de dados e exibir erros de forma segura (sem vazar credenciais).

## 2. Frontend (Tailwind + DaisyUI)
- **Tema**: `corporate`.
- **Botões**:
    - Primário: `btn-primary`.
    - Perigo: `btn-error`.
    - Fantasma: `btn-ghost`.
- **Inputs**: `input input-bordered w-full`.
- **Modais**: Utilizar a tag `<dialog class="modal">` com controle via JavaScript (`modal.showModal()`).
- **Icons**: Importados via CDN do Phosphor Icons.

## 3. SQL e Banco de Dados
- **Relacionamentos**: Utilizar `ON DELETE CASCADE` para garantir integridade (ex: permissões de usuário excluídas com o usuário).
- **Tipagem**: IDs como `INT AUTO_INCREMENT`, chaves de configuração como `VARCHAR`.
- **Engine**: `InnoDB` para suporte a transações e chaves estrangeiras.

## 4. UI/UX (Consistência)
- **Feedback Visual**: Exibir parâmetros de filtros ativos em badges (`contratos.php`).
- **Estados de Sidebar**: Sincronizar classes CSS com a variável `$_SESSION['sidebar_collapsed']`.
- **Mobile First**: Garantir que o drawer lateral funcione corretamente em telas menores.
