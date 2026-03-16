-- SQL para a nova funcionalidade de anexos de contratos

-- 1. Tabela auxiliar de categorias de anexos
CREATE TABLE IF NOT EXISTS `contratos_anexos_categorias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `descricao` VARCHAR(100) NOT NULL,
    `abreviacao` VARCHAR(10) NOT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed inicial de categorias
INSERT INTO `contratos_anexos_categorias` (`descricao`, `abreviacao`) VALUES 
('Contrato', 'Contr'),
('Publicação', 'Publ'),
('Errata', 'Errat'),
('Outros', 'Outr');

-- 2. Tabela de anexos vinculados aos contratos
CREATE TABLE IF NOT EXISTS `contratos_anexos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `contrato_id` INT NOT NULL,
    `categoria_id` INT NOT NULL,
    `nome_arquivo_original` VARCHAR(255) NOT NULL,
    `nome_arquivo_servidor` VARCHAR(255) NOT NULL,
    `caminho_arquivo` VARCHAR(500) NOT NULL,
    `descricao` TEXT,
    `usuario_id` INT NOT NULL,
    `data_upload` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_anexos_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `Contratos` (`Id`) ON DELETE CASCADE,
    CONSTRAINT `fk_anexos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `contratos_anexos_categorias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
