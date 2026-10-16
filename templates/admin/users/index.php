<div class="space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-purple-500/20">
        <div>
            <div class="terminal-prompt flex items-center gap-2">
                <span class="text-cyan-400 font-bold">></span>
                <span class="text-gray-400">auth.users --master-scope --view=list</span>
                <span class="typing-cursor"></span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-['Orbitron'] font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent">
                    Gestión de Usuarios
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/20 text-amber-400 border border-amber-500/40">
                    MASTER ONLY
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">
                Control de identidades, privilegios de administrador y cuentas de la plataforma.
            </p>
        </div>

        <div>
            <a href="/admin/users/create"
               hx-get="/admin/users/create"
               hx-target="#main-content"
               hx-push-url="true"
               class="btn-primary inline-flex items-center gap-2 text-sm shadow-lg shadow-purple-500/25">
                <span class="text-base">👤➕</span>
                <span>Nuevo Usuario</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php 
        $masterCount = count(array_filter($users, fn($u) => $u['role'] === 'master_admin'));
        $adminCount = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
        $regularCount = count(array_filter($users, fn($u) => $u['role'] === 'user'));
        ?>
        <div class="p-4 rounded-xl bg-[#0d0d1a]/80 border border-amber-500/30 flex items-center justify-between">
            <div>
                <span class="text-xs font-mono uppercase text-gray-400">Master Admin</span>
                <div class="text-2xl font-['Orbitron'] font-bold text-amber-400"><?= $masterCount ?></div>
            </div>
            <span class="text-2xl">⭐</span>
        </div>
        <div class="p-4 rounded-xl bg-[#0d0d1a]/80 border border-purple-500/30 flex items-center justify-between">
            <div>
                <span class="text-xs font-mono uppercase text-gray-400">Administradores</span>
                <div class="text-2xl font-['Orbitron'] font-bold text-purple-400"><?= $adminCount ?></div>
            </div>
            <span class="text-2xl">🛡️</span>
        </div>
        <div class="p-4 rounded-xl bg-[#0d0d1a]/80 border border-cyan-500/30 flex items-center justify-between">
            <div>
                <span class="text-xs font-mono uppercase text-gray-400">Usuarios Lectores</span>
                <div class="text-2xl font-['Orbitron'] font-bold text-cyan-400"><?= $regularCount ?></div>
            </div>
            <span class="text-2xl">👤</span>
        </div>
    </div>

    <!-- Users Listing -->
    <?php if (empty($users)): ?>
        <div class="card bg-[#0d0d1a]/80 backdrop-blur-xl border border-purple-500/20 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-3xl">
                👥
            </div>
            <h3 class="text-lg font-['Orbitron'] font-bold text-gray-200">No hay usuarios registrados</h3>
            <p class="text-sm text-gray-500 font-mono mt-1">Crea nuevos usuarios o administradores para comenzar.</p>
            <div class="mt-6">
                <a href="/admin/users/create" 
                   hx-get="/admin/users/create" 
                   hx-target="#main-content" 
                   hx-push-url="true"
                   class="btn-primary text-xs">
                    + Nuevo Usuario
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
                            <th class="py-3.5 px-4 font-semibold">Usuario</th>
                            <th class="py-3.5 px-4 font-semibold">Correo Electrónico</th>
                            <th class="py-3.5 px-4 font-semibold">Rol del Sistema</th>
                            <th class="py-3.5 px-4 font-semibold">Fecha Registro</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-500/10 font-sans text-sm">
                        <?php foreach ($users as $user): ?>
                        <?php $isMaster = ($user['role'] === 'master_admin'); ?>
                        <tr class="transition-colors group <?= $isMaster ? 'bg-amber-500/[0.04] border-l-2 border-l-amber-400' : 'hover:bg-white/[0.03]' ?>">
                            
                            <!-- Username -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold font-mono <?= $isMaster ? 'bg-amber-500/20 border border-amber-500/50 text-amber-300' : ($user['role'] === 'admin' ? 'bg-purple-500/20 border border-purple-500/40 text-purple-300' : 'bg-cyan-500/20 border border-cyan-500/40 text-cyan-300') ?>">
                                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-200 group-hover:text-cyan-300 transition-colors flex items-center gap-1.5">
                                            <span><?= e($user['username']) ?></span>
                                            <?php if ($isMaster): ?>
                                                <span class="text-amber-400 text-xs" title="Superusuario">⭐</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-[10px] text-gray-500 font-mono">UID #<?= (int)$user['id'] ?></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-3.5 px-4 text-gray-300 font-mono text-xs">
                                <?= e($user['email']) ?>
                            </td>

                            <!-- Rol (Colored Badge) -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <?php if ($user['role'] === 'master_admin'): ?>
                                    <span class="badge bg-amber-500/20 text-amber-400 border-amber-500/30 font-mono flex items-center gap-1 w-fit">
                                        <span>⭐</span> Master Admin
                                    </span>
                                <?php elseif ($user['role'] === 'admin'): ?>
                                    <span class="badge bg-purple-500/20 text-purple-400 border-purple-500/30 font-mono flex items-center gap-1 w-fit">
                                        <span>🛡️</span> Admin
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-cyan-500/20 text-cyan-400 border-cyan-500/30 font-mono flex items-center gap-1 w-fit">
                                        <span>👤</span> Usuario
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Fecha Registro -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs text-gray-400">
                                <div><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
                                <div class="text-[10px] text-gray-600"><?= date('H:i:s', strtotime($user['created_at'])) ?></div>
                            </td>

                            <!-- Acciones (Non-editable for Master Admin) -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <?php if ($isMaster): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-mono font-medium text-amber-400/80 bg-amber-500/10 border border-amber-500/20">
                                        <span>🔒</span> Protegido
                                    </span>
                                <?php else: ?>
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit User -->
                                        <a href="/admin/users/edit/<?= (int)$user['id'] ?>"
                                           hx-get="/admin/users/edit/<?= (int)$user['id'] ?>"
                                           hx-target="#main-content"
                                           hx-push-url="true"
                                           title="Editar Usuario"
                                           class="px-2.5 py-1.5 rounded-lg bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 hover:bg-cyan-500/20 hover:text-cyan-300 transition-all text-xs flex items-center gap-1 font-mono">
                                            <span>✏️</span>
                                            <span class="hidden lg:inline">Editar</span>
                                        </a>

                                        <!-- Delete User -->
                                        <form method="POST" 
                                              action="/admin/users/delete/<?= (int)$user['id'] ?>"
                                              hx-post="/admin/users/delete/<?= (int)$user['id'] ?>"
                                              hx-target="#main-content"
                                              hx-confirm="¿Estás seguro de que deseas eliminar permanentemente al usuario '<?= e($user['username']) ?>'?">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    title="Eliminar Usuario"
                                                    class="px-2.5 py-1.5 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all text-xs flex items-center gap-1 font-mono">
                                                <span>🗑️</span>
                                                <span class="hidden lg:inline">Borrar</span>
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards View (Shown on Mobile only) -->
        <div class="md:hidden space-y-4">
            <?php foreach ($users as $user): ?>
            <?php $isMaster = ($user['role'] === 'master_admin'); ?>
            <div class="card bg-[#0d0d1a]/90 backdrop-blur-xl rounded-xl p-4 shadow-lg space-y-3 <?= $isMaster ? 'border-2 border-amber-500/40 bg-amber-500/[0.04]' : 'border border-purple-500/20' ?>">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-mono font-bold text-sm <?= $isMaster ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : ($user['role'] === 'admin' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40') ?>">
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-200 text-sm flex items-center gap-1">
                                <span><?= e($user['username']) ?></span>
                                <?php if ($isMaster): ?>
                                    <span class="text-amber-400 text-xs">⭐</span>
                                <?php endif; ?>
                            </h3>
                            <p class="text-xs text-gray-400 font-mono mt-0.5"><?= e($user['email']) ?></p>
                        </div>
                    </div>

                    <div>
                        <?php if ($user['role'] === 'master_admin'): ?>
                            <span class="badge bg-amber-500/20 text-amber-400 border-amber-500/30 text-[10px] font-mono">MASTER</span>
                        <?php elseif ($user['role'] === 'admin'): ?>
                            <span class="badge bg-purple-500/20 text-purple-400 border-purple-500/30 text-[10px] font-mono">ADMIN</span>
                        <?php else: ?>
                            <span class="badge bg-cyan-500/20 text-cyan-400 border-cyan-500/30 text-[10px] font-mono">USER</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs font-mono pt-2 border-t border-purple-500/10 text-gray-500">
                    <span>UID #<?= (int)$user['id'] ?></span>
                    <span>Reg: <?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                </div>

                <!-- Actions for mobile -->
                <?php if ($isMaster): ?>
                <div class="pt-2 border-t border-amber-500/20 text-center">
                    <span class="text-xs font-mono text-amber-400/80">🔒 Superusuario protegido del sistema</span>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-purple-500/10">
                    <a href="/admin/users/edit/<?= (int)$user['id'] ?>"
                       hx-get="/admin/users/edit/<?= (int)$user['id'] ?>"
                       hx-target="#main-content"
                       hx-push-url="true"
                       class="py-2 text-center rounded-lg bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 hover:bg-cyan-500/20 text-xs font-semibold flex items-center justify-center gap-1">
                        <span>✏️</span> Editar
                    </a>

                    <form method="POST" 
                          action="/admin/users/delete/<?= (int)$user['id'] ?>"
                          hx-post="/admin/users/delete/<?= (int)$user['id'] ?>"
                          hx-target="#main-content"
                          hx-confirm="¿Estás seguro de que deseas eliminar permanentemente al usuario '<?= e($user['username']) ?>'?">
                        <?= csrf_field() ?>
                        <button type="submit" 
                                class="w-full py-2 text-center rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 text-xs font-semibold flex items-center justify-center gap-1">
                            <span>🗑️</span> Borrar
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>
