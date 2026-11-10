<?php 

require_once __DIR__ . "/../core/model.php";
// La clase Producto hereda de Model. Al heredar, también puede utilizar la
//conexión $pdo que fue creada en la clase Model.

class Cliente extends Model {  // hereda $pdo de Model

    public function getAll() {
    // Consulta SQL para seleccionar todos los registros de la tabla clientes.
    $sql = "SELECT * FROM clientes";
     // Ejecutamos la consulta utilizando la conexión almacenada
    //en $this->pdo.
    $resultado = $this->pdo->query($sql);
    // Convertimos todos los resultados en un arreglo asociativo
    // y lo devolvemos para que pueda ser utilizado por el Controller.
     return $resultado->fetchAll(PDO::FETCH_ASSOC);
 }

 public function getByid_cliente($id_cliente) {
 // Consulta SQL para buscar un producto específico.
 // :id_cliente es un parámetro que será reemplazado posteriormente.
 $sql = "SELECT * FROM clientes WHERE id_cliente = :id_cliente";
 // Preparamos la consulta antes de ejecutarla.
 // Esto permite trabajar con parámetros de forma segura.
 $stmt = $this->pdo->prepare($sql);
 // Enviamos el valor de $id_obra al parámetro :id_cliente y ejecutamos la consulta.
 $stmt->execute(["id_cliente" => $id_cliente]);
 // Obtenemos un solo producto y lo devolvemos como un arreglo
//asociativo.
 return $stmt->fetch(PDO::FETCH_ASSOC);
 }

 public function create($nombre, $telefono, $direccion, $ciudad, $correo) {
    // Consulta SQL para insertar un nuevo registro
    // en la tabla clientes.
    $sql = "INSERT INTO clientes (nombre, telefono, direccion, ciudad, correo)
        VALUES (:nombre, :telefono, :direccion, :ciudad, :correo)";
    // Preparamos la consulta antes de ejecutarla.
    $stmt = $this->pdo->prepare($sql);
    // Ejecutamos la consulta enviando los valores que corresponden a cada
    // parámetro.
    return $stmt->execute([
        "nombre" => $nombre,
        "telefono" => $telefono,
        "direccion" => $direccion,
        "ciudad" => $ciudad,
        "correo" => $correo
    ]);
 }

    // Modifica los datos de un producto existente.
 // Recibe el id_obra del producto y un arreglo con los nuevos datos.

public function update($id_cliente, $datos) {

    $sql = "UPDATE clientes SET
        nombre = :nombre,
        telefono = :telefono,
        direccion = :direccion,
        ciudad = :ciudad,
        correo = :correo
        WHERE id_cliente = :id_cliente";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        "id_cliente" => $id_cliente,
        "nombre" => $datos["nombre"],
        "telefono" => $datos["telefono"],
        "direccion" => $datos["direccion"],
        "ciudad" => $datos["ciudad"],
        "correo" => $datos["correo"]
    ]);
}


 // Elimina un producto de la base de datos.
 // Recibe como parámetro el id_cliente del cliente que se quiere eliminar.
 public function delete($id_cliente) {
 // Consulta SQL para eliminar el cliente indicado.
 $sql = "DELETE FROM clientes WHERE id_cliente = :id_cliente";
 // Preparamos la consulta.
 $stmt = $this->pdo->prepare($sql);
 // Enviamos el id_cliente del cliente y ejecutamos la consulta.
 return $stmt->execute(["id_cliente" => $id_cliente]);
 }


 }




?>