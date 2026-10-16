<div class="space-y-6" x-data="{ searchFocused: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-purple-500/20">
        <div>
            <div class="terminal-prompt flex items-center gap-2">
                <span class="text-cyan-400 font-bold">></span>
                <span class="text-gray-400">catalog.inventory --admin --view=all</span>
                <span class="typing-cursor"></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent">
                Gestión de Libros
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Administra el inventario, categorías, niveles técnicos, stock y precios del catálogo.
            </p>
        </div>

        <div>
            <a href="/admin/books/create"
               hx-get="/admin/books/create"
               hx-target="#main-content"
               hx-push-url="true"
               class="btn-primary inline-flex items-center gap-2 text-sm shadow-lg shadow-purple-500/25">
                <span class="text-base">➕</span>
                <span>Nuevo Libro</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-xl p-4 sm:p-5 shadow-xl">
        <form method="GET" action="/admin/books" 
              hx-get="/admin/books" 
              hx-target="#main-content" 
              hx-push-url="true"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Text Search -->
            <div class="lg:col-span-6 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 font-mono text-sm">
                    🔍
                </span>
                <input type="text" 
                       name="search" 
                       value="<?= e($filters['search'] ?? '') ?>"
                       placeholder="Buscar por título, autor o ISBN..."
                       hx-get="/admin/books"
                       hx-trigger="keyup changed delay:300ms"
                       hx-target="#main-content"
                       hx-push-url="true"
                       hx-include="closest form"
                       class="w-full pl-10 pr-4 py-2.5 bg-black/60 border border-purple-500/20 rounded-lg text-sm text-gray-200 placeholder-gray-500 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
            </div>

            <!-- Category Filter -->
            <div class="lg:col-span-3">
                <select name="categoria" 
                        hx-get="/admin/books"
                        hx-trigger="change"
                        hx-target="#main-content"
                        hx-push-url="true"
                        hx-include="closest form"
                        class="w-full px-3 py-2.5 bg-black/60 border border-purple-500/20 rounded-lg text-sm text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                    <option value="">Todas las categorías</option>
                    <option value="lenguajes" <?= ($filters['categoria'] ?? '') === 'lenguajes' ? 'selected' : '' ?>>
                        Lenguajes
                    </option>
                    <option value="fundamentos" <?= ($filters['categoria'] ?? '') === 'fundamentos' ? 'selected' : '' ?>>
                        Aprendizaje / Fundamentos
                    </option>
                    <option value="frameworks" <?= ($filters['categoria'] ?? '') === 'frameworks' ? 'selected' : '' ?>>
                        Frameworks & Herramientas
                    </option>
                </select>
            </div>

            <!-- Level Filter -->
            <div class="lg:col-span-3 flex gap-2">
                <select name="nivel" 
                        hx-get="/admin/books"
                        hx-trigger="change"
                        hx-target="#main-content"
                        hx-push-url="true"
                        hx-include="closest form"
                        class="w-full px-3 py-2.5 bg-black/60 border border-purple-500/20 rounded-lg text-sm text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                    <option value="">Todos los niveles</option>
                    <option value="principiante" <?= ($filters['nivel'] ?? '') === 'principiante' ? 'selected' : '' ?>>
                        Principiante
                    </option>
                    <option value="intermedio" <?= ($filters['nivel'] ?? '') === 'intermedio' ? 'selected' : '' ?>>
                        Intermedio
                    </option>
                    <option value="avanzado" <?= ($filters['nivel'] ?? '') === 'avanzado' ? 'selected' : '' ?>>
                        Avanzado
                    </option>
                </select>

                <?php if (!empty($filters['search']) || !empty($filters['categoria']) || !empty($filters['nivel'])): ?>
                <a href="/admin/books"
                   hx-get="/admin/books"
                   hx-target="#main-content"
                   hx-push-url="true"
                   title="Limpiar filtros"
                   class="px-3 py-2.5 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 text-xs flex items-center justify-center transition-all">
                    ✕
                </a>
                <?php endif; ?>
            </div>

        </form>
    </div>

    <!-- Active Filters Feedback & Count -->
    <div class="flex items-center justify-between text-xs font-mono text-gray-400 px-1">
        <div>
            <span>Resultados: <strong class="text-cyan-400"><?= count($books) ?></strong> libros encontrados</span>
        </div>
        <?php if (!empty($filters['search']) || !empty($filters['categoria']) || !empty($filters['nivel'])): ?>
        <div class="flex items-center gap-2">
            <span class="text-gray-500">Filtro activo:</span>
            <?php if (!empty($filters['search'])): ?>
                <span class="px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30">"<?= e($filters['search']) ?>"</span>
            <?php endif; ?>
            <?php if (!empty($filters['categoria'])): ?>
                <span class="px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30"><?= e(categoria_label($filters['categoria'])) ?></span>
            <?php endif; ?>
            <?php if (!empty($filters['nivel'])): ?>
                <span class="px-2 py-0.5 rounded bg-pink-500/20 text-pink-300 border border-pink-500/30"><?= e(nivel_label($filters['nivel'])) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Book Listing: Desktop Table & Mobile Cards -->
    <?php if (empty($books)): ?>
        <!-- Empty State -->
        <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-3xl">
                🔎
            </div>
            <h3 class="text-lg font-['Orbitron'] font-bold text-gray-200">No se encontraron libros</h3>
            <p class="text-sm text-gray-500 font-mono mt-1 max-w-md mx-auto">
                No hay ningún libro que coincida con los criterios de búsqueda o filtros seleccionados.
            </p>
            <div class="mt-6 flex items-center justify-center gap-3">
                <a href="/admin/books" 
                   hx-get="/admin/books" 
                   hx-target="#main-content" 
                   hx-push-url="true"
                   class="btn-secondary text-xs">
                    Limpiar Filtros
                </a>
                <a href="/admin/books/create" 
                   hx-get="/admin/books/create" 
                   hx-target="#main-content" 
                   hx-push-url="true"
                   class="btn-primary text-xs">
                    + Crear Nuevo Libro
                </a>
            </div>
        </div>
    <?php else: ?>

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl overflow-hidden shadow-2xl p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-purple-500/20 bg-black/40 text-[11px] font-mono uppercase tracking-wider text-gray-400">
                            <th class="py-3.5 px-4 font-semibold">Título / Tech</th>
                            <th class="py-3.5 px-4 font-semibold">Autor</th>
                            <th class="py-3.5 px-4 font-semibold">Categoría</th>
                            <th class="py-3.5 px-4 font-semibold">Nivel</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Stock</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Precio</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-500/10 font-sans text-sm">
                        <?php foreach ($books as $book): ?>
                        <tr class="hover:bg-white/[0.03] transition-colors group">
                            <!-- Título / Tech / ISBN -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-200 group-hover:text-cyan-300 transition-colors">
                                    <?= e($book['titulo']) ?>
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 font-mono">
                                    <?php if (!empty($book['version_tecnologia'])): ?>
                                        <span class="text-cyan-400/80"><?= e($book['version_tecnologia']) ?></span>
                                        <span>•</span>
                                    <?php endif; ?>
                                    <span><?= e($book['isbn'] ?: 'S/N ISBN') ?></span>
                                    <span>•</span>
                                    <span class="text-gray-400"><?= e(formato_label($book['formato'])) ?></span>
                                </div>
                            </td>

                            <!-- Autor -->
                            <td class="py-3.5 px-4 text-gray-300">
                                <?= e($book['autor']) ?>
                            </td>

                            <!-- Categoría -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="badge <?= categoria_color($book['categoria']) ?>">
                                    <?= e(categoria_label($book['categoria'])) ?>
                                </span>
                            </td>

                            <!-- Nivel -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="badge <?= nivel_color($book['nivel']) ?>">
                                    <?= e(nivel_label($book['nivel'])) ?>
                                </span>
                            </td>

                            <!-- Stock -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono">
                                <?php $stock = (int)$book['stock']; ?>
                                <?php if ($stock <= 0): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30">
                                        AGOTADO
                                    </span>
                                <?php elseif ($stock <= 5): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                        <?= $stock ?> un.
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                        <?= $stock ?> un.
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Precio -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono font-bold text-gray-200">
                                <?= format_price((float)$book['precio']) ?>
                            </td>

                            <!-- Acciones -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit -->
                                    <a href="/admin/books/edit/<?= $book['id'] ?>"
                                       hx-get="/admin/books/edit/<?= $book['id'] ?>"
                                       hx-target="#main-content"
                                       hx-push-url="true"
                                       title="Editar Libro"
                                       class="px-2.5 py-1.5 rounded-lg bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 hover:bg-cyan-500/20 hover:text-cyan-300 transition-all text-xs flex items-center gap-1 font-mono">
                                        <span>✏️</span>
                                        <span class="hidden lg:inline">Editar</span>
                                    </a>

                                    <!-- Delete -->
                                    <form method="POST" 
                                          action="/admin/books/delete/<?= $book['id'] ?>"
                                          hx-post="/admin/books/delete/<?= $book['id'] ?>"
                                          hx-target="#main-content"
                                          hx-confirm="¿Confirmas que deseas eliminar el libro '<?= e($book['titulo']) ?>'?">
                                        <?= csrf_field() ?>
                                        <button type="submit" 
                                                title="Eliminar Libro"
                                                class="px-2.5 py-1.5 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all text-xs flex items-center gap-1 font-mono">
                                            <span>🗑️</span>
                                            <span class="hidden lg:inline">Borrar</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards View (Shown on Mobile only) -->
        <div class="md:hidden space-y-4">
            <?php foreach ($books as $book): ?>
            <div class="card bg-[#0d0d1a]/90 backdrop-blur-xl border border-purple-500/20 rounded-xl p-4 shadow-lg space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-gray-200 text-base leading-tight">
                            <?= e($book['titulo']) ?>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5"><?= e($book['autor']) ?></p>
                    </div>
                    <span class="font-mono font-bold text-cyan-400 text-sm whitespace-nowrap">
                        <?= format_price((float)$book['precio']) ?>
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="badge <?= categoria_color($book['categoria']) ?>">
                        <?= e(categoria_label($book['categoria'])) ?>
                    </span>
                    <span class="badge <?= nivel_color($book['nivel']) ?>">
                        <?= e(nivel_label($book['nivel'])) ?>
                    </span>
                    <span class="badge bg-white/5 text-gray-400 border-white/10">
                        <?= e(formato_label($book['formato'])) ?>
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs font-mono pt-2 border-t border-purple-500/10">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Stock:</span>
                        <?php $stock = (int)$book['stock']; ?>
                        <span class="font-bold <?= $stock <= 0 ? 'text-red-400' : ($stock <= 5 ? 'text-yellow-400' : 'text-green-400') ?>">
                            <?= $stock > 0 ? "{$stock} un." : 'AGOTADO' ?>
                        </span>
                    </div>

                    <?php if (!empty($book['version_tecnologia'])): ?>
                    <span class="text-cyan-400"><?= e($book['version_tecnologia']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Actions for mobile -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-purple-500/10">
                    <a href="/admin/books/edit/<?= $book['id'] ?>"
                       hx-get="/admin/books/edit/<?= $book['id'] ?>"
                       hx-target="#main-content"
                       hx-push-url="true"
                       class="py-2 text-center rounded-lg bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 hover:bg-cyan-500/20 text-xs font-semibold flex items-center justify-center gap-1">
                        <span>✏️</span> Editar
                    </a>

                    <form method="POST" 
                          action="/admin/books/delete/<?= $book['id'] ?>"
                          hx-post="/admin/books/delete/<?= $book['id'] ?>"
                          hx-target="#main-content"
                          hx-confirm="¿Confirmas que deseas eliminar el libro '<?= e($book['titulo']) ?>'?">
                        <?= csrf_field() ?>
                        <button type="submit" 
                                class="w-full py-2 text-center rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 text-xs font-semibold flex items-center justify-center gap-1">
                            <span>🗑️</span> Borrar
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>
