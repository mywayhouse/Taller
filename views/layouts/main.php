<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>

    <!-- CSS Global -->
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/<?= $currentPage ?? 'dashboard' ?>.css">
</head>
<body>
    <!-- ============================================================
         BARRA DE NAVEGACIÓN SUPERIOR
         ============================================================ -->
    <header class="topbar">
        <div class="topbar-brand">
            <h1><?= htmlspecialchars(APP_NAME) ?></h1>
        </div>
        <nav class="topbar-nav">
            <a href="<?= APP_URL ?>/dashboard">Dashboard</a>
            <a href="<?= APP_URL ?>/clientes">Clientes</a>
            <a href="<?= APP_URL ?>/vehiculos">Vehículos</a>
            <a href="<?= APP_URL ?>/ordenes">Órdenes</a>
            <a href="<?= APP_URL ?>/repuestos">Repuestos</a>
            <a href="<?= APP_URL ?>/facturas">Facturas</a>
            <a href="<?= APP_URL ?>/usuarios">Usuarios</a>
        </nav>
        <div class="topbar-user">
            <span><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Invitado') ?></span>
            <a href="<?= APP_URL ?>/auth/logout" class="btn-logout">Cerrar Sesión</a>
        </div>
    </header>

    <div class="layout-wrapper">
        <!-- ============================================================
             MENÚ LATERAL
             ============================================================ -->
        <aside class="sidebar">
            <ul>
                <li class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= APP_URL ?>/dashboard">Dashboard</a>
                </li>
                <li class="<?= $currentPage === 'clientes' ? 'active' : '' ?>">
                    <a href="<?= APP_URL ?>/clientes">Clientes</a>
                </li>
                <li class="<?= $currentPage === 'vehiculos' ? 'active' : '' ?>">
                    <a href="<?= APP_URL ?>/vehiculos">Vehículos</a>
                </li>
                <li class="<?= $currentPage === 'ordenes' ? 'active' : '' ?>">
                    <a href="<?= APP_URL ?>/ordenes">Órdenes de Servicio</a>
                </li>
                <li class="<?= $currentPage === 'repuestos' ? 'active' : '' ?>">
                    <a href="<?= APP_URL ?>/repuestos">Repuestos</a>
                </li>
                <li class="<?= $currentPage === 'facturas' ? 'active' : '' ?>">
                    <a href="<?= APP_URL ?>/facturas">Facturación</a>
                </li>
                <li class="<?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                    <a href="<?= APP_URL ?>/usuarios">Usuarios</a>
                </li>
            </ul>
        </aside>

        <!-- ============================================================
             CONTENIDO PRINCIPAL
             ============================================================ -->
        <main class="main-content">
            <!-- Mensajes flash (éxito/error) -->
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                    <?php unset($_SESSION['mensaje']); ?>
                </div>
            <?php endif; ?>

            <h2 class="page-title"><?= htmlspecialchars($pageTitle ?? '') ?></h2>

            <!-- Aquí se inyecta la vista específica -->
            <?php if (isset($contentView)): ?>
                <?php require_once VIEWS . "/{$contentView}.php"; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- JS Global -->
    <script src="<?= PUBLIC_URL ?>/assets/js/main.js"></script>
    <script src="<?= PUBLIC_URL ?>/assets/js/<?= $currentPage ?? 'dashboard' ?>.js"></script>
</body>
</html>
