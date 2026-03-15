# Documento de Requisitos do Produto (PRD) - GestorGov

## 1. Visão Geral do Produto
O **GestorGov** é um sistema web integrado voltado para a administração pública (focado nas demandas da Secretaria de Estado da Fazenda do Pará - SEFA). Seu objetivo primário é centralizar a gestão de operações internas através de módulos independentes, começando pelo **Módulo de Contratos**. O sistema preza pela segurança, rastreabilidade (auditoria) e uma experiência de usuário (UX) moderna e fluida.

## 2. Objetivos e Metas
- **Centralização:** Criar um "Portal" único (Launcher) onde os servidores acessam todas as ferramentas necessárias.
- **Segurança e Conformidade:** Eliminar senhas fracas através de login *passwordless* (Magic Link via E-mail) e garantir rastreabilidade total das ações (Auditoria).
- **Eficiência:** Facilitar a gestão do ciclo de vida de contratos, aditivos, fornecedores e documentos anexos.

## 3. Perfis de Usuário (Personas)
O sistema trabalha com um Controle de Acesso Baseado em Funções (RBAC).

1. **Administrador:** Acesso irrestrito a todos os módulos, configurações globais, gestão de usuários e logs de auditoria.
2. **Gestor (Módulo Contratos):** Acesso total às rotinas de negócio do módulo, incluindo deleção de registros sensíveis e exclusão de documentos.
3. **Consultor (Módulo Contratos):** Permissão operacional para criar e atualizar registros (Contratos, Termos, Fornecedores e Upload de Anexos), mas sem permissão de exclusão (Delete).
4. **Leitor:** Acesso estritamente de visualização aos dados operacionais.

## 4. Escopo Funcional (Features)

### 4.1. Autenticação e Portal (Launcher)
- **Login Passwordless:** Acesso exclusivo para e-mails institucionais (`@sefa.pa.gov.br`). O usuário insere o e-mail e recebe um token de acesso válido por 30 dias.
- **Gestão de Sessão:** Controle rigoroso de tokens no banco de dados e cookies *HttpOnly*.
- **Portal Central (Launcher):** Interface com *cards* configuráveis que direcionam o usuário para os módulos disponíveis baseados em suas permissões.

### 4.2. Módulo de Contratos (`app-contratos`)
- **Dashboard:** Visão geral com KPIs (Total de Contratos, Vigentes, A Vencer em 30 dias, Valor Global Acumulado).
- **Gestão de Fornecedores (Prestadores):** 
  - CRUD de Pessoas Físicas e Jurídicas com integração via API ViaCEP.
  - Gestão de múltiplos contatos por fornecedor.
  - Proteção contra exclusão de fornecedores que possuem contratos vinculados.
- **Gestão de Contratos e Termos Aditivos:**
  - Cadastro detalhado (Objeto, Valores, Datas de Vigência, Fiscais, Dotação Orçamentária).
  - Cálculo automático de *Vigência Efetiva* (considerando o maior prazo entre o contrato original e seus aditivos).
  - Sistema de alertas visuais para contratos vencidos ou a vencer.
- **Dossiê Digital (Anexos):**
  - Upload de múltiplos arquivos simultâneos associados a categorias (Contrato, Publicação, Errata, etc.).
  - Padronização automática do nome do arquivo no servidor (`[nome-original]+GGov+[Abrev].[ext]`).
  - Restrição de exclusão de anexos apenas para Gestores e Administradores.

### 4.3. Administração e Segurança
- **Gestão de Usuários:** Interface para cadastro, inativação e envio de e-mail de "Boas-vindas" para os servidores.
- **Logs de Auditoria:** 
  - Captura global de ações (`Create`, `Update`, `Delete`, `Upload`, `Login`).
  - Registro de payload detalhado (JSON) do que foi modificado, além do IP e *User Agent*.
  - Interface de visualização para Administradores com filtros avançados e exportação para CSV.
- **Configurações Globais:** Parametrização de tabelas auxiliares do sistema (Diretorias, Modalidades, Tipos de Documento, Categorias de Anexo).

## 5. Requisitos Não Funcionais (NFRs)
- **Tecnologia:** PHP 8.x (Vanilla), Banco de Dados MySQL (com PDO/Prepared Statements).
- **Interface:** TailwindCSS e DaisyUI (Tema: `corporate`). Ícones: Phosphor Icons.
- **Desempenho:** Consultas paginadas para listagens e uso intensivo de AJAX para não recarregar páginas desnecessariamente (ex: busca de fornecedores).
- **Segurança:** 
  - Proteção contra SQL Injection (PDO obrigatório).
  - Prevenção de XSS via `htmlspecialchars()` nas views.
  - Bloqueio de acesso a pastas de *upload* (`chmod 777` apenas onde estritamente necessário).

## 6. Fluxos Críticos
1. **Upload de Anexo:** O *Consultor* anexa um PDF em um Contrato > O sistema valida o `php.ini` (tamanho) > Valida a sessão > Move o arquivo para `uploads/{ano}/{numero_contrato}/` > Registra no banco `contratos_anexos` > Salva o evento em `sistema_logs`.
2. **Atualização de Permissão:** O *Administrador* muda um usuário de Consultor para Gestor > Sistema atualiza a tabela `usuarios` e `contratos_permissoes` > Salva o evento de alteração de permissão em `sistema_logs`.
