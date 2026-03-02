<header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-4">
        <!-- Mobile menu button -->
        <button onclick="document.querySelector('aside').classList.toggle('hidden')"
            class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h2 class="text-lg font-semibold text-gray-800">
            <?= $pageTitle ?? 'Dashboard' ?>
        </h2>
    </div>
    <div class="flex items-center gap-3">
        <span
            class="hidden sm:inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">
            <span class="w-2 h-2 bg-whatsapp-green rounded-full animate-pulse"></span>
            Online
        </span>
    </div>
</header>