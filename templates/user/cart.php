<?php
/**
 * Vista Principal del Carrito de Compras
 * 
 * @var array $cart Lista de elementos en el carrito
 * @var float $total Total monetario de las compras
 */

$totalItems = array_sum(array_column($cart, 'quantity'));
?>

<div class="space-y-6">

    <!-- Header Section -->
    <div class="border-b border-purple-500/20 pb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-mono text-cyan-400 mb-1">
                    <span>></span>
                    <span class="text-purple-400">cart.view</span>
                    <span class="text-gray-500">--items=<?= e($totalItems) ?></span>
                    <span class="text-gray-600">--mode=active</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold text-white tracking-wide flex items-center gap-3">
                    <span>Mi Carrito</span>
                    <span class="text-xs font-mono font-normal px-2.5 py-1 rounded-md bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        <?= e($totalItems) ?> <?= $totalItems === 1 ? 'artículo' : 'artículos' ?>
                    </span>
                </h1>
            </div>

            <!-- Terminal badge status -->
            <div class="flex items-center gap-3 text-xs font-mono text-gray-500">
                <a href="/catalog" 
                   hx-get="/catalog" 
                   hx-target="#main-content" 
                   hx-push-url="true"
                   class="px-3 py-1.5 rounded-lg bg-black/40 border border-purple-500/20 text-cyan-400 hover:text-cyan-300 hover:border-cyan-400/50 transition-colors flex items-center gap-1.5">
                    <span>←</span> Seguir Comprando
                </a>
            </div>
        </div>
    </div>

    <!-- Target container for HTMX partial swaps -->
    <div id="cart-container">
        <?php view('partials/cart_items', ['cart' => $cart, 'total' => $total]); ?>
    </div>

</div>
