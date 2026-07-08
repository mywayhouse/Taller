<?php
// ============================================================
// alerts.php — Fragmento reutilizable para mensajes flash
// ============================================================
// Incluir en cualquier vista que necesite mostrar mensajes
// de éxito, error o advertencia al usuario.
//
// Uso:   require_once VIEWS . '/partials/alerts.php';
// ============================================================
?>
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
        <?php unset($_SESSION['mensaje']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['warning'])): ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars($_SESSION['warning']) ?>
        <?php unset($_SESSION['warning']); ?>
    </div>
<?php endif; ?>
