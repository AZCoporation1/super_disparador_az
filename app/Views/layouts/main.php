<!DOCTYPE html>
<html lang="pt-BR" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $pageTitle ?? 'Super Disparador AZ' ?>
    </title>
    <meta name="description" content="Sistema profissional de disparo em massa para WhatsApp com IA integrada">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
                            400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
                            800: '#3730a3', 900: '#312e81',
                        },
                        whatsapp: { light: '#dcf8c6', dark: '#075e54', green: '#25d366', bg: '#ece5dd' },
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link {
            @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200;
        }

        .sidebar-link:hover {
            @apply bg-white/10;
        }

        .sidebar-link.active {
            @apply bg-white/20 text-white;
        }

        .card {
            @apply bg-white rounded-2xl shadow-sm border border-gray-100 p-6;
        }

        .btn-primary {
            @apply bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md;
        }

        .btn-secondary {
            @apply bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-medium transition-all duration-200;
        }

        .btn-danger {
            @apply bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200;
        }

        .btn-success {
            @apply bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200;
        }

        .input-field {
            @apply w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all duration-200;
        }

        .badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }

        .progress-bar {
            @apply h-1.5 rounded-full bg-gray-200 overflow-hidden;
        }

        .progress-bar-fill {
            @apply h-full rounded-full transition-all duration-300 ease-out;
        }
    </style>
</head>

<body class="h-full bg-gray-50">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="flex h-full">
            <!-- Sidebar -->
            <?php include BASE_PATH . '/app/Views/partials/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Header -->
                <?php include BASE_PATH . '/app/Views/partials/header.php'; ?>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                    <?php include BASE_PATH . '/app/Views/partials/flash.php'; ?>
                    <?= $content ?? '' ?>
                </main>
            </div>
        </div>
    <?php else: ?>
        <?php include BASE_PATH . '/app/Views/partials/flash.php'; ?>
        <?= $content ?? '' ?>
    <?php endif; ?>
</body>

</html>