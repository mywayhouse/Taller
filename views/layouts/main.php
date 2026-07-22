<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>
    
    <!-- Google Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">
    
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/main.css">
    <?php $pageCssFile = ROOT . '/public/assets/css/' . ($currentPage ?? 'dashboard') . '.css'; ?>
    <?php if (file_exists($pageCssFile)): ?>
        <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/<?= $currentPage ?? 'dashboard' ?>.css">
    <?php endif; ?>
</head>
<body>
    <div class="layout-wrapper">
        <!-- MENÚ LATERAL -->
        <aside class="sidebar" id="sidebar">
            <!-- Header del Sidebar -->
            <div class="sidebar-header">
                <div class="logo-container">
                    <span class="logo-icon"></span>
                    <h1 class="logo-text"><?= htmlspecialchars(APP_NAME) ?></h1>
                </div>
                <button id="toggleSidebar" class="toggle-btn" title="Colapsar menú">
                    <span class="material-icons-outlined">chevron_left</span>
                </button>
            </div>

            <!-- Navegación Principal -->
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <?php if (in_array($_SESSION['usuario_rol'] ?? '', ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'])): ?>
                    <li class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/dashboard" class="nav-link">
                            <span class="material-icons-outlined nav-icon">dashboard</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'clientes' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/clientes" class="nav-link">
                            <span class="material-icons-outlined nav-icon">people</span>
                            <span class="nav-text">Clientes</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'vehiculos' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/vehiculos" class="nav-link">
                            <span class="material-icons-outlined nav-icon">directions_car</span>
                            <span class="nav-text">Vehículos</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'ordenes' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/ordenes" class="nav-link">
                            <span class="material-icons-outlined nav-icon">assignment</span>
                            <span class="nav-text">Órdenes</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'repuestos' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/repuestos" class="nav-link">
                            <span class="material-icons-outlined nav-icon">build</span>
                            <span class="nav-text">Repuestos</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['usuario_rol'] ?? '', ['ADMINISTRADOR', 'RECEPCIONISTA'])): ?>
                    <li class="nav-item <?= $currentPage === 'proveedores' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/proveedores" class="nav-link">
                            <span class="material-icons-outlined nav-icon">inventory_2</span>
                            <span class="nav-text">Proveedores</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'facturas' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/facturas" class="nav-link">
                            <span class="material-icons-outlined nav-icon">receipt_long</span>
                            <span class="nav-text">Facturación</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($_SESSION['usuario_rol'] ?? '', ['ADMINISTRADOR'])): ?>
                    <li class="nav-item <?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/usuarios" class="nav-link">
                            <span class="material-icons-outlined nav-icon">manage_accounts</span>
                            <span class="nav-text">Usuarios</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $currentPage === 'logs' ? 'active' : '' ?>">
                        <a href="<?= APP_URL ?>/auditoria" class="nav-link">
                            <span class="material-icons-outlined nav-icon">history</span>
                            <span class="nav-text">Auditoría</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Footer de Usuario -->
            <div class="sidebar-footer">
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">
                        <span class="material-icons-outlined">account_circle</span>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Invitado') ?></span>
                        <span class="user-role"><?= htmlspecialchars($_SESSION['usuario_rol'] ?? 'Usuario') ?></span>
                    </div>
                    <button class="user-menu-trigger" id="userMenuTrigger" title="Opciones de usuario">
                        <span class="material-icons-outlined">expand_less</span>
                    </button>
                </div>
                
                <!-- Popup de usuario (FUERA del user-profile) -->
                <div class="user-popup" id="userPopup">
                    <ul class="popup-menu">

                    <!-- Opciones de usuario que no usaremos xd
                        <li>
                            <a href="<?= APP_URL ?>/perfil" class="popup-link">
                                <span class="material-icons-outlined">person</span>
                                <span>It´s time to be awesome xdxdxdxd</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/perfil" class="popup-link">
                                <span class="material-icons-outlined">person</span>
                                <span>Mi Perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= APP_URL ?>/configuracion" class="popup-link">
                                <span class="material-icons-outlined">settings</span>
                                <span>Configuración</span>
                            </a>
                        </li>
                    -->

                        <li class="popup-divider"></li>
                        <li>
                            <a href="<?= APP_URL ?>/auth/logout" class="popup-link logout-link">
                                <span class="material-icons-outlined">logout</span>
                                <span>Cerrar Sesión</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">
            <!-- Mensajes flash -->
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-success">
                    <span class="material-icons-outlined">check_circle</span>
                    <span><?= htmlspecialchars($_SESSION['mensaje']) ?></span>
                    <?php unset($_SESSION['mensaje']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <span class="material-icons-outlined">error</span>
                    <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <h2 class="page-title"><?= htmlspecialchars($pageTitle ?? '') ?></h2>

            <!-- Aquí se inyecta la vista específica -->
            <?php if (isset($contentView)): ?>
                <?php require_once VIEWS . "/{$contentView}.php"; ?>
            <?php endif; ?>
        </main>
    </div>

    <script src="<?= PUBLIC_URL ?>/assets/js/main.js"></script>
    <?php $pageJsFile = ROOT . '/public/assets/js/' . ($currentPage ?? 'dashboard') . '.js'; ?>
    <?php if (file_exists($pageJsFile)): ?>
        <script src="<?= PUBLIC_URL ?>/assets/js/<?= $currentPage ?? 'dashboard' ?>.js"></script>
    <?php endif; ?>
</body>
</html>