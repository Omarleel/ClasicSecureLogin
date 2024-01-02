<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/applications/Social_Login_Manager.php');

class Login_Validate
{
    public function getUserInfo()
    {
        if (isset($_GET['code'])) {
            $socialLoginManager = new SocialLoginManager();

            // Obtener la URL de referencia
            $refererUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

            // Handle Google login
            if (strpos($refererUrl, "google.com") !== false) {
                $userInfo = $socialLoginManager->loginWithGoogle($_GET['code']);
            }
            // Handle Facebook login
            elseif (strpos($refererUrl, "facebook.com") !== false) {
                $userInfo = $socialLoginManager->loginWithFacebook($_GET['code']);
            }
            // Handle Apple login
            elseif (strpos($refererUrl, "apple.com") !== false) {
                $authorizationCode = $_GET['code'];
                $identityToken = $_GET['id_token'];
                $userInfo = $socialLoginManager->loginWithApple($authorizationCode, $identityToken);
            }
            $userInfoJson = $socialLoginManager->convertUserInfoToJson($userInfo);
            return $userInfoJson;
        }
        return false;
    }
}
// Ejemplo de uso
$loginValidator = new Login_Validate();
echo $loginValidator->getUserInfo();
?>