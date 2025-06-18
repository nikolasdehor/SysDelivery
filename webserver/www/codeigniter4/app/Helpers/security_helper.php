<?php

if (!function_exists('sanitize_input')) {
    /**
     * Sanitiza entrada de dados
     */
    function sanitize_input($data, $type = 'string')
    {
        if (is_array($data)) {
            return array_map(function($item) use ($type) {
                return sanitize_input($item, $type);
            }, $data);
        }

        // Remove espaços em branco
        $data = trim($data);

        switch ($type) {
            case 'email':
                return filter_var($data, FILTER_SANITIZE_EMAIL);
            
            case 'url':
                return filter_var($data, FILTER_SANITIZE_URL);
            
            case 'int':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            
            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            case 'string':
            default:
                // Remove tags HTML e caracteres especiais
                $data = strip_tags($data);
                $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
                return $data;
        }
    }
}

if (!function_exists('validate_csrf')) {
    /**
     * Valida token CSRF
     */
    function validate_csrf()
    {
        $request = \Config\Services::request();
        $security = \Config\Services::security();
        
        if (!$security->verify($request)) {
            throw new \CodeIgniter\Security\Exceptions\SecurityException('Token CSRF inválido');
        }
        
        return true;
    }
}

if (!function_exists('generate_csrf_token')) {
    /**
     * Gera token CSRF
     */
    function generate_csrf_token()
    {
        $security = \Config\Services::security();
        return $security->getCSRFToken();
    }
}

if (!function_exists('validate_password_strength')) {
    /**
     * Valida força da senha
     */
    function validate_password_strength($password, $min_length = 8)
    {
        $errors = [];
        
        if (strlen($password) < $min_length) {
            $errors[] = "A senha deve ter pelo menos {$min_length} caracteres";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "A senha deve conter pelo menos uma letra maiúscula";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "A senha deve conter pelo menos uma letra minúscula";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "A senha deve conter pelo menos um número";
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "A senha deve conter pelo menos um caractere especial";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength' => calculate_password_strength($password)
        ];
    }
}

if (!function_exists('calculate_password_strength')) {
    /**
     * Calcula força da senha (0-100)
     */
    function calculate_password_strength($password)
    {
        $strength = 0;
        $length = strlen($password);
        
        // Pontuação por comprimento
        if ($length >= 8) $strength += 25;
        if ($length >= 12) $strength += 15;
        if ($length >= 16) $strength += 10;
        
        // Pontuação por tipos de caracteres
        if (preg_match('/[a-z]/', $password)) $strength += 10;
        if (preg_match('/[A-Z]/', $password)) $strength += 10;
        if (preg_match('/[0-9]/', $password)) $strength += 10;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $strength += 20;
        
        return min(100, $strength);
    }
}

if (!function_exists('validate_cpf')) {
    /**
     * Valida CPF
     */
    function validate_cpf($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Calcula primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        // Verifica primeiro dígito
        if ($cpf[9] != $digit1) {
            return false;
        }
        
        // Calcula segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        // Verifica segundo dígito
        return $cpf[10] == $digit2;
    }
}

if (!function_exists('format_cpf')) {
    /**
     * Formata CPF
     */
    function format_cpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) == 11) {
            return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }
        return $cpf;
    }
}

if (!function_exists('validate_phone')) {
    /**
     * Valida telefone brasileiro
     */
    function validate_phone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Telefone fixo: 10 dígitos (com DDD)
        // Celular: 11 dígitos (com DDD e 9)
        return strlen($phone) == 10 || strlen($phone) == 11;
    }
}

if (!function_exists('format_phone')) {
    /**
     * Formata telefone
     */
    function format_phone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) == 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        } elseif (strlen($phone) == 10) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
        }
        
        return $phone;
    }
}

if (!function_exists('prevent_sql_injection')) {
    /**
     * Previne SQL Injection básico
     */
    function prevent_sql_injection($data)
    {
        if (is_array($data)) {
            return array_map('prevent_sql_injection', $data);
        }
        
        // Remove caracteres perigosos
        $dangerous = ['--', ';', '/*', '*/', 'xp_', 'sp_', 'DROP', 'DELETE', 'INSERT', 'UPDATE', 'UNION', 'SELECT'];
        
        foreach ($dangerous as $pattern) {
            $data = str_ireplace($pattern, '', $data);
        }
        
        return $data;
    }
}

if (!function_exists('rate_limit_check')) {
    /**
     * Verifica rate limiting
     */
    function rate_limit_check($key, $max_attempts = 5, $time_window = 300)
    {
        $cache = \Config\Services::cache();
        $attempts = $cache->get($key) ?: 0;

        if ($attempts >= $max_attempts) {
            return false;
        }

        $cache->save($key, $attempts + 1, $time_window);
        return true;
    }
}

if (!function_exists('rate_limit_clear')) {
    /**
     * Limpa rate limiting para uma chave específica
     */
    function rate_limit_clear($key)
    {
        $cache = \Config\Services::cache();
        return $cache->delete($key);
    }
}

if (!function_exists('rate_limit_clear_user')) {
    /**
     * Limpa rate limiting para um usuário específico (por IP ou login)
     */
    function rate_limit_clear_user($identifier = null)
    {
        $cache = \Config\Services::cache();

        // Se não especificado, usa o IP atual
        if ($identifier === null) {
            $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }

        // Limpa diferentes tipos de rate limiting
        $keys = [
            "login_attempts_{$identifier}",
            "api_rate_limit_{$identifier}",
            "rate_limit_{$identifier}"
        ];

        $cleared = 0;
        foreach ($keys as $key) {
            if ($cache->delete($key)) {
                $cleared++;
            }
        }

        return $cleared;
    }
}

if (!function_exists('log_security_event')) {
    /**
     * Registra evento de segurança
     */
    function log_security_event($event, $details = [])
    {
        $logger = \Config\Services::logger();
        
        $logData = [
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s'),
            'details' => $details
        ];
        
        $logger->warning('Security Event: ' . $event, $logData);
    }
}

if (!function_exists('hash_password_secure')) {
    /**
     * Hash seguro de senha
     */
    function hash_password_secure($password)
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3,         // 3 threads
        ]);
    }
}

if (!function_exists('verify_password_secure')) {
    /**
     * Verifica senha com hash seguro
     */
    function verify_password_secure($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
