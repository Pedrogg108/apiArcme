<?php

require_once __DIR__ . "/../core/model.php";

class Proyecto extends Model {

    // Obtener todos los proyectos junto con sus fotografías.
    public function getAll() {

        $sql = "SELECT * FROM proyectos ORDER BY id_obra DESC";

        $resultado = $this->pdo->query($sql);

        $proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proyectos as &$proyecto) {
            $proyecto["fotos"] = $this->getFotos($proyecto["id_obra"]);
        }

        unset($proyecto);

        return $proyectos;
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

    // Obtener los proyectos publicados en el catálogo.
    public function getCatalogo() {

        $sql = "SELECT * FROM proyectos
                WHERE visible_catalogo = 1";

        $resultado = $this->pdo->query($sql);

        $proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proyectos as &$proyecto) {
            $proyecto["fotos"] = $this->getFotos($proyecto["id_obra"]);
        }

        unset($proyecto);

        return $proyectos;
    }

    // Alternar la visibilidad de un proyecto.
    public function cambiarVisibilidadCatalogo($id_obra) {

        $sql = "UPDATE proyectos
                SET visible_catalogo =
                    IF(visible_catalogo = 1, 0, 1)
                WHERE id_obra = :id_obra";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "id_obra" => $id_obra
        ]);
    }
}