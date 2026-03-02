<?php $pageTitle = 'Criar Conta'; ?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — Super Disparador AZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body
    class="h-full bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500 rounded-2xl shadow-2xl mb-4">
                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Criar Conta</h1>
            <p class="text-indigo-300 mt-1">Comece a disparar mensagens hoje</p>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white/20">
            <?php if (!empty($flash)): ?>
                <div
                    class="mb-6 p-3 rounded-xl text-sm <?= $flash['type'] === 'success' ? 'bg-emerald-500/20 text-emerald-200' : 'bg-red-500/20 text-red-200' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">Nome completo</label>
                    <input type="text" name="name" required placeholder="Seu nome"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:ring-2 focus:ring-green-400 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">E-mail</label>
                    <input type="email" name="email" required placeholder="seu@email.com"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:ring-2 focus:ring-green-400 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">Senha</label>
                    <input type="password" name="password" required placeholder="Mínimo 6 caracteres"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:ring-2 focus:ring-green-400 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">Confirmar senha</label>
                    <input type="password" name="password_confirm" required placeholder="Repita a senha"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:ring-2 focus:ring-green-400 focus:border-transparent outline-none transition-all">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
                    Criar Conta
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-indigo-300 text-sm">
                    Já tem conta?
                    <a href="/login" class="text-green-400 hover:text-green-300 font-medium transition-colors">Fazer
                        login</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>