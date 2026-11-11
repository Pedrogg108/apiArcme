
<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/../utils/response.php";

// Cargar todas las rutas
require_once __DIR__ . "/../routes/ClientesRoutes.php";
require_once __DIR__ . "/../routes/usuariosRoutes.php";
require_once __DIR__ . "/../routes/routesAgenda.php";
require_once __DIR__ . "/../routes/routes.php";

?>