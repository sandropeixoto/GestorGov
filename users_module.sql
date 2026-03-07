CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `nivel` ENUM('Administrador', 'Gestor', 'Consultor') DEFAULT 'Consultor',
  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir você como admin inicial se não existir
INSERT INTO `usuarios` (nome, email, nivel, status) 
VALUES ('Sandro Peixoto', 'sandro.peixoto@sefa.pa.gov.br', 'Administrador', 1)
ON DUPLICATE KEY UPDATE nivel = 'Administrador';
