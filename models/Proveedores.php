
<?php

require_once __DIR__ . "/../core/model.php";

class Proveedor extends Model {

    public function getAll() {
        $sql = "SELECT * FROM proveedores";
        $resultado = $this->pdo->query($sql);
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByid_proveedor($id_proveedores) {

        $sql = "SELECT * FROM proveedores WHERE id_proveedores = :id_proveedores";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["id_proveedores" => $id_proveedores]);

        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($proveedor) {

            $sqlProductos = "SELECT producto FROM proveedor_productos WHERE id_proveedores = :id_proveedores";
            $stmtProductos = $this->pdo->prepare($sqlProductos);
            $stmtProductos->execute(["id_proveedores" => $id_proveedores]);

            $proveedor["productos"] = $stmtProductos->fetchAll(PDO::FETCH_COLUMN);
        }

        return $proveedor;
    }

    public function create( $proveedor, $nombre_contacto, $telefono, $correo, $ciudad, $direccion, $descripcion, $categoria, $productos = []) {

        try {

            // Iniciamos una transacción.
            $this->pdo->beginTransaction();

            // Guardamos el proveedor.
            $sql = "INSERT INTO proveedores ( proveedor, nombre_contacto, telefono, correo, ciudad, direccion, descripcion, categoria)
                    VALUES ( :proveedor, :nombre_contacto, :telefono, :correo, :ciudad, :direccion, :descripcion, :categoria)";


//id_obra iria en insert into y values pero no anda.
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                // "id_obra" => $id_obra,
                "proveedor" => $proveedor,
                "nombre_contacto" => $nombre_contacto,
                "telefono" => $telefono,
                "correo" => $correo,
                "ciudad" => $ciudad,
                "direccion" => $direccion,
                "descripcion" => $descripcion,
                "categoria" => $categoria
            ]);

            // Obtenemos el ID del proveedor creado.
            $idProveedor = $this->pdo->lastInsertId();

            // Guardamos los productos relacionados con el proveedor.
            $sqlProducto = "INSERT INTO proveedor_productos (id_proveedores, producto)
                            VALUES (:id_proveedores, :producto)";

            $stmtProducto = $this->pdo->prepare($sqlProducto);

            foreach ($productos as $producto) {

                if (trim($producto) !== '') {
                    $stmtProducto->execute([
                        "id_proveedores" => $idProveedor,
                        "producto" => trim($producto)
                    ]);
                }

            }

            // Confirmamos los cambios.
            $this->pdo->commit();

            return $idProveedor;

        } catch (Exception $e) {

            // Si ocurre un error, deshacemos los cambios.
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function update($id_proveedores, $datos) {

        try {

            // Iniciamos una transacción.
            $this->pdo->beginTransaction();

            // Actualizamos los datos del proveedor.
            $sql = "UPDATE proveedores SET
                    -- id_obra = :id_obra,
                    proveedor = :proveedor,
                    nombre_contacto = :nombre_contacto,
                    telefono = :telefono,
                    correo = :correo,
                    ciudad = :ciudad,
                    direccion = :direccion,
                    descripcion = :descripcion,
                    categoria = :categoria
                    WHERE id_proveedores = :id_proveedores";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                "id_proveedores" => $id_proveedores,
                // "id_obra" => $datos["id_obra"],
                "proveedor" => $datos["proveedor"],
                "nombre_contacto" => $datos["nombre_contacto"],
                "telefono" => $datos["telefono"],
                "correo" => $datos["correo"],
                "ciudad" => $datos["ciudad"],
                "direccion" => $datos["direccion"],
                "descripcion" => $datos["descripcion"],
                "categoria" => $datos["categoria"]
            ]);

            // Eliminamos los productos anteriores.
            $sqlEliminar = "DELETE FROM proveedor_productos WHERE id_proveedores = :id_proveedores";
            $stmtEliminar = $this->pdo->prepare($sqlEliminar);
            $stmtEliminar->execute(["id_proveedores" => $id_proveedores]);

            // Guardamos los productos nuevos.
            $sqlProducto = "INSERT INTO proveedor_productos (id_proveedores, producto)
                            VALUES (:id_proveedores, :producto)";

            $stmtProducto = $this->pdo->prepare($sqlProducto);

            foreach ($datos["productos"] ?? [] as $producto) {

                if (trim($producto) !== '') {
                    $stmtProducto->execute([
                        "id_proveedores" => $id_proveedores,
                        "producto" => trim($producto)
                    ]);
                }

            }

            // Confirmamos los cambios.
            $this->pdo->commit();

            return true;

        } catch (Exception $e) {

            // Si ocurre un error, deshacemos los cambios.
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function delete($id_proveedores) {
        $sql = "DELETE FROM proveedores WHERE id_proveedores = :id_proveedores";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(["id_proveedores" => $id_proveedores]);
    }

}

?>
