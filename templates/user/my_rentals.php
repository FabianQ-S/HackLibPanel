<?php
/**
 * Historial de Préstamos del Usuario
 * 
 * @var array $rentals Lista de préstamos del usuario actual
 */

$totalRentals = count($rentals);
?>

<div class="space-y-8">

    <!-- Header Section -->
    <div class="border-b border-purple-500/20 pb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-mono text-cyan-400 mb-1">
                    <span>></span>
                    <span class="text-purple-400">rentals.list</span>
                    <span class="text-gray-500">--user=current</span>
                    <span class="text-gray-600">--count=<?= e($totalRentals) ?></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold text-white tracking-wide flex items-center gap-3">
                    <span>Mis Préstamos</span>
                    <span class="text-xs font-mono font-normal px-2.5 py-1 rounded-md bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        <?= e($totalRentals) ?> <?= $totalRentals === 1 ? 'solicitud' : 'solicitudes' ?>
                    </span>
                </h1>
            </div>

            <div>
                <a href="/catalog" 
                   hx-get="/catalog" 
                   hx-target="#main-content" 
                   hx-push-url="true"
                   class="px-4 py-2 rounded-xl bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 hover:border-cyan-400 transition-all duration-200 text-xs font-mono flex items-center gap-2 shadow-sm">
                    <span>📖</span> Solicitar Nuevo Préstamo
                </a>
            </div>
        </div>
    </div>

    <!-- Rentals Table / Cards -->
    <?php if (empty($rentals)): ?>
        <!-- Empty State -->
        <div class="bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-12 text-center shadow-xl">
            <div class="w-20 h-20 mx-auto mb-4 bg-purple-500/10 border border-purple-500/30 rounded-2xl flex items-center justify-center text-4xl">
                📋
            </div>
            <h3 class="text-xl font-['Orbitron'] font-bold text-white mb-2">Sin Préstamos Registrados</h3>
            <p class="text-gray-400 font-mono text-sm max-w-md mx-auto mb-6">
                > query.rentals = 0_RECORDS // No has solicitado préstamos de libros aún.
            </p>
            <a href="/catalog" 
               hx-get="/catalog" 
               hx-target="#main-content" 
               hx-push-url="true"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-500 hover:to-cyan-500 text-white font-['Orbitron'] font-semibold text-xs tracking-wider transition-all duration-200 shadow-lg shadow-purple-500/20">
                <span>📖</span> Explorar Catálogo y Solicitar Libro
            </a>
        </div>
    <?php else: ?>
        <div class="bg-[#0d0d1a]/90 backdrop-blur-xl border border-purple-500/20 rounded-2xl overflow-hidden shadow-2xl shadow-purple-500/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs">
                    <thead>
                        <tr class="bg-black/60 border-b border-purple-500/20 text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-6">Código de Retiro</th>
                            <th class="py-4 px-6">Libro & Autor</th>
                            <th class="py-4 px-6">Estado</th>
                            <th class="py-4 px-6">Fecha de Solicitud</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-500/10">
                        <?php foreach ($rentals as $rental): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                
                                <!-- Código with copy -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2" x-data="{ copied: false }">
                                        <span class="text-sm font-bold text-cyan-400 font-mono bg-black/60 px-3 py-1.5 rounded-lg border border-cyan-500/30 group-hover:border-cyan-400 transition-colors neon-text">
                                            <?= e($rental['codigo']) ?>
                                        </span>
                                        <button type="button" 
                                                @click="navigator.clipboard.writeText('<?= e($rental['codigo']) ?>'); copied = true; setTimeout(() => copied = false, 2000)"
                                                title="Copiar código al portapapeles"
                                                class="p-1.5 rounded-lg border transition-all duration-200"
                                                :class="copied ? 'bg-green-500/20 text-green-300 border-green-500/40' : 'bg-black/40 text-gray-400 hover:text-cyan-300 border-purple-500/20 hover:border-cyan-400/50'">
                                            <span x-show="!copied">📋</span>
                                            <span x-show="copied" x-cloak class="text-green-400 font-bold">✓</span>
                                        </button>
                                    </div>
                                </td>

                                <!-- Libro & Autor -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white text-sm group-hover:text-purple-300 transition-colors">
                                        <?= e($rental['book_titulo']) ?>
                                    </div>
                                    <div class="text-gray-400 text-[11px] mt-0.5">
                                        por <?= e($rental['book_autor']) ?>
                                    </div>
                                </td>

                                <!-- Estado Badge -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium border <?= rental_estado_color($rental['estado']) ?>">
                                        <?= e(rental_estado_label($rental['estado'])) ?>
                                    </span>
                                </td>

                                <!-- Fecha de Solicitud -->
                                <td class="py-4 px-6 whitespace-nowrap text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <span>🕒</span>
                                        <span><?= date('d/m/Y H:i', strtotime($rental['created_at'])) ?></span>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Status Legend Section -->
    <div class="bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-6 shadow-xl space-y-4">
        <div class="flex items-center gap-2 border-b border-purple-500/20 pb-3">
            <span class="text-sm">📌</span>
            <h3 class="text-xs font-mono font-bold text-gray-300 uppercase tracking-widest">
                Guía de Estados de Préstamo
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 font-mono text-xs">
            
            <!-- Pendiente -->
            <div class="flex items-start gap-3 p-3 rounded-xl bg-black/40 border border-purple-500/10">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] border shrink-0 <?= rental_estado_color('pendiente') ?>">
                    Pendiente
                </span>
                <span class="text-gray-400">
                    Tu solicitud está siendo procesada por el personal de biblioteca.
                </span>
            </div>

            <!-- Aprobado -->
            <div class="flex items-start gap-3 p-3 rounded-xl bg-black/40 border border-purple-500/10">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] border shrink-0 <?= rental_estado_color('aprobado') ?>">
                    Aprobado
                </span>
                <span class="text-gray-400">
                    Ve al punto de préstamo con tu código para recoger tu ejemplar.
                </span>
            </div>

            <!-- Prestado -->
            <div class="flex items-start gap-3 p-3 rounded-xl bg-black/40 border border-purple-500/10">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] border shrink-0 <?= rental_estado_color('prestado') ?>">
                    Prestado
                </span>
                <span class="text-gray-400">
                    El libro está en tu poder durante el período activo de préstamo.
                </span>
            </div>

            <!-- Devuelto -->
            <div class="flex items-start gap-3 p-3 rounded-xl bg-black/40 border border-purple-500/10">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] border shrink-0 <?= rental_estado_color('devuelto') ?>">
                    Devuelto
                </span>
                <span class="text-gray-400">
                    Libro devuelto correctamente al inventario de la biblioteca.
                </span>
            </div>

            <!-- Cancelado -->
            <div class="flex items-start gap-3 p-3 rounded-xl bg-black/40 border border-purple-500/10">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] border shrink-0 <?= rental_estado_color('cancelado') ?>">
                    Cancelado
                </span>
                <span class="text-gray-400">
                    Solicitud cancelada. El ejemplar ha sido liberado al inventario.
                </span>
            </div>

        </div>
    </div>

</div>
