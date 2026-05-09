<?php
require_once '../config/db.php';

class User {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    // Buscar usuario por username
    public function findUserByUsername($username) {
        $query = "
            SELECT 
                u.*,
                p.document_number,
                p.first_name,
                p.last_name,
                p.email,
                r.name AS rol
            FROM users u
            INNER JOIN ws_roles r
                ON r.id = u.role_id
            INNER JOIN ws_persons p
                ON p.id = u.person_id
            WHERE u.username = :username
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar usuario por id
    public function findUserById($id) {
        $query = "
            SELECT 
                u.*,
                p.document_number,
                p.first_name,
                p.last_name,
                p.email,
                r.name AS rol
            FROM users u
            INNER JOIN ws_roles r
                ON r.id = u.role_id
            INNER JOIN ws_persons p
                ON p.id = u.person_id
            WHERE u.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los usuarios
    public function getAllUsers() {
        $query = "
            SELECT 
                u.*,
                p.document_number,
                p.first_name,
                p.last_name,
                p.email,
                r.name AS rol
            FROM users u
            INNER JOIN ws_roles r
                ON r.id = u.role_id
            INNER JOIN ws_persons p
                ON p.id = u.person_id
            ORDER BY u.id DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRoles() {
        $query = "SELECT * FROM ws_roles WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear nuevo usuario
    public function createUser($data) {
        try {
            $this->db->beginTransaction();

            // Validar username duplicado
            $checkUsername = $this->db->prepare("
                SELECT id FROM users WHERE username = :username
            ");
            $checkUsername->bindParam(':username', $data['username']);
            $checkUsername->execute();

            if ($checkUsername->fetch()) {
                throw new Exception("Ya existe un usuario con ese nombre de usuario.");
            }

            // Validar cédula duplicada
            $checkDocument = $this->db->prepare("
                SELECT id FROM ws_persons WHERE document_number = :document_number
            ");
            $checkDocument->bindParam(':document_number', $data['document_number']);
            $checkDocument->execute();

            if ($checkDocument->fetch()) {
                throw new Exception("Ya existe una persona con esa cédula.");
            }

            // Validar email duplicado
            $checkEmail = $this->db->prepare("
                SELECT id FROM ws_persons WHERE email = :email
            ");
            $checkEmail->bindParam(':email', $data['email']);
            $checkEmail->execute();

            if ($checkEmail->fetch()) {
                throw new Exception("Ya existe una persona con ese correo.");
            }

            // Insertar persona
            $personQuery = "
                INSERT INTO ws_persons (
                    document_number,
                    first_name,
                    last_name,
                    email,
                    is_active
                ) VALUES (
                    :document_number,
                    :first_name,
                    :last_name,
                    :email,
                    1
                )
            ";

            $personStmt = $this->db->prepare($personQuery);
            $personStmt->bindParam(':document_number', $data['document_number']);
            $personStmt->bindParam(':first_name', $data['first_name']);
            $personStmt->bindParam(':last_name', $data['last_name']);
            $personStmt->bindParam(':email', $data['email']);
            $personStmt->execute();

            $personId = $this->db->lastInsertId();

            // Insertar usuario
            $passwordHash = sha1($data['password']);

            $userQuery = "
                INSERT INTO users (
                    person_id,
                    username,
                    password,
                    role_id,
                    is_active
                ) VALUES (
                    :person_id,
                    :username,
                    :password,
                    :role_id,
                    1
                )
            ";

            $userStmt = $this->db->prepare($userQuery);
            $userStmt->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $userStmt->bindParam(':username', $data['username']);
            $userStmt->bindParam(':password', $passwordHash);
            $userStmt->bindParam(':role_id', $data['role_id'], PDO::PARAM_INT);
            $userStmt->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Actualizar usuario
    public function updateUser($id, $data) {
        try {
            $this->db->beginTransaction();

            // Obtener person_id
            $userQuery = $this->db->prepare("
                SELECT person_id
                FROM users
                WHERE id = :id
            ");
            $userQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $userQuery->execute();
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("El usuario no existe.");
            }

            $personId = $user['person_id'];

            // Validar username duplicado en otro usuario
            $checkUsername = $this->db->prepare("
                SELECT id
                FROM users
                WHERE username = :username
                  AND id <> :id
            ");
            $checkUsername->bindParam(':username', $data['username']);
            $checkUsername->bindParam(':id', $id, PDO::PARAM_INT);
            $checkUsername->execute();

            if ($checkUsername->fetch()) {
                throw new Exception("Ya existe otro usuario con ese nombre de usuario.");
            }

            // Validar cédula duplicada en otra persona
            $checkDocument = $this->db->prepare("
                SELECT id
                FROM ws_persons
                WHERE document_number = :document_number
                  AND id <> :person_id
            ");
            $checkDocument->bindParam(':document_number', $data['document_number']);
            $checkDocument->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $checkDocument->execute();

            if ($checkDocument->fetch()) {
                throw new Exception("Ya existe otra persona con esa cédula.");
            }

            // Validar email duplicado en otra persona
            $checkEmail = $this->db->prepare("
                SELECT id
                FROM ws_persons
                WHERE email = :email
                  AND id <> :person_id
            ");
            $checkEmail->bindParam(':email', $data['email']);
            $checkEmail->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $checkEmail->execute();

            if ($checkEmail->fetch()) {
                throw new Exception("Ya existe otra persona con ese correo.");
            }

            // Actualizar persona
            $personUpdate = "
                UPDATE ws_persons
                SET
                    document_number = :document_number,
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email
                WHERE id = :person_id
            ";

            $personStmt = $this->db->prepare($personUpdate);
            $personStmt->bindParam(':document_number', $data['document_number']);
            $personStmt->bindParam(':first_name', $data['first_name']);
            $personStmt->bindParam(':last_name', $data['last_name']);
            $personStmt->bindParam(':email', $data['email']);
            $personStmt->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $personStmt->execute();

            // Actualizar usuario
            $userUpdate = "
                UPDATE users
                SET
                    username = :username,
                    role_id = :role_id,
                    is_active = :is_active
                WHERE id = :id
            ";

            $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

            $userStmt = $this->db->prepare($userUpdate);
            $userStmt->bindParam(':username', $data['username']);
            $userStmt->bindParam(':role_id', $data['role_id'], PDO::PARAM_INT);
            $userStmt->bindParam(':is_active', $isActive, PDO::PARAM_INT);
            $userStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $userStmt->execute();

            // Actualizar contraseña solo si viene informada
            if (!empty($data['password'])) {
                $passwordHash = sha1($data['password']);

                $passwordUpdate = "
                    UPDATE users
                    SET password = :password
                    WHERE id = :id
                ";
                $passwordStmt = $this->db->prepare($passwordUpdate);
                $passwordStmt->bindParam(':password', $passwordHash);
                $passwordStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $passwordStmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Eliminar usuario
    public function deleteUser($id) {
        try {
            $this->db->beginTransaction();

            $userQuery = $this->db->prepare("
                SELECT person_id
                FROM users
                WHERE id = :id
            ");
            $userQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $userQuery->execute();
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("El usuario no existe.");
            }

            $personId = $user['person_id'];

            $deleteUser = $this->db->prepare("
                DELETE FROM users
                WHERE id = :id
            ");
            $deleteUser->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteUser->execute();

            $deletePerson = $this->db->prepare("
                DELETE FROM ws_persons
                WHERE id = :person_id
            ");
            $deletePerson->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $deletePerson->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>