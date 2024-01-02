<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/libraries/phpdotenv/vendor/autoload.php';

class EnvironmentManager
{
    private $envPath;
    private $variables;

    public function __construct()
    {
        $this->envPath = 'YOUR_ENV_PATH';
        $this->loadEnvironmentVariables();
    }

    private function loadEnvironmentVariables()
    {
        $dotenv = Dotenv\Dotenv::createImmutable($this->envPath);
        $dotenv->load();
        $this->variables = $_ENV;
    }

    public function get($key, $default = null)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : $default;
    }
}
?>