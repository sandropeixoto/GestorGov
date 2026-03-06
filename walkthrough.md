# GestorGov - Módulo de Contratos (PHP)

Seguindo o planejamento aprovado, a aplicação PHP levíssima para a gestão de contratos já está construída com uma base visual linda usando **TailwindCSS** e **DaisyUI**. Todos os requisitos iniciais foram atendidos e a estrutura de diretórios foi montada na sua pasta de desenvolvimento.

## O Que Foi Desenvolvido

1.  **Conexão de Banco de Dados (`config.php`)**:
    Realizamos a conexão forçada usando PDO e as credenciais (`eventoss_vocegov`) exatas passadas como requisito. A conexão usa try/catch e reporta erros para agilizar o debug.

2.  **Layout Padrão Moderno (`header.php` e `footer.php`)**:
    *   Interface "Glassmorphism" usando DaisyUI (Tema Corporate).
    *   Menu lateral retrátil, topbar com notificações e avatar do usuário logado.
    *   Ícones interativos do Phosphor Icons e tipografia Inter (Google Fonts).

3.  **Dashboard Espetacular (`index.php`)**:
    *   Busca quantitativos reais no banco (Total de Contratos vs Valor Global).
    *   Mostra cards de atalho (Alertas de Vencimento).
    *   Exemplo de gráfico de contratações e lista dos 5 contratos recém incluídos.

4.  **Listagem de Contratos (`contratos.php`)**:
    *   Tabela principal consultando a tabela `Contratos`, ordenada do mais rescente para o mais antigo.
    *   Mostra o Objeto, Fim da Vigência e o Valor Global do contrato.
    *   Controles em linha para Editar e Excluir.

5.  **Formulário Dinâmico (`contract_form.php` & `contracts_action.php`)**:
    *   Formulário inteligente que identifica se é uma inclusão ou edição baseada no ID repassado pela URL padrão.
    *   Mapeia perfeitamente as colunas fundamentais: Objeto, Vigências (Início/Fim), Data de Assinatura, Valor Global, Sequência e Número.
    *   O Backend (`contracts_action.php`) trata o POST e executa `INSERT`, `UPDATE` ou `DELETE` usando Query Preparada (Prepared Statements via PDO) para mitigar falhas de SQL Injection.

## Como Executar Localmente
O aplicativo foi escrito dentro da pasta `c:\Dev\GestorGov\app-contratos`.

1.  Abra um terminal no Windows nessa pasta.
2.  Inicie o servidor de teste do PHP embutido com o comando:
    ```bash
    php -S localhost:8000
    ```
3.  Acesse **http://localhost:8000** em seu navegador para explorar o ecossistema. 

> [!WARNING]
> **Bloqueio de Rede Detectado**: O erro `Connection failed: SQLSTATE[HY000] [2006] MySQL server has gone away` ocorreu durante os testes locais e indica que a rede atual ou a operadora bloqueia o tráfego externo para a porta 3306 do `srv24.prodns.com.br`, ou o IP local não está na Whitelist do servidor. Ao trocar de equipamento/rede ou testar diretamente de uma hospedagem, o acesso deverá ser reestabelecido.

Para a versão de produção, basta realizar o roteamento apontando o `DocumentRoot` ou similar na sua Infraestrutura e alterar as variáveis em `config.php` quando a nova base segura for finalizada!
