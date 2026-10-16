<?php
/**
 * Filas de la Tabla de Préstamos para Administración
 * Utilizado por HTMX para refrescar la lista reactivamente
 * 
 * @var array $rentals Lista de todos los registros de préstamos
 */

$statuses = [
    'pendiente' => 'Pendiente',
    'aprobado' => 'Aprobado',
    'prestado' => 'Prestado',
    'devuelto' => 'Devuelto',
    'cancelado' => 'Cancelado',
];
?>

<?php if (empty($rentals)): ?>
    <tr>
        <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-mono text-xs">
            <div class="flex flex-col items-center justify-center gap-2">
                <span class="text-2xl">📋</span>
                <span>No se encontraron registros de préstamos en el sistema.</span>
            </div>
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($rentals as $rental): ?>
        <tr class="hover:bg-white/5 border-b border-purple-500/10 transition-colors group">
            
            <!-- Código -->
            <td class="py-3.5 px-4 whitespace-nowrap">
                <span class="font-mono text-cyan-400 font-semibold text-xs bg-black/60 px-2.5 py-1 rounded border border-cyan-500/30 group-hover:border-cyan-400 transition-colors select-all">
                    <?= e($rental['codigo']) ?>
                </span>
            </td>

            <!-- Usuario -->
            <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="flex items-center gap-1.5 font-mono text-xs text-gray-200">
                    <span class="text-purple-400">👤</span>
                    <span class="font-medium"><?= e($rental['user_username']) ?></span>
                </div>
            </td>

            <!-- Libro & Autor -->
            <td class="py-3.5 px-4">
                <div class="font-medium text-white text-xs truncate max-w-xs group-hover:text-purple-300 transition-colors">
                    <?= e($rental['book_titulo']) ?>
                </div>
                <div class="text-[10px] text-gray-500 font-mono truncate max-w-xs">
                    por <?= e($rental['book_autor']) ?>
                </div>
            </td>

            <!-- Estado Badge -->
            <td class="py-3.5 px-4 whitespace-nowrap">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-medium border <?= rental_estado_color($rental['estado']) ?>">
                    <?= e(rental_estado_label($rental['estado'])) ?>
                </span>
            </td>

            <!-- Fecha de Solicitud -->
            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs text-gray-400">
                <?= date('d/m/Y H:i', strtotime($rental['created_at'])) ?>
            </td>

            <!-- Cambiar Estado Form -->
            <td class="py-3.5 px-4 whitespace-nowrap">
                <form hx-post="/admin/rentals/status/<?= e($rental['id']) ?>" 
                      hx-target="closest tbody" 
                      hx-swap="innerHTML"
                      method="POST" 
                      class="flex items-center gap-2"
                      x-data="{ updating: false }"
                      @htmx:before-request="updating = true">
                    <?= csrf_field() ?>
                    
                    <select name="estado" 
                            class="bg-black/60 border border-purple-500/30 rounded-lg px-2 py-1 text-xs font-mono text-gray-200 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400/40">
                        <?php foreach ($statuses as $val => $lbl): ?>
                            <option value="<?= e($val) ?>" <?= $rental['estado'] === $val ? 'selected' : '' ?>>
                                <?= e($lbl) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" 
                            :disabled="updating"
                            title="Actualizar estado del préstamo"
                            class="px-2.5 py-1 rounded-lg text-xs font-mono bg-purple-600/30 hover:bg-purple-600/50 text-purple-200 border border-purple-500/40 hover:border-cyan-400 transition-all duration-200 disabled:opacity-50 disabled:cursor-wait flex items-center gap-1 active:scale-95">
                        <span x-show="!updating">💾</span>
                        <span x-show="updating" x-cloak class="animate-spin text-[10px]">⚙</span>
                        <span class="hidden sm:inline">Actualizar</span>
                    </button>
                </form>
            </td>

        </tr>
    <?php endforeach; ?>
<?php endif; ?>
