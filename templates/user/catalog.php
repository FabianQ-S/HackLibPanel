<?php
/**
 * Catálogo de Libros para Usuarios
 * 
 * @var array $books Lista de libros
 * @var int $total Total de libros encontrados
 * @var array $filters Filtros aplicados (search, categoria, nivel, formato)
 */

$currentSearch = $filters['search'] ?? '';
$currentCat = $filters['categoria'] ?? '';
$currentNivel = $filters['nivel'] ?? '';
$currentFormato = $filters['formato'] ?? '';

// Helper para construir URLs conservando filtros existentes
$buildFilterUrl = function(array $newParams) use ($filters): string {
    $merged = array_merge($filters, $newParams);
    $clean = array_filter($merged, fn($val) => $val !== null && $val !== '');
    return '/catalog' . (!empty($clean) ? '?' . http_build_query($clean) : '');
};

// Formato icono helper
$getFormatIcon = function(string $formato): string {
    return match($formato) {
        'ebook' => '⚡',
        'tapa_dura' => '📚',
        default => '📖',
    };
};
?>

<div class="space-y-8" x-data="{ viewMode: 'grid' }">

    <!-- Terminal Header -->
    <div class="border-b border-purple-500/20 pb-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-mono text-cyan-400 mb-1">
                    <span>></span>
                    <span class="text-purple-400">catalog.search</span>
                    <span class="text-gray-500">--mode=browse</span>
                    <span class="text-gray-600">--filter-count=<?= e((int)(!empty($currentCat) + !empty($currentNivel) + !empty($currentFormato) + !empty($currentSearch))) ?></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold text-white tracking-wide flex items-center gap-3">
                    <span>Catálogo de Libros</span>
                    <span class="text-xs font-mono font-normal px-2.5 py-1 rounded-md bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        <?= e($total) ?> <?= $total === 1 ? 'libro' : 'libros' ?>
                    </span>
                </h1>
            </div>

            <!-- Terminal status info -->
            <div class="flex items-center gap-3 text-xs font-mono text-gray-500">
                <span class="px-2.5 py-1 rounded bg-black/40 border border-purple-500/20 text-cyan-400">
                    STATUS: READY
                </span>
                <span class="text-gray-600 hidden sm:inline">|</span>
                <span class="text-purple-400 hidden sm:inline">HTMX_LIVE=ON</span>
            </div>
        </div>
    </div>

    <!-- Search & Filters Container -->
    <div class="bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-5 shadow-xl shadow-purple-500/5 space-y-5">
        
        <!-- Live Search Bar -->
        <form id="catalog-filters-form"
              method="GET" 
              action="/catalog" 
              hx-get="/catalog" 
              hx-target="#main-content" 
              hx-push-url="true" 
              hx-trigger="submit"
              class="relative">
            
            <!-- Hidden inputs to preserve filters when searching -->
            <?php if (!empty($currentCat)): ?>
                <input type="hidden" name="categoria" value="<?= e($currentCat) ?>">
            <?php endif; ?>
            <?php if (!empty($currentNivel)): ?>
                <input type="hidden" name="nivel" value="<?= e($currentNivel) ?>">
            <?php endif; ?>
            <?php if (!empty($currentFormato)): ?>
                <input type="hidden" name="formato" value="<?= e($currentFormato) ?>">
            <?php endif; ?>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="<?= e($currentSearch) ?>"
                       placeholder="Buscar por título, autor o ISBN... (búsqueda en tiempo real)"
                       autocomplete="off"
                       hx-get="/catalog"
                       hx-target="#main-content"
                       hx-push-url="true"
                       hx-trigger="keyup changed delay:400ms"
                       hx-include="#catalog-filters-form"
                       class="w-full pl-12 pr-12 py-3.5 bg-black/60 border border-purple-500/30 rounded-xl text-gray-100 placeholder-gray-500 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 focus:outline-none transition-all duration-300 font-mono text-sm shadow-inner">
                
                <?php if (!empty($currentSearch)): ?>
                    <a href="<?= e($buildFilterUrl(['search' => ''])) ?>" 
                       hx-get="<?= e($buildFilterUrl(['search' => ''])) ?>"
                       hx-target="#main-content"
                       hx-push-url="true"
                       title="Limpiar búsqueda"
                       class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-cyan-400 transition-colors">
                        <span class="text-xs font-mono bg-white/5 px-2 py-1 rounded border border-white/10">✕ ESC</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Filter Pills Section -->
        <div class="space-y-4 pt-2 border-t border-purple-500/10">
            
            <!-- Categorías -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-mono text-gray-400 uppercase tracking-wider min-w-[90px] flex items-center gap-1.5">
                    <span class="text-cyan-400">#</span> Categoría:
                </span>
                <div class="flex flex-wrap gap-1.5">
                    <?php
                    $categories = [
                        '' => 'Todos',
                        'lenguajes' => 'Lenguajes',
                        'fundamentos' => 'Fundamentos',
                        'frameworks' => 'Frameworks',
                    ];
                    foreach ($categories as $catKey => $catLabel):
                        $isActive = ($currentCat === $catKey);
                    ?>
                        <a href="<?= e($buildFilterUrl(['categoria' => $catKey])) ?>"
                           hx-get="<?= e($buildFilterUrl(['categoria' => $catKey])) ?>"
                           hx-target="#main-content"
                           hx-push-url="true"
                           class="px-3 py-1 rounded-lg text-xs font-mono transition-all duration-200 <?= $isActive 
                                ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-400/60 shadow-sm shadow-cyan-500/20 font-semibold' 
                                : 'bg-black/40 text-gray-400 hover:text-gray-200 border border-purple-500/20 hover:border-purple-500/40 hover:bg-white/5' ?>">
                            <?= e($catLabel) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Niveles -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-mono text-gray-400 uppercase tracking-wider min-w-[90px] flex items-center gap-1.5">
                    <span class="text-purple-400">★</span> Nivel:
                </span>
                <div class="flex flex-wrap gap-1.5">
                    <?php
                    $levels = [
                        '' => 'Todos',
                        'principiante' => 'Principiante',
                        'intermedio' => 'Intermedio',
                        'avanzado' => 'Avanzado',
                    ];
                    foreach ($levels as $lvlKey => $lvlLabel):
                        $isActive = ($currentNivel === $lvlKey);
                    ?>
                        <a href="<?= e($buildFilterUrl(['nivel' => $lvlKey])) ?>"
                           hx-get="<?= e($buildFilterUrl(['nivel' => $lvlKey])) ?>"
                           hx-target="#main-content"
                           hx-push-url="true"
                           class="px-3 py-1 rounded-lg text-xs font-mono transition-all duration-200 <?= $isActive 
                                ? 'bg-purple-500/20 text-purple-300 border border-purple-400/60 shadow-sm shadow-purple-500/20 font-semibold' 
                                : 'bg-black/40 text-gray-400 hover:text-gray-200 border border-purple-500/20 hover:border-purple-500/40 hover:bg-white/5' ?>">
                            <?= e($lvlLabel) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Formatos -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-mono text-gray-400 uppercase tracking-wider min-w-[90px] flex items-center gap-1.5">
                    <span class="text-pink-400">📦</span> Formato:
                </span>
                <div class="flex flex-wrap gap-1.5">
                    <?php
                    $formats = [
                        '' => 'Todos',
                        'tapa_blanda' => 'Tapa Blanda',
                        'tapa_dura' => 'Tapa Dura',
                        'ebook' => 'eBook / PDF',
                    ];
                    foreach ($formats as $fmtKey => $fmtLabel):
                        $isActive = ($currentFormato === $fmtKey);
                    ?>
                        <a href="<?= e($buildFilterUrl(['formato' => $fmtKey])) ?>"
                           hx-get="<?= e($buildFilterUrl(['formato' => $fmtKey])) ?>"
                           hx-target="#main-content"
                           hx-push-url="true"
                           class="px-3 py-1 rounded-lg text-xs font-mono transition-all duration-200 <?= $isActive 
                                ? 'bg-pink-500/20 text-pink-300 border border-pink-400/60 shadow-sm shadow-pink-500/20 font-semibold' 
                                : 'bg-black/40 text-gray-400 hover:text-gray-200 border border-purple-500/20 hover:border-purple-500/40 hover:bg-white/5' ?>">
                            <?= e($fmtLabel) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Botón limpiar filtros activos -->
            <?php if (!empty($currentSearch) || !empty($currentCat) || !empty($currentNivel) || !empty($currentFormato)): ?>
                <div class="pt-2 flex items-center justify-between">
                    <span class="text-xs font-mono text-gray-500">
                        Filtros aplicados
                    </span>
                    <a href="/catalog"
                       hx-get="/catalog"
                       hx-target="#main-content"
                       hx-push-url="true"
                       class="text-xs font-mono text-red-400 hover:text-red-300 underline underline-offset-4 flex items-center gap-1">
                        <span>✕</span> Restablecer todos los filtros
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Book Grid / Content Section -->
    <?php if (empty($books)): ?>
        <!-- Empty State -->
        <div class="bg-[#0d0d1a]/60 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-12 text-center shadow-xl">
            <div class="w-16 h-16 mx-auto mb-4 bg-purple-500/10 border border-purple-500/30 rounded-2xl flex items-center justify-center text-3xl">
                🔍
            </div>
            <h3 class="text-xl font-['Orbitron'] font-bold text-gray-200 mb-2">No se encontraron libros</h3>
            <p class="text-gray-400 text-sm max-w-md mx-auto mb-6 font-mono">
                > query.status = 0_RESULTS<br>
                No hay libros que coincidan con los criterios de búsqueda y filtros seleccionados.
            </p>
            <a href="/catalog" 
               hx-get="/catalog" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600/30 text-purple-300 border border-purple-500/40 hover:bg-purple-600/50 hover:border-cyan-400 transition-all duration-200 font-mono text-sm">
                <span>🔄</span> Limpiar filtros y ver todos los libros
            </a>
        </div>
    <?php else: ?>
        <!-- Grid: 1 col mobile, 2 tablet, 3 desktop -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($books as $book): ?>
                <div class="group relative bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 hover:border-cyan-400/60 rounded-2xl p-5 shadow-lg shadow-black/40 hover:shadow-cyan-500/10 transition-all duration-300 flex flex-col justify-between"
                     x-data="{ hovered: false }"
                     @mouseenter="hovered = true"
                     @mouseleave="hovered = false"
                     :class="{ 'neon-border': hovered }">

                    <!-- Corner decorative tech tag -->
                    <div class="absolute top-3 right-3 text-[10px] font-mono text-gray-600 group-hover:text-cyan-400 transition-colors">
                        #ID_<?= str_pad((string)$book['id'], 3, '0', STR_PAD_LEFT) ?>
                    </div>

                    <div>
                        <!-- Badges Header -->
                        <div class="flex flex-wrap items-center gap-2 mb-3 pr-12">
                            <!-- Categoría Badge -->
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono border <?= categoria_color($book['categoria']) ?>">
                                <?= e(categoria_label($book['categoria'])) ?>
                            </span>

                            <!-- Nivel Badge -->
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono border <?= nivel_color($book['nivel']) ?>">
                                <?= e(nivel_label($book['nivel'])) ?>
                            </span>
                        </div>

                        <!-- Book Title -->
                        <h3 class="text-lg font-bold text-white group-hover:text-cyan-300 transition-colors leading-snug mb-1">
                            <a href="/catalog/<?= e($book['id']) ?>" 
                               hx-get="/catalog/<?= e($book['id']) ?>" 
                               hx-target="#main-content" 
                               hx-push-url="true"
                               class="hover:underline underline-offset-2">
                                <?= e($book['titulo']) ?>
                            </a>
                        </h3>

                        <!-- Author -->
                        <p class="text-sm text-gray-400 mb-4 flex items-center gap-1.5">
                            <span class="text-gray-600 font-mono">by</span>
                            <span class="text-gray-300"><?= e($book['autor']) ?></span>
                        </p>

                        <!-- Tech info metadata -->
                        <div class="bg-black/40 border border-purple-500/10 rounded-xl p-3 mb-4 space-y-2 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Formato:</span>
                                <span class="text-gray-300 flex items-center gap-1">
                                    <span><?= $getFormatIcon($book['formato']) ?></span>
                                    <span><?= e(formato_label($book['formato'])) ?></span>
                                </span>
                            </div>

                            <?php if (!empty($book['version_tecnologia'])): ?>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Versión:</span>
                                <span class="text-cyan-400/90"><?= e($book['version_tecnologia']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($book['isbn'])): ?>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">ISBN:</span>
                                <span class="text-gray-400 truncate max-w-[150px]"><?= e($book['isbn']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer: Price, Stock & Actions -->
                    <div class="space-y-3 pt-3 border-t border-purple-500/10">
                        <!-- Price & Stock Indicator -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-mono text-gray-500 block">Precio Compra</span>
                                <span class="text-xl font-bold font-mono text-cyan-400 neon-text">
                                    <?= format_price((float)$book['precio']) ?>
                                </span>
                            </div>

                            <div class="text-right">
                                <?php if ($book['stock'] > 0): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <?= e($book['stock']) ?> en stock
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-medium bg-red-500/10 text-red-400 border border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Sin stock
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Form Comprar -->
                            <form hx-post="/cart/add" 
                                  hx-swap="none" 
                                  class="w-full" 
                                  x-data="{ adding: false }" 
                                  @htmx:before-request="adding = true" 
                                  @htmx:after-request="adding = false">
                                <?= csrf_field() ?>
                                <input type="hidden" name="book_id" value="<?= e($book['id']) ?>">
                                <input type="hidden" name="type" value="compra">
                                <button type="submit" 
                                        <?= $book['stock'] <= 0 ? 'disabled' : '' ?>
                                        class="w-full py-2 px-2.5 rounded-xl text-xs font-semibold font-mono flex items-center justify-center gap-1.5 transition-all duration-200 <?= $book['stock'] > 0 
                                            ? 'bg-gradient-to-r from-cyan-600/20 to-cyan-500/30 hover:from-cyan-500/40 hover:to-cyan-400/50 text-cyan-200 border border-cyan-500/40 hover:border-cyan-400 hover:shadow-md hover:shadow-cyan-500/20 active:scale-95' 
                                            : 'bg-gray-800/40 text-gray-600 border border-gray-700/30 cursor-not-allowed opacity-50' ?>">
                                    <span x-show="!adding">🛒 Comprar</span>
                                    <span x-show="adding" x-cloak class="animate-pulse">Añadiendo...</span>
                                </button>
                            </form>

                            <!-- Form Alquilar -->
                            <form hx-post="/cart/add" 
                                  hx-swap="none" 
                                  class="w-full" 
                                  x-data="{ renting: false }" 
                                  @htmx:before-request="renting = true" 
                                  @htmx:after-request="renting = false">
                                <?= csrf_field() ?>
                                <input type="hidden" name="book_id" value="<?= e($book['id']) ?>">
                                <input type="hidden" name="type" value="alquiler">
                                <button type="submit" 
                                        <?= $book['stock'] <= 0 ? 'disabled' : '' ?>
                                        class="w-full py-2 px-2.5 rounded-xl text-xs font-semibold font-mono flex items-center justify-center gap-1.5 transition-all duration-200 <?= $book['stock'] > 0 
                                            ? 'bg-gradient-to-r from-purple-600/20 to-purple-500/30 hover:from-purple-500/40 hover:to-purple-400/50 text-purple-200 border border-purple-500/40 hover:border-purple-400 hover:shadow-md hover:shadow-purple-500/20 active:scale-95' 
                                            : 'bg-gray-800/40 text-gray-600 border border-gray-700/30 cursor-not-allowed opacity-50' ?>">
                                    <span x-show="!renting">📖 Alquilar</span>
                                    <span x-show="renting" x-cloak class="animate-pulse">Añadiendo...</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
