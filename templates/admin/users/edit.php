<div class="max-w-2xl mx-auto space-y-6" x-data="{ loading: false, showPass: false }">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-purple-500/20">
        <div>
            <div class="terminal-prompt flex items-center gap-2">
                <span class="text-cyan-400 font-bold">></span>
                <span class="text-gray-400">auth.modify --uid=<?= (int)$editUser['id'] ?></span>
                <span class="typing-cursor"></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent">
                Editar: <?= e($editUser['username']) ?>
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Modifica el nombre de usuario, correo de contacto, contraseña o rol del sistema.
            </p>
        </div>

        <div>
            <a href="/admin/users"
               hx-get="/admin/users"
               hx-target="#main-content"
               hx-push-url="true"
               class="btn-secondary inline-flex items-center gap-2 text-xs sm:text-sm">
                <span>←</span>
                <span>Volver a Usuarios</span>
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card bg-[#0d0d1a]/85 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-purple-500/10">
        
        <div class="flex items-center justify-between mb-6 pb-3 border-b border-purple-500/10">
            <div class="flex items-center gap-2">
                <span class="text-cyan-400 font-mono text-sm">></span>
                <span class="text-xs text-purple-300 font-['Orbitron'] uppercase tracking-wider">Modificar Cuenta UID #<?= (int)$editUser['id'] ?></span>
            </div>
            <span class="text-xs font-mono text-cyan-400">STATUS: ACTIVE</span>
        </div>

        <form method="POST" action="/admin/users/update/<?= (int)$editUser['id'] ?>" @submit="loading = true">
            <?= csrf_field() ?>

            <div class="space-y-5">
                
                <!-- Nombre de Usuario -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Nombre de Usuario <span class="text-pink-400">*</span>
                    </label>
                    <input type="text" 
                           name="username" 
                           required 
                           minlength="3"
                           value="<?= e($editUser['username'] ?? '') ?>"
                           autocomplete="username"
                           class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Correo Electrónico <span class="text-pink-400">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           required 
                           value="<?= e($editUser['email'] ?? '') ?>"
                           autocomplete="email"
                           class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                </div>

                <!-- Contraseña (Opcional) -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Nueva Contraseña (Opcional)
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" 
                               name="password" 
                               minlength="6"
                               placeholder="Dejar en blanco para mantener la actual"
                               autocomplete="new-password"
                               class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all pr-12 font-mono">
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-cyan-400 transition-colors p-1"
                                tabindex="-1">
                            <span x-text="showPass ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-500 font-mono mt-1">
                        Deja este campo vacío si no deseas modificar la contraseña del usuario.
                    </p>
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Nivel de Privilegios / Rol <span class="text-pink-400">*</span>
                    </label>
                    <select name="role" 
                            required
                            class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                        <option value="user" <?= ($editUser['role'] ?? '') === 'user' ? 'selected' : '' ?>>
                            👤 Usuario Regular (Solo lectura y solicitud de préstamos)
                        </option>
                        <option value="admin" <?= ($editUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                            🛡️ Administrador (Gestión de libros, stock y préstamos)
                        </option>
                    </select>
                </div>

            </div>

            <!-- Submit & Controls -->
            <div class="mt-8 pt-6 border-t border-purple-500/20 flex flex-col-reverse sm:flex-row items-center justify-end gap-4">
                <a href="/admin/users"
                   hx-get="/admin/users"
                   hx-target="#main-content"
                   hx-push-url="true"
                   class="w-full sm:w-auto px-5 py-3 rounded-lg text-center text-sm text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 border border-purple-500/20 transition-all">
                    Cancelar
                </a>

                <button type="submit" 
                        :disabled="loading"
                        class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-purple-600 via-pink-600 to-cyan-600 hover:from-purple-500 hover:via-pink-500 hover:to-cyan-500 text-white font-semibold rounded-lg shadow-lg shadow-purple-500/25 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm">
                    <template x-if="loading">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Guardando cambios...' : '💾 Actualizar Usuario'"></span>
                </button>
            </div>

        </form>

    </div>

</div>
