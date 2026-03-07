<?php
// admin_settings.php na raiz
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestorGov - Configurações Gerais</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Topbar -->
    <header class="h-16 flex items-center justify-between px-8 bg-[#0f172a] text-white shadow-lg">
        <div class="flex items-center gap-4">
            <a href="home.php" class="btn btn-square btn-ghost btn-sm">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold tracking-tight">Configurações Gerais</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-xs opacity-60"><?php echo $_SESSION['user_email']; ?></span>
            <div class="avatar online">
                <div class="w-8 rounded-full">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sandro" />
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 p-8">
        <div class="max-w-6xl mx-auto space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Seção de Usuários -->
                <div class="card bg-white shadow-xl border border-base-200 group hover:border-primary transition-all">
                    <div class="card-body">
                        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-4">
                            <i class="ph ph-users-three text-2xl"></i>
                        </div>
                        <h3 class="card-title text-slate-800">Gestão de Usuários</h3>
                        <p class="text-sm text-slate-500">Controle quem tem acesso aos módulos e seus níveis de permissão.</p>
                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-sm btn-ghost text-primary">Configurar <i class="ph ph-arrow-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Seção de Logs -->
                <div class="card bg-white shadow-xl border border-base-200 group hover:border-secondary transition-all">
                    <div class="card-body">
                        <div class="w-12 h-12 bg-secondary/10 text-secondary rounded-xl flex items-center justify-center mb-4">
                            <i class="ph ph-list-magnifying-glass text-2xl"></i>
                        </div>
                        <h3 class="card-title text-slate-800">Logs de Auditoria</h3>
                        <p class="text-sm text-slate-500">Rastreie todas as alterações críticas realizadas em qualquer módulo.</p>
                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-sm btn-ghost text-secondary">Visualizar <i class="ph ph-arrow-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Seção de Parâmetros -->
                <div class="card bg-white shadow-xl border border-base-200 group hover:border-accent transition-all">
                    <div class="card-body">
                        <div class="w-12 h-12 bg-accent/10 text-accent rounded-xl flex items-center justify-center mb-4">
                            <i class="ph ph-wrench text-2xl"></i>
                        </div>
                        <h3 class="card-title text-slate-800">Parâmetros Globais</h3>
                        <p class="text-sm text-slate-500">Definições de e-mail, SMTP, timeouts e variáveis de ambiente.</p>
                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-sm btn-ghost text-accent">Ajustar <i class="ph ph-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerta Informativo -->
            <div class="alert shadow-lg bg-white border border-info/20">
                <i class="ph ph-info text-info text-2xl"></i>
                <div>
                    <h3 class="font-bold">Acesso Restrito</h3>
                    <div class="text-xs opacity-60">Este módulo é visível apenas para administradores do sistema GestorGov.</div>
                </div>
            </div>
        </div>
    </main>

    <footer class="p-8 text-center text-slate-400 text-xs">
        &copy; 2026 GestorGov - Tecnologia Fazendária
    </footer>
</body>
</html>
