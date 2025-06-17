<?php
$host = '127.0.0.1';
$user = 'root';
$pass = 'Gemelas2000#';
$target_db = 'webservices';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass);
    echo "✅ CONEXIÓN AL SERVIDOR EXITOSA<br>";

    $result = $conn->query("SHOW DATABASES LIKE '$target_db'");
    if ($result->num_rows == 0) {
        $conn->query("CREATE DATABASE `$target_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "✅ Base de datos '$target_db' creada exitosamente...";
    } else {
        echo "📦 Base de datos '$target_db' ya existe...";
    }

    $conn->select_db($target_db);

    // --- CLIENTES ---
    $result = $conn->query("SHOW TABLES LIKE 'ventas_resumen'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'ventas_resumen' no existe. Creando...";
        $create_clientes_sql = "
            CREATE TABLE `ventas_resumen` (
            `id_ventas` int(20) NOT NULL AUTO_INCREMENT,
            `nro_factura` varchar(11) NOT NULL,
            `corr_fiscal` varchar(11) NOT NULL,
            `cod_cliente` varchar(40) NOT NULL,
            `nombre_cliente` varchar(200) NOT NULL,
            `direccion` varchar(300) NOT NULL,
            `telefono` varchar(100) NOT NULL,
            `descripcion` mediumtext DEFAULT NULL,
            `fecha_emision` datetime NOT NULL,
            `fecha_vencimiento` date DEFAULT NULL,
            `total_neto` decimal(10,2) NOT NULL,
            `total_factura` decimal(10,2) NOT NULL,
            `sub_total` decimal(10,2) NOT NULL,
            `abono` decimal(10,2) NOT NULL,
            `saldo` decimal(10,2) NOT NULL,
            `base_imp` decimal(10,2) NOT NULL,
            `tasa_iva` decimal(10,2) NOT NULL,
            `iva` decimal(10,2) NOT NULL,
            `exento` decimal(10,2) NOT NULL,
            `status` varchar(50) NOT NULL,
            `descuento` decimal(10,2) NOT NULL,
            `t_descuento` decimal(10,2) NOT NULL,
            `cantidad_renglon` int(10) NOT NULL,
            `tasa_cambio` decimal(10,2) NOT NULL,
            `total_bsd` decimal(13,2) NOT NULL,
            `total_fact_bsd` decimal(13,2) NOT NULL,
            `nro_control` varchar(10) NOT NULL,
            `fecha` datetime NOT NULL,
            `ip_estacion` varchar(60) NOT NULL,
            `usuario` varchar(200) NOT NULL,
            `empresa` varchar(20) NOT NULL,
            `sucursal` varchar(20) NOT NULL,
            `usr_nivel` varchar(20) NOT NULL,
            `id_telecomunicaciones` varchar(20) NOT NULL,
            `rif_fiscal` varchar(5000) NOT NULL,
            `razon_social` varchar(200) NOT NULL,
            `ciudad` varchar(40) NOT NULL,
            `iva_bs` decimal(13,2) NOT NULL,
            `sub_total_bs` decimal(13,2) NOT NULL,
            `fact_fiscal` varchar(20) NOT NULL,
            `id_cliente` int(11) DEFAULT NULL,
            `id_cliente_hijo` int(11) DEFAULT NULL,
            `vuelto` decimal(11,2) DEFAULT NULL,
            `cierre` int(2) DEFAULT NULL,
            `id_cxc_cobro_resumen` int(11) DEFAULT NULL,
            `id_fact_digital` varchar(255) DEFAULT NULL,
            `fec_anula` datetime DEFAULT NULL,
            `usu_anula` varchar(200) DEFAULT NULL,
            `mot_anula` varchar(500) DEFAULT NULL,
            `exento_bs` decimal(13,2) DEFAULT NULL,
            `base_imp_bs` decimal(13,2) DEFAULT NULL,
            `en_cola` varchar(10) DEFAULT NULL,
            `fecha_desde` date DEFAULT NULL,
            `fecha_hasta` date DEFAULT NULL,
            `fecha_factura_generada` date DEFAULT NULL,
            PRIMARY KEY (`id_ventas`),
            KEY `nro_factura` (`nro_factura`),
            KEY `corr_fiscal` (`corr_fiscal`),
            KEY `cod_cliente` (`cod_cliente`),
            KEY `nombre_cliente` (`nombre_cliente`),
            KEY `nro_control` (`nro_control`),
            KEY `rif_fiscal` (`rif_fiscal`(768)),
            KEY `fact_fiscal` (`fact_fiscal`),
            KEY `fecha_emision` (`fecha_emision`),
            KEY `razon_social` (`razon_social`),
            KEY `id_cliente` (`id_cliente`),
            KEY `sucursal_empresa` (`empresa`,`sucursal`),
            KEY `di_ventas` (`id_ventas`),
            KEY `nrofactura_empresa` (`nro_factura`,`empresa`),
            KEY `ic_cliente` (`id_cliente`,`empresa`),
            KEY `id_ventas_empresa_sucursal` (`id_ventas`,`empresa`,`sucursal`),
            KEY `vent_cli` (`id_ventas`,`id_cliente`),
            KEY `idx_venc_vent` (`id_ventas`,`fecha_vencimiento`),
            KEY `descripcion` (`descripcion`(500),`id_ventas`),
            KEY `idx_ventas_resumen_id_fact_digital` (`id_fact_digital`),
            KEY `idx_ventas_resumen_compuesto` (`id_fact_digital`,`corr_fiscal`)
            ) ENGINE=InnoDB AUTO_INCREMENT=704470 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $conn->query($create_clientes_sql);
        echo "✅ Tabla 'ventas_resumen' creada correctamente....";
    } else {
        echo "🛠 La tabla 'clientes' ya existe. Aplicando modificaciones...";
            $alter_clientes_sqls = "ALTER TABLE `ventas_resumen`
            MODIFY COLUMN `nro_factura` VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `corr_fiscal` VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `cod_cliente` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `nombre_cliente` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `direccion` VARCHAR(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `telefono` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `descripcion` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            MODIFY COLUMN `status` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `nro_control` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `ip_estacion` VARCHAR(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `usuario` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `empresa` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `sucursal` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `usr_nivel` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `id_telecomunicaciones` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `rif_fiscal` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `razon_social` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `ciudad` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `fact_fiscal` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            MODIFY COLUMN `id_fact_digital` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            MODIFY COLUMN `usu_anula` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            MODIFY COLUMN `mot_anula` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            MODIFY COLUMN `en_cola` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;";

        foreach ($alter_ventas_resumen_sqls as $sql) {
            $conn->query("ALTER TABLE ventas_resumen $sql");

        echo "✅ Estructura de la tabla 'clientes' actualizada exitosamente...";
    }


1
    echo "✅ ✅ ESTRUCURA TRIGGERS PROCESO CORRECTAMENTE ✅ ✅...";
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("❌ Error de conexión o SQL: " . $e->getMessage());
}
?>