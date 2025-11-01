<?php

/**
 * FlowHelper
 * Helper para integración con Flow API
 * Documentación: https://developers.flow.cl/
 */

namespace App\Helpers;

class FlowHelper
{
    private $apiKey;
    private $secretKey;
    private $apiUrl;
    private $sandbox;

    private $modoLocal;

    public function __construct()
    {
        $this->apiKey = getenv('FLOW_API_KEY') ?: '4BDAF26D-2D4A-45A5-A5B5-79D5A0DL0A05';
        $this->secretKey = getenv('FLOW_SECRET_KEY') ?: '0d697a08e5fa0cba649451c5b8cbca7c5bd3a736';
        $this->sandbox = getenv('FLOW_SANDBOX') === 'true';
        $this->modoLocal = getenv('FLOW_LOCAL_MODE') === 'true'; // Modo de prueba local
        $this->apiUrl = $this->sandbox 
            ? 'https://sandbox.flow.cl/api' 
            : 'https://www.flow.cl/api';
    }

    /**
     * Crear una orden de pago en Flow
     * 
     * @param array $params Parámetros de la orden
     * @return array|false Respuesta de Flow o false en caso de error
     */
    public function crearOrden($params)
    {
        // Si está en modo local, simular respuesta de Flow
        if ($this->modoLocal) {
            return $this->simularCrearOrden($params);
        }

        $endpoint = '/payment/create';
        
        // Parámetros requeridos por Flow
        $data = [
            'apiKey' => $this->apiKey,
            'commerceOrder' => $params['commerceOrder'], // ID único de la orden
            'subject' => $params['subject'], // Descripción del pago
            'currency' => $params['currency'] ?? 'CLP',
            'amount' => $params['amount'], // Monto en pesos chilenos
            'email' => $params['email'], // Email del pagador
            'urlConfirmation' => $params['urlConfirmation'], // URL de confirmación (callback)
            'urlReturn' => $params['urlReturn'], // URL de retorno después del pago
        ];

        // Parámetros opcionales
        if (isset($params['optional'])) {
            $data['optional'] = $params['optional'];
        }

        // Generar firma
        $data['s'] = $this->generarFirma($data);

        // Realizar petición a Flow
        $response = $this->realizarPeticion($endpoint, $data);

        return $response;
    }

    /**
     * Simular creación de orden (modo local)
     */
    private function simularCrearOrden($params)
    {
        error_log("=== MODO LOCAL: Simulando creación de orden ===");
        
        // Generar token simulado
        $token = 'LOCAL_' . bin2hex(random_bytes(16));
        
        $response = [
            'token' => $token,
            'url' => BASE_URL . '/pago/simulador?token=' . $token,
            'flowOrder' => time(),
            'commerceOrder' => $params['commerceOrder'],
            'amount' => $params['amount'],
            'subject' => $params['subject'],
            'email' => $params['email']
        ];
        
        error_log("Respuesta simulada: " . print_r($response, true));
        
        return $response;
    }

    /**
     * Obtener el estado de una orden
     * 
     * @param string $token Token de Flow
     * @return array|false Respuesta de Flow o false en caso de error
     */
    public function obtenerEstadoOrden($token)
    {
        $endpoint = '/payment/getStatus';
        
        $data = [
            'apiKey' => $this->apiKey,
            'token' => $token
        ];

        $data['s'] = $this->generarFirma($data);

        return $this->realizarPeticion($endpoint, $data, 'GET');
    }

    /**
     * Validar firma de Flow (para callbacks)
     * 
     * @param array $params Parámetros recibidos de Flow
     * @param string $firma Firma recibida
     * @return bool True si la firma es válida
     */
    public function validarFirma($params, $firma)
    {
        $firmaCalculada = $this->generarFirma($params);
        return $firmaCalculada === $firma;
    }

    /**
     * Generar firma para peticiones a Flow
     * 
     * @param array $params Parámetros a firmar
     * @return string Firma generada
     */
    private function generarFirma($params)
    {
        // Ordenar parámetros alfabéticamente
        ksort($params);
        
        // Concatenar valores
        $cadena = '';
        foreach ($params as $key => $value) {
            $cadena .= $key . $value;
        }
        
        // Agregar secret key
        $cadena .= $this->secretKey;
        
        // Generar hash SHA256
        return hash('sha256', $cadena);
    }

    /**
     * Realizar petición HTTP a Flow
     * 
     * @param string $endpoint Endpoint de la API
     * @param array $data Datos a enviar
     * @param string $method Método HTTP (GET o POST)
     * @return array|false Respuesta decodificada o false en caso de error
     */
    private function realizarPeticion($endpoint, $data, $method = 'POST')
    {
        $url = $this->apiUrl . $endpoint;

        error_log("=== FLOW API REQUEST ===");
        error_log("URL: $url");
        error_log("Method: $method");
        error_log("Data: " . print_r($data, true));

        $ch = curl_init();

        if ($method === 'GET') {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$this->sandbox);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Log para debugging
        error_log("Flow API Response Code: $httpCode");
        error_log("Flow API Response: $response");

        if ($error) {
            error_log("Flow API cURL Error: $error");
            return false;
        }

        if ($httpCode !== 200) {
            error_log("Flow API HTTP Error: $httpCode");
            error_log("Response body: $response");
            return false;
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Flow API JSON Error: " . json_last_error_msg());
            error_log("Raw response: $response");
            return false;
        }

        error_log("Flow API Success: " . print_r($decoded, true));
        return $decoded;
    }

    /**
     * Obtener URL de pago de Flow
     * 
     * @param string $token Token de Flow
     * @return string URL de pago
     */
    public function obtenerUrlPago($token)
    {
        // Si está en modo local, usar simulador
        if ($this->modoLocal || strpos($token, 'LOCAL_') === 0) {
            return BASE_URL . '/pago/simulador?token=' . $token;
        }

        $baseUrl = $this->sandbox 
            ? 'https://sandbox.flow.cl/app/web/pay.php' 
            : 'https://www.flow.cl/app/web/pay.php';
        
        return $baseUrl . '?token=' . $token;
    }

    /**
     * Obtener precio de destacado según tipo
     * 
     * @param string $tipo Tipo de destacado (destacada15 o destacada30)
     * @return int Precio en pesos chilenos
     */
    public static function obtenerPrecioDestacado($tipo)
    {
        $precios = [
            'destacada15' => 15000,
            'destacada30' => 25000
        ];

        return $precios[$tipo] ?? 0;
    }

    /**
     * Obtener días de destacado según tipo
     * 
     * @param string $tipo Tipo de destacado
     * @return int Días de destacado
     */
    public static function obtenerDiasDestacado($tipo)
    {
        // Normalizar el tipo (aceptar ambos formatos)
        $tipo = str_replace('destacado_', 'destacada', $tipo);
        
        $dias = [
            'destacada15' => 15,
            'destacada30' => 30
        ];

        return $dias[$tipo] ?? 0;
    }
    
    /**
     * Convertir tipo de destacado al formato de la base de datos
     * destacada15 -> destacado_15
     * destacada30 -> destacado_30
     * 
     * @param string $tipo Tipo de destacado
     * @return string Tipo en formato BD
     */
    public static function convertirTipoParaBD($tipo)
    {
        return str_replace('destacada', 'destacado_', $tipo);
    }
    
    /**
     * Convertir tipo de destacado desde formato BD al formato de la aplicación
     * destacado_15 -> destacada15
     * destacado_30 -> destacada30
     * 
     * @param string $tipo Tipo desde BD
     * @return string Tipo en formato aplicación
     */
    public static function convertirTipoDesdeDB($tipo)
    {
        return str_replace(['destacado_', 'destacado'], ['destacada', 'destacada'], $tipo);
    }
}
