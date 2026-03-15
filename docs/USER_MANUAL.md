# Manual do Usuário - GestorGov

Bem-vindo ao **GestorGov**, a plataforma centralizada para gestão administrativa. Este manual irá guiá-lo pelas principais funcionalidades do sistema.

---

## 1. Primeiro Acesso e Login

O GestorGov utiliza um sistema de login sem senha (Passwordless) para garantir maior segurança.

1. Acesse a página inicial do sistema.
2. No campo de login, insira a primeira parte do seu e-mail institucional (antes do `@sefa.pa.gov.br`).
3. Clique em **Entrar**.
4. Verifique sua caixa de entrada no seu e-mail institucional. Você receberá um e-mail com o assunto "Link de Acesso - GestorGov".
5. Clique no botão **Acessar o Sistema** dentro do e-mail.
6. Você será redirecionado para o **Portal (Launcher)** e sua sessão ficará salva neste navegador por 30 dias.

---

## 2. O Portal Central (Launcher)

Após o login, você verá o Portal. Aqui estarão listados todos os módulos aos quais você tem acesso (ex: Módulo de Contratos, Configurações).

- **Módulos:** Clique no card correspondente ao módulo que deseja acessar.
- **Menu Superior:** No canto superior direito, você pode visualizar seu perfil e sair do sistema (Logout).
- Se você for um **Administrador**, verá também a opção de "Configurações Gerais", onde poderá gerenciar o próprio Launcher e os acessos.

---

## 3. Módulo de Contratos

Este é o módulo principal para o acompanhamento do ciclo de vida de contratos e fornecedores.

### 3.1. Dashboard
Ao entrar no módulo, a tela inicial (Dashboard) apresentará um resumo dos seus indicadores:
- Quantidade total de contratos.
- Contratos vigentes.
- Contratos a vencer nos próximos 30 dias.
- Gráficos de status e distribuição.

### 3.2. Gestão de Fornecedores (Prestadores)
1. No menu lateral, clique em **Fornecedores**.
2. **Consultar:** Utilize a barra de busca para encontrar um fornecedor por Nome, CNPJ/CPF ou Cidade.
3. **Novo Fornecedor (Apenas Consultores e Gestores):** Clique em "Novo Fornecedor". Preencha o CPF ou CNPJ, e o sistema buscará o endereço automaticamente ao preencher o CEP. Você pode adicionar múltiplos contatos (Financeiro, Suporte, etc.) no botão "Adicionar".
4. **Detalhes:** Clique no nome do fornecedor na tabela para abrir um modal com o endereço completo e lista de contatos.

### 3.3. Gestão de Contratos
1. No menu lateral, clique em **Lista de Contratos**.
2. **Novo Contrato:** Clique no botão "Novo Contrato". Preencha os dados (Número, Ano, Objeto, Datas).
   - **Dica:** Ao preencher o CPF/CNPJ do fornecedor e clicar fora do campo, o sistema validará automaticamente se ele existe na base de dados.
3. **Dossiê do Contrato (Visualização):** Clique na linha do contrato na tabela para ver todos os detalhes. O Dossiê é dividido em:
   - **Informações Básicas** (valores, objeto, vigência).
   - **Termos e Aditivos:** Lista de apostilamentos e aditivos. Para adicionar um novo, clique em "Adicionar Termo".
   - **Documentos Anexos:** Área para gestão de arquivos (PDFs, planilhas).

### 3.4. Anexando Arquivos (Uploads)
Dentro do Dossiê do Contrato, na seção "Documentos Anexos":
1. Clique em **Novo Anexo** (Disponível para Consultores, Gestores e Administradores).
2. Uma janela se abrirá. Clique em **Escolher Arquivo** e selecione o documento no seu computador.
3. Selecione a **Categoria** do documento (ex: Contrato, Errata, Publicação).
4. (Opcional) Digite uma breve descrição.
5. Se desejar enviar mais de um arquivo de uma vez, clique em **Adicionar mais arquivos**.
6. Clique em **Iniciar Upload**.

*Nota: Arquivos devem respeitar o limite de tamanho configurado no servidor (geralmente 2MB a 8MB).*

---

## 4. Auditoria e Logs (Apenas Administradores)

O GestorGov rastreia todas as ações realizadas no sistema. Para consultar o que foi feito:
1. No Portal, clique em **Configurações Gerais** (ícone de engrenagem).
2. Clique no card **Logs de Auditoria**.
3. **Filtrar:** Utilize os filtros de "Módulo", "Ação" (ex: Create, Update, Delete), "Usuário" ou "Período" para encontrar ações específicas.
4. **Detalhes:** Na coluna "Detalhes", clique no ícone de "olho" para ver exatamente qual foi o payload de dados gravado no banco de dados.
5. **Exportar CSV:** Para gerar relatórios, clique em "Exportar CSV" no topo da página. O arquivo respeitará os filtros que você aplicou na tela.

---

## 5. Dúvidas e Suporte
Se houver mensagens de erro como "Você não tem permissão para realizar esta ação", verifique com o seu **Administrador do Sistema** se o seu nível de perfil (`Leitor`, `Consultor` ou `Gestor`) está correto para o Módulo de Contratos.
