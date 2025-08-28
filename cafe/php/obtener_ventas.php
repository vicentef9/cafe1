<?php
date_default_timezone_set('America/Mexico_City');
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

try {
    // Construir la consulta base para las ventas (tabla principal)
    $query = "SELECT v.*, e.nombre as empleado_nombre FROM ventas v JOIN empleados e ON v.empleado_id = e.id";
    $conditions = [];
    $params = [];

    // Aplicar filtro de fecha
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
        $conditions[] = "DATE(v.fecha_venta) = :fecha";
        $params[':fecha'] = $_GET['fecha'];
    }

    // Aplicar otros filtros
    if (isset($_GET['metodo_pago']) && !empty($_GET['metodo_pago'])) {
        $conditions[] = "v.metodo_pago = :metodo_pago";
        $params[':metodo_pago'] = $_GET['metodo_pago'];
    }

    if (isset($_GET['estado']) && !empty($_GET['estado'])) {
        $conditions[] = "v.estado = :estado";
        $params[':estado'] = $_GET['estado'];
    }

    if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
        $conditions[] = "(v.id LIKE :busqueda OR e.nombre LIKE :busqueda)";
        $params[':busqueda'] = '%' . $_GET['busqueda'] . '%';
    }

    // Unir todas las condiciones
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Ordenar por fecha más reciente
    $query .= " ORDER BY v.fecha_venta DESC";

    // Ejecutar la consulta
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener estadísticas de Ventas del Día y Total de Ventas
    $query_daily_sales_stats = "SELECT
                                COUNT(v.id) as total_ventas,
                                IFNULL(SUM(v.total), 0) as total_ingresos,
                                COUNT(DISTINCT v.empleado_id) as total_empleados,
                                MIN(v.fecha_venta) as primera_venta,
                                MAX(v.fecha_venta) as ultima_venta
                                FROM ventas v";
    $params_daily = [];
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
        $query_daily_sales_stats .= " WHERE DATE(v.fecha_venta) = :fecha";
        $params_daily[':fecha'] = $_GET['fecha'];
    } else {
        $query_daily_sales_stats .= " WHERE DATE(v.fecha_venta) = CURDATE()";
    }

    $stmt_daily_sales_stats = $conn->prepare($query_daily_sales_stats);
    foreach ($params_daily as $key => $value) {
        $stmt_daily_sales_stats->bindValue($key, $value);
    }
    $stmt_daily_sales_stats->execute();
    $daily_sales_stats = $stmt_daily_sales_stats->fetch(PDO::FETCH_ASSOC);

    // Obtener cantidad de productos vendidos del día
    $query_daily_products_sold = "SELECT
                                  IFNULL(SUM(dv.cantidad), 0) as total_productos_vendidos
                                  FROM detalles_venta dv
                                  JOIN ventas v ON dv.venta_id = v.id";
    $params_products_sold = [];
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
        $query_daily_products_sold .= " WHERE DATE(v.fecha_venta) = :fecha";
        $params_products_sold[':fecha'] = $_GET['fecha'];
    } else {
        $query_daily_products_sold .= " WHERE DATE(v.fecha_venta) = CURDATE()";
    }
    $stmt_daily_products_sold = $conn->prepare($query_daily_products_sold);
    foreach ($params_products_sold as $key => $value) {
        $stmt_daily_products_sold->bindValue($key, $value);
    }
    $stmt_daily_products_sold->execute();
    $daily_products_sold = $stmt_daily_products_sold->fetch(PDO::FETCH_ASSOC);

    // Obtener ventas del mes actual
    $query_ventas_mes = "SELECT
                        COUNT(id) as total_ventas_mes,
                        IFNULL(SUM(total), 0) as total_ingresos_mes
                        FROM ventas";
    $params_ventas_mes = [];
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
        // Si se filtra por fecha, calcular el mes de esa fecha
        $query_ventas_mes .= " WHERE MONTH(fecha_venta) = MONTH(:fecha) AND YEAR(fecha_venta) = YEAR(:fecha)";
        $params_ventas_mes[':fecha'] = $_GET['fecha'];
    } else {
        // Si no hay filtro de fecha, usar el mes actual
        $query_ventas_mes .= " WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())";
    }

    $stmt_ventas_mes = $conn->prepare($query_ventas_mes);
    foreach ($params_ventas_mes as $key => $value) {
        $stmt_ventas_mes->bindValue($key, $value);
    }
    $stmt_ventas_mes->execute();
    $ventas_mes = $stmt_ventas_mes->fetch(PDO::FETCH_ASSOC);

    // Inicializar estadísticas con valores por defecto
    $stats = [
        'total_ventas' => (int)($daily_sales_stats['total_ventas'] ?? 0),
        'total_ingresos' => (float)($daily_sales_stats['total_ingresos'] ?? 0.00),
        'total_productos_vendidos' => (int)($daily_products_sold['total_productos_vendidos'] ?? 0),
        'total_empleados' => (int)($daily_sales_stats['total_empleados'] ?? 0),
        'primera_venta' => $daily_sales_stats['primera_venta'] ?? null,
        'ultima_venta' => $daily_sales_stats['ultima_venta'] ?? null,
        'total_ventas_mes' => (int)($ventas_mes['total_ventas_mes'] ?? 0),
        'total_ingresos_mes' => (float)($ventas_mes['total_ingresos_mes'] ?? 0.00)
    ];

    // Calcular ticket promedio
    $stats['promedio_venta'] = ($stats['total_ventas'] > 0) ? 
        round($stats['total_ingresos'] / $stats['total_ventas'], 2) : 0.00;

    // Obtener productos más vendidos
    $query_populares = "SELECT p.nombre, IFNULL(SUM(dv.cantidad), 0) as total_vendido
                       FROM detalles_venta dv
                       JOIN productos p ON dv.producto_id = p.id
                       JOIN ventas v ON dv.venta_id = v.id";
    $params_populares = [];
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
        $query_populares .= " WHERE DATE(v.fecha_venta) = :fecha";
        $params_populares[':fecha'] = $_GET['fecha'];
    } else {
        $query_populares .= " WHERE DATE(v.fecha_venta) = CURDATE()";
    }
    $query_populares .= " GROUP BY p.id, p.nombre
                       ORDER BY total_vendido DESC
                       LIMIT 5";
    $stmt_populares = $conn->prepare($query_populares);
    foreach ($params_populares as $key => $value) {
        $stmt_populares->bindValue($key, $value);
    }
    $stmt_populares->execute();
    $productos_populares = $stmt_populares->fetchAll(PDO::FETCH_ASSOC);

    // Combinar resultados
    $resultado = [
        'ventas' => $ventas,
        'estadisticas' => $stats,
        'productos_populares' => $productos_populares
    ];

    header('Content-Type: application/json');
    echo json_encode($resultado);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al obtener las ventas: ' . $e->getMessage()]);
}
?> 