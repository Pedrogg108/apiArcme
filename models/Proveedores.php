<?php 

require_once __DIR__ . "/../core/model.php";
// La clase Producto hereda de Model. Al heredar, también puede utilizar la
//conexión $pdo que fue creada en la clase Model.

class Proveedor extends Model {  // hereda $pdo de Model

    public function getAll() {
    // Consulta SQL para seleccionar todos los registros de la tabla clientes.
    $sql = "SELECT * FROM proveedores";
     // Ejecutamos la consulta utilizando la conexión almacenada
    //en $this->pdo.
    $resultado = $this->pdo->query($sql);
    // Convertimos todos los resultados en un arreglo asociativo
    // y lo devolvemos para que pueda ser utilizado por el Controller.
     return $resultado->fetchAll(PDO::FETCH_ASSOC);
 }

 public function getByid_cliente($id_proveedores) {
 // Consulta SQL para buscar un producto específico.
 // :id_cliente es un parámetro que será reemplazado posteriormente.
 $sql = "SELECT * FROM proveedores WHERE id_proveedores = :id_proveedores";
 // Preparamos la consulta antes de ejecutarla.
 // Esto permite trabajar con parámetros de forma segura.
 $stmt = $this->pdo->prepare($sql);
 // Enviamos el valor de $id_obra al parámetro :id_cliente y ejecutamos la consulta.
 $stmt->execute(["id_proveedores" => $id_proveedores]);
 // Obtenemos un solo producto y lo devolvemos como un arreglo
//asociativo.
 return $stmt->fetch(PDO::FETCH_ASSOC);
 }

 public function create($proveedor,$nombre_contacto, $telefono,$correo, $ciudad,$direccion, $descripcion,$categoria) {
    // Consulta SQL para insertar un nuevo registro
    // en la tabla clientes.
    $sql = "INSERT INTO proveedores (proveedor,nombre_contacto, telefono,correo, ciudad, direccion,descripcion,categoria)
        VALUES (:proveedor,:nombre_contacto, :telefono,:correo,:ciudad,:direccion, :descripcion, :categoria)";
    // Preparamos la consulta antes de ejecutarla.
    $stmt = $this->pdo->prepare($sql);
    // Ejecutamos la consulta enviando los valores que corresponden a cada
    // parámetro.
    return $stmt->execute([
        "proveedor" => $proveedor,
        "nombre_contacto" => $nombre_contacto,
        "telefono" => $telefono,
        "correo" =>$correo,
        "ciudad" => $ciudad,
         "direccion" => $direccion,
        "descripcion" => $descripcion,
        "categoria" => $categoria
    ]);
 }

    // Modifica los datos de un producto existente.
 // Recibe el id_obra del producto y un arreglo con los nuevos datos.
 public function update($id_proveedores, $datos) {
 // Consulta SQL para actualizar los datos del producto.
 // El WHERE permite indicar qué cliente se debe modificar.
 $sql = "UPDATE proveedores SET proveedor = :proveedor, nombre_contacto=: nombre_contacto, telefono = :telefono,correo = :correo,ciudad = :ciudad, direccion = :direccion, descripcion = :descripcion,categoria=:categoria WHERE id_proveedores = :id_proveedores";
 // Preparamos la consulta.
 $stmt = $this->pdo->prepare($sql);
 // Ejecutamos la consulta enviando los nuevos valores.
 // Cada parámetro recibe el dato correspondiente
 // del arreglo $datos.
 return $stmt->execute([
 "id_proveedores" =>$datos["id_proveedores"],"proveedor"=>$datos["proveedor"],"nombre_contacto" => $datos["nombre_contacto"],"telefono"=> $datos["telefono"] ,"correo" => $datos["correo"],"ciudad"=>$datos["ciudad"],"direccion"=> $datos["direccion"],"descripcion" => $datos["descripcion"], "categoria" => $datos["categoria"]

 ]);
 }

 // Elimina un producto de la base de datos.
 // Recibe como parámetro el id_cliente del cliente que se quiere eliminar.
 public function delete($id_proveedores) {
 // Consulta SQL para eliminar el cliente indicado.
 $sql = "DELETE FROM proveedores WHERE id_proveedores = :id_proveedores";
 // Preparamos la consulta.
 $stmt = $this->pdo->prepare($sql);
 // Enviamos el id_cliente del cliente y ejecutamos la consulta.
 return $stmt->execute(["id_proveedores" => $id_proveedores]);
 }


 }




?>