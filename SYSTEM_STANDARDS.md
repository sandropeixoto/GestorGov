# 🛠️ Padrões Técnicos do Sistema

## 1. Backend (PHP 8.x)
- **Prepared Statements**: SEMPRE utilizar `PDO::prepare` e `execute` para evitar SQL Injection.
- **Segurança de Acesso**: Todo arquivo de ação (`_action.php`) deve carregar `auth_check.php` e `auth_module.php` e validar as permissões (`CONTRATOS_GESTOR`, `CONTRATOS_CONSULTOR`).
- **Autenticação**: Baseada em links de acesso enviados por e-mail, linkados à tabela `login_tokens`.

## 2. Frontend (Tailwind + DaisyUI)
- **Tema**: `corporate` (DaisyUI).
- **Framework**: TailwindCSS via CDN para desenvolvimento rápido.
- **Icons**: Phosphor Icons (`ph ph-xxx`).
- **Gráficos**: Chart.js para visualização de dados no Dashboard.

## 3. SQL e Banco de Dados
- **Identificação**: Contratos são identificados pela dupla `SeqContrato` / `AnoContrato`.
- **Hierarquia**: `PaiId = 0` para contratos principais; `PaiId > 0` para termos vinculados.
- **Vigência**: Vigência efetiva é o maior valor de `VigenciaFim` entre o contrato principal e seus aditivos.
