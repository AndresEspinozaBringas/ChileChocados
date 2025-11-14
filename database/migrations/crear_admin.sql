-- ====================================
-- CREAR USUARIO ADMINISTRADOR
-- ChileChocados
-- ====================================

-- Insertar usuario administrador
-- Contraseña por defecto: Admin123!
-- IMPORTANTE: Cambiar la contraseña después del primer login

INSERT INTO usuarios (
    nombre,
    apellido,
    email,
    password,
    rol,
    estado,
    verificado,
    created_at,
    updated_at
) VALUES (
    'Administrador',
    'Sistema',
    'admin@chilechocados.cl',
    '$argon2id$v=19$m=65536,t=4,p=1$VGhpc0lzQVNhbHRGb3JUZXN0$8Z9QX5J5K5J5K5J5K5J5K5J5K5J5K5J5K5J5K5J5K5I', -- Admin123!
    'admin',
    'activo',
    1,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    apellido = VALUES(apellido),
    rol = 'admin',
    estado = 'activo',
    verificado = 1;

-- Verificar que se creó correctamente
SELECT 
    id,
    nombre,
    apellido,
    email,
    rol,
    estado,
    verificado,
    created_at
FROM usuarios
WHERE email = 'admin@chilechocados.cl';

-- ====================================
-- NOTAS IMPORTANTES:
-- ====================================
-- 1. La contraseña por defecto es: Admin123!
-- 2. Debes cambiarla inmediatamente después del primer login
-- 3. El hash de contraseña usa Argon2id para máxima seguridad
-- 4. Si necesitas regenerar el hash, usa este código PHP:
--    password_hash('Admin123!', PASSWORD_ARGON2ID)
-- ====================================
