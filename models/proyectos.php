<?php
// models/Producto.php
// Incluimos el archivo Model.php porque la clase Producto
// va a heredar sus características y métodos.
require_once __DIR__ .
"/../core/model.php";
// La clase Producto hereda de Model. Al heredar, también puede utilizar la
//conexión $pdo que fue creada en la clase Model.
class Proyecto extends Model { // hereda $pdo de Model
 // Obtiene todos los proyectos de la base de datos.
 public function getAll() {
 // Consulta SQL para seleccionar todos los registros de la tabla
//proyectos.
 $sql = "SELECT * FROM proyectos";
 // Ejecutamos la consulta utilizando la conexión almacenada
 //en $this->pdo.
 $resultado = $this->pdo->query($sql);
 // Convertimos todos los resultados en un arreglo asociativo
 // y lo devolvemos para que pueda ser utilizado por el Controller.
 return $resultado->fetchAll(PDO::FETCH_ASSOC);
 }
 // Busca un producto utilizando su id_obra.
 public function getByid_obra($id_obra) {
 // Consulta SQL para buscar un producto específico.
 // :id_obra es un parámetro que será reemplazado posteriormente.
 $sql = "SELECT * FROM proyectos WHERE id_obra = :id_obra";
 // Preparamos la consulta antes de ejecutarla.
 // Esto permite trabajar con parámetros de forma segura.
 $stmt = $this->pdo->prepare($sql);
 // Enviamos el valor de $id_obra al parámetro :id_obra y ejecutamos la consulta.
 $stmt->execute(["id_obra" => $id_obra]);
 // Obtenemos un solo producto y lo devolvemo como un arreglo
//asociativo.
 return $stmt->fetch(PDO::FETCH_ASSOC);
 }


 // Crea un nuevo producto en la base de datos.
 // Recibe los datos necesarios para crear el producto.
 public function create($titulo,$superficie,$ubicacion, $precio,$descripcion,$nomenclatura,$estado,$memoria,$fecha_inicio,$fecha_finalizacion) {
 // Consulta SQL para insertar un nuevo registro
 // en la tabla proyectos.
 $sql = "INSERT INTO proyectos (titulo,superficie,ubicacion, precio,descripcion,nomenclatura,estado,memoria,fecha_inicio,fecha_finalizacion)
 VALUES (:titulo,:superficie,:ubicacion, :precio,:descripcion,:nomenclatura,:estado,:memoria,:fecha_inicio,:fecha_finalizacion)";
 // Preparamos la consulta antes de ejecutarla.
 $stmt = $this->pdo->prepare($sql);
 // Ejecutamos la consulta enviando los valores que corresponden a cada
//parámetro.
 Return $stmt->execute([
    
 "titulo" => $titulo,
 "superficie"=>$superficie,
 "ubicacion" =>$ubicacion,
 "precio" =>$precio,
 "descripcion" => $descripcion,
 "nomenclatura" =>$nomenclatura ,
"estado" =>$estado,
"memoria" =>$memoria,
"fecha_inicio"=>$fecha_inicio,
"fecha_finalizacion"=>$fecha_finalizacion,
 ]);
 }



 // Modifica los datos de un producto existente.
 // Recibe el id_obra del producto y un arreglo con los nuevos datos.

public function update($id_obra, $datos) {

    $sql = "UPDATE proyectos SET
                titulo = :titulo,
                superficie = :superficie,
                ubicacion = :ubicacion,
                precio = :precio,
                descripcion = :descripcion,
                nomenclatura = :nomenclatura,
                estado = :estado,
                memoria = :memoria,
                fecha_inicio = :fecha_inicio,
                fecha_finalizacion = :fecha_finalizacion
            WHERE id_obra = :id_obra";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        "id_obra" => $id_obra,
        "titulo" => $datos["titulo"],
        "superficie" => $datos["superficie"],
        "ubicacion" => $datos["ubicacion"],
        "precio" => $datos["precio"],
        "descripcion" => $datos["descripcion"],
        "nomenclatura" => $datos["nomenclatura"],
        "estado" => $datos["estado"],
        "memoria" => $datos["memoria"],
        "fecha_inicio" => $datos["fecha_inicio"],
        "fecha_finalizacion" => $datos["fecha_finalizacion"]
    ]);
}



 // Elimina un producto de la base de datos.
 // Recibe como parámetro el id_obra del producto que se quiere eliminar.
 public function delete($id_obra) {
 // Consulta SQL para eliminar el producto indicado.
 $sql = "DELETE FROM proyectos WHERE id_obra = :id_obra";
 // Preparamos la consulta.
 $stmt = $this->pdo->prepare($sql);
 // Enviamos el id_obra del producto y ejecutamos la consulta.
 return $stmt->execute(["id_obra" => $id_obra]);
 }


 
    // Obtener solamente los proyectos publicados en el catálogo.
    public function getCatalogo() {
        $sql = "SELECT * FROM proyectos
                WHERE visible_catalogo = 1";

        $resultado = $this->pdo->query($sql);

        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    // Publicar u ocultar un proyecto del catálogo.
    public function cambiarVisibilidadCatalogo($id_obra,$visible) {
        $sql = "UPDATE proyectos SET visible_catalogo = :visible WHERE id_obra = :id_obra";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "visible" => $visible ? 1 : 0,
            "id_obra" => $id_obra
        ]);
    }

 }
