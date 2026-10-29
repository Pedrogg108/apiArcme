<?php
// models/Evento.php
// Este archivo habla con la base de datos. Solo tiene consultas SQL.
require_once __DIR__ . "/../core/model.php";

// Evento hereda de Model, así que puede usar la conexión $this->pdo
class Evento extends Model {

    // Trae todos los evento
    public function getAll() {
        $sql = "SELECT * FROM evento";
        $resultado = $this->pdo->query($sql);
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae un solo evento usando su id
    public function getById($id_evento) {
        $sql = "SELECT * FROM evento WHERE id_evento = :id_evento";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["id_evento" => $id_evento]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crea un evento nuevo
    public function create($titulo, $fecha, $hora, $categoria, $descripcion) {
        $sql = "INSERT INTO evento (titulo, fecha, hora, categoria, descripcion)
                VALUES (:titulo, :fecha, :hora, :categoria, :descripcion)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            "titulo"      => $titulo,
            "fecha"       => $fecha,
            "hora"        => $hora,
            "categoria"   => $categoria,
            "descripcion" => $descripcion
        ]);
    }

    // Modifica un evento que ya existe
    public function update($id_evento, $datos) {
        $sql = "UPDATE evento SET
                    titulo = :titulo,
                    fecha = :fecha,
                    hora = :hora,
                    categoria = :categoria,
                    descripcion = :descripcion
                WHERE id_evento = :id_evento";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            "id_evento"   => $id_evento,
            "titulo"      => $datos["titulo"],
            "fecha"       => $datos["fecha"],
            "hora"        => $datos["hora"],
            "categoria"   => $datos["categoria"],
            "descripcion" => $datos["descripcion"]
        ]);
    }

    // Borra un evento
    public function delete($id_evento) {
        $sql = "DELETE FROM evento WHERE id_evento = :id_evento";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(["id_evento" => $id_evento]);
    }
}