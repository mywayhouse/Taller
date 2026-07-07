<?php
// inventario/guardar_repuesto.php
// Procesador Backend para insertar repuestos de forma segura en S.A.V. Corporativo

header('Content-Type: application/json');
require_once 'conexion.php'; // Incluye tu archivo de conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir y limpiar los valores enviados por POST
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
    $stock_minimo = isset($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
    $unidad = isset($_POST['unidad']) ? trim($_POST['unidad']) : '';
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.0;

    // Validación estricta en el servidor
    if (empty($nombre) || empty($unidad) || $stock < 0 || $stock_minimo < 0 || $precio < 0) {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios y válidos.']);
        exit;
    }

    try {
        // NOTA: Asegúrate de que los nombres de las columnas coincidan con tu Base_taller.sql
        // Ajusta "repuestos" si tu tabla tiene otro nombre (ej: inventario, productos)
        $sql = "INSERT INTO repuestos (nombre, stock, stock_minimo, unidad_medida, precio) 
                VALUES (:nombre, :stock, :stock_minimo, :unidad, :precio)";
        
        $stmt = $conexion->prepare($sql);
        
        // Vincular parámetros de forma segura (Evita Inyección SQL)
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':stock_minimo', $stock_minimo, PDO::PARAM_INT);
        $stmt->bindParam(':unidad', $unidad, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'El repuesto se ha registrado correctamente en la base de datos.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo ejecutar el registro de existencias.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error de Base de Datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de petición denegado.']);
}
?>