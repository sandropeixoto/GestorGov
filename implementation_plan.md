# Contract Management System (PHP)

This plan outlines the creation of a lightweight, visually incredible PHP application to manage contracts based on the `eventoss_vocegov` database schema.

## Proposed Changes

### Configuration
#### [NEW] config.php
Will contain the PDO connection setup using the hardcoded credentials provided by the user to speed up development.

### UI & Template
#### [NEW] header.php
Standardized header including TailwindCSS (via CDN), DaisyUI for beautiful components, FontAwesome for icons, and the top navigation/sidebar layout.
#### [NEW] footer.php
Standardized footer closing the layout tags.

### Core Pages
#### [NEW] index.php (Dashboard)
A modern dashboard displaying:
- Total active contracts
- Contracts expiring soon (based on `VigenciaFim`)
- Charts or visual metric cards for quick analysis.

#### [NEW] contracts.php
A beautiful data table listing records from the `Contratos` table with badges for status (joining with `SituacoesContratos`) and category context (`CategoriaContrato`).
Includes buttons for View, Edit, and Delete.

#### [NEW] contract_form.php
A responsive form to create or edit a contract with all fields from `Contratos`:
- Objeto (Textarea)
- Observacao (Textarea)
- VigenciaInicio (Date)
- VigenciaFim (Date)
- DataAssinatura (Date)
- NumeroContrato (Text)
- SeqContrato (Number)

#### [NEW] contracts_action.php
Handles the POST requests to actually insert, update, or delete contracts in the database using PDO prepared statements.

## Verification Plan
### Manual Verification
1. I will instantiate a local PHP server using `php -S localhost:8000` in the directory.
2. We will open `http://localhost:8000/index.php` to verify the dashboard loads without errors and connects to the remote DB.
3. We will navigate to the Contracts list and test creating a new mock contract.
