<?php
// app-contratos/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Contratos</title>
    <!-- Tailwind CSS & DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-base-200 min-h-screen flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-primary text-primary-content flex flex-col shadow-xl hidden md:flex">
        <div class="px-6 py-8 border-b border-primary-content/20">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <i class="ph ph-files text-3xl"></i> GestorGov
            </h1>
            <p class="text-sm opacity-80 mt-1">Módulo de Contratos</p>
        </div>
        
        <div class="p-4 flex-1">
            <ul class="menu menu-md w-full gap-2">
                <li>
                    <a href="index.php" class="hover:bg-primary-focus p-3 rounded-lg flex items-center gap-3">
                        <i class="ph ph-squares-four text-xl"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="contratos.php" class="hover:bg-primary-focus p-3 rounded-lg flex items-center gap-3">
                        <i class="ph ph-folder-open text-xl"></i> Contratos
                    </a>
                </li>
                <li>
                    <a href="prestadores.php" class="hover:bg-primary-focus p-3 rounded-lg flex items-center gap-3">
                        <i class="ph ph-buildings text-xl"></i> Fornecedores
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="hover:bg-primary-focus p-3 rounded-lg flex items-center gap-3">
                        <i class="ph ph-gear text-xl"></i> Configurações
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="p-4 border-t border-primary-content/20">
            <div class="flex items-center gap-3">
                <div class="avatar placeholder">
                    <div class="bg-neutral text-neutral-content rounded-full w-10">
                        <span>SP</span>
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-sm">Sandro Peixoto</p>
                    <p class="text-xs opacity-70">Administrador</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Drawer wrapper (DaisyUI) can be added here, keeping it simple for now -->

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-base-100 shadow-sm flex items-center justify-between px-6 z-10 glass-panel">
            <div class="flex items-center gap-4">
                <button class="btn btn-square btn-ghost md:hidden">
                    <i class="ph ph-list text-2xl"></i>
                </button>
                <div class="text-sm breadcrumbs hidden sm:block">
                    <ul>
                        <li><a href="index.php">Início</a></li> 
                        <li><a href="contratos.php?clear=1">Módulo de Contratos</a></li>
                    </ul>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                        <div class="indicator">
                            <i class="ph ph-bell text-xl"></i>
                            <span class="badge badge-sm badge-error indicator-item">3</span>
                        </div>
                    </div>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                        <li><a>3 Contratos Vencendo</a></li>
                        <li><a>Atualização de Sistema</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content Viewport -->
        <div class="flex-1 overflow-auto p-6 md:p-8 bg-gradient-to-br from-base-200 to-base-300">
