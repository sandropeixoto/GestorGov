-- Tabela para gestão de tokens de acesso passwordless
CREATE TABLE IF NOT EXISTS `login_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `used` TINYINT(1) DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`token`),
  INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
