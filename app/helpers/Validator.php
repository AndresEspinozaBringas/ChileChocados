<?php
/**
 * Validator Helper - ChileChocados
 * Validaciones de datos con soporte para formatos chilenos
 */

namespace App\Helpers;

class Validator
{
    private $errors = [];
    private $data = [];
    
    /**
     * Constructor
     * 
     * @param array $data Datos a validar
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }
    
    /**
     * Validar datos según reglas
     * 
     * @param array $rules Reglas de validación
     * @return bool
     */
    public function validate($rules)
    {
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            
            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Aplicar una regla de validación
     * 
     * @param string $field Campo
     * @param string $rule Regla
     */
    private function applyRule($field, $rule)
    {
        $value = $this->data[$field] ?? null;
        
        // Extraer parámetros de la regla (ej: min:8)
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleParam = $ruleParts[1] ?? null;
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "El campo {$field} es obligatorio");
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El campo {$field} debe ser un email válido");
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $ruleParam) {
                    $this->addError($field, "El campo {$field} debe tener al menos {$ruleParam} caracteres");
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $ruleParam) {
                    $this->addError($field, "El campo {$field} no debe superar {$ruleParam} caracteres");
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "El campo {$field} debe ser numérico");
                }
                break;
                
            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->addError($field, "El campo {$field} solo debe contener letras");
                }
                break;
                
            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->addError($field, "El campo {$field} solo debe contener letras y números");
                }
                break;
                
            case 'rut':
                if (!empty($value) && !self::validateRUT($value)) {
                    $this->addError($field, "El RUT ingresado no es válido");
                }
                break;
                
            case 'phone_cl':
                if (!empty($value) && !self::validateChileanPhone($value)) {
                    $this->addError($field, "El número de teléfono no es válido");
                }
                break;
                
            case 'strong_password':
                if (!empty($value) && !self::validateStrongPassword($value)) {
                    $this->addError($field, "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial");
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->addError($field, "Los campos no coinciden");
                }
                break;
                
            case 'unique':
                // Formato: unique:tabla,columna
                if (!empty($ruleParam)) {
                    list($table, $column) = explode(',', $ruleParam);
                    if (self::existsInDatabase($table, $column, $value)) {
                        $this->addError($field, "El {$field} ya está registrado");
                    }
                }
                break;
                
            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "El campo {$field} debe ser una URL válida");
                }
                break;
                
            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, "El campo {$field} debe ser una fecha válida");
                }
                break;
                
            case 'in':
                // Formato: in:valor1,valor2,valor3
                if (!empty($ruleParam)) {
                    $allowedValues = explode(',', $ruleParam);
                    if (!in_array($value, $allowedValues)) {
                        $this->addError($field, "El valor seleccionado para {$field} no es válido");
                    }
                }
                break;
        }
    }
    
    /**
     * Agregar error de validación
     * 
     * @param string $field Campo
     * @param string $message Mensaje de error
     */
    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $this->errors[$field][] = $message;
    }
    
    /**
     * Obtener todos los errores
     * 
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * Obtener primer error de un campo
     * 
     * @param string $field Campo
     * @return string|null
     */
    public function firstError($field)
    {
        return $this->errors[$field][0] ?? null;
    }
    
    /**
     * Verificar si hay errores
     * 
     * @return bool
     */
    public function fails()
    {
        return !empty($this->errors);
    }
    
    /**
     * Verificar si la validación pasó
     * 
     * @return bool
     */
    public function passes()
    {
        return empty($this->errors);
    }
    
    // ============================================================================
    // MÉTODOS ESTÁTICOS DE VALIDACIÓN
    // ============================================================================
    
    /**
     * Validar RUT chileno
     * 
     * @param string $rut RUT a validar
     * @return bool
     */
    public static function validateRUT($rut)
    {
        // Limpiar RUT
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (strlen($rut) < 2) {
            return false;
        }
        
        // Separar número y dígito verificador
        $rutNumber = substr($rut, 0, -1);
        $dv = strtoupper(substr($rut, -1));
        
        // Calcular dígito verificador
        $sum = 0;
        $multiplier = 2;
        
        for ($i = strlen($rutNumber) - 1; $i >= 0; $i--) {
            $sum += $rutNumber[$i] * $multiplier;
            $multiplier = $multiplier == 7 ? 2 : $multiplier + 1;
        }
        
        $calculatedDV = 11 - ($sum % 11);
        
        if ($calculatedDV == 11) {
            $calculatedDV = '0';
        } elseif ($calculatedDV == 10) {
            $calculatedDV = 'K';
        } else {
            $calculatedDV = (string) $calculatedDV;
        }
        
        return $dv === $calculatedDV;
    }
    
    /**
     * Validar teléfono chileno
     * Formatos válidos:
     * - +56 9 1234 5678
     * - 9 1234 5678
     * - 912345678
     * - +56912345678
     * 
     * @param string $phone Teléfono a validar
     * @return bool
     */
    public static function validateChileanPhone($phone)
    {
        // Limpiar espacios y caracteres especiales
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // Patrones válidos
        $patterns = [
            '/^\+569\d{8}$/',     // +56912345678
            '/^9\d{8}$/',          // 912345678
            '/^\+56\d{9}$/',       // +56212345678 (fijo)
            '/^\d{9}$/'            // 212345678 (fijo)
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $phone)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validar contraseña segura
     * Requisitos:
     * - Al menos 8 caracteres
     * - Al menos una mayúscula
     * - Al menos una minúscula
     * - Al menos un número
     * - Al menos un carácter especial
     * 
     * @param string $password Contraseña a validar
     * @return bool
     */
    public static function validateStrongPassword($password)
    {
        if (strlen($password) < 8) {
            return false;
        }
        
        // Al menos una mayúscula
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // Al menos una minúscula
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // Al menos un número
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        // Al menos un carácter especial
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validar email
     * 
     * @param string $email Email a validar
     * @return bool
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Sanitizar string
     * 
     * @param string $string String a sanitizar
     * @return string
     */
    public static function sanitize($string)
    {
        return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Limpiar RUT (formato: 12345678-9)
     * 
     * @param string $rut RUT a limpiar
     * @return string
     */
    public static function formatRUT($rut)
    {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (strlen($rut) < 2) {
            return $rut;
        }
        
        $rutNumber = substr($rut, 0, -1);
        $dv = strtoupper(substr($rut, -1));
        
        return number_format($rutNumber, 0, '', '.') . '-' . $dv;
    }
    
    /**
     * Limpiar teléfono chileno (formato: +56 9 1234 5678)
     * 
     * @param string $phone Teléfono a limpiar
     * @return string
     */
    public static function formatChileanPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Si comienza con 9 (móvil)
        if (substr($phone, 0, 1) === '9' && strlen($phone) === 9) {
            return '+56 ' . substr($phone, 0, 1) . ' ' . substr($phone, 1, 4) . ' ' . substr($phone, 5);
        }
        
        // Si ya tiene código de país
        if (substr($phone, 0, 2) === '56' && strlen($phone) === 11) {
            return '+' . substr($phone, 0, 2) . ' ' . substr($phone, 2, 1) . ' ' . substr($phone, 3, 4) . ' ' . substr($phone, 7);
        }
        
        return $phone;
    }
    
    /**
     * Verificar si un valor existe en la base de datos
     * 
     * @param string $table Tabla
     * @param string $column Columna
     * @param mixed $value Valor
     * @return bool
     */
    private static function existsInDatabase($table, $column, $value)
    {
        try {
            require_once __DIR__ . '/../config/database.php';
            
            $db = \App\Config\Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?");
            $stmt->execute([$value]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}
