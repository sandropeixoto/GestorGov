-- ========================================================
-- ATUALIZAÇÃO DO SISTEMA DE GESTÃO DE CONTRATOS
-- DATA: 2026-03-06
-- DESCRIÇÃO: Implementação de Tipos de Documentos e Dossiês
-- ========================================================

-- 1. Criar tabela de Tipos de Documentos
CREATE TABLE IF NOT EXISTS `TiposDocumentos` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Nome` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Popular os tipos de documentos padrão
INSERT INTO `TiposDocumentos` (`Id`, `Nome`) VALUES
(1, 'Contrato'),
(2, 'Termo Aditivo'),
(3, 'Termo de Apostilamento'),
(4, 'Termo de Rescisão'),
(5, 'Outros')
ON DUPLICATE KEY UPDATE `Nome` = VALUES(`Nome`);

-- 3. Adicionar coluna TipoDocumentoId na tabela Contratos
ALTER TABLE `Contratos` ADD COLUMN `TipoDocumentoId` INT DEFAULT 1;

-- 4. Migração do Legado: Transformar todos os filhos (TACs) em 'Termo Aditivo'
UPDATE `Contratos` SET `TipoDocumentoId` = 2 WHERE `PaiId` > 0;

-- 5. Garantir que os registros principais sejam 'Contrato'
UPDATE `Contratos` SET `TipoDocumentoId` = 1 WHERE `PaiId` = 0;
