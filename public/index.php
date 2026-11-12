<?php
// public/index.php
header("Access-Control-Allow-Origin: http://localhost:4200"); // O un '*' para permitir cualquier origen en desarrollo
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Si la petición es de tipo OPTIONS (preflight de CORS), puedes retornar una respuesta vacía con código 200 inmediatamente:
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . "/../utils/response.php";
require_once __DIR__ . "/../routes/routesAgenda.php";
require __DIR__ . "/../routes/ClientesRoutes.php";
require __DIR__ . "/../routes/routes.php";

?>

