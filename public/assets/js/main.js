/**
 * main.js — Scripts globales del sistema
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
    
    // ============================================================
    // 1. TOGGLE DEL SIDEBAR
    // ============================================================
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    
    if (toggleBtn && sidebar) {
        // Crear overlay para móvil
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.style.cssText = `
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        `;
        document.body.appendChild(overlay);
        
        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                // Móvil: toggle expanded
                sidebar.classList.toggle('expanded');
                overlay.style.display = sidebar.classList.contains('expanded') ? 'block' : 'none';
            } else {
                // Desktop: toggle collapsed
                sidebar.classList.toggle('collapsed');
            }
            const userPopup = document.getElementById('userPopup');
            if (userPopup) {
                userPopup.classList.remove('show');
            }
            // Guardar estado
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Cerrar con overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('expanded');
            overlay.style.display = 'none';
        });
        
        // Restaurar estado
        if (window.innerWidth > 768 && localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }
    }
    
    // ============================================================
    // 2. POPUP DE USUARIO
    // ============================================================
    const userMenuTrigger = document.getElementById('userMenuTrigger');
    const userPopup = document.getElementById('userPopup');
    const userProfile = document.getElementById('userProfile');
    
    if (userMenuTrigger && userPopup) {
        // Toggle popup
        function togglePopup(e) {
            e.stopPropagation();
            userPopup.classList.toggle('show');
            
            // Cambiar icono de flecha
            const icon = userMenuTrigger.querySelector('.material-icons-outlined');
            if (icon) {
                icon.textContent = userPopup.classList.contains('show') ? 'expand_more' : 'expand_less';
            }
        }
        
        userMenuTrigger.addEventListener('click', togglePopup);
        
        // También abrir al hacer clic en el perfil completo
        userProfile.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu-trigger')) {
                togglePopup(e);
            }
        });
        
        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target) && !userPopup.contains(e.target)) {
                userPopup.classList.remove('show');
                const icon = userMenuTrigger.querySelector('.material-icons-outlined');
                if (icon) icon.textContent = 'expand_less';
            }
        });
        
        // Cerrar con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userPopup.classList.contains('show')) {
                userPopup.classList.remove('show');
                const icon = userMenuTrigger.querySelector('.material-icons-outlined');
                if (icon) icon.textContent = 'expand_less';
            }
        });
    }
    
    // ============================================================
    // 3. OCULTAR ALERTAS
    // ============================================================
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s, transform 0.5s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000);
    });
});