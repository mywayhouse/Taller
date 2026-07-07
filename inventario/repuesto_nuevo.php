<?php
// inventario/repuesto_nuevo.php
// PROMPT IA: Formulario corporativo premium S.A.V. con alta legibilidad, fondo atenuado y transiciones limpias.
// TECNOLOGÍA IA: Gemini 2026.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.A.V. Corporativo - Nuevo Repuesto</title>
    <!-- CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95)), 
                        url('https://images.unsplash.com/photo-1517524206127-48bbd363f3d7?q=80&w=1600') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .text-neon-blue {
            color: #00d2ff;
            text-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
        }

        .card-transparent {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }

        /* Texto de las etiquetas en blanco puro para máxima visibilidad */
        .form-label-visible {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Inputs oscuros pero nítidos con bordes legibles */
        .form-control-custom {
            background-color: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            padding: 10px 12px;
        }
        .form-control-custom::placeholder {
            color: #64748b !important;
        }
        .form-control-custom:focus {
            border-color: #00d2ff !important;
            box-shadow: 0 0 8px rgba(0, 210, 255, 0.4) !important;
        }

        .btn-action-success {
            background-color: #10b981;
            color: white;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease-in-out;
        }
        .btn-action-success:hover {
            background-color: #059669;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.6);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="mb-4">
            <a href="inventario_general.php" class="text-decoration-none text-info fw-bold">
                <i class="fas fa-arrow-left me-2"></i> Volver al Catálogo
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-transparent p-4 shadow-lg">
                    <h2 class="fw-bold mb-4 text-white"><i class="fas fa-plus-circle text-neon-blue me-2"></i> Registrar en <span class="text-neon-blue">S.A.V. Inventario</span></h2>
                    
                    <form id="form-nuevo-repuesto" novalidate>
                        <div class="mb-3">
                            <label class="form-label form-label-visible">Nombre del Repuesto:</label>
                            <input type="text" id="txt-nombre" class="form-control form-control-custom" placeholder="Ej: Amortiguador Delantero" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-visible">Stock Inicial:</label>
                                <input type="number" id="txt-stock" class="form-control form-control-custom" min="0" placeholder="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-visible">Stock Mínimo:</label>
                                <input type="number" id="txt-stock-minimo" class="form-control form-control-custom" min="0" placeholder="5" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-visible">Unidad de Medida:</label>
                                <select id="txt-unidad" class="form-control form-control-custom" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Unidad">Unidad</option>
                                    <option value="Par">Par</option>
                                    <option value="Kit">Kit</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-visible">Precio de Venta (Lps):</label>
                                <input type="number" id="txt-precio" class="form-control form-control-custom" min="0" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-action-success px-5 py-2">
                                <i class="fas fa-save me-2"></i>Guardar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/inventario.js"></script>
</body>
</html>