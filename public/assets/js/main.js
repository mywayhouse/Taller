/**
 * ============================================================
 * main.js — Scripts globales del sistema
 * ============================================================
 * Contiene funciones utilitarias compartidas por todas las
 * vistas del sistema.
 * ============================================================
 */

'use strict';

/**
 * Oculta automáticamente los mensajes flash después de 4 segundos.
 */
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function () {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 4000);
    });
});
