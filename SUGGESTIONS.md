# 🚀 Sugestões de Melhoria - Módulo de Contratos

## [2026-03-06] Refatoração de Vigência Efetiva
- **Caching de Vigência Efetiva**: Implementar coluna calculada e atualizada via Triggers para otimizar queries de larga escala.
- **Notificações Automáticas**: Criar script de segundo plano (Cron) para alertas por e-mail baseados na data real de término.
- **Log de Auditoria**: Rastrear modificações em campos sensíveis (datas e valores) para conformidade e transparência.

## [2026-03-06] Filtros de Vencimento e UX
- **Destaque Visual**: Colorir linhas da tabela com base na proximidade do vencimento (Heatmap).
- **Relatórios Rápidos**: Botão para exportar a visualização filtrada atual em PDF/Excel.
- **Projeção de Médio Prazo**: Incluir indicadores de 60 e 90 dias diretamente no Dashboard.

## [2026-03-06] Navegação e Persistência Avançada
- **Ordenação Dinâmica**: Permitir ordenação por colunas (Data, Valor, Nome) mantendo o estado dos filtros.
- **Modo de Exibição Compacto**: Toggle para alternar entre visualização confortável e densa de dados.
- **Autocomplete de Ano Dinâmico**: Conversão de input para select baseado nos dados existentes (Implementado).
- **Persistência de Sessão**: Filtros mantidos durante a navegação entre páginas do módulo (Implementado).
- **Indicadores de Filtro Ativo**: Badges visuais para facilitar o reconhecimento do contexto da lista (Implementado).

## [2026-03-06] Gestão de Dados Mestre (Auxiliares)
- **Validação de Documentos**: Integrar validação de CNPJ/CPF no cadastro de prestadores.
- **Dossiê do Fornecedor**: Visualização consolidada de todos os contratos e aditivos de um prestador específico.
- **Integridade Referencial**: Soft-delete para tabelas auxiliares para evitar quebra de histórico em contratos antigos.

## [2026-03-06] UX de Navegação e Responsividade
- **Indicador de Página Ativa**: Destaque visual no menu lateral para a página que o usuário está acessando no momento.
- **Drawer Mobile Funcional**: Implementar o comportamento de abertura/fechamento do menu em dispositivos móveis.
- **Busca Global**: Um atalho de pesquisa no Header para localizar rapidamente um contrato por número em qualquer tela.

## [2026-03-06] Melhorias em Processos Licitatórios
- **Máscara de Dados**: Padronização visual para campos de Número de Modalidade e Processo.
- **Analytics de Modalidade**: Gráficos no Dashboard para visualizar a concentração de gastos por tipo de licitação.

  🚀 Sugestões de Melhoria


   1. Ordenação Multicolunas: Permitir que o usuário clique no cabeçalho das colunas (ex: "Vencimento" ou "Valor Global") para alternar
      a ordenação dos resultados, mantendo os filtros ativos.
   2. Toggle de Visualização: Adicionar um modo "Compacto" na tabela para gestores que preferem visualizar mais registros
      simultaneamente sem a necessidade de scroll excessivo.
