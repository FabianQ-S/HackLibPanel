<div class="space-y-8" x-data="{ hoveredCard: null }">

    <!-- Terminal-style Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-purple-500/20">
        <div>
            <div class="terminal-prompt flex items-center gap-2">
                <span class="text-cyan-400 font-bold">></span>
                <span class="text-gray-400">admin.dashboard --status=active</span>
                <span class="typing-cursor"></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent glitch-text" data-text="Panel de Control">
                Panel de Control
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Monitoreo del sistema en tiempo real, inventario de libros y actividad de préstamos.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/60 border border-purple-500/30 text-xs">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                </span>
                <span class="text-gray-400 font-mono">SYS_STATUS:</span>
                <span class="text-cyan-400 font-mono font-semibold">ONLINE</span>
            </div>
            
            <a href="/admin/books/create"
               hx-get="/admin/books/create" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="btn-primary inline-flex items-center gap-2 text-xs sm:text-sm">
                <span>➕</span>
                <span>Nuevo Libro</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid (Desktop 4 columns, Tablet 2, Mobile 1) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Libros -->
        <div class="stat-card bg-[#0d0d1a]/90 border border-purple-500/20 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] hover:border-cyan-400/50 hover:shadow-lg hover:shadow-purple-500/20 group cursor-default">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Total Libros</span>
                <div class="w-10 h-10 rounded-lg bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    📚
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-['Orbitron'] font-bold text-white group-hover:text-cyan-300 transition-colors">
                    <?= (int)($stats['total'] ?? 0) ?>
                </span>
                <span class="text-xs text-gray-500 font-mono">títulos</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400 border-t border-purple-500/10 pt-2 font-mono">
                <span>Catálogo activo</span>
                <a href="/admin/books" hx-get="/admin/books" hx-target="#main-content" hx-push-url="true" class="text-cyan-400 hover:text-cyan-300 hover:underline">Ver todos →</a>
            </div>
        </div>

        <!-- Stock Total -->
        <div class="stat-card bg-[#0d0d1a]/90 border border-purple-500/20 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] hover:border-cyan-400/50 hover:shadow-lg hover:shadow-cyan-500/20 group cursor-default">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Stock Total</span>
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    📦
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-['Orbitron'] font-bold text-white group-hover:text-cyan-300 transition-colors">
                    <?= (int)($stats['total_stock'] ?? 0) ?>
                </span>
                <span class="text-xs text-gray-500 font-mono">ejemplares</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400 border-t border-purple-500/10 pt-2 font-mono">
                <span>En almacén</span>
                <span class="text-green-400">DISPONIBLE</span>
            </div>
        </div>

        <!-- Préstamos Pendientes (Glowing if > 0) -->
        <?php $hasPending = (int)($stats['pending_rentals'] ?? 0) > 0; ?>
        <div class="stat-card bg-[#0d0d1a]/90 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] cursor-default <?= $hasPending ? 'border border-yellow-500/60 shadow-lg shadow-yellow-500/20 ring-1 ring-yellow-500/40 animate-pulse-slow' : 'border border-purple-500/20 hover:border-cyan-400/50 hover:shadow-lg hover:shadow-purple-500/20' ?> group">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider <?= $hasPending ? 'text-yellow-400 font-bold' : 'text-gray-400' ?>">
                    Préstamos Pendientes
                </span>
                <div class="w-10 h-10 rounded-lg <?= $hasPending ? 'bg-yellow-500/20 border border-yellow-500/50 text-yellow-300' : 'bg-white/5 border border-purple-500/30' ?> flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    ⚠️
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-['Orbitron'] font-bold <?= $hasPending ? 'text-yellow-400' : 'text-white' ?>">
                    <?= (int)($stats['pending_rentals'] ?? 0) ?>
                </span>
                <span class="text-xs text-gray-500 font-mono">por autorizar</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] border-t border-purple-500/10 pt-2 font-mono">
                <span class="<?= $hasPending ? 'text-yellow-400' : 'text-gray-500' ?>">
                    <?= $hasPending ? 'Requiere atención' : 'Sin pendientes' ?>
                </span>
                <a href="/admin/rentals?estado=pendiente" hx-get="/admin/rentals?estado=pendiente" hx-target="#main-content" hx-push-url="true" class="text-cyan-400 hover:text-cyan-300 hover:underline">Revisar →</a>
            </div>
        </div>

        <!-- Préstamos Activos -->
        <div class="stat-card bg-[#0d0d1a]/90 border border-purple-500/20 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] hover:border-cyan-400/50 hover:shadow-lg hover:shadow-purple-500/20 group cursor-default">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Préstamos Activos</span>
                <div class="w-10 h-10 rounded-lg bg-pink-500/10 border border-pink-500/30 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    🔑
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-['Orbitron'] font-bold text-white group-hover:text-pink-300 transition-colors">
                    <?= (int)($stats['active_rentals'] ?? 0) ?>
                </span>
                <span class="text-xs text-gray-500 font-mono">en curso</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400 border-t border-purple-500/10 pt-2 font-mono">
                <span>En circulación</span>
                <a href="/admin/rentals" hx-get="/admin/rentals" hx-target="#main-content" hx-push-url="true" class="text-cyan-400 hover:text-cyan-300 hover:underline">Gestionar →</a>
            </div>
        </div>

        <!-- Usuarios Registrados -->
        <div class="stat-card bg-[#0d0d1a]/90 border border-purple-500/20 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] hover:border-cyan-400/50 hover:shadow-lg hover:shadow-cyan-500/20 group cursor-default">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Usuarios Registrados</span>
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    👥
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-['Orbitron'] font-bold text-white group-hover:text-cyan-300 transition-colors">
                    <?= (int)($stats['total_users'] ?? 0) ?>
                </span>
                <span class="text-xs text-gray-500 font-mono">lectores</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400 border-t border-purple-500/10 pt-2 font-mono">
                <span>Comunidad activa</span>
                <?php if (is_master_admin()): ?>
                    <a href="/admin/users" hx-get="/admin/users" hx-target="#main-content" hx-push-url="true" class="text-cyan-400 hover:text-cyan-300 hover:underline">Ver usuarios →</a>
                <?php else: ?>
                    <span class="text-gray-500">Usuarios</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Admins -->
        <div class="stat-card bg-[#0d0d1a]/90 border border-purple-500/20 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] hover:border-cyan-400/50 hover:shadow-lg hover:shadow-purple-500/20 group cursor-default">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Administradores</span>
                <div class="w-10 h-10 rounded-lg bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    🛡️
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-['Orbitron'] font-bold text-white group-hover:text-purple-300 transition-colors">
                    <?= (int)($stats['total_admins'] ?? 0) ?>
                </span>
                <span class="text-xs text-gray-500 font-mono">operadores</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400 border-t border-purple-500/10 pt-2 font-mono">
                <span>Acceso elevado</span>
                <span class="text-purple-400">STAFF</span>
            </div>
        </div>

        <!-- Precio Promedio -->
        <div class="stat-card bg-[#0d0d1a]/90 border border-purple-500/20 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] hover:border-cyan-400/50 hover:shadow-lg hover:shadow-green-500/20 group cursor-default">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Precio Promedio</span>
                <div class="w-10 h-10 rounded-lg bg-green-500/10 border border-green-500/30 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    💰
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-['Orbitron'] font-bold text-white group-hover:text-green-300 transition-colors">
                    <?= format_price((float)($stats['avg_price'] ?? 0)) ?>
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400 border-t border-purple-500/10 pt-2 font-mono">
                <span>Valor catálogo</span>
                <span class="text-cyan-400">USD</span>
            </div>
        </div>

        <!-- Terminal System Health -->
        <div class="stat-card bg-[#0d0d1a]/90 border border-purple-500/20 rounded-xl p-5 transition-all duration-300 hover:scale-[1.02] hover:border-cyan-400/50 hover:shadow-lg hover:shadow-cyan-500/20 group cursor-default">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Terminal Matrix</span>
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    ⚡
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-['Orbitron'] font-bold text-cyan-400">
                    SEC_ZONE_0
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-gray-400 border-t border-purple-500/10 pt-2 font-mono">
                <span>Kernel PHP 8.x</span>
                <span class="text-green-400">100% OK</span>
            </div>
        </div>

    </div>

    <!-- Charts / Breakdown Sections (Category & Level) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Distribución por Categoría -->
        <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-purple-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-sm">
                        📊
                    </div>
                    <div>
                        <h2 class="text-base font-['Orbitron'] font-bold text-gray-200">
                            Distribución por Categoría
                        </h2>
                        <p class="text-xs text-gray-500 font-mono">Inventario agrupado por área temática</p>
                    </div>
                </div>
                <span class="text-xs text-cyan-400 font-mono">
                    <?= count($stats['by_category'] ?? []) ?> categorías
                </span>
            </div>

            <div class="space-y-5">
                <?php 
                $totalBooks = max((int)($stats['total'] ?? 0), 1);
                $categories = $stats['by_category'] ?? [];
                if (empty($categories)):
                ?>
                    <p class="text-sm text-gray-500 text-center py-4 font-mono">No hay datos de categorías disponibles.</p>
                <?php else: ?>
                    <?php foreach ($categories as $cat): 
                        $count = (int)$cat['count'];
                        $pct = round(($count / $totalBooks) * 100);
                        $catName = $cat['categoria'];
                        
                        // Gradient style depending on category
                        $barGradient = match($catName) {
                            'lenguajes' => 'from-cyan-500 to-blue-500',
                            'fundamentos' => 'from-purple-500 to-violet-500',
                            'frameworks' => 'from-pink-500 to-rose-500',
                            default => 'from-gray-500 to-gray-400'
                        };
                    ?>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="badge <?= categoria_color($catName) ?>">
                                    <?= e(categoria_label($catName)) ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2 font-mono">
                                <span class="text-gray-300 font-bold"><?= $count ?></span>
                                <span class="text-gray-500 text-[11px]">(<?= $pct ?>%)</span>
                            </div>
                        </div>
                        <div class="w-full h-2.5 bg-black/60 rounded-full overflow-hidden p-0.5 border border-white/5">
                            <div class="h-full rounded-full bg-gradient-to-r <?= $barGradient ?> transition-all duration-700 ease-out"
                                 style="width: <?= max($pct, 4) ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Distribución por Nivel -->
        <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-purple-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-sm">
                        📶
                    </div>
                    <div>
                        <h2 class="text-base font-['Orbitron'] font-bold text-gray-200">
                            Distribución por Nivel
                        </h2>
                        <p class="text-xs text-gray-500 font-mono">Dificultad de la colección técnica</p>
                    </div>
                </div>
                <span class="text-xs text-purple-400 font-mono">
                    <?= count($stats['by_level'] ?? []) ?> niveles
                </span>
            </div>

            <div class="space-y-5">
                <?php 
                $levels = $stats['by_level'] ?? [];
                if (empty($levels)):
                ?>
                    <p class="text-sm text-gray-500 text-center py-4 font-mono">No hay datos de niveles disponibles.</p>
                <?php else: ?>
                    <?php foreach ($levels as $lvl): 
                        $count = (int)$lvl['count'];
                        $pct = round(($count / $totalBooks) * 100);
                        $lvlName = $lvl['nivel'];

                        // Gradient style depending on level
                        $barGradient = match($lvlName) {
                            'principiante' => 'from-green-500 to-emerald-400',
                            'intermedio' => 'from-yellow-500 to-amber-400',
                            'avanzado' => 'from-red-500 to-rose-400',
                            default => 'from-gray-500 to-gray-400'
                        };
                    ?>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="badge <?= nivel_color($lvlName) ?>">
                                    <?= e(nivel_label($lvlName)) ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2 font-mono">
                                <span class="text-gray-300 font-bold"><?= $count ?></span>
                                <span class="text-gray-500 text-[11px]">(<?= $pct ?>%)</span>
                            </div>
                        </div>
                        <div class="w-full h-2.5 bg-black/60 rounded-full overflow-hidden p-0.5 border border-white/5">
                            <div class="h-full rounded-full bg-gradient-to-r <?= $barGradient ?> transition-all duration-700 ease-out"
                                 style="width: <?= max($pct, 4) ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Quick Actions Section -->
    <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-purple-500/10">
            <div class="flex items-center gap-2">
                <span class="text-cyan-400 font-mono text-sm">></span>
                <h3 class="text-sm font-['Orbitron'] font-bold uppercase tracking-wider text-gray-300">
                    Acciones Rápidas del Sistema
                </h3>
            </div>
            <span class="text-xs text-gray-500 font-mono">MODO: ADMINISTRADOR</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Books Management -->
            <a href="/admin/books"
               hx-get="/admin/books" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="flex items-center gap-3 p-3.5 rounded-xl bg-white/5 border border-purple-500/20 hover:border-cyan-400/50 hover:bg-white/10 transition-all duration-200 group">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    📖
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 group-hover:text-cyan-300 transition-colors">
                        Gestionar Libros
                    </h4>
                    <p class="text-xs text-gray-500">Catálogo, precios y stock</p>
                </div>
            </a>

            <!-- Rentals Management -->
            <a href="/admin/rentals"
               hx-get="/admin/rentals" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="flex items-center gap-3 p-3.5 rounded-xl bg-white/5 border border-purple-500/20 hover:border-cyan-400/50 hover:bg-white/10 transition-all duration-200 group">
                <div class="w-10 h-10 rounded-lg bg-pink-500/20 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    🔑
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 group-hover:text-pink-300 transition-colors">
                        Gestionar Préstamos
                    </h4>
                    <p class="text-xs text-gray-500">Aprobar, entregar y devolver</p>
                </div>
            </a>

            <!-- Create New Book -->
            <a href="/admin/books/create"
               hx-get="/admin/books/create" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="flex items-center gap-3 p-3.5 rounded-xl bg-white/5 border border-purple-500/20 hover:border-cyan-400/50 hover:bg-white/10 transition-all duration-200 group">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    ➕
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 group-hover:text-cyan-300 transition-colors">
                        Nuevo Libro
                    </h4>
                    <p class="text-xs text-gray-500">Registrar un nuevo título</p>
                </div>
            </a>

            <?php if (is_master_admin()): ?>
            <!-- Users Management (Master Admin only) -->
            <a href="/admin/users"
               hx-get="/admin/users" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="flex items-center gap-3 p-3.5 rounded-xl bg-gradient-to-r from-amber-500/10 to-purple-500/10 border border-amber-500/30 hover:border-amber-400 hover:bg-amber-500/20 transition-all duration-200 group">
                <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    👥
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-amber-300 group-hover:text-amber-200 transition-colors flex items-center gap-1.5">
                        <span>Usuarios & Roles</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30">MASTER</span>
                    </h4>
                    <p class="text-xs text-gray-500">Control de acceso y cuentas</p>
                </div>
            </a>
            <?php else: ?>
            <!-- Catalog Link for regular admin -->
            <a href="/catalog"
               hx-get="/catalog" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="flex items-center gap-3 p-3.5 rounded-xl bg-white/5 border border-purple-500/20 hover:border-cyan-400/50 hover:bg-white/10 transition-all duration-200 group">
                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    🌐
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 group-hover:text-green-300 transition-colors">
                        Vista Pública
                    </h4>
                    <p class="text-xs text-gray-500">Explorar el catálogo</p>
                </div>
            </a>
            <?php endif; ?>

        </div>
    </div>

</div>
