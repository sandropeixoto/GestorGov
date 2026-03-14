# GestorGov - Brownfield Architecture Document (High Level)

## Introduction

This document captures the current state of the GestorGov codebase, reflecting the architecture of both the central Launcher (Portal) and the specialized Contracts Module. It serves as a high-level reference for AI agents and developers.

### Document Scope
- **Portal/Launcher**: Root directory management, authentication, and module routing.
- **Contracts Module**: Located in `app-contratos/`, focused on contract lifecycle management.

---

## High Level Architecture

### System Flow
1. **Authentication**: Users log in via an email-based token system (passwordless).
2. **Launcher**: Once authenticated, users land on a central dashboard (`home.php`) to choose a module.
3. **Module Handoff**: Modules are accessed via URLs defined in the database, potentially using SSO tokens for session continuity.
4. **Operations**: Each module (like `app-contratos`) manages its own permissions and business logic while sharing the core database.

### Technical Stack (Actual)

| Category | Technology | Usage |
|----------|------------|-------|
| Runtime | PHP 8.x | Server-side logic (Vanilla PHP) |
| Database | MySQL | Data persistence (PDO) |
| Frontend | TailwindCSS + DaisyUI | UI Styling (via CDN) |
| Icons | Phosphor Icons | Visual elements |
| Auth | Token-based | Passwordless email login |
| Mailer | Custom Socket | SMTP authenticated mail delivery |

---

## Repository Structure

```text
/ (Project Root - Launcher)
├── config.php             # Central DB & Mailer configuration
├── auth_check.php         # Root session & cookie validation
├── auth_action.php        # Authentication logic (token generation/email)
├── index.php              # Login entry point
├── home.php               # Module Launcher dashboard
├── manage_users.php       # Admin: User management
├── launcher_system.sql    # Core database schema
│
└── app-contratos/         # Contracts Module
    ├── config.php         # Module-specific DB connection
    ├── auth_module.php    # Granular permission logic (Gestor, Consultor, etc.)
    ├── index.php          # Module dashboard (KPIs/Charts)
    ├── contratos.php      # Main contract list (Data Table)
    ├── contract_form.php  # Create/Edit contract wizard
    └── contracts_action.php # CRUD operations for contracts
```

---

## Core Components & Logic

### 1. Authentication Engine (Root)
- **Token System**: Uses the `login_tokens` table to store temporary access keys.
- **Session Rehydration**: `auth_check.php` verifies the token or a long-term cookie (`gestorgov_session`) to restore the user session.
- **Mailer**: A custom `enviarEmailViaSocket` function in `config.php` handles SMTP communication without external dependencies like PHPMailer.

### 2. Permissions & Security (`app-contratos/`)
- **Granular Roles**: Defined via `CONTRATOS_GESTOR`, `CONTRATOS_CONSULTOR`, etc., in `auth_module.php`.
- **Permission Mapping**: Stored in `contratos_permissoes`, mapping `usuario_id` to a specific role.
- **SSOT Compliance**: UI follows `GEMINI.md` standards for colors, spacing, and interaction patterns.

### 3. Data Integrity
- **PDO Prepared Statements**: Used across all `_action.php` files to prevent SQL Injection.
- **Hierarchical Contracts**: Managed via `PaiId` for linking Addendums (Termos Aditivos) to main contracts.

---

## Integration Points

- **Shared Database**: Both Portal and Modules share the same MySQL database instance.
- **Session Sharing**: Modules rely on `$_SESSION['user_email']` set by the root authentication.
- **External Links**: The Launcher can redirect to external systems with optional SSO token parameters.

---

## Development Standards (Brief)

1. **Naming**: Action files end in `_action.php`. View files use snake_case (e.g., `contract_view.php`).
2. **UI**: Always use Tailwind utility classes. Prefer DaisyUI components for cards, modals, and buttons.
3. **Security**: Every action file MUST include authentication checks at the top.
4. **Commits**: Follow the standard `type(scope): description` format (e.g., `feat(auth): add cookie-based persistence`).

---

## Future Considerations

- **New Modules**: Can be added by creating a new subdirectory and registering it in `launcher_modules`.
- **API Layer**: Potential for a REST API to serve data to modern frontend frameworks if needed.
- **Notification Center**: Expanding the SMTP logic to handle automated alerts for expiring contracts.

— Orion, documentando a arquitetura 🎯
