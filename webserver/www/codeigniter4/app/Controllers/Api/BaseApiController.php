<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class BaseApiController extends ResourceController
{
    use ResponseTrait;

    protected $session;
    protected $request;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        helper(['security', 'functions']);
    }

    /**
     * Verifica autenticação via API
     */
    protected function authenticateApi()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            return $this->failUnauthorized('Token de autorização necessário');
        }

        // Verifica se é Bearer token
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $this->failUnauthorized('Formato de token inválido');
        }

        $token = $matches[1];
        
        // Aqui você implementaria a validação do JWT ou outro token
        // Por simplicidade, vamos usar uma validação básica
        if (!$this->validateApiToken($token)) {
            return $this->failUnauthorized('Token inválido');
        }

        return true;
    }

    /**
     * Valida token da API (implementação básica)
     */
    protected function validateApiToken($token)
    {
        // Implementação básica - em produção use JWT
        $validTokens = [
            'api_token_admin_123456',
            'api_token_user_789012'
        ];

        return in_array($token, $validTokens);
    }

    /**
     * Aplica rate limiting
     */
    protected function applyRateLimit($key = null, $maxAttempts = 60, $timeWindow = 3600)
    {
        $clientIp = $this->request->getIPAddress();
        $rateLimitKey = $key ?: "api_rate_limit_{$clientIp}";

        if (!rate_limit_check($rateLimitKey, $maxAttempts, $timeWindow)) {
            return $this->failTooManyRequests('Rate limit excedido');
        }

        return true;
    }

    /**
     * Valida dados de entrada
     */
    protected function validateInput($rules, $data = null)
    {
        $validation = \Config\Services::validation();
        $data = $data ?: $this->request->getJSON(true);

        if (!$validation->run($data, $rules)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        return $data;
    }

    /**
     * Resposta de sucesso padronizada
     */
    protected function respondSuccess($data = null, $message = 'Sucesso', $code = 200)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'timestamp' => date('c')
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $this->respond($response, $code);
    }

    /**
     * Resposta de erro padronizada
     */
    protected function respondError($message = 'Erro interno', $code = 500, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'timestamp' => date('c')
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        log_security_event('API_ERROR', [
            'message' => $message,
            'code' => $code,
            'endpoint' => $this->request->getUri()->getPath()
        ]);

        return $this->respond($response, $code);
    }

    /**
     * Resposta de dados paginados
     */
    protected function respondPaginated($data, $total, $page = 1, $perPage = 20)
    {
        $totalPages = ceil($total / $perPage);

        return $this->respondSuccess([
            'items' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ]);
    }

    /**
     * Sanitiza dados de entrada
     */
    protected function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }

        return sanitize_input($data);
    }

    /**
     * Log de atividade da API
     */
    protected function logApiActivity($action, $details = [])
    {
        $logger = \Config\Services::logger();
        
        $logData = [
            'action' => $action,
            'ip' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'endpoint' => $this->request->getUri()->getPath(),
            'method' => $this->request->getMethod(),
            'timestamp' => date('Y-m-d H:i:s'),
            'details' => $details
        ];

        $logger->info('API Activity: ' . $action, $logData);
    }

    /**
     * Verifica permissões do usuário
     */
    protected function checkPermission($requiredLevel = 0)
    {
        // Implementação básica de verificação de permissão
        // Em uma implementação real, você extrairia isso do token JWT
        
        $userLevel = $this->getUserLevelFromToken();
        
        if ($userLevel < $requiredLevel) {
            return $this->failForbidden('Permissão insuficiente');
        }

        return true;
    }

    /**
     * Extrai nível do usuário do token (implementação básica)
     */
    protected function getUserLevelFromToken()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        
        if (strpos($authHeader, 'api_token_admin') !== false) {
            return 2; // Admin
        } elseif (strpos($authHeader, 'api_token_user') !== false) {
            return 0; // User
        }

        return -1; // Sem permissão
    }

    /**
     * Extrai ID do usuário do token (implementação básica)
     */
    protected function getUserIdFromToken()
    {
        // Em uma implementação real, isso viria do JWT
        return 1; // Usuário padrão para teste
    }

    /**
     * Middleware de CORS
     */
    protected function handleCors()
    {
        $origin = $this->request->getHeaderLine('Origin');
        $allowedOrigins = ['http://localhost:3000', 'https://yourdomain.com'];

        if (in_array($origin, $allowedOrigins)) {
            $this->response->setHeader('Access-Control-Allow-Origin', $origin);
        }

        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $this->response->setHeader('Access-Control-Max-Age', '86400');

        if ($this->request->getMethod() === 'OPTIONS') {
            return $this->respond('', 200);
        }

        return true;
    }

    /**
     * Valida formato de data
     */
    protected function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Converte dados para formato de API
     */
    protected function formatForApi($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        if (is_array($data)) {
            // Remove campos sensíveis
            $sensitiveFields = ['senha', 'password', 'token', 'secret'];
            
            foreach ($sensitiveFields as $field) {
                if (isset($data[$field])) {
                    unset($data[$field]);
                }
            }

            // Converte timestamps para ISO 8601
            foreach ($data as $key => $value) {
                if (strpos($key, 'data') !== false || strpos($key, 'created') !== false || strpos($key, 'updated') !== false) {
                    if ($value && $value !== '0000-00-00 00:00:00') {
                        $data[$key] = date('c', strtotime($value));
                    }
                }
            }
        }

        return $data;
    }
}
