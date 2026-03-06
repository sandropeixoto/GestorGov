# 🚀 Sugestões de Melhoria - Módulo de Contratos

## [2026-03-06] Refatoração de Vigência Efetiva
- **Caching de Vigência Efetiva**: Implementar coluna calculada e atualizada via Triggers para otimizar queries de larga escala.
- **Notificações Automáticas**: Criar script de segundo plano (Cron) para alertas por e-mail baseados na data real de término.
- **Log de Auditoria**: Rastrear modificações em campos sensíveis (datas e valores) para conformidade e transparência.

## [2026-03-06] Filtros de Vencimento e UX
- **Destaque Visual**: Colorir linhas da tabela com base na proximidade do vencimento (Heatmap).
- **Relatórios Rápidos**: Botão para exportar a visualização filtrada atual em PDF/Excel.
- **Projeção de Médio Prazo**: Incluir indicadores de 60 e 90 dias diretamente no Dashboard.
