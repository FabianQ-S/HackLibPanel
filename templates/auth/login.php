<div class="w-full max-w-md" x-data="{ loading: false }">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-cyan-400 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/25">
                <span class="text-2xl">📚</span>
            </div>
        </div>
        <h1 class="text-3xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent glitch-text" data-text="HackLib">
            HackLib
        </h1>
        <p class="text-gray-500 text-sm mt-2">Sistema de Gestión de Librería</p>
    </div>

    <!-- Login Form -->
    <div class="bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-8 shadow-2xl shadow-purple-500/10">
        <div class="flex items-center gap-2 mb-6">
            <div class="h-px flex-1 bg-gradient-to-r from-transparent to-purple-500/50"></div>
            <span class="text-xs text-purple-400 uppercase tracking-widest">Acceso al Sistema</span>
            <div class="h-px flex-1 bg-gradient-to-l from-transparent to-purple-500/50"></div>
        </div>

        <form method="POST" action="/login" @submit="loading = true">
            <?= csrf_field() ?>
            
            <div class="space-y-5">
                <!-- Username -->
                <div>
                    <label class="block text-xs text-gray-500 uppercase tracking-wider mb-2">
                        <span class="text-cyan-400">$</span> Usuario
                    </label>
                    <input type="text" name="username" required autocomplete="username"
                           class="w-full px-4 py-3 bg-black/50 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all duration-300"
                           placeholder="tu_usuario">
                </div>

                <!-- Password -->
                <div x-data="{ showPass: false }">
                    <label class="block text-xs text-gray-500 uppercase tracking-wider mb-2">
                        <span class="text-cyan-400">$</span> Contraseña
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               class="w-full px-4 py-3 bg-black/50 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all duration-300 pr-12"
                               placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-cyan-400 transition-colors">
                            <span x-text="showPass ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        :disabled="loading"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-500 hover:to-cyan-500 text-white font-semibold rounded-lg transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/25 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <template x-if="loading">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Iniciando sesión...' : '🔓 Iniciar Sesión'"></span>
                </button>
            </div>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                ¿No tienes cuenta? 
                <a href="/register" class="text-cyan-400 hover:text-cyan-300 transition-colors underline underline-offset-4">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </div>

    <!-- Terminal-style decoration -->
    <div class="mt-6 text-center text-xs text-gray-700 font-mono">
        <p>> system.auth.status: <span class="text-red-400">LOCKED</span></p>
        <p>> awaiting credentials<span class="typing-cursor"></span></p>
    </div>
</div>
