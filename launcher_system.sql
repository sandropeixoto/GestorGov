CREATE TABLE IF NOT EXISTS `launcher_modules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255),
  `icon` VARCHAR(50) DEFAULT 'ph-cube',
  `url` VARCHAR(255) NOT NULL,
  `display_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `is_external` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir os módulos atuais para não perder dados
INSERT INTO `launcher_modules` (title, description, icon, url, display_order, is_active) VALUES 
('Módulo de Contratos', 'Gestão de vigências, aditivos, fornecedores e métricas financeiras.', 'ph-file-text', 'app-contratos/index.php', 1, 1),
('Configurações Gerais', 'Gerenciamento de usuários, permissões e parâmetros globais do sistema.', 'ph-gear', 'admin_settings.php', 2, 1);
