<div class="max-w-4xl mx-auto space-y-6" x-data="{ loading: false }">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-purple-500/20">
        <div>
            <div class="terminal-prompt flex items-center gap-2">
                <span class="text-cyan-400 font-bold">></span>
                <span class="text-gray-400">books.provision --create-entry</span>
                <span class="typing-cursor"></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent">
                Nuevo Libro
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Registra un nuevo título en la base de datos del inventario técnico.
            </p>
        </div>

        <div>
            <a href="/admin/books"
               hx-get="/admin/books"
               hx-target="#main-content"
               hx-push-url="true"
               class="btn-secondary inline-flex items-center gap-2 text-xs sm:text-sm">
                <span>←</span>
                <span>Volver a Libros</span>
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card bg-[#0d0d1a]/85 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-purple-500/10">
        
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-purple-500/10">
            <span class="text-cyan-400 font-mono text-sm">></span>
            <span class="text-xs text-purple-300 font-['Orbitron'] uppercase tracking-wider">Metadatos del Libro</span>
        </div>

        <form method="POST" action="/admin/books/store" @submit="loading = true">
            <?= csrf_field() ?>

            <!-- Form Grid (2 Columns on Desktop) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Título -->
                <div class="md:col-span-2">
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Título del Libro <span class="text-pink-400">*</span>
                    </label>
                    <input type="text" 
                           name="titulo" 
                           required 
                           placeholder="ej. The Rust Programming Language"
                           class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                </div>

                <!-- Autor -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Autor(es) <span class="text-pink-400">*</span>
                    </label>
                    <input type="text" 
                           name="autor" 
                           required 
                           placeholder="ej. Steve Klabnik & Carol Nichols"
                           class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                </div>

                <!-- Versión de Tecnología -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Versión / Stack Tecnológico
                    </label>
                    <input type="text" 
                           name="version_tecnologia" 
                           placeholder="ej. Rust 1.70, PHP 8.3, Linux 6.x"
                           class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                </div>

                <!-- Categoría -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Categoría <span class="text-pink-400">*</span>
                    </label>
                    <select name="categoria" 
                            required
                            class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                        <option value="" disabled selected>Selecciona una categoría...</option>
                        <option value="lenguajes">Lenguajes</option>
                        <option value="fundamentos">Aprendizaje / Fundamentos</option>
                        <option value="frameworks">Frameworks & Herramientas</option>
                    </select>
                </div>

                <!-- Nivel -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Nivel de Dificultad <span class="text-pink-400">*</span>
                    </label>
                    <select name="nivel" 
                            required
                            class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                        <option value="" disabled selected>Selecciona un nivel...</option>
                        <option value="principiante">Principiante</option>
                        <option value="intermedio">Intermedio</option>
                        <option value="avanzado">Avanzado</option>
                    </select>
                </div>

                <!-- Formato -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Formato de Entrega <span class="text-pink-400">*</span>
                    </label>
                    <select name="formato" 
                            required
                            class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                        <option value="" disabled selected>Selecciona el formato...</option>
                        <option value="tapa_blanda">Tapa Blanda</option>
                        <option value="tapa_dura">Tapa Dura</option>
                        <option value="ebook">eBook / PDF</option>
                    </select>
                </div>

                <!-- ISBN -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Código ISBN
                    </label>
                    <input type="text" 
                           name="isbn" 
                           placeholder="ej. 978-1718503106"
                           class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none font-mono transition-all">
                </div>

                <!-- Stock -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Stock Inicial <span class="text-pink-400">*</span>
                    </label>
                    <input type="number" 
                           name="stock" 
                           required 
                           min="0" 
                           value="10"
                           class="w-full px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none font-mono transition-all">
                </div>

                <!-- Precio -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Precio (USD) <span class="text-pink-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-cyan-400 font-mono">$</span>
                        <input type="number" 
                               name="precio" 
                               required 
                               step="0.01" 
                               min="0" 
                               value="29.99"
                               class="w-full pl-8 pr-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none font-mono transition-all">
                    </div>
                </div>

                <!-- Año de Publicación -->
                <div class="md:col-span-2">
                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">
                        <span class="text-cyan-400 font-mono">$</span> Año de Publicación
                    </label>
                    <input type="number" 
                           name="anio_publicacion" 
                           min="1950" 
                           max="2099" 
                           value="<?= date('Y') ?>"
                           class="w-full max-w-xs px-4 py-3 bg-black/60 border border-purple-500/20 rounded-lg text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none font-mono transition-all">
                </div>

            </div>

            <!-- Submit & Controls -->
            <div class="mt-8 pt-6 border-t border-purple-500/20 flex flex-col-reverse sm:flex-row items-center justify-end gap-4">
                <a href="/admin/books"
                   hx-get="/admin/books"
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
                    <span x-text="loading ? 'Guardando libro...' : '💾 Registrar Libro'"></span>
                </button>
            </div>

        </form>

    </div>

</div>
