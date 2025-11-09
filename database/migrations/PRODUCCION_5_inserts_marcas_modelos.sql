-- ============================================
-- SCRIPT 5: INSERT DE MARCAS Y MODELOS
-- ============================================
-- Generado automáticamente desde JSON
-- Fecha: 2025-11-08 20:35:41
-- Total marcas: 27
-- ============================================

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar tablas (opcional - comentar si no quieres borrar datos existentes)
-- TRUNCATE TABLE modelos;
-- TRUNCATE TABLE marcas;

-- Habilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- Marca: Chevrolet (54 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(1, 'Chevrolet', 54, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Chevrolet
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(1, 'Spark', 1, NOW()),
(1, 'Onix', 1, NOW()),
(1, 'Sonic', 1, NOW()),
(1, 'Cruze', 1, NOW()),
(1, 'Malibu', 1, NOW()),
(1, 'Impala', 1, NOW()),
(1, 'Aveo', 1, NOW()),
(1, 'Sail', 1, NOW()),
(1, 'Prisma', 1, NOW()),
(1, 'Cobalt', 1, NOW()),
(1, 'Lacetti', 1, NOW()),
(1, 'Optra', 1, NOW()),
(1, 'Astra', 1, NOW()),
(1, 'Vectra', 1, NOW()),
(1, 'Epica', 1, NOW()),
(1, 'Trax', 1, NOW()),
(1, 'Tracker', 1, NOW()),
(1, 'Equinox', 1, NOW()),
(1, 'Traverse', 1, NOW()),
(1, 'Tahoe', 1, NOW()),
(1, 'Suburban', 1, NOW()),
(1, 'Blazer', 1, NOW()),
(1, 'Captiva', 1, NOW()),
(1, 'Trailblazer', 1, NOW()),
(1, 'Acadia', 1, NOW()),
(1, 'Enclave', 1, NOW()),
(1, 'Orlando', 1, NOW()),
(1, 'Spin', 1, NOW()),
(1, 'Groove', 1, NOW()),
(1, 'Montana', 1, NOW()),
(1, 'S10', 1, NOW()),
(1, 'Colorado', 1, NOW()),
(1, 'Silverado', 1, NOW()),
(1, 'Cheyenne', 1, NOW()),
(1, 'Tornado', 1, NOW()),
(1, 'LUV', 1, NOW()),
(1, 'Express', 1, NOW()),
(1, 'N300', 1, NOW()),
(1, 'N400', 1, NOW()),
(1, 'NPR', 1, NOW()),
(1, 'NQR', 1, NOW()),
(1, 'Dmax', 1, NOW()),
(1, 'Camaro', 1, NOW()),
(1, 'Corvette', 1, NOW()),
(1, 'SS', 1, NOW()),
(1, 'Bolt', 1, NOW()),
(1, 'Volt', 1, NOW()),
(1, 'Spark EV', 1, NOW()),
(1, 'Joy', 1, NOW()),
(1, 'Beat', 1, NOW()),
(1, 'Agile', 1, NOW()),
(1, 'Celta', 1, NOW()),
(1, 'Meriva', 1, NOW()),
(1, 'Zafira', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Toyota (72 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(2, 'Toyota', 72, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Toyota
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(2, 'Corolla', 1, NOW()),
(2, 'Camry', 1, NOW()),
(2, 'Avalon', 1, NOW()),
(2, 'Yaris', 1, NOW()),
(2, 'Prius', 1, NOW()),
(2, 'Mirai', 1, NOW()),
(2, 'Vitz', 1, NOW()),
(2, 'Platz', 1, NOW()),
(2, 'Belta', 1, NOW()),
(2, 'Vios', 1, NOW()),
(2, 'Etios', 1, NOW()),
(2, 'Yaris Sedan', 1, NOW()),
(2, 'Corolla Cross', 1, NOW()),
(2, 'Corolla Altis', 1, NOW()),
(2, 'Camry Hybrid', 1, NOW()),
(2, 'Crown', 1, NOW()),
(2, 'RAV4', 1, NOW()),
(2, 'Highlander', 1, NOW()),
(2, 'Sequoia', 1, NOW()),
(2, 'Land Cruiser', 1, NOW()),
(2, '4Runner', 1, NOW()),
(2, 'C-HR', 1, NOW()),
(2, 'Venza', 1, NOW()),
(2, 'Harrier', 1, NOW()),
(2, 'Kluger', 1, NOW()),
(2, 'Prado', 1, NOW()),
(2, 'FJ Cruiser', 1, NOW()),
(2, 'Rush', 1, NOW()),
(2, 'Raize', 1, NOW()),
(2, 'Urban Cruiser', 1, NOW()),
(2, 'Fortuner', 1, NOW()),
(2, 'RAV4 Hybrid', 1, NOW()),
(2, 'Highlander Hybrid', 1, NOW()),
(2, 'Vanguard', 1, NOW()),
(2, 'Kluger Hybrid', 1, NOW()),
(2, 'Hilux', 1, NOW()),
(2, 'Tacoma', 1, NOW()),
(2, 'Tundra', 1, NOW()),
(2, 'Hiace', 1, NOW()),
(2, 'Coaster', 1, NOW()),
(2, 'Dyna', 1, NOW()),
(2, 'ToyoAce', 1, NOW()),
(2, 'Quantum', 1, NOW()),
(2, 'Granvia', 1, NOW()),
(2, 'Alphard', 1, NOW()),
(2, 'Vellfire', 1, NOW()),
(2, 'Noah', 1, NOW()),
(2, 'Voxy', 1, NOW()),
(2, 'Esquire', 1, NOW()),
(2, 'Sienta', 1, NOW()),
(2, 'Supra', 1, NOW()),
(2, 'GR86', 1, NOW()),
(2, 'GR Yaris', 1, NOW()),
(2, 'GR Supra', 1, NOW()),
(2, 'Celica', 1, NOW()),
(2, 'MR2', 1, NOW()),
(2, '86', 1, NOW()),
(2, 'GT86', 1, NOW()),
(2, 'Prius Prime', 1, NOW()),
(2, 'Prius C', 1, NOW()),
(2, 'Prius V', 1, NOW()),
(2, 'Aqua', 1, NOW()),
(2, 'bZ4X', 1, NOW()),
(2, 'Mirai', 1, NOW()),
(2, 'Sienna', 1, NOW()),
(2, 'Previa', 1, NOW()),
(2, 'Avanza', 1, NOW()),
(2, 'Innova', 1, NOW()),
(2, 'Wish', 1, NOW()),
(2, 'Ipsum', 1, NOW()),
(2, 'Picnic', 1, NOW()),
(2, 'Estima', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Mitsubishi (13 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(3, 'Mitsubishi', 13, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Mitsubishi
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(3, 'Mirage', 1, NOW()),
(3, 'Lancer', 1, NOW()),
(3, 'Galant', 1, NOW()),
(3, 'Eclipse', 1, NOW()),
(3, 'Outlander', 1, NOW()),
(3, 'ASX', 1, NOW()),
(3, 'Pajero', 1, NOW()),
(3, 'Montero', 1, NOW()),
(3, 'L200', 1, NOW()),
(3, 'Triton', 1, NOW()),
(3, 'Colt', 1, NOW()),
(3, 'Space Star', 1, NOW()),
(3, 'Grandis', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Ford (28 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(4, 'Ford', 28, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Ford
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(4, 'Ka', 1, NOW()),
(4, 'Fiesta', 1, NOW()),
(4, 'Focus', 1, NOW()),
(4, 'Fusion', 1, NOW()),
(4, 'Mondeo', 1, NOW()),
(4, 'Taurus', 1, NOW()),
(4, 'EcoSport', 1, NOW()),
(4, 'Kuga', 1, NOW()),
(4, 'Escape', 1, NOW()),
(4, 'Edge', 1, NOW()),
(4, 'Explorer', 1, NOW()),
(4, 'Expedition', 1, NOW()),
(4, 'Bronco', 1, NOW()),
(4, 'Ranger', 1, NOW()),
(4, 'F-150', 1, NOW()),
(4, 'F-250', 1, NOW()),
(4, 'F-350', 1, NOW()),
(4, 'Maverick', 1, NOW()),
(4, 'Mustang', 1, NOW()),
(4, 'GT', 1, NOW()),
(4, 'Mustang Mach-E', 1, NOW()),
(4, 'F-150 Lightning', 1, NOW()),
(4, 'Transit', 1, NOW()),
(4, 'Transit Connect', 1, NOW()),
(4, 'E-Series', 1, NOW()),
(4, 'Territory', 1, NOW()),
(4, 'Everest', 1, NOW()),
(4, 'Bronco Sport', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Mazda (17 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(5, 'Mazda', 17, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Mazda
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(5, 'Mazda2', 1, NOW()),
(5, 'Mazda3', 1, NOW()),
(5, 'Mazda6', 1, NOW()),
(5, 'CX-3', 1, NOW()),
(5, 'CX-30', 1, NOW()),
(5, 'CX-5', 1, NOW()),
(5, 'CX-7', 1, NOW()),
(5, 'CX-9', 1, NOW()),
(5, 'CX-90', 1, NOW()),
(5, 'BT-50', 1, NOW()),
(5, 'MX-5', 1, NOW()),
(5, 'RX-7', 1, NOW()),
(5, 'RX-8', 1, NOW()),
(5, 'MPV', 1, NOW()),
(5, 'Tribute', 1, NOW()),
(5, '5', 1, NOW()),
(5, 'Premacy', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Suzuki (20 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(6, 'Suzuki', 20, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Suzuki
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(6, 'Swift', 1, NOW()),
(6, 'Baleno', 1, NOW()),
(6, 'Ciaz', 1, NOW()),
(6, 'Dzire', 1, NOW()),
(6, 'Ertiga', 1, NOW()),
(6, 'XL6', 1, NOW()),
(6, 'Vitara', 1, NOW()),
(6, 'S-Cross', 1, NOW()),
(6, 'Jimny', 1, NOW()),
(6, 'Grand Vitara', 1, NOW()),
(6, 'SX4', 1, NOW()),
(6, 'Kizashi', 1, NOW()),
(6, 'Alto', 1, NOW()),
(6, 'Wagon R', 1, NOW()),
(6, 'Celerio', 1, NOW()),
(6, 'Ignis', 1, NOW()),
(6, 'Splash', 1, NOW()),
(6, 'APV', 1, NOW()),
(6, 'Carry', 1, NOW()),
(6, 'Super Carry', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Volkswagen (25 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(7, 'Volkswagen', 25, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Volkswagen
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(7, 'Gol', 1, NOW()),
(7, 'Polo', 1, NOW()),
(7, 'Golf', 1, NOW()),
(7, 'Jetta', 1, NOW()),
(7, 'Passat', 1, NOW()),
(7, 'Arteon', 1, NOW()),
(7, 'Virtus', 1, NOW()),
(7, 'T-Cross', 1, NOW()),
(7, 'Tiguan', 1, NOW()),
(7, 'Touareg', 1, NOW()),
(7, 'Atlas', 1, NOW()),
(7, 'Taos', 1, NOW()),
(7, 'ID.4', 1, NOW()),
(7, 'Amarok', 1, NOW()),
(7, 'Saveiro', 1, NOW()),
(7, 'Crafter', 1, NOW()),
(7, 'Caddy', 1, NOW()),
(7, 'Transporter', 1, NOW()),
(7, 'ID.3', 1, NOW()),
(7, 'ID.4', 1, NOW()),
(7, 'ID.Buzz', 1, NOW()),
(7, 'Beetle', 1, NOW()),
(7, 'Scirocco', 1, NOW()),
(7, 'CC', 1, NOW()),
(7, 'Voyage', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Peugeot (15 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(8, 'Peugeot', 15, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Peugeot
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(8, '108', 1, NOW()),
(8, '208', 1, NOW()),
(8, '308', 1, NOW()),
(8, '408', 1, NOW()),
(8, '508', 1, NOW()),
(8, '2008', 1, NOW()),
(8, '3008', 1, NOW()),
(8, '5008', 1, NOW()),
(8, 'Partner', 1, NOW()),
(8, 'Expert', 1, NOW()),
(8, 'Boxer', 1, NOW()),
(8, 'RCZ', 1, NOW()),
(8, '207', 1, NOW()),
(8, '307', 1, NOW()),
(8, '407', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Opel (10 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(9, 'Opel', 10, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Opel
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(9, 'Corsa', 1, NOW()),
(9, 'Astra', 1, NOW()),
(9, 'Insignia', 1, NOW()),
(9, 'Mokka', 1, NOW()),
(9, 'Crossland', 1, NOW()),
(9, 'Grandland', 1, NOW()),
(9, 'Combo', 1, NOW()),
(9, 'Vivaro', 1, NOW()),
(9, 'Movano', 1, NOW()),
(9, 'Zafira', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Changan (10 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(10, 'Changan', 10, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Changan
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(10, 'CS35', 1, NOW()),
(10, 'CS55', 1, NOW()),
(10, 'CS75', 1, NOW()),
(10, 'CS95', 1, NOW()),
(10, 'Eado', 1, NOW()),
(10, 'Alsvin', 1, NOW()),
(10, 'Raeton', 1, NOW()),
(10, 'CX20', 1, NOW()),
(10, 'CX30', 1, NOW()),
(10, 'Benni', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Chery (12 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(11, 'Chery', 12, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Chery
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(11, 'QQ', 1, NOW()),
(11, 'Tiggo', 1, NOW()),
(11, 'Arrizo', 1, NOW()),
(11, 'Fulwin', 1, NOW()),
(11, 'A1', 1, NOW()),
(11, 'A3', 1, NOW()),
(11, 'A5', 1, NOW()),
(11, 'E3', 1, NOW()),
(11, 'E5', 1, NOW()),
(11, 'Oriental Son', 1, NOW()),
(11, 'Eastar', 1, NOW()),
(11, 'Very', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Hyundai (22 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(12, 'Hyundai', 22, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Hyundai
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(12, 'Grand i10', 1, NOW()),
(12, 'i20', 1, NOW()),
(12, 'Accent', 1, NOW()),
(12, 'Elantra', 1, NOW()),
(12, 'Sonata', 1, NOW()),
(12, 'Genesis', 1, NOW()),
(12, 'Venue', 1, NOW()),
(12, 'Kona', 1, NOW()),
(12, 'Creta', 1, NOW()),
(12, 'Tucson', 1, NOW()),
(12, 'Santa Fe', 1, NOW()),
(12, 'Palisade', 1, NOW()),
(12, 'H100', 1, NOW()),
(12, 'Porter', 1, NOW()),
(12, 'Starex', 1, NOW()),
(12, 'Ioniq', 1, NOW()),
(12, 'Kona Electric', 1, NOW()),
(12, 'Ioniq 5', 1, NOW()),
(12, 'Ioniq 6', 1, NOW()),
(12, 'Veloster', 1, NOW()),
(12, 'Genesis Coupe', 1, NOW()),
(12, 'Azera', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: JAC (13 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(13, 'JAC', 13, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de JAC
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(13, 'J2', 1, NOW()),
(13, 'J3', 1, NOW()),
(13, 'J4', 1, NOW()),
(13, 'J5', 1, NOW()),
(13, 'J6', 1, NOW()),
(13, 'S2', 1, NOW()),
(13, 'S3', 1, NOW()),
(13, 'S5', 1, NOW()),
(13, 'T6', 1, NOW()),
(13, 'T8', 1, NOW()),
(13, 'Refine', 1, NOW()),
(13, 'Rein', 1, NOW()),
(13, 'Sunray', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: MG (13 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(14, 'MG', 13, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de MG
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(14, 'MG3', 1, NOW()),
(14, 'MG5', 1, NOW()),
(14, 'MG6', 1, NOW()),
(14, 'MG7', 1, NOW()),
(14, 'ZS', 1, NOW()),
(14, 'HS', 1, NOW()),
(14, 'RX5', 1, NOW()),
(14, 'RX8', 1, NOW()),
(14, 'GT', 1, NOW()),
(14, 'TF', 1, NOW()),
(14, 'ZR', 1, NOW()),
(14, 'ZT', 1, NOW()),
(14, 'F', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Nissan (54 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(15, 'Nissan', 54, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Nissan
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(15, 'Versa', 1, NOW()),
(15, 'Sentra', 1, NOW()),
(15, 'Altima', 1, NOW()),
(15, 'Maxima', 1, NOW()),
(15, 'March', 1, NOW()),
(15, 'Note', 1, NOW()),
(15, 'Tiida', 1, NOW()),
(15, 'Almera', 1, NOW()),
(15, 'Primera', 1, NOW()),
(15, 'Sunny', 1, NOW()),
(15, 'Tsuru', 1, NOW()),
(15, 'Platina', 1, NOW()),
(15, 'Aprio', 1, NOW()),
(15, 'Latio', 1, NOW()),
(15, 'Kicks', 1, NOW()),
(15, 'Qashqai', 1, NOW()),
(15, 'X-Trail', 1, NOW()),
(15, 'Murano', 1, NOW()),
(15, 'Pathfinder', 1, NOW()),
(15, 'Armada', 1, NOW()),
(15, 'Patrol', 1, NOW()),
(15, 'Juke', 1, NOW()),
(15, 'Rogue', 1, NOW()),
(15, 'Xterra', 1, NOW()),
(15, 'Terrano', 1, NOW()),
(15, 'Paladin', 1, NOW()),
(15, 'Livina X-Gear', 1, NOW()),
(15, 'Navara', 1, NOW()),
(15, 'Frontier', 1, NOW()),
(15, 'Titan', 1, NOW()),
(15, 'Hardbody', 1, NOW()),
(15, 'King Cab', 1, NOW()),
(15, 'NP300', 1, NOW()),
(15, 'GT-R', 1, NOW()),
(15, '370Z', 1, NOW()),
(15, '350Z', 1, NOW()),
(15, '300ZX', 1, NOW()),
(15, '240SX', 1, NOW()),
(15, 'Silvia', 1, NOW()),
(15, 'Skyline', 1, NOW()),
(15, 'Leaf', 1, NOW()),
(15, 'Ariya', 1, NOW()),
(15, 'e-NV200', 1, NOW()),
(15, 'Urvan', 1, NOW()),
(15, 'Cabstar', 1, NOW()),
(15, 'NV200', 1, NOW()),
(15, 'NV400', 1, NOW()),
(15, 'Interstar', 1, NOW()),
(15, 'Livina', 1, NOW()),
(15, 'Grand Livina', 1, NOW()),
(15, 'Cube', 1, NOW()),
(15, 'Micra', 1, NOW()),
(15, 'Pixo', 1, NOW()),
(15, 'Quest', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: BMW (33 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(16, 'BMW', 33, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de BMW
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(16, 'Serie 1', 1, NOW()),
(16, 'Serie 2', 1, NOW()),
(16, 'Serie 3', 1, NOW()),
(16, 'Serie 4', 1, NOW()),
(16, 'Serie 5', 1, NOW()),
(16, 'Serie 6', 1, NOW()),
(16, 'Serie 7', 1, NOW()),
(16, 'Serie 8', 1, NOW()),
(16, 'X1', 1, NOW()),
(16, 'X2', 1, NOW()),
(16, 'X3', 1, NOW()),
(16, 'X4', 1, NOW()),
(16, 'X5', 1, NOW()),
(16, 'X6', 1, NOW()),
(16, 'X7', 1, NOW()),
(16, 'i3', 1, NOW()),
(16, 'i4', 1, NOW()),
(16, 'iX', 1, NOW()),
(16, 'i7', 1, NOW()),
(16, 'iX3', 1, NOW()),
(16, 'Z3', 1, NOW()),
(16, 'Z4', 1, NOW()),
(16, 'M2', 1, NOW()),
(16, 'M3', 1, NOW()),
(16, 'M4', 1, NOW()),
(16, 'M5', 1, NOW()),
(16, 'M6', 1, NOW()),
(16, 'M8', 1, NOW()),
(16, '1 Series GT', 1, NOW()),
(16, '2 Series GT', 1, NOW()),
(16, '3 Series GT', 1, NOW()),
(16, '5 Series GT', 1, NOW()),
(16, '6 Series GT', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Kia (22 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(17, 'Kia', 22, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Kia
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(17, 'Picanto', 1, NOW()),
(17, 'Rio', 1, NOW()),
(17, 'Forte', 1, NOW()),
(17, 'Optima', 1, NOW()),
(17, 'Cadenza', 1, NOW()),
(17, 'K5', 1, NOW()),
(17, 'Seltos', 1, NOW()),
(17, 'Sportage', 1, NOW()),
(17, 'Sorento', 1, NOW()),
(17, 'Mohave', 1, NOW()),
(17, 'Telluride', 1, NOW()),
(17, 'Bongo', 1, NOW()),
(17, 'K2500', 1, NOW()),
(17, 'K3000', 1, NOW()),
(17, 'Soul EV', 1, NOW()),
(17, 'Niro', 1, NOW()),
(17, 'EV6', 1, NOW()),
(17, 'Stinger', 1, NOW()),
(17, 'Forte Koup', 1, NOW()),
(17, 'Soul', 1, NOW()),
(17, 'Carnival', 1, NOW()),
(17, 'Sedona', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Mercedes (0 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(18, 'Mercedes', 0, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Marca: Skoda (10 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(19, 'Skoda', 10, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Skoda
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(19, 'Fabia', 1, NOW()),
(19, 'Rapid', 1, NOW()),
(19, 'Octavia', 1, NOW()),
(19, 'Superb', 1, NOW()),
(19, 'Kamiq', 1, NOW()),
(19, 'Karoq', 1, NOW()),
(19, 'Kodiaq', 1, NOW()),
(19, 'Citigo', 1, NOW()),
(19, 'Yeti', 1, NOW()),
(19, 'Roomster', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Audi (29 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(20, 'Audi', 29, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Audi
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(20, 'A1', 1, NOW()),
(20, 'A3', 1, NOW()),
(20, 'A4', 1, NOW()),
(20, 'A5', 1, NOW()),
(20, 'A6', 1, NOW()),
(20, 'A7', 1, NOW()),
(20, 'A8', 1, NOW()),
(20, 'Q2', 1, NOW()),
(20, 'Q3', 1, NOW()),
(20, 'Q4', 1, NOW()),
(20, 'Q5', 1, NOW()),
(20, 'Q7', 1, NOW()),
(20, 'Q8', 1, NOW()),
(20, 'TT', 1, NOW()),
(20, 'R8', 1, NOW()),
(20, 'RS3', 1, NOW()),
(20, 'RS4', 1, NOW()),
(20, 'RS5', 1, NOW()),
(20, 'RS6', 1, NOW()),
(20, 'RS7', 1, NOW()),
(20, 'e-tron', 1, NOW()),
(20, 'e-tron GT', 1, NOW()),
(20, 'Q4 e-tron', 1, NOW()),
(20, 'S3', 1, NOW()),
(20, 'S4', 1, NOW()),
(20, 'S5', 1, NOW()),
(20, 'S6', 1, NOW()),
(20, 'S7', 1, NOW()),
(20, 'S8', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: SEAT (10 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(21, 'SEAT', 10, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de SEAT
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(21, 'Ibiza', 1, NOW()),
(21, 'Leon', 1, NOW()),
(21, 'Toledo', 1, NOW()),
(21, 'Arona', 1, NOW()),
(21, 'Ateca', 1, NOW()),
(21, 'Tarraco', 1, NOW()),
(21, 'Alhambra', 1, NOW()),
(21, 'Altea', 1, NOW()),
(21, 'Cordoba', 1, NOW()),
(21, 'Mii', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Haval (10 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(22, 'Haval', 10, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Haval
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(22, 'H1', 1, NOW()),
(22, 'H2', 1, NOW()),
(22, 'H6', 1, NOW()),
(22, 'H7', 1, NOW()),
(22, 'H8', 1, NOW()),
(22, 'H9', 1, NOW()),
(22, 'F5', 1, NOW()),
(22, 'F7', 1, NOW()),
(22, 'Jolion', 1, NOW()),
(22, 'Dargo', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Volvo (14 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(23, 'Volvo', 14, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Volvo
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(23, 'S60', 1, NOW()),
(23, 'S90', 1, NOW()),
(23, 'V40', 1, NOW()),
(23, 'V60', 1, NOW()),
(23, 'V90', 1, NOW()),
(23, 'XC40', 1, NOW()),
(23, 'XC60', 1, NOW()),
(23, 'XC90', 1, NOW()),
(23, 'C30', 1, NOW()),
(23, 'C70', 1, NOW()),
(23, 'S40', 1, NOW()),
(23, 'V50', 1, NOW()),
(23, 'V70', 1, NOW()),
(23, 'XC70', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Foton (0 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(24, 'Foton', 0, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Marca: MINI (8 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(25, 'MINI', 8, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de MINI
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(25, 'Cooper', 1, NOW()),
(25, 'Countryman', 1, NOW()),
(25, 'Clubman', 1, NOW()),
(25, 'Convertible', 1, NOW()),
(25, 'Coupe', 1, NOW()),
(25, 'Roadster', 1, NOW()),
(25, 'Paceman', 1, NOW()),
(25, 'One', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Geely (11 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(26, 'Geely', 11, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Geely
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(26, 'Emgrand', 1, NOW()),
(26, 'GC6', 1, NOW()),
(26, 'GC7', 1, NOW()),
(26, 'GX7', 1, NOW()),
(26, 'LC', 1, NOW()),
(26, 'MK', 1, NOW()),
(26, 'Panda', 1, NOW()),
(26, 'Vision', 1, NOW()),
(26, 'Coolray', 1, NOW()),
(26, 'Azkarra', 1, NOW()),
(26, 'Okavango', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- Marca: Renault (19 modelos)
INSERT INTO marcas (id, nombre, cantidad_modelos, activa, fecha_creacion) VALUES
(27, 'Renault', 19, 1, NOW())
ON DUPLICATE KEY UPDATE 
    cantidad_modelos = VALUES(cantidad_modelos),
    fecha_actualizacion = NOW();

-- Modelos de Renault
INSERT INTO modelos (marca_id, nombre, activo, fecha_creacion) VALUES
(27, 'Clio', 1, NOW()),
(27, 'Megane', 1, NOW()),
(27, 'Fluence', 1, NOW()),
(27, 'Talisman', 1, NOW()),
(27, 'Logan', 1, NOW()),
(27, 'Sandero', 1, NOW()),
(27, 'Captur', 1, NOW()),
(27, 'Kadjar', 1, NOW()),
(27, 'Koleos', 1, NOW()),
(27, 'Duster', 1, NOW()),
(27, 'Kangoo', 1, NOW()),
(27, 'Master', 1, NOW()),
(27, 'Trafic', 1, NOW()),
(27, 'Kwid', 1, NOW()),
(27, 'Symbol', 1, NOW()),
(27, 'Alpine A110', 1, NOW()),
(27, 'Scenic', 1, NOW()),
(27, 'Espace', 1, NOW()),
(27, 'Twingo', 1, NOW())
ON DUPLICATE KEY UPDATE 
    activo = VALUES(activo),
    fecha_actualizacion = NOW();

-- ============================================
-- VERIFICACIÓN
-- ============================================

SELECT 'Importación completada' AS resultado;
SELECT COUNT(*) AS total_marcas FROM marcas;
SELECT COUNT(*) AS total_modelos FROM modelos;

-- Top 10 marcas con más modelos
SELECT m.nombre as marca, COUNT(mo.id) as total_modelos
FROM marcas m
LEFT JOIN modelos mo ON m.id = mo.marca_id
GROUP BY m.id
ORDER BY total_modelos DESC
LIMIT 10;
