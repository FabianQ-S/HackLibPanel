<div class="space-y-6" x-data="{ currentFilter: '<?= e($filters['estado'] ?? '') ?>' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-purple-500/20">
        <div>
            <div class="terminal-prompt flex items-center gap-2">
                <span class="text-cyan-400 font-bold">></span>
                <span class="text-gray-400">rentals.monitor --live-cycle</span>
                <span class="typing-cursor"></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent">
                Gestión de Préstamos
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Monitorea el ciclo de vida de los préstamos: aprobación, entrega, devolución y cancelaciones.
            </p>
        </div>

        <!-- Quick Summary Badge -->
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-lg bg-black/60 border border-purple-500/30 text-xs font-mono text-gray-300">
                <span class="text-cyan-400 font-bold"><?= count($rentals) ?></span> registros
            </span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-xl p-4 sm:p-5 shadow-xl">
        <form method="GET" action="/admin/rentals" 
              hx-get="/admin/rentals" 
              hx-target="#main-content" 
              hx-push-url="true"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Text Search -->
            <div class="lg:col-span-7 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 font-mono text-sm">
                    🔍
                </span>
                <input type="text" 
                       name="search" 
                       value="<?= e($filters['search'] ?? '') ?>"
                       placeholder="Buscar por código (ej. RENT-), usuario o título de libro..."
                       hx-get="/admin/rentals"
                       hx-trigger="keyup changed delay:300ms"
                       hx-target="#main-content"
                       hx-push-url="true"
                       hx-include="closest form"
                       class="w-full pl-10 pr-4 py-2.5 bg-black/60 border border-purple-500/20 rounded-lg text-sm text-gray-200 placeholder-gray-500 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
            </div>

            <!-- Estado Dropdown Filter -->
            <div class="lg:col-span-5 flex gap-2">
                <select name="estado" 
                        hx-get="/admin/rentals"
                        hx-trigger="change"
                        hx-target="#main-content"
                        hx-push-url="true"
                        hx-include="closest form"
                        class="w-full px-3 py-2.5 bg-black/60 border border-purple-500/20 rounded-lg text-sm text-gray-200 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?= ($filters['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>
                        ⏳ Pendiente
                    </option>
                    <option value="aprobado" <?= ($filters['estado'] ?? '') === 'aprobado' ? 'selected' : '' ?>>
                        ✔️ Aprobado
                    </option>
                    <option value="prestado" <?= ($filters['estado'] ?? '') === 'prestado' ? 'selected' : '' ?>>
                        📖 Prestado (En posesión)
                    </option>
                    <option value="devuelto" <?= ($filters['estado'] ?? '') === 'devuelto' ? 'selected' : '' ?>>
                        ✅ Devuelto
                    </option>
                    <option value="cancelado" <?= ($filters['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>
                        ❌ Cancelado
                    </option>
                </select>

                <?php if (!empty($filters['search']) || !empty($filters['estado'])): ?>
                <a href="/admin/rentals"
                   hx-get="/admin/rentals"
                   hx-target="#main-content"
                   hx-push-url="true"
                   title="Limpiar filtros"
                   class="px-3 py-2.5 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 text-xs flex items-center justify-center transition-all">
                    ✕
                </a>
                <?php endif; ?>
            </div>

        </form>

        <!-- Fast Status Pills Navigation -->
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-purple-500/10 text-xs font-mono">
            <span class="text-gray-500 mr-1 text-[11px] uppercase">Filtro Rápido:</span>
            
            <a href="/admin/rentals"
               hx-get="/admin/rentals"
               hx-target="#main-content"
               hx-push-url="true"
               class="px-2.5 py-1 rounded-md border transition-all <?= empty($filters['estado']) ? 'bg-purple-500/20 text-purple-300 border-purple-500/40 shadow-sm' : 'bg-white/5 text-gray-400 border-white/10 hover:border-purple-500/30 hover:text-gray-200' ?>">
                Todos
            </a>

            <?php foreach (['pendiente', 'aprobado', 'prestado', 'devuelto', 'cancelado'] as $pillState): ?>
            <a href="/admin/rentals?estado=<?= $pillState ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>"
               hx-get="/admin/rentals?estado=<?= $pillState ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>"
               hx-target="#main-content"
               hx-push-url="true"
               class="px-2.5 py-1 rounded-md border transition-all <?= ($filters['estado'] ?? '') === $pillState ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40 shadow-sm' : 'bg-white/5 text-gray-400 border-white/10 hover:border-cyan-500/30 hover:text-gray-200' ?>">
                <?= e(rental_estado_label($pillState)) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Rentals Table Container -->
    <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl overflow-hidden shadow-2xl p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="border-b border-purple-500/20 bg-black/40 text-[11px] font-mono uppercase tracking-wider text-gray-400">
                        <th class="py-3.5 px-4 font-semibold">Código de Préstamo</th>
                        <th class="py-3.5 px-4 font-semibold">Usuario</th>
                        <th class="py-3.5 px-4 font-semibold">Libro Solicitado</th>
                        <th class="py-3.5 px-4 font-semibold">Estado Actual</th>
                        <th class="py-3.5 px-4 font-semibold">Fecha</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Actualizar Estado</th>
                    </tr>
                </thead>
                <tbody id="rentals-table-body" class="divide-y divide-purple-500/10 font-sans text-sm">
                    <?php 
                    // Render rows partial directly
                    view('partials/rental_rows', ['rentals' => $rentals]); 
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
