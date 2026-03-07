# 🛠️ Padrões Técnicos do Sistema GestorGov

## Arquitetura de Software
- **Linguagem**: PHP 8.2+
- **Database**: MySQL 8.0+ utilizando o driver PDO.
- **Frontend**: 
    - CSS: TailwindCSS 3.4+
    - UI: DaisyUI 4.7+
    - Gráficos: Chart.js 4.4+
    - Ícones: Phosphor Icons 2.1+

## Convenções de Código
- **Arquivos**: Nomeação em snake_case (ex: `contract_view.php`).
- **Segurança**:
    - Sanitização de inputs globais.
    - XSS Prevention: Uso de `htmlspecialchars()` em todos os echos de dados do usuário.
    - SQLi Prevention: 100% de uso de Prepared Statements.
- **Persistence**: Filtros de tabelas persistidos em `$_SESSION['contratos_filters']`.

## Workflow de Contratos
1. **Cadastro**: Novo contrato (ID 1).
2. **Aditavação**: Criar novo registro vinculando `PaiId` e definindo `TipoDocumentoId` (2, 3, 4...).
3. **Consulta**: Listagem utiliza `HAVING` para filtrar pela `VigenciaEfetiva` calculada em tempo real via SQL Aggregation.

## Layout e UX
- **Sidebar**: Toggle via GET param `?toggle_sidebar=1` que inverte booleano na sessão.
- **Responsividade**: Mobile-first approach com breakpoints do Tailwind (`md:`, `lg:`).
- **Navigation**: Breadcrumbs dinâmicos e persistência de retorno ao estado anterior da lista.
