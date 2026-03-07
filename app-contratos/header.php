<?php
// app-contratos/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Controle de estado da Sidebar (Colapsada ou não)
if (isset($_GET['toggle_sidebar'])) {
    $_SESSION['sidebar_collapsed'] = !($_SESSION['sidebar_collapsed'] ?? false);
    $redirect = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $redirect");
    exit;
}

$is_collapsed = $_SESSION['sidebar_collapsed'] ?? false;
$sidebar_width = $is_collapsed ? 'lg:w-20' : 'lg:w-72';
$logo_display = $is_collapsed ? 'hidden' : 'block';
$menu_text_display = $is_collapsed ? 'hidden' : 'block';

// Lógica para marcar link ativo
$current_page = basename($_SERVER['PHP_SELF']);
function isActive($page, $current_page) {
    return $page === $current_page ? 'bg-white/10 text-white font-bold' : 'text-white/70 hover:bg-white/5 hover:text-white';
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestorGov - Gestão de Contratos</title>
    <!-- Tailwind CSS & DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .menu-item-center { justify-content: center; padding-left: 0; padding-right: 0; }
    </style>
</head>
<body class="bg-base-200 min-h-screen flex overflow-hidden text-base-content">

    <div class="drawer lg:drawer-open flex-1">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />
        
        <!-- Conteúdo Principal -->
        <div class="drawer-content flex flex-col h-screen overflow-hidden w-full">
            
            <!-- Topbar -->
            <header class="h-16 flex items-center justify-between px-4 md:px-8 z-20 glass-panel shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Botão Toggle Sidebar (Desktop) -->
                    <a href="?toggle_sidebar=1" class="btn btn-square btn-ghost hidden lg:flex">
                        <i class="ph <?php echo $is_collapsed ? 'ph-caret-double-right' : 'ph-caret-double-left'; ?> text-xl"></i>
                    </a>

                    <!-- Botão Mobile -->
                    <label for="main-drawer" class="btn btn-square btn-ghost lg:hidden">
                        <i class="ph ph-list text-2xl"></i>
                    </label>
                    
                    <!-- Breadcrumbs -->
                    <div class="text-sm breadcrumbs hidden sm:block">
                        <ul>
                            <li><a href="index.php" class="flex items-center gap-1"><i class="ph ph-house"></i> Início</a></li> 
                            <li><a href="contratos.php?clear=1">Módulo de Contratos</a></li>
                        </ul>
                    </div>
                </div>

                <div class="flex items-center gap-2 md:gap-4">
                    <!-- Notificações -->
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                            <div class="indicator">
                                <i class="ph ph-bell text-xl"></i>
                                <span class="badge badge-sm badge-error indicator-item font-bold text-[10px]">3</span>
                            </div>
                        </div>
                        <ul tabindex="0" class="mt-3 z-[30] p-2 shadow-2xl menu menu-sm dropdown-content bg-base-100 rounded-xl w-64 border border-base-200">
                            <li class="menu-title font-bold text-xs uppercase opacity-50 px-4 py-2">Alertas Recentes</li>
                            <li><a class="py-3 px-4">3 Contratos Vencendo (30d)</a></li>
                            <li><a class="py-3 px-4">Backup concluído</a></li>
                        </ul>
                    </div>

                    <!-- Perfil -->
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost gap-2 pl-2 border border-base-300/50 rounded-full hover:bg-base-200">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-8 shadow-sm">
                                    <span class="text-xs">SP</span>
                                </div>
                            </div>
                            <i class="ph ph-caret-down text-xs opacity-50 hidden md:block"></i>
                        </div>
                        <ul tabindex="0" class="mt-3 z-[30] p-2 shadow-2xl menu menu-sm dropdown-content bg-base-100 rounded-xl w-52 border border-base-200">
                            <li><a class="py-3 px-4"><i class="ph ph-user"></i> Meu Perfil</a></li>
                            <li><a class="py-3 px-4"><i class="ph ph-gear"></i> Preferências</a></li>
                            <div class="divider my-1"></div>
                            <li><a class="py-3 px-4 text-error"><i class="ph ph-sign-out"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Viewport de Conteúdo (Área de Trabalho) -->
            <main class="flex-1 overflow-auto p-4 md:p-8 bg-gradient-to-br from-base-200 to-base-300/50 relative">
<?php
// O restante do conteúdo da página será injetado aqui pelo arquivo que chama o header
?>
        </div> 

        <!-- Sidebar Side (Drawer Side) -->
        <div class="drawer-side z-40">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="<?php echo $sidebar_width; ?> min-h-full bg-[#0f172a] text-white flex flex-col shadow-2xl sidebar-transition overflow-hidden">
                <!-- Logo Area -->
                <div class="px-6 py-8 border-b border-white/5 shrink-0 h-24 flex items-center <?php echo $is_collapsed ? 'justify-center' : 'justify-start'; ?>">
                    <div class="flex items-center gap-3">
                        <i class="ph-fill ph-files text-primary <?php echo $is_collapsed ? 'text-3xl' : 'text-4xl'; ?>"></i> 
                        <h1 class="text-2xl font-black tracking-tighter <?php echo $logo_display; ?>">
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-white/60">GestorGov</span>
                        </h1>
                    </div>
                </div>
                
                <!-- Menu Principal -->
                <nav class="flex-1 mt-4 overflow-y-auto overflow-x-hidden p-3 text-white">
                    <ul class="menu menu-md w-full gap-1 p-0">
                        <li class="menu-title text-white/20 text-[10px] uppercase font-bold tracking-widest px-4 mb-2 <?php echo $logo_display; ?>">Principal</li>
                        
                        <li>
                            <a href="index.php" class="p-3 rounded-xl flex items-center gap-4 transition-all <?php echo isActive('index.php', $current_page); ?> <?php echo $is_collapsed ? 'justify-center tooltip tooltip-right' : ''; ?>" <?php echo $is_collapsed ? 'data-tip="Dashboard"' : ''; ?>>
                                <i class="ph ph-squares-four text-2xl shrink-0"></i> 
                                <span class="tracking-tight whitespace-nowrap <?php echo $menu_text_display; ?>">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="contratos.php" class="p-3 rounded-xl flex items-center gap-4 transition-all <?php echo isActive('contratos.php', $current_page); ?> <?php echo $is_collapsed ? 'justify-center tooltip tooltip-right' : ''; ?>" <?php echo $is_collapsed ? 'data-tip="Contratos"' : ''; ?>>
                                <i class="ph ph-folder-open text-2xl shrink-0"></i> 
                                <span class="tracking-tight whitespace-nowrap <?php echo $menu_text_display; ?>">Contratos</span>
                            </a>
                        </li>
                        
                        <li class="menu-title text-white/20 text-[10px] uppercase font-bold tracking-widest px-4 mt-6 mb-2 <?php echo $logo_display; ?>">Administração</li>
                        
                        <li>
                            <a href="prestadores.php" class="p-3 rounded-xl flex items-center gap-4 transition-all <?php echo isActive('prestadores.php', $current_page); ?> <?php echo $is_collapsed ? 'justify-center tooltip tooltip-right' : ''; ?>" <?php echo $is_collapsed ? 'data-tip="Fornecedores"' : ''; ?>>
                                <i class="ph ph-buildings text-2xl shrink-0"></i> 
                                <span class="tracking-tight whitespace-nowrap <?php echo $menu_text_display; ?>">Fornecedores</span>
                            </a>
                        </li>
                        <li>
                            <a href="settings.php" class="p-3 rounded-xl flex items-center gap-4 transition-all <?php echo isActive('settings.php', $current_page); ?> <?php echo $is_collapsed ? 'justify-center tooltip tooltip-right' : ''; ?>" <?php echo $is_collapsed ? 'data-tip="Configurações"' : ''; ?>>
                                <i class="ph ph-gear text-2xl shrink-0"></i> 
                                <span class="tracking-tight whitespace-nowrap <?php echo $menu_text_display; ?>">Configurações</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- User Footer -->
                <div class="p-4 border-t border-white/5 bg-black/20 shrink-0">
                    <div class="flex items-center gap-3 <?php echo $is_collapsed ? 'justify-center' : ''; ?>">
                        <div class="avatar online shadow-lg">
                            <div class="w-10 rounded-xl ring ring-primary ring-offset-base-100 ring-offset-1">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sandro" />
                            </div>
                        </div>
                        <div class="overflow-hidden <?php echo $logo_display; ?>">
                            <p class="font-bold text-xs truncate">Sandro Peixoto</p>
                            <p class="text-[9px] uppercase font-black opacity-30">Administrador</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
