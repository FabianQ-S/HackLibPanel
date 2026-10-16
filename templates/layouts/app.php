<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'HackLib Panel') ?> | HackLib</title>
    
    <!-- Tailwind CSS compiled & CDN fallback -->
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        mono: ['"JetBrains Mono"', 'monospace'],
                        orbitron: ['"Orbitron"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    <!-- Custom Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0f; }
        ::-webkit-scrollbar-thumb { background: #7c3aed; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #8b5cf6; }
        
        /* Glitch effect */
        .glitch-text {
            position: relative;
            animation: glitch-skew 1s infinite linear alternate-reverse;
        }
        .glitch-text::before,
        .glitch-text::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .glitch-text::before {
            left: 2px;
            text-shadow: -2px 0 #ff00ff;
            clip: rect(44px, 450px, 56px, 0);
            animation: glitch-anim 5s infinite linear alternate-reverse;
        }
        .glitch-text::after {
            left: -2px;
            text-shadow: -2px 0 #00ffff;
            clip: rect(44px, 450px, 56px, 0);
            animation: glitch-anim2 1s infinite linear alternate-reverse;
        }
        @keyframes glitch-anim {
            0% { clip: rect(31px, 9999px, 94px, 0); transform: skew(0.85deg); }
            20% { clip: rect(112px, 9999px, 76px, 0); transform: skew(0.26deg); }
            40% { clip: rect(85px, 9999px, 59px, 0); transform: skew(0.58deg); }
            60% { clip: rect(37px, 9999px, 62px, 0); transform: skew(0.45deg); }
            80% { clip: rect(54px, 9999px, 43px, 0); transform: skew(0.85deg); }
            100% { clip: rect(97px, 9999px, 29px, 0); transform: skew(0.53deg); }
        }
        @keyframes glitch-anim2 {
            0% { clip: rect(65px, 9999px, 119px, 0); transform: skew(0.28deg); }
            20% { clip: rect(79px, 9999px, 16px, 0); transform: skew(0.56deg); }
            40% { clip: rect(16px, 9999px, 89px, 0); transform: skew(0.19deg); }
            60% { clip: rect(105px, 9999px, 53px, 0); transform: skew(0.72deg); }
            80% { clip: rect(23px, 9999px, 85px, 0); transform: skew(0.31deg); }
            100% { clip: rect(57px, 9999px, 33px, 0); transform: skew(0.61deg); }
        }
        @keyframes glitch-skew {
            0% { transform: skew(-2deg); }
            10% { transform: skew(1deg); }
            20% { transform: skew(0deg); }
            100% { transform: skew(0deg); }
        }

        /* Neon glow */
        .neon-border { box-shadow: 0 0 5px theme('colors.purple.500'), 0 0 20px theme('colors.purple.500/0.3'); }
        .neon-text { text-shadow: 0 0 10px theme('colors.cyan.400'), 0 0 40px theme('colors.cyan.400/0.3'); }
        
        /* Scan line effect */
        .scanlines::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 0, 0, 0.03) 2px,
                rgba(0, 0, 0, 0.03) 4px
            );
            pointer-events: none;
            z-index: 9999;
        }
        
        /* Typing cursor */
        .typing-cursor::after {
            content: '▊';
            animation: blink 1s step-end infinite;
            color: #22d3ee;
        }
        @keyframes blink {
            50% { opacity: 0; }
        }

        /* Matrix rain background */
        .matrix-bg {
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(34, 211, 238, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 50%),
                linear-gradient(180deg, #0a0a0f 0%, #0d0d1a 100%);
        }
    </style>
</head>
<body class="bg-[#0a0a0f] text-gray-200 font-['JetBrains_Mono'] min-h-screen matrix-bg scanlines"
      x-data="{ 
          sidebarOpen: window.innerWidth >= 1024,
          toasts: [],
          addToast(message, type = 'success') {
              const id = Date.now();
              this.toasts.push({ id, message, type });
              setTimeout(() => this.removeToast(id), 4000);
          },
          removeToast(id) {
              this.toasts = this.toasts.filter(t => t.id !== id);
          }
      }"
      @show-toast.window="addToast($event.detail.message, $event.detail.type)"
>

    <!-- HTMX Event Listeners for Toasts -->
    <script>
        document.addEventListener('showToast', function(e) {
            window.dispatchEvent(new CustomEvent('show-toast', { detail: e.detail }));
        });
        document.addEventListener('cartUpdated', function(e) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.textContent = e.detail.count;
                badge.style.display = e.detail.count > 0 ? 'flex' : 'none';
            }
        });
    </script>

    <!-- Toast Notifications -->
    <div class="fixed top-4 right-4 z-[9998] space-y-2" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 :class="{
                     'border-green-500/50 bg-green-500/10': toast.type === 'success',
                     'border-red-500/50 bg-red-500/10': toast.type === 'error',
                     'border-yellow-500/50 bg-yellow-500/10': toast.type === 'warning',
                     'border-cyan-500/50 bg-cyan-500/10': toast.type === 'info'
                 }"
                 class="px-4 py-3 rounded-lg border backdrop-blur-sm text-sm max-w-sm cursor-pointer"
                 @click="removeToast(toast.id)">
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <?php if (is_authenticated()): ?>
    <!-- AUTHENTICATED LAYOUT -->
    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               @click.outside="if(window.innerWidth < 1024) sidebarOpen = false">
            <div class="h-full bg-[#0d0d1a]/95 backdrop-blur-xl border-r border-purple-500/20 flex flex-col">
                
                <!-- Logo -->
                <div class="p-6 border-b border-purple-500/20">
                    <a href="/" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-cyan-400 rounded-lg flex items-center justify-center">
                            <span class="text-xl">📚</span>
                        </div>
                        <div>
                            <h1 class="text-lg font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent">
                                HackLib
                            </h1>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest">Panel v1.0</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <?php if (is_admin()): ?>
                        <!-- Admin Navigation -->
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest px-3 py-2">Dashboard</p>
                        <a href="/admin" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 <?= current_uri() === '/admin' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>"
                           hx-get="/admin" hx-target="#main-content" hx-push-url="true">
                            <span>⚡</span> Panel Principal
                        </a>
                        
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest px-3 py-2 mt-4">Gestión</p>
                        <a href="/admin/books"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 <?= str_starts_with(current_uri(), '/admin/books') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>"
                           hx-get="/admin/books" hx-target="#main-content" hx-push-url="true">
                            <span>📖</span> Libros
                        </a>
                        <a href="/admin/rentals"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 <?= str_starts_with(current_uri(), '/admin/rentals') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>"
                           hx-get="/admin/rentals" hx-target="#main-content" hx-push-url="true">
                            <span>🔑</span> Préstamos
                        </a>
                        
                        <?php if (is_master_admin()): ?>
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest px-3 py-2 mt-4">Master Admin</p>
                        <a href="/admin/users"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 <?= str_starts_with(current_uri(), '/admin/users') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>"
                           hx-get="/admin/users" hx-target="#main-content" hx-push-url="true">
                            <span>👥</span> Usuarios
                        </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- User Navigation -->
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest px-3 py-2">Explorar</p>
                        <a href="/catalog"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 <?= current_uri() === '/catalog' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>"
                           hx-get="/catalog" hx-target="#main-content" hx-push-url="true">
                            <span>📖</span> Catálogo
                        </a>

                        <p class="text-[10px] text-gray-600 uppercase tracking-widest px-3 py-2 mt-4">Mi Cuenta</p>
                        <a href="/cart"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 <?= current_uri() === '/cart' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>"
                           hx-get="/cart" hx-target="#main-content" hx-push-url="true">
                            <span>🛒</span> Carrito
                            <?php $cartCount = array_sum(array_column($_SESSION['cart'] ?? [], 'quantity')); ?>
                            <?php if ($cartCount > 0): ?>
                            <span id="cart-badge" class="ml-auto bg-cyan-500 text-black text-xs px-2 py-0.5 rounded-full font-bold"><?= $cartCount ?></span>
                            <?php else: ?>
                            <span id="cart-badge" class="ml-auto bg-cyan-500 text-black text-xs px-2 py-0.5 rounded-full font-bold" style="display:none">0</span>
                            <?php endif; ?>
                        </a>
                        <a href="/my-rentals"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 <?= current_uri() === '/my-rentals' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' ?>"
                           hx-get="/my-rentals" hx-target="#main-content" hx-push-url="true">
                            <span>📋</span> Mis Préstamos
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- User Info & Logout -->
                <div class="p-4 border-t border-purple-500/20">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-cyan-500 flex items-center justify-center text-sm font-bold">
                            <?= strtoupper(substr(auth()['username'], 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-200 truncate"><?= e(auth()['username']) ?></p>
                            <p class="text-[10px] text-gray-500 uppercase"><?= e(auth()['role'] === 'master_admin' ? '⭐ Master Admin' : (auth()['role'] === 'admin' ? '🔧 Admin' : '👤 Usuario')) ?></p>
                        </div>
                    </div>
                    <a href="/logout" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-400 hover:bg-red-500/10 transition-all duration-200">
                        <span>🚪</span> Cerrar Sesión
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-40 bg-[#0a0a0f]/80 backdrop-blur-xl border-b border-purple-500/10">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h2 class="text-sm text-gray-400">
                            <span class="text-purple-400">~/</span><?= e($title ?? 'HackLib') ?>
                        </h2>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-600">
                        <span class="hidden sm:inline">⚡ <?= date('H:i:s') ?></span>
                        <span class="hidden sm:inline text-green-500">● ONLINE</span>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if (has_flash('success') || has_flash('error') || has_flash('warning')): ?>
            <div class="px-6 pt-4">
                <?php if ($msg = get_flash('success')): ?>
                <div class="p-4 rounded-lg border border-green-500/30 bg-green-500/10 text-green-400 text-sm"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition>
                    ✅ <?= $msg ?>
                </div>
                <?php endif; ?>
                <?php if ($msg = get_flash('error')): ?>
                <div class="p-4 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 text-sm"
                     x-data="{ show: true }" x-show="show"
                     x-transition>
                    ❌ <?= $msg ?>
                </div>
                <?php endif; ?>
                <?php if ($msg = get_flash('warning')): ?>
                <div class="p-4 rounded-lg border border-yellow-500/30 bg-yellow-500/10 text-yellow-400 text-sm"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition>
                    ⚠️ <?= $msg ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <main id="main-content" class="p-6">
                <?= $content ?>
            </main>

            <!-- Footer -->
            <footer class="border-t border-purple-500/10 px-6 py-4 text-center text-xs text-gray-600">
                <span class="text-purple-400">HackLib Panel</span> &copy; <?= date('Y') ?> — 
                <span class="text-cyan-400">Sistema de Gestión de Librería</span>
            </footer>
        </div>
    </div>

    <!-- Sidebar Overlay for mobile -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/60 lg:hidden"
         x-cloak></div>

    <?php else: ?>
    <!-- UNAUTHENTICATED LAYOUT (Login/Register) -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Flash Messages for login/register -->
        <?php if (has_flash('success') || has_flash('error')): ?>
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-md px-4">
            <?php if ($msg = get_flash('success')): ?>
            <div class="p-4 rounded-lg border border-green-500/30 bg-green-500/10 text-green-400 text-sm backdrop-blur-sm"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                ✅ <?= $msg ?>
            </div>
            <?php endif; ?>
            <?php if ($msg = get_flash('error')): ?>
            <div class="p-4 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 text-sm backdrop-blur-sm"
                 x-data="{ show: true }" x-show="show" x-transition>
                ❌ <?= $msg ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?= $content ?>
    </div>
    <?php endif; ?>

</body>
</html>
