<?php
require_once('Environment_Manager.php');

class RecaptchaValidator
{
    private $secretKey;
    private $minimumScore;

    public function __construct()
    {
        $envManager = new EnvironmentManager();
        $this->secretKey = $envManager->get('GCAPTCHA_SECRET_KEY');
        $this->minimumScore = 0.5;
    }

    public function validateToken($token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $this->secretKey, 'response' => $token]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            // Manejar el error en la solicitud cURL
            $error = curl_error($ch);
            curl_close($ch);
            error_log('Error al realizar la solicitud cURL: ' . $error);
            return false;
        }
        curl_close($ch);

        $responseData = json_decode($response);
        if (!$responseData) {
            // Manejar el error en la decodificación JSON
            error_log('Error al decodificar la respuesta JSON');
            return false;
        }

        if (isset($responseData->success) && isset($responseData->score) &&
            $responseData->success && $responseData->score >= $this->minimumScore) {
            // Considerar como acción realizada por humano
            return true;
        }

        // Considerar como acción sospechosa
        return false;
    }
}
?>