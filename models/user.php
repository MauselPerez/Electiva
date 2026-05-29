<?php
require_once '../config/db.php';

class User {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    private function getPrimaryRoleSubquery() {
        return "
            SELECT
                wur.user_id,
                MIN(wur.role_id) AS role_id
            FROM ws_user_roles wur
            WHERE wur.is_active = 1
            GROUP BY wur.user_id
        ";
    }

    // Buscar usuario por username
    public function findUserByUsername($username) {
        $query = "
            SELECT
                u.id,
                u.person_id,
                u.username,
                u.password,
                u.is_active,
                p.document_number,
                p.first_name,
                p.last_name,
                p.email,
                r.id AS role_id,
                r.name AS rol,
                GROUP_CONCAT(DISTINCT r_all.name ORDER BY r_all.name SEPARATOR ', ') AS role_names
            FROM users u
            INNER JOIN ws_persons p
                ON p.id = u.person_id
            LEFT JOIN (" . $this->getPrimaryRoleSubquery() . ") ur
                ON ur.user_id = u.id
            LEFT JOIN ws_roles r
                ON r.id = ur.role_id
            LEFT JOIN ws_user_roles wur_all
                ON wur_all.user_id = u.id
               AND wur_all.is_active = 1
            LEFT JOIN ws_roles r_all
                ON r_all.id = wur_all.role_id
            WHERE u.username = :username
            GROUP BY
                u.id,
                u.person_id,
                u.username,
                u.password,
                u.is_active,
                p.document_number,
                p.first_name,
                p.last_name,
                p.email,
                r.id,
                r.name
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar usuario por id
    public function findUserById($id) {
        $query = "
            SELECT
                u.id,
                u.person_id,
                u.username,
                u.password,
                u.is_active,
                p.document_number,
                p.first_name,
                p.last_name,
                p.email,
                r.id AS role_id,
                r.name AS rol
            FROM users u
            INNER JOIN ws_persons p
                ON p.id = u.person_id
            LEFT JOIN (" . $this->getPrimaryRoleSubquery() . ") ur
                ON ur.user_id = u.id
            LEFT JOIN ws_roles r
                ON r.id = ur.role_id
            WHERE u.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los usuarios
    public function getAllUsers() {
        $query = "
            SELECT
                u.id,
                u.person_id,
                u.username,
                u.is_active,
                p.document_number,
                p.first_name,
                p.last_name,
                p.email,
                r.id AS role_id,
                COALESCE(r.name, 'SIN ROL') AS rol
            FROM users u
            INNER JOIN ws_persons p
                ON p.id = u.person_id
            LEFT JOIN (" . $this->getPrimaryRoleSubquery() . ") ur
                ON ur.user_id = u.id
            LEFT JOIN ws_roles r
                ON r.id = ur.role_id
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

    private function assignPrimaryRole($userId, $roleId) {
        $query = "
            INSERT INTO ws_user_roles (
                user_id,
                role_id,
                is_active,
                assigned_at,
                assigned_by
            ) VALUES (
                :user_id,
                :role_id,
                1,
                NOW(),
                NULL
            )
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Crear nuevo usuario
    public function createUser($data) {
        try {
            $this->db->beginTransaction();

            $checkUsername = $this->db->prepare("SELECT id FROM users WHERE username = :username");
            $checkUsername->bindParam(':username', $data['username']);
            $checkUsername->execute();

            if ($checkUsername->fetch()) {
                throw new Exception('Ya existe un usuario con ese nombre de usuario.');
            }

            $checkDocument = $this->db->prepare("SELECT id FROM ws_persons WHERE document_number = :document_number");
            $checkDocument->bindParam(':document_number', $data['document_number']);
            $checkDocument->execute();

            if ($checkDocument->fetch()) {
                throw new Exception('Ya existe una persona con esa cedula.');
            }

            $checkEmail = $this->db->prepare("SELECT id FROM ws_persons WHERE email = :email");
            $checkEmail->bindParam(':email', $data['email']);
            $checkEmail->execute();

            if ($checkEmail->fetch()) {
                throw new Exception('Ya existe una persona con ese correo.');
            }

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

            $personId = (int) $this->db->lastInsertId();
            $passwordHash = $this->hashPassword($data['password']);

            $userQuery = "
                INSERT INTO users (
                    person_id,
                    username,
                    password,
                    is_active
                ) VALUES (
                    :person_id,
                    :username,
                    :password,
                    1
                )
            ";

            $userStmt = $this->db->prepare($userQuery);
            $userStmt->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $userStmt->bindParam(':username', $data['username']);
            $userStmt->bindParam(':password', $passwordHash);
            $userStmt->execute();

            $userId = (int) $this->db->lastInsertId();

            if (!$this->assignPrimaryRole($userId, (int) $data['role_id'])) {
                throw new Exception('No fue posible asignar el rol al usuario.');
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Actualizar usuario
    public function updateUser($id, $data) {
        try {
            $this->db->beginTransaction();

            $userQuery = $this->db->prepare("SELECT person_id FROM users WHERE id = :id");
            $userQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $userQuery->execute();
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('El usuario no existe.');
            }

            $personId = (int) $user['person_id'];

            $checkUsername = $this->db->prepare("SELECT id FROM users WHERE username = :username AND id <> :id");
            $checkUsername->bindParam(':username', $data['username']);
            $checkUsername->bindParam(':id', $id, PDO::PARAM_INT);
            $checkUsername->execute();

            if ($checkUsername->fetch()) {
                throw new Exception('Ya existe otro usuario con ese nombre de usuario.');
            }

            $checkDocument = $this->db->prepare("SELECT id FROM ws_persons WHERE document_number = :document_number AND id <> :person_id");
            $checkDocument->bindParam(':document_number', $data['document_number']);
            $checkDocument->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $checkDocument->execute();

            if ($checkDocument->fetch()) {
                throw new Exception('Ya existe otra persona con esa cedula.');
            }

            $checkEmail = $this->db->prepare("SELECT id FROM ws_persons WHERE email = :email AND id <> :person_id");
            $checkEmail->bindParam(':email', $data['email']);
            $checkEmail->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $checkEmail->execute();

            if ($checkEmail->fetch()) {
                throw new Exception('Ya existe otra persona con ese correo.');
            }

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

            $isActive = isset($data['is_active']) ? (int) $data['is_active'] : 1;
            $userUpdate = "
                UPDATE users
                SET
                    username = :username,
                    is_active = :is_active
                WHERE id = :id
            ";

            $userStmt = $this->db->prepare($userUpdate);
            $userStmt->bindParam(':username', $data['username']);
            $userStmt->bindParam(':is_active', $isActive, PDO::PARAM_INT);
            $userStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $userStmt->execute();

            $deleteRoles = $this->db->prepare("DELETE FROM ws_user_roles WHERE user_id = :user_id");
            $deleteRoles->bindParam(':user_id', $id, PDO::PARAM_INT);
            $deleteRoles->execute();

            if (!$this->assignPrimaryRole((int) $id, (int) $data['role_id'])) {
                throw new Exception('No fue posible reasignar el rol del usuario.');
            }

            if (!empty($data['password'])) {
                $passwordHash = $this->hashPassword($data['password']);
                $passwordUpdate = "UPDATE users SET password = :password WHERE id = :id";
                $passwordStmt = $this->db->prepare($passwordUpdate);
                $passwordStmt->bindParam(':password', $passwordHash);
                $passwordStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $passwordStmt->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Eliminar usuario
    public function deleteUser($id) {
        try {
            $this->db->beginTransaction();

            $userQuery = $this->db->prepare("SELECT person_id FROM users WHERE id = :id");
            $userQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $userQuery->execute();
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('El usuario no existe.');
            }

            $personId = (int) $user['person_id'];

            $deleteRoles = $this->db->prepare("DELETE FROM ws_user_roles WHERE user_id = :id");
            $deleteRoles->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteRoles->execute();

            $deleteUser = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $deleteUser->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteUser->execute();

            $hasProfiles = $this->db->prepare("SELECT COUNT(*) AS total FROM ws_person_profiles WHERE person_id = :person_id");
            $hasProfiles->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $hasProfiles->execute();
            $profileCount = (int) ($hasProfiles->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            if ($profileCount === 0) {
                $deletePerson = $this->db->prepare("DELETE FROM ws_persons WHERE id = :person_id");
                $deletePerson->bindParam(':person_id', $personId, PDO::PARAM_INT);
                $deletePerson->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function verifyPassword($storedPassword, $plainPassword) {
        if (password_verify($plainPassword, $storedPassword)) {
            return true;
        }

        return hash_equals(sha1($plainPassword), (string) $storedPassword);
    }

    public function needsPasswordRehash($storedPassword) {
        return password_get_info($storedPassword)['algo'] === 0;
    }

    public function rehashPasswordForUser($userId, $plainPassword) {
        $newHash = $this->hashPassword($plainPassword);
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':password', $newHash);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>