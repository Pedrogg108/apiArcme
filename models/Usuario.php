<?php
require_once __DIR__ .
"/../core/model.php";


public function registro($dni,$contraseña,$nombre,$telefono,$correo) {
$sql = "INSERT INTO usuario (dni,contraseña,nombre,telefono,correo) VALUES (:dni, :contraseña, :nombre, :telefono, :correo)";

// Preparamos la consulta antes de ejecutarla.
 $stmt = $this->pdo->prepare($sql);

 // Ejecutamos la consulta enviando los valores que corresponden a cada
//parámetro.

Return $stmt->execute([
    
 "dni" => $dni,
 "contraseña"=>$contraseña,
 "nombre" =>$nombre,
 "telefono" =>$telefono,
 "correo" => $correo
 ]);
 }


?>