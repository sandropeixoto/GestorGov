<?php
// home.php na raiz
require_once 'auth_check.php';
require_once 'config.php';

try {
    $modules = $pdo->query("SELECT * FROM launcher_modules ORDER BY display_order ASC")->fetchAll();
} catch (PDOException $e) {
    $modules = [];
}

// Função para gerar URL com token SSO se necessário
function getModuleUrl($m) {
    // Redireciona via handler central para log e geração de token SSO (se externo)
    return "sso_redirect.php?id=" . $m['id'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestorGov - Início</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .hero-bg { background: radial-gradient(circle at top right, #1e293b, #0f172a); }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header Simples -->
    <header class="h-20 flex items-center justify-between px-8 bg-white border-b border-base-200">
        <h1 class="text-2xl font-black tracking-tighter flex items-center gap-2">
            <i class="ph-fill ph-files text-primary text-3xl"></i> GestorGov
        </h1>
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium opacity-60"><?php echo $_SESSION['user_email']; ?></span>
            <a href="logout.php" class="btn btn-ghost btn-sm text-error">Sair</a>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center p-8">
        <div class="max-w-4xl w-full text-center space-y-12">
            <div>
                <h2 class="text-5xl font-black text-slate-900 tracking-tight mb-4 animate-fade-in">Portal de Sistemas</h2>
                <p class="text-xl text-slate-500 max-w-2xl mx-auto">Selecione o módulo que deseja gerenciar hoje. Mais ferramentas estarão disponíveis em breve.</p>
            </div>

            <!-- Launcher Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto pb-12">
                <?php foreach ($modules as $m): ?>
                    <?php if ($m['is_active']): ?>
                        <!-- Módulo Ativo -->
                        <a href="<?php echo getModuleUrl($m); ?>" 
                           <?php echo ($m['open_in_new_tab'] ?? 0) ? 'target="_blank"' : ''; ?>
                           class="group relative bg-white p-8 rounded-[2rem] shadow-xl hover:shadow-2xl transition-all duration-500 border border-slate-100 flex flex-col items-center text-center hover:-translate-y-2 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="w-20 h-20 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500">
                                <i class="ph <?php echo $m['icon']; ?> text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 mb-2"><?php echo htmlspecialchars($m['title']); ?></h3>
                            <p class="text-slate-500 text-xs mb-8"><?php echo htmlspecialchars($m['description']); ?></p>
                            
                            <div class="mt-auto w-full flex justify-center">
                                <div class="btn btn-primary btn-sm btn-wide rounded-xl gap-2 shadow-lg group-hover:gap-4 transition-all text-white border-none">
                                    Acessar <i class="ph ph-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    <?php else: ?>
                        <!-- Módulo Em Breve -->
                        <div class="group relative bg-slate-50 p-8 rounded-[2rem] border border-dashed border-slate-200 flex flex-col items-center text-center opacity-60 grayscale blur-[1px] hover:blur-0 transition-all duration-500">
                            <div class="w-20 h-20 bg-slate-200 text-slate-400 rounded-2xl flex items-center justify-center mb-6 text-4xl">
                                <i class="ph <?php echo $m['icon']; ?>"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-400 mb-2 text-slate-800"><?php echo htmlspecialchars($m['title']); ?></h3>
                            <p class="text-slate-400 text-xs mb-6 text-slate-500"><?php echo htmlspecialchars($m['description']); ?></p>
                            <div class="btn btn-disabled btn-sm btn-wide rounded-xl">Em breve</div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer class="p-8 text-center text-slate-400 text-xs">
        &copy; 2026 GestorGov - Secretaria de Estado da Fazenda do Pará (SEFA)
    </footer>

    <!-- Alertas e Mensagens -->
    <?php if (isset($_GET['error'])): ?>
        <div id="alert_container" class="toast toast-top toast-center z-[100] animate-bounce-in">
            <?php 
                $error_msg = 'Ocorreu um erro inesperado.';
                $error_type = 'alert-error';

                switch($_GET['error']) {
                    case 'unauthorized': 
                        $error_msg = 'ACESSO NEGADO: Você não tem permissão para acessar este recurso.'; 
                        break;
                    case 'expired': 
                        $error_msg = 'SESSÃO EXPIRADA: Por favor, faça login novamente.'; 
                        break;
                    case 'db': 
                        $error_msg = 'ERRO DE BANCO: Falha na conexão com o servidor.'; 
                        break;
                    case 'invalid_module': 
                        $error_msg = 'MÓDULO INVÁLIDO: O sistema selecionado não está disponível.'; 
                        break;
                }
            ?>
            <div class="alert <?php echo $error_type; ?> shadow-2xl border-none text-white font-bold py-4 px-8 rounded-2xl flex items-center gap-3">
                <i class="ph-fill ph-warning-circle text-2xl"></i>
                <span><?php echo $error_msg; ?></span>
            </div>
        </div>

        <script>
            // Remove o alerta após 5 segundos
            setTimeout(() => {
                const alert = document.getElementById('alert_container');
                if (alert) {
                    alert.classList.add('animate-fade-out');
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);

            // Limpa a URL removendo o parâmetro de erro sem recarregar a página
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('error');
                window.history.replaceState({}, '', url);
            }
        </script>
        
        <style>
            @keyframes bounce-in {
                0% { transform: translate(-50%, -200%); opacity: 0; }
                70% { transform: translate(-50%, 10%); opacity: 1; }
                100% { transform: translate(-50%, 0); opacity: 1; }
            }
            @keyframes fade-out {
                from { opacity: 1; transform: translate(-50%, 0); }
                to { opacity: 0; transform: translate(-50%, -20px); }
            }
            .animate-bounce-in {
                position: fixed;
                left: 50%;
                top: 2rem;
                transform: translateX(-50%);
                animation: bounce-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }
            .animate-fade-out {
                animation: fade-out 0.5s ease forwards;
            }
        </style>
    <?php endif; ?>
</body>
</html>
