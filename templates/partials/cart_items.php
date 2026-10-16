<?php
/**
 * Parcial de Elementos del Carrito
 * 
 * @var array $cart Lista de elementos en el carrito
 * @var float $total Total acumulado de compras
 */

$compraCount = 0;
$alquilerCount = 0;
foreach ($cart as $item) {
    if ($item['type'] === 'compra') {
        $compraCount += $item['quantity'];
    } else {
        $alquilerCount += $item['quantity'];
    }
}
?>

<?php if (empty($cart)): ?>
    <!-- Empty State -->
    <div class="bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-12 text-center shadow-xl">
        <div class="w-20 h-20 mx-auto mb-5 bg-gradient-to-br from-purple-500/10 to-cyan-500/10 border border-purple-500/30 rounded-2xl flex items-center justify-center text-4xl shadow-inner">
            🛒
        </div>
        <h3 class="text-xl sm:text-2xl font-['Orbitron'] font-bold text-white mb-2">Tu Carrito está Vacío</h3>
        <p class="text-gray-400 font-mono text-sm max-w-md mx-auto mb-6">
            > cart.status = EMPTY // No se han detectado libros para compra o solicitud de préstamo.
        </p>
        <a href="/catalog" 
           hx-get="/catalog" 
           hx-target="#main-content" 
           hx-push-url="true"
           class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-500 hover:to-cyan-500 text-white font-['Orbitron'] font-semibold text-xs tracking-wider transition-all duration-300 shadow-lg shadow-purple-500/20 hover:shadow-cyan-500/30 active:scale-95">
            <span>📖</span> Explorar Catálogo de Libros
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Cart Items List (2 cols on lg) -->
        <div class="lg:col-span-2 space-y-4">
            <?php foreach ($cart as $index => $item): ?>
                <div class="bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 hover:border-purple-500/40 rounded-2xl p-5 shadow-lg transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group">
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <!-- Type Badge -->
                            <?php if ($item['type'] === 'compra'): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    🛒 Compra
                                </span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                    📖 Alquiler / Préstamo
                                </span>
                            <?php endif; ?>

                            <!-- Quantity Badge -->
                            <span class="px-2 py-0.5 rounded bg-black/50 text-gray-400 border border-purple-500/20 text-xs font-mono">
                                Cant: <?= e($item['quantity']) ?>
                            </span>
                        </div>

                        <!-- Title -->
                        <h4 class="text-base sm:text-lg font-bold text-white truncate group-hover:text-cyan-300 transition-colors">
                            <a href="/catalog/<?= e($item['book_id']) ?>"
                               hx-get="/catalog/<?= e($item['book_id']) ?>"
                               hx-target="#main-content"
                               hx-push-url="true"
                               class="hover:underline">
                                <?= e($item['titulo']) ?>
                            </a>
                        </h4>

                        <!-- Author -->
                        <p class="text-xs sm:text-sm text-gray-400 font-mono">
                            <span class="text-gray-600">por</span> <?= e($item['autor']) ?>
                        </p>
                    </div>

                    <!-- Price & Remove Action -->
                    <div class="flex items-center justify-between sm:justify-end gap-6 pt-3 sm:pt-0 border-t sm:border-t-0 border-purple-500/10">
                        <div class="text-left sm:text-right">
                            <?php if ($item['type'] === 'compra'): ?>
                                <span class="text-xs text-gray-500 font-mono block">Subtotal</span>
                                <span class="text-lg font-bold font-mono text-cyan-400 neon-text">
                                    <?= format_price((float)$item['precio'] * $item['quantity']) ?>
                                </span>
                                <?php if ($item['quantity'] > 1): ?>
                                    <span class="text-[10px] text-gray-500 font-mono block">
                                        (<?= format_price((float)$item['precio']) ?> c/u)
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-xs text-gray-500 font-mono block">Modalidad</span>
                                <span class="text-sm font-semibold font-mono text-purple-300">
                                    Préstamo (Gratis)
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Remove Button Form -->
                        <form hx-post="/cart/remove" 
                              hx-target="#cart-container"
                              hx-swap="innerHTML"
                              x-data="{ deleting: false }"
                              @htmx:before-request="deleting = true"
                              class="inline-flex">
                            <?= csrf_field() ?>
                            <input type="hidden" name="index" value="<?= e($index) ?>">
                            <button type="submit" 
                                    title="Eliminar este artículo del carrito"
                                    class="p-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 hover:border-red-500/40 transition-all duration-200 active:scale-90">
                                <svg x-show="!deleting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <svg x-show="deleting" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <!-- Order Summary & Checkout Card (1 col on lg) -->
        <div class="bg-[#0d0d1a]/90 backdrop-blur-xl border border-purple-500/30 rounded-2xl p-6 shadow-xl space-y-6 sticky top-20">
            
            <div class="border-b border-purple-500/20 pb-4">
                <div class="text-xs font-mono text-cyan-400 mb-1">> summary.checkout</div>
                <h3 class="text-xl font-['Orbitron'] font-bold text-white">Resumen de Orden</h3>
            </div>

            <!-- Breakdown list -->
            <div class="space-y-3 font-mono text-sm">
                <div class="flex items-center justify-between text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <span>🛒</span> Libros para Compra:
                    </span>
                    <span class="text-gray-200"><?= e($compraCount) ?></span>
                </div>

                <div class="flex items-center justify-between text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <span>📖</span> Libros para Préstamo:
                    </span>
                    <span class="text-purple-300"><?= e($alquilerCount) ?></span>
                </div>

                <div class="pt-3 border-t border-purple-500/10 flex items-center justify-between">
                    <span class="text-base text-gray-300 font-semibold">Total a Pagar:</span>
                    <span class="text-2xl font-bold text-cyan-400 neon-text">
                        <?= format_price((float)$total) ?>
                    </span>
                </div>
            </div>

            <!-- Informational Note -->
            <div class="bg-black/50 border border-purple-500/20 rounded-xl p-3.5 text-xs font-mono text-gray-400 space-y-1.5">
                <p class="text-cyan-400 font-semibold flex items-center gap-1">
                    <span>ℹ</span> Información Importante
                </p>
                <p class="text-[11px] leading-relaxed">
                    Los préstamos no generan cargo económico. Al completar el pedido se generará un código para cada libro que deberás presentar al retirar.
                </p>
            </div>

            <!-- Dramatic Checkout Button -->
            <form method="POST" 
                  action="/checkout" 
                  hx-post="/checkout" 
                  hx-target="#main-content" 
                  hx-push-url="true"
                  x-data="{ processing: false }" 
                  @submit="processing = true">
                <?= csrf_field() ?>
                
                <button type="submit" 
                        :disabled="processing"
                        class="w-full py-4 px-6 rounded-xl font-['Orbitron'] font-bold text-sm text-black tracking-wider bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-500 hover:from-cyan-300 hover:via-purple-300 hover:to-pink-400 transition-all duration-300 shadow-lg shadow-cyan-500/25 hover:shadow-cyan-400/40 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                    <template x-if="processing">
                        <svg class="animate-spin h-5 w-5 text-black" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span class="text-lg" x-show="!processing">⚡</span>
                    <span x-text="processing ? 'PROCESANDO ORDEN...' : 'CONFIRMAR Y PROCESAR ORDEN'"></span>
                </button>
            </form>

            <div class="text-center">
                <a href="/catalog" 
                   hx-get="/catalog" 
                   hx-target="#main-content" 
                   hx-push-url="true"
                   class="text-xs font-mono text-gray-400 hover:text-cyan-300 transition-colors">
                    ← Seguir explorando catálogo
                </a>
            </div>

        </div>

    </div>
<?php endif; ?>
