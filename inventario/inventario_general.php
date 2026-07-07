<?php
// inventario/inventario_general.php
// PROMPT IA: Interfaz corporativa premium S.A.V. con fondo industrial atenuado y tabla de repuestos integrada de alta legibilidad.
// TECNOLOGÍA IA: Gemini 2026.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.A.V. Corporativo - Control de Inventario</title>
    <!-- CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alertas de SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Fondo industrial premium oscurecido para garantizar un contraste limpio y legible */
        body { 
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95)), 
                        url('https://images.unsplash.com/photo-1517524206127-48bbd363f3d7?q=80&w=1600') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar Superior Corporativo con desenfoque de cristal (Glassmorphism) */
        .navbar-custom {
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .text-neon-blue {
            color: #00d2ff;
            text-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
        }

        /* Tarjetas semi-transparentes de alta definición para los datos */
        .card-transparent {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }

        /* Botón de acción con la transición de luz flotante (glow) refinada y profesional */
        .btn-action-blue {
            background-color: #0284c7;
            color: white;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease-in-out;
        }
        .btn-action-blue:hover {
            background-color: #0369a1;
            box-shadow: 0 0 15px rgba(2, 132, 199, 0.6);
            transform: translateY(-1px);
            color: white;
        }

        /* Estilos de la Tabla de Inventario */
        .table-custom { color: #ffffff; }
        .table-custom th { 
            border-bottom: 2px solid rgba(255, 255, 255, 0.15); 
            color: #00d2ff; 
            font-weight: 600;
        }
        .table-custom td { border-bottom: 1px solid rgba(255, 255, 255, 0.06); }

        /* Fila con alerta formal de bajo stock */
        .bg-low-stock { 
            background-color: rgba(239, 68, 68, 0.12) !important; 
            border-left: 4px solid #ef4444; 
        }
    </style>
</head>
<body>

    <!-- Menú de Navegación Oficial de S.A.V. Corporativo -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="#">
                <i class="fas fa-cubes text-neon-blue me-2"></i>S.A.V. <span class="text-neon-blue">CORPORATIVO</span>
            </a>
            <span class="badge bg-dark border border-secondary text-secondary">Sistema de Administración Vehicular</span>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Encabezado del Módulo Limpio -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <h1 class="fw-bold mb-2">Control de Inventario y <span class="text-neon-blue">Repuestos</span></h1>
                <p class="text-secondary fs-6">Administración integral de existencias en almacén, unidades de medida y alertas automáticas de criticidad de stock.</p>
            </div>
        </div>

        <!-- Tabla principal estructurada dentro de la tarjeta premium -->
        <div class="card card-transparent p-4 shadow-lg mb-5">
            <div class="row mb-3 align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <a href="repuesto_nuevo.php" class="btn btn-action-blue px-4 py-2">
                        <i class="fas fa-plus me-2"></i>Registrar Nuevo Repuesto
                    </a>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white"><i class="fas fa-search"></i></span>
                        <input type="text" id="txt-buscar" class="form-control bg-dark text-white border-secondary" placeholder="Buscar repuesto por nombre...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle m-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Repuesto</th>
                            <th>Stock Actual</th>
                            <th>Stock Mínimo</th>
                            <th>U. Medida</th>
                            <th>Precio Venta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="fw-semibold">Pastillas de Freno Delanteras</td>
                            <td><span class="badge bg-success px-3 py-2">15 unidades</span></td>
                            <td>5</td>
                            <td>Par</td>
                            <td>L. 450.00</td>
                            <td>
                                <button class="btn btn-sm btn-outline-light me-1"><i class="fas fa-edit text-warning"></i></button>
                                <button onclick="confirmarBorrado(1)" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr class="bg-low-stock">
                            <td>2</td>
                            <td class="fw-semibold text-danger">Filtro de Aceite de Motor</td>
                            <td><span class="badge bg-danger px-3 py-2">2 unidades</span></td>
                            <td>5</td>
                            <td>Unidad</td>
                            <td>L. 180.00</td>
                            <td>
                                <button class="btn btn-sm btn-outline-light me-1"><i class="fas fa-edit text-warning"></i></button>
                                <button onclick="confirmarBorrado(2)" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Enlace a tu script de validaciones -->
    <script src="js/inventario.js"></script>
</body>
</html>
