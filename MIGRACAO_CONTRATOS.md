# 🚀 Guia de Migração: Módulo de Contratos (GestorGov)

Este documento serve como a **Única Fonte de Verdade (SSOT)** para a nova IA que assumirá o projeto de Contratos de forma isolada.

## 1. Contexto do Projeto
O sistema de Contratos foi extraído do portal **GestorGov**. Ele opera de forma satélite, recebendo autenticação via **SSO (Single Sign-On)** a partir de um Portal centralizador, mas possuindo seu próprio banco de dados independente.

## 2. Credenciais de Banco de Dados (Destino)
A IA deve configurar o arquivo `config.php` com as seguintes credenciais:
- **Host:** `192.185.214.25`
- **Banco de Dados:** `eventoss_contratos`
- **Usuário:** `eventoss_contratos`
- **Senha:** `Senh@2025`
- **Charset:** `utf8mb4`

## 3. Arquitetura de Autenticação (SSO)
O acesso ao sistema ocorre prioritariamente via `auth_sso.php`.
- **Mecanismo:** Token assinado via HMAC-SHA256.
- **Chave Secreta (Shared Key):** `GestorGov_Secure_Integration_Token_2026!` (Deve ser idêntica à do Portal).
- **Fluxo:** 
    1. Portal envia `sso_payload` (Base64) e `sso_sig` (Assinatura).
    2. `auth_sso.php` valida a assinatura.
    3. Payload decodificado hidrata a `\$_SESSION` local.
    4. `auth_module.php` faz o controle de acesso fino (RBAC) baseado nas tabelas locais.

## 4. Manifesto de Arquivos (Checklist de Migração)
A IA deve verificar a existência e integridade dos seguintes arquivos no novo projeto:

### 📁 Raiz do Projeto (Extraídos de /app-contratos/)
- [ ] `config.php`: Configurações de banco, constantes de caminhos e a `SSO_SECRET_KEY`.
- [ ] `auth_sso.php`: Receptor de login do Portal. **Crítico.**
- [ ] `auth_module.php`: Lógica de travas de segurança e níveis (Admin, Gestor, Consultor).
- [ ] `logger.php`: (Migrar da raiz original) Script de auditoria de logs.
- [ ] `header.php` / `footer.php`: Templates visuais (DaisyUI/Tailwind).
- [ ] `sidebar_content.php`: Menu lateral dinâmico.
- [ ] `index.php`: Dashboard principal de contratos (KPIs e Stats).
- [ ] `contratos.php`: Listagem principal com filtros avançados.
- [ ] `contract_view.php`: Dossiê completo do contrato (Visualização).
- [ ] `contract_form.php`: Formulário de criação/edição e inclusão de Aditivos.
- [ ] `contracts_action.php`: Processamento de dados (CRUD) de contratos.
- [ ] `prestadores.php`: Gestão de fornecedores.
- [ ] `prestador_form.php` / `prestadores_action.php`: CRUD de fornecedores.
- [ ] `ajax_prestador.php`: Busca dinâmica de prestadores.
- [ ] `contratos_anexos_action.php`: Upload e gestão de documentos anexos.
- [ ] `settings.php` / `settings_action.php`: Configurações específicas do módulo.
- [ ] `uploads/`: Pasta para armazenamento físico dos anexos.

## 5. Instruções para a Nova IA
1. **Validação de Ambiente:** Ao iniciar, verifique se todos os arquivos listados acima estão presentes.
2. **Conexão de Banco:** Teste a conexão com `eventoss_contratos` e verifique a existência das tabelas principais (`Contratos`, `Prestador`, `usuarios`).
3. **Robustez de Nível:** Certifique-se de que a comparação de `\$_SESSION['user_level']` seja **Case-Insensitive** e suporte IDs numéricos (1=Admin, 2=Gestor).
4. **Logs:** Todas as ações críticas (Create/Update/Delete) devem chamar a função `logSistema()` definida em `logger.php`.
5. **SSO:** Nunca permita acesso direto às páginas sem uma sessão ativa ou sem passar pelo `auth_sso.php`.

---
*Documento gerado pelo Agente Orion (AIOX Master) em 16/03/2026.*
