<?php
require_once('Environment_Manager.php');
class Database
{
    protected $db;

    public function __construct()
    {
        try {
            $envManager = new EnvironmentManager();
            $dbHost = $envManager->get('DB_HOST');
            $dbName = $envManager->get('DB_NAME');
            $dbUserName = $envManager->get('DB_USERNAME');
            $dbPassword = $envManager->get('DB_PASSWORD');

            $this->db = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUserName, $dbPassword);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Error en la conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>
