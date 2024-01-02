<?php
require_once('Environment_Manager.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/libraries/oauth2.0/google-apiclient/vendor/autoload.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/libraries/oauth2.0/facebook/vendor/autoload.php');

use Google\Client as GoogleClient;
use Facebook\Facebook as FacebookClient;

class SocialLoginManager
{
    private $googleClientId;
    private $googleClientSecret;
    private $facebokAppId;
    private $facebookAppSecret;
    private $redirectUri;

    public function __construct()
    {
        // Asigna tus credenciales reales aquí
        $envManager = new EnvironmentManager();
        $this->googleClientId = $envManager->get('googleClientId');
        $this->googleClientSecret = $envManager->get('googleClientSecret');

        $this->facebookAppId = $envManager->get('facebookAppId');
        $this->facebookAppSecret = $envManager->get('facebookAppSecret');

        $this->redirectUri = "https://" . $_SERVER["HTTP_HOST"] . "/applications/Login_Validate.php";
    }

    public function getGoogleLoginUrl()
    {
        try {
            $client = new GoogleClient();
            $client->setClientId($this->googleClientId);
            $client->setClientSecret($this->googleClientSecret);
            $client->setRedirectUri($this->redirectUri);
            $client->addScope('email');
            $client->addScope('profile');
            // $client->addScope('https://www.googleapis.com/auth/user.gender.read');
            // $client->addScope('https://www.googleapis.com/auth/user.birthday.read');

            return $client->createAuthUrl();
        } catch (\Exception $e) {
            // Manejo de errores
            $errorMessage = "Error en getGoogleLoginUrl: " . $e->getMessage();
            error_log($errorMessage, 0);
            return null; // o manejar de otra manera, por ejemplo, lanzar una excepción personalizada
        }
    }
    public function loginWithGoogle($code)
    {
        try {
            $client = new GoogleClient();
            $client->setClientId($this->googleClientId);
            $client->setClientSecret($this->googleClientSecret);
            $client->setRedirectUri($this->redirectUri);

            $client->authenticate($code);
            $accessToken = $client->getAccessToken();

            // Obtener información del usuario
            $oauth2 = new Google\Service\Oauth2($client);
            $userInfo = $oauth2->userinfo->get();

            // Implementar lógica de inicio de sesión con la información del usuario
            // ...

            return $userInfo;
        } catch (Exception $e) {
            error_log('Google login error: ' . $e->getMessage());
            return null;
        }
    }

    public function getFacebookLoginUrl()
    {
        try {
            $fb = new FacebookClient([
                'app_id' =>  $this->facebookAppId,
                'app_secret' => $this->facebookAppSecret,
                'default_graph_version' => 'v2.10',
            ]);

            $permissions = ['email', 'user_birthday', 'user_gender']; // Agrega los permisos necesarios, incluyendo user_gender
            $loginUrl = $fb->getRedirectLoginHelper()->getLoginUrl($this->redirectUri, $permissions);

            return $loginUrl;
        } catch (\Exception $e) {
            // Manejo de errores
            $errorMessage = "Error en getFacebookLoginUrl: " . $e->getMessage();
            error_log($errorMessage, 0);
            return null; // o manejar de otra manera, por ejemplo, lanzar una excepción personalizada
        }
    }

    /**
     * Convierte el objeto $userInfo a formato JSON.
     *
     * @param mixed $userInfo
     * @return string|false Devuelve la representación JSON del objeto o false en caso de error.
     */
    public function convertUserInfoToJson($userInfo){
        try {
            // Convierte el objeto a un array para asegurar que sea JSON válido
            $userInfoArray = json_decode(json_encode($userInfo), true);

            // Convierte el array a formato JSON
            $jsonUserInfo = json_encode($userInfoArray, JSON_PRETTY_PRINT);
            if($jsonUserInfo !== false) {
                return $jsonUserInfo;
            }
            else{
                error_log("Error al convertir a JSON");
                return null;
            }
        } catch (\Exception $e) {
            error_log("Error al convertir a JSON: " . $e->getMessage());
            return null; 
        }
    }
}
?>
