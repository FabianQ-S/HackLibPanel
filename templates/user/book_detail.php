<?php
/**
 * Detalle de Libro para Usuarios
 * 
 * @var array $book Datos completos del libro
 */

$formatIcon = match($book['formato']) {
    'ebook' => '⚡',
    'tapa_dura' => '📚',
    default => '📖',
};
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation & Terminal Line -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-purple-500/20 pb-4">
        <div>
            <a href="/catalog" 
               hx-get="/catalog" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="inline-flex items-center gap-2 text-xs font-mono text-cyan-400 hover:text-cyan-300 transition-colors group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span>VOLVER AL CATÁLOGO</span>
                <span class="text-gray-600">// catalog.exit</span>
            </a>
            <div class="text-[11px] font-mono text-gray-500 mt-1">
                > system.catalog.inspect --id=<?= e($book['id']) ?> --checksum=0x<?= strtoupper(dechex($book['id'] * 4192)) ?>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-mono px-3 py-1 rounded bg-black/50 border border-purple-500/30 text-purple-300">
                REGISTRO #<?= str_pad((string)$book['id'], 4, '0', STR_PAD_LEFT) ?>
            </span>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-[#0d0d1a]/90 backdrop-blur-xl border border-purple-500/30 rounded-2xl overflow-hidden shadow-2xl shadow-purple-500/10">
        
        <!-- Header banner with Cyberpunk Graphic Accent -->
        <div class="relative bg-gradient-to-r from-purple-900/40 via-purple-800/20 to-cyan-900/40 p-6 sm:p-8 border-b border-purple-500/20">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <!-- Categoría Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-mono border font-medium <?= categoria_color($book['categoria']) ?>">
                    # <?= e(categoria_label($book['categoria'])) ?>
                </span>
                
                <!-- Nivel Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-mono border font-medium <?= nivel_color($book['nivel']) ?>">
                    ★ <?= e(nivel_label($book['nivel'])) ?>
                </span>

                <!-- Formato Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-mono border bg-pink-500/10 text-pink-300 border-pink-500/30">
                    <?= $formatIcon ?> <?= e(formato_label($book['formato'])) ?>
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-2xl sm:text-4xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-300 via-pink-300 to-cyan-300 bg-clip-text text-transparent mb-2">
                <?= e($book['titulo']) ?>
            </h1>

            <!-- Author -->
            <p class="text-base sm:text-lg text-gray-300 flex items-center gap-2 font-mono">
                <span class="text-cyan-400 font-bold">$</span>
                <span class="text-gray-400">Autor:</span>
                <span class="text-cyan-200 font-medium"><?= e($book['autor']) ?></span>
            </p>
        </div>

        <div class="p-6 sm:p-8 space-y-8">

            <!-- Metadata Grid -->
            <div>
                <h3 class="text-xs font-mono text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="text-purple-400">■</span> Especificaciones Técnicas del Libro
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 font-mono">
                    
                    <!-- Categoría -->
                    <div class="bg-black/50 border border-purple-500/20 rounded-xl p-4">
                        <span class="text-xs text-gray-500 block mb-1">Categoría</span>
                        <span class="text-sm text-gray-200 font-semibold flex items-center gap-1.5">
                            <span class="text-cyan-400">◈</span> <?= e(categoria_label($book['categoria'])) ?>
                        </span>
                    </div>

                    <!-- Nivel -->
                    <div class="bg-black/50 border border-purple-500/20 rounded-xl p-4">
                        <span class="text-xs text-gray-500 block mb-1">Nivel Recomendado</span>
                        <span class="text-sm text-gray-200 font-semibold flex items-center gap-1.5">
                            <span class="text-yellow-400">◈</span> <?= e(nivel_label($book['nivel'])) ?>
                        </span>
                    </div>

                    <!-- Versión Tecnología -->
                    <div class="bg-black/50 border border-purple-500/20 rounded-xl p-4">
                        <span class="text-xs text-gray-500 block mb-1">Versión / Tecnología</span>
                        <span class="text-sm text-cyan-300 font-semibold">
                            <?= e($book['version_tecnologia'] ?: 'No especificada') ?>
                        </span>
                    </div>

                    <!-- Formato -->
                    <div class="bg-black/50 border border-purple-500/20 rounded-xl p-4">
                        <span class="text-xs text-gray-500 block mb-1">Formato Físico / Digital</span>
                        <span class="text-sm text-gray-200 font-semibold flex items-center gap-1.5">
                            <span><?= $formatIcon ?></span> <?= e(formato_label($book['formato'])) ?>
                        </span>
                    </div>

                    <!-- ISBN -->
                    <div class="bg-black/50 border border-purple-500/20 rounded-xl p-4">
                        <span class="text-xs text-gray-500 block mb-1">Código ISBN</span>
                        <span class="text-sm text-gray-300 font-semibold">
                            <?= e($book['isbn'] ?: 'No asignado') ?>
                        </span>
                    </div>

                    <!-- Año de Publicación -->
                    <div class="bg-black/50 border border-purple-500/20 rounded-xl p-4">
                        <span class="text-xs text-gray-500 block mb-1">Año de Publicación</span>
                        <span class="text-sm text-gray-200 font-semibold">
                            <?= e((string)($book['anio_publicacion'] ?: 'N/D')) ?>
                        </span>
                    </div>

                </div>
            </div>

            <!-- Price & Availability Box -->
            <div class="bg-gradient-to-r from-purple-950/40 to-cyan-950/40 border border-purple-500/30 rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                
                <div>
                    <span class="text-xs font-mono text-gray-400 uppercase tracking-widest block mb-1">Precio Unitario (Compra)</span>
                    <div class="text-4xl sm:text-5xl font-['Orbitron'] font-bold text-cyan-400 neon-text">
                        <?= format_price((float)$book['precio']) ?>
                    </div>
                    <p class="text-xs text-gray-500 font-mono mt-1">
                        * Los préstamos son gratuitos durante el período reglamentario.
                    </p>
                </div>

                <div class="flex flex-col items-start md:items-end gap-2">
                    <span class="text-xs font-mono text-gray-400 uppercase tracking-widest block">Disponibilidad en Almacén</span>
                    <?php if ($book['stock'] > 0): ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-mono font-medium bg-emerald-500/15 text-emerald-300 border border-emerald-500/40 shadow-sm shadow-emerald-500/20">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span><?= e($book['stock']) ?> <?= $book['stock'] === 1 ? 'ejemplar disponible' : 'ejemplares disponibles' ?></span>
                        </div>
                    <?php else: ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-mono font-medium bg-red-500/15 text-red-300 border border-red-500/40">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span>Agotado temporalmente (0 stock)</span>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Action Buttons Section -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-mono text-gray-400 uppercase tracking-widest flex items-center gap-2">
                    <span class="text-cyan-400">⚡</span> Opciones de Adquisición
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Comprar Button Form -->
                    <form hx-post="/cart/add" 
                          hx-swap="none" 
                          x-data="{ adding: false }" 
                          @htmx:before-request="adding = true" 
                          @htmx:after-request="adding = false"
                          class="w-full">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= e($book['id']) ?>">
                        <input type="hidden" name="type" value="compra">
                        <button type="submit" 
                                <?= $book['stock'] <= 0 ? 'disabled' : '' ?>
                                class="w-full py-4 px-6 rounded-xl font-['Orbitron'] font-semibold text-sm flex items-center justify-center gap-3 transition-all duration-300 <?= $book['stock'] > 0 
                                    ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 hover:from-cyan-500 hover:to-cyan-400 text-black shadow-lg shadow-cyan-500/25 hover:shadow-cyan-400/40 active:scale-[0.98]' 
                                    : 'bg-gray-800/40 text-gray-600 border border-gray-700/30 cursor-not-allowed opacity-50' ?>">
                            <span class="text-lg">🛒</span>
                            <span x-show="!adding">Comprar Libro (<?= format_price((float)$book['precio']) ?>)</span>
                            <span x-show="adding" x-cloak class="animate-pulse">Añadiendo al Carrito...</span>
                        </button>
                    </form>

                    <!-- Alquilar Button Form -->
                    <form hx-post="/cart/add" 
                          hx-swap="none" 
                          x-data="{ renting: false }" 
                          @htmx:before-request="renting = true" 
                          @htmx:after-request="renting = false"
                          class="w-full">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= e($book['id']) ?>">
                        <input type="hidden" name="type" value="alquiler">
                        <button type="submit" 
                                <?= $book['stock'] <= 0 ? 'disabled' : '' ?>
                                class="w-full py-4 px-6 rounded-xl font-['Orbitron'] font-semibold text-sm flex items-center justify-center gap-3 transition-all duration-300 <?= $book['stock'] > 0 
                                    ? 'bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white shadow-lg shadow-purple-500/25 hover:shadow-purple-400/40 active:scale-[0.98]' 
                                    : 'bg-gray-800/40 text-gray-600 border border-gray-700/30 cursor-not-allowed opacity-50' ?>">
                            <span class="text-lg">📖</span>
                            <span x-show="!renting">Solicitar Préstamo / Alquilar</span>
                            <span x-show="renting" x-cloak class="animate-pulse">Añadiendo al Carrito...</span>
                        </button>
                    </form>

                </div>

                <?php if ($book['stock'] <= 0): ?>
                    <p class="text-center text-xs font-mono text-red-400">
                        * Este libro no cuenta con stock disponible en este momento para compra o préstamo.
                    </p>
                <?php endif; ?>
            </div>

        </div>

    </div>

</div>
