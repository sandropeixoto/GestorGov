<?php
// index.php na raiz
session_start();
if (isset($_SESSION['user_email'])) {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GestorGov</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .login-bg { background-color: #0f172a; }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white tracking-tighter flex items-center justify-center gap-3">
                <i class="ph-fill ph-files text-primary text-5xl"></i> GestorGov
            </h1>
            <p class="text-white/40 uppercase tracking-widest text-[10px] mt-2 font-bold font-mono">Sistema de Gestão Governamental</p>
        </div>

        <!-- Login Card -->
        <div class="card bg-base-100 shadow-2xl border border-white/5 overflow-hidden" id="login-card">
            <div class="card-body p-8">
                <h2 class="text-2xl font-bold text-center mb-2">Bem-vindo</h2>
                <p class="text-center text-sm text-base-content/60 mb-8">Informe seu e-mail fazendário para receber seu link de acesso.</p>
                
                <form action="auth_action.php" method="POST" id="auth-form">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold uppercase text-[10px] opacity-50">E-mail Corporativo</span></label>
                        <div class="join w-full border border-base-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-primary transition-all">
                            <input type="text" name="email_prefix" required autofocus 
                                   class="input join-item w-full border-none focus:outline-none pr-0" 
                                   placeholder="nome.sobrenome" />
                            <span class="join-item bg-base-200 flex items-center px-4 font-semibold text-sm opacity-70">@sefa.pa.gov.br</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="btn btn-primary w-full shadow-lg gap-2 text-white">
                            Solicitar Acesso <i class="ph ph-paper-plane-tilt"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="p-4 bg-base-200/50 text-center border-t border-base-300/50">
                <p class="text-[10px] text-base-content/40 font-medium">Acesso restrito a servidores autorizados da SEFA-PA</p>
            </div>
        </div>

        <!-- Success Message (Hidden by default) -->
        <div class="card bg-base-100 shadow-2xl hidden" id="success-card">
            <div class="card-body p-8 text-center py-12">
                <div class="w-20 h-20 bg-success/10 text-success rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                    <i class="ph ph-envelope-simple text-4xl"></i>
                </div>
                <h2 class="text-2xl font-bold mb-4">E-mail Enviado!</h2>
                <p class="text-base-content/70 leading-relaxed">
                    Um link de acesso seguro foi enviado para o seu e-mail.<br>
                    <strong>Verifique sua caixa de entrada e clique no link para entrar.</strong>
                </p>
                <div class="divider my-6"></div>
                <button onclick="location.reload()" class="btn btn-ghost btn-sm text-primary">Não recebeu? Tentar novamente</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('auth-form').onsubmit = async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading loading-spinner"></span> Enviando...';

            const formData = new FormData(e.target);
            try {
                const response = await fetch('auth_action.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('login-card').classList.add('hidden');
                    document.getElementById('success-card').classList.remove('hidden');
                } else {
                    alert(result.error || 'Ocorreu um erro ao processar sua solicitação.');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (err) {
                alert('Erro de conexão com o servidor.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        };
    </script>
</body>
</html>
