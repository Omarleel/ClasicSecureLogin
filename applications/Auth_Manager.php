<?php
require_once('PDO_Connection.php');
class AuthManager extends Database
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    public function register($username, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = $this->db->prepare("INSERT INTO Usuarios (Usuario, Contraseña) VALUES (:Usuario, :Contrasena)");
        $query->bindParam(':Usuario', $username);
        $query->bindParam(':Contrasena', $hashedPassword);

        return $query->execute();
    }

    public function login($username, $password)
    {
        $query = $this->db->prepare("SELECT * FROM Usuarios WHERE Usuario = :Usuario");
        $query->bindParam(':Usuario', $username);
        $query->execute();

        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false; // El usuario no existe
        }

        if (password_verify($password, $user['Contraseña'])) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['user_name'] = $username;
            return true; // Inicio de sesión exitoso
        } else {
            return false; // Contraseña incorrecta
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        return true;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            $query = $this->db->prepare("SELECT * FROM users WHERE id = :user_id");
            $query->bindParam(':user_id', $_SESSION['user_id']);
            $query->execute();

            return $query->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }
}
?>