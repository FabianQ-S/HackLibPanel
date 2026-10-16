<?php
/**
 * Resultado de Checkout / Confirmación de Operación
 * 
 * @var array $rentalCodes Códigos de préstamo generados (codigo, libro)
 * @var array $purchasedBooks Libros de compra simulada (titulo, autor, precio, quantity)
 */

$totalSimulated = 0;
foreach ($purchasedBooks as $pb) {
    $totalSimulated += $pb['precio'] * $pb['quantity'];
}
?>

<div class="max-w-4xl mx-auto space-y-8">

    <!-- Celebratory Hacker-Style Header -->
    <div class="text-center py-6 border-b border-purple-500/20">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-mono mb-4">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-ping"></span>
            TRANSACTION_VERIFIED // STATUS 200 OK
        </div>

        <h1 class="text-3xl sm:text-5xl font-['Orbitron'] font-black bg-gradient-to-r from-green-400 via-cyan-400 to-purple-400 bg-clip-text text-transparent glitch-text mb-3"
            data-text="¡Operación Completada!">
            ¡Operación Completada!
        </h1>

        <p class="text-gray-400 font-mono text-xs sm:text-sm max-w-lg mx-auto">
            > checkout.process() finished successfully. Tus órdenes y solicitudes han sido registradas en el sistema.
        </p>
    </div>

    <!-- Section: Préstamos Registrados (if any) -->
    <?php if (!empty($rentalCodes)): ?>
        <div class="bg-[#0d0d1a]/90 backdrop-blur-xl border-2 border-purple-500/40 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-purple-500/10 space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-purple-500/20 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/40 flex items-center justify-center text-xl">
                        🔑
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-['Orbitron'] font-bold text-purple-300">
                            Préstamos Registrados
                        </h2>
                        <span class="text-xs font-mono text-gray-500">
                            <?= count($rentalCodes) ?> <?= count($rentalCodes) === 1 ? 'código generado' : 'códigos generados' ?>
                        </span>
                    </div>
                </div>

                <span class="px-3 py-1 rounded bg-purple-500/10 text-purple-300 border border-purple-500/30 text-xs font-mono">
                    ESTADO: PENDIENTE
                </span>
            </div>

            <!-- Important Instructions Alert -->
            <div class="bg-purple-950/30 border border-purple-500/30 rounded-xl p-4 text-xs font-mono text-purple-200 space-y-2">
                <p class="font-bold flex items-center gap-2 text-cyan-300 text-sm">
                    <span>⚠️</span> Debes presentarte en el lugar de préstamo con los siguientes códigos:
                </p>
                <p class="text-gray-300 leading-relaxed">
                    Guarda estos códigos. Son necesarios para recoger tus libros físicos en el punto de entrega. Tu solicitud ya está visible en el sistema del bibliotecario.
                </p>
            </div>

            <!-- Rental Codes List -->
            <div class="space-y-4">
                <?php foreach ($rentalCodes as $index => $item): ?>
                    <div class="bg-black/60 border border-purple-500/30 hover:border-cyan-400/50 rounded-xl p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-200"
                         x-data="{ copied: false }">
                        
                        <!-- Book Info -->
                        <div class="flex-1">
                            <span class="text-[10px] font-mono uppercase text-gray-500 block mb-1">
                                Préstamo #<?= $index + 1 ?>
                            </span>
                            <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                                <span class="text-purple-400">📖</span>
                                <?= e($item['libro']) ?>
                            </h3>
                        </div>

                        <!-- Code Display & Copy Button -->
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-lg sm:text-xl font-bold text-cyan-400 tracking-wider bg-black/80 px-4 py-2 rounded-xl border border-cyan-500/40 select-all neon-text">
                                <?= e($item['codigo']) ?>
                            </span>

                            <button type="button" 
                                    @click="navigator.clipboard.writeText('<?= e($item['codigo']) ?>'); copied = true; setTimeout(() => copied = false, 2500)"
                                    class="px-4 py-2.5 rounded-xl text-xs font-mono font-semibold border transition-all duration-200 flex items-center gap-1.5 active:scale-95"
                                    :class="copied 
                                        ? 'bg-green-500/20 text-green-300 border-green-500/50 shadow-md shadow-green-500/20' 
                                        : 'bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border-cyan-500/30 hover:border-cyan-400'">
                                <span x-show="!copied">📋</span>
                                <span x-show="copied" x-cloak>✓</span>
                                <span x-text="copied ? '¡COPIADO!' : 'COPIAR'"></span>
                            </button>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Caution alert -->
            <div class="border-t border-purple-500/20 pt-4 flex items-center justify-between text-xs font-mono text-gray-400">
                <span class="text-yellow-400 flex items-center gap-1">
                    <span>⚡</span> Recuerda que puedes consultar tus códigos en todo momento desde tu perfil.
                </span>
                <a href="/my-rentals" 
                   hx-get="/my-rentals" 
                   hx-target="#main-content" 
                   hx-push-url="true"
                   class="text-cyan-400 hover:text-cyan-300 underline underline-offset-4">
                    Ir a Mis Préstamos →
                </a>
            </div>

        </div>
    <?php endif; ?>

    <!-- Section: Compras Simuladas (if any) -->
    <?php if (!empty($purchasedBooks)): ?>
        <div class="bg-[#0d0d1a]/90 backdrop-blur-xl border-2 border-emerald-500/40 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-emerald-500/10 space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-emerald-500/20 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-xl">
                        🛒
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-['Orbitron'] font-bold text-emerald-300">
                            Compras Simuladas
                        </h2>
                        <span class="text-xs font-mono text-gray-500">
                            <?= count($purchasedBooks) ?> <?= count($purchasedBooks) === 1 ? 'título adquirido' : 'títulos adquiridos' ?>
                        </span>
                    </div>
                </div>

                <span class="px-3 py-1 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 text-xs font-mono">
                    SIMULATED_PURCHASE
                </span>
            </div>

            <!-- Simulated notice -->
            <div class="bg-emerald-950/20 border border-emerald-500/30 rounded-xl p-4 text-xs font-mono text-emerald-200">
                <p class="font-medium">
                    🛒 ¡Has "comprado" los siguientes libros! (Esto es una simulación didáctica, no se ha modificado la base de datos de inventario para compras comerciales).
                </p>
            </div>

            <!-- Purchased Books Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs">
                    <thead>
                        <tr class="border-b border-emerald-500/20 text-gray-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Libro</th>
                            <th class="py-3 px-4">Autor</th>
                            <th class="py-3 px-4 text-center">Cantidad</th>
                            <th class="py-3 px-4 text-right">Precio Unitario</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-500/10">
                        <?php foreach ($purchasedBooks as $item): ?>
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-white">
                                    <?= e($item['titulo']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-gray-400">
                                    <?= e($item['autor']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center text-gray-300">
                                    <?= e($item['quantity']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right text-gray-400">
                                    <?= format_price((float)$item['precio']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-cyan-400">
                                    <?= format_price((float)$item['precio'] * $item['quantity']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-emerald-500/30 font-bold text-sm">
                            <td colspan="4" class="py-4 px-4 text-right text-gray-300">
                                Total Simulado:
                            </td>
                            <td class="py-4 px-4 text-right text-cyan-400 neon-text text-lg">
                                <?= format_price((float)$totalSimulated) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    <?php endif; ?>

    <!-- Navigation Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
        <a href="/catalog" 
           hx-get="/catalog" 
           hx-target="#main-content" 
           hx-push-url="true"
           class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-['Orbitron'] font-semibold text-xs text-white tracking-wider bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/40 hover:border-cyan-400 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-purple-500/20">
            <span>📖</span> Volver al Catálogo
        </a>

        <a href="/my-rentals" 
           hx-get="/my-rentals" 
           hx-target="#main-content" 
           hx-push-url="true"
           class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-['Orbitron'] font-semibold text-xs text-black tracking-wider bg-gradient-to-r from-cyan-400 to-purple-400 hover:from-cyan-300 hover:to-purple-300 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-cyan-500/20">
            <span>📋</span> Ver Mis Préstamos
        </a>
    </div>

</div>
