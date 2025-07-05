<?php
// This script restructures the database to ensure all decimal fields are defined correctly.
// Damian Diaz
$host = '127.0.0.1';
$user = 'root';
$pass = 'Gemelas2000#';
$target_db = 'webservices';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass);
    echo "✅ CONEXIÓN AL SERVIDOR EXITOSA";
    echo '';

    $result = $conn->query("SHOW DATABASES LIKE '$target_db'");
    if ($result->num_rows == 0) {
        $conn->query("CREATE DATABASE `$target_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "✅ Base de datos '$target_db' creada exitosamente...";
        echo '';
    } else {
        echo "📦 Base de datos '$target_db' ya existe...";
        echo '';
    }

    $conn->select_db($target_db);

    $var_decimal = "DECIMAL(15,6)";

    // --- VENTAS RESUMEN ---
    $nombre_tabla = 'ventas_resumen';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe.  Creando...";
        echo '';
        $create_ventas_resumen_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_ventas`                                     INT(20) NOT NULL AUTO_INCREMENT,
            `nro_factura`                                   VARCHAR(11) NOT NULL,
            `corr_fiscal`                                   VARCHAR(11) NOT NULL,
            `cod_cliente`                                   VARCHAR(40) NOT NULL,
            `nombre_cliente`                                VARCHAR(200) NOT NULL,
            `direccion`                                     VARCHAR(300) NOT NULL,
            `telefono`                                      VARCHAR(100) NOT NULL,
            `descripcion`                                   mediumtext DEFAULT NULL,
            `fecha_emision`                                 DATETIME NOT NULL,
            `fecha_vencimiento`                             DATE DEFAULT NULL,
            `total_neto`                                    $var_decimal NOT NULL,
            `total_factura`                                 $var_decimal NOT NULL,
            `sub_total`                                     $var_decimal NOT NULL,
            `abono`                                         $var_decimal NOT NULL,
            `saldo`                                         $var_decimal NOT NULL,
            `base_imp`                                      $var_decimal NOT NULL,
            `tasa_iva`                                      $var_decimal NOT NULL,
            `iva`                                           $var_decimal NOT NULL,
            `exento`                                        $var_decimal NOT NULL,
            `status`                                        VARCHAR(50) NOT NULL,
            `descuento`                                     $var_decimal NOT NULL,
            `t_descuento`                                   $var_decimal NOT NULL,
            `cantidad_renglon`                              INT(10) NOT NULL,
            `tasa_cambio`                                   $var_decimal NOT NULL,
            `total_bsd`                                     $var_decimal NOT NULL,
            `total_fact_bsd`                                $var_decimal NOT NULL,
            `nro_control`                                   VARCHAR(10) NOT NULL,
            `fecha`                                         DATETIME NOT NULL,
            `ip_estacion`                                   VARCHAR(60) NOT NULL,
            `usuario`                                       VARCHAR(200) NOT NULL,
            `empresa`                                       VARCHAR(20) NOT NULL,
            `sucursal`                                      VARCHAR(20) NOT NULL,
            `usr_nivel`                                     VARCHAR(20) NOT NULL,
            `id_telecomunicaciones`                         VARCHAR(20) NOT NULL,
            `rif_fiscal`                                    VARCHAR(50) NOT NULL,
            `razon_social`                                  VARCHAR(200) NOT NULL,
            `ciudad`                                        VARCHAR(40) NOT NULL,
            `iva_bs`                                        $var_decimal NOT NULL,
            `sub_total_bs`                                  $var_decimal NOT NULL,
            `fact_fiscal`                                   VARCHAR(20) NOT NULL,
            `id_cliente`                                    INT(11) DEFAULT NULL,
            `id_cliente_hijo`                               INT(11) DEFAULT NULL,
            `vuelto`                                        $var_decimal DEFAULT NULL,
            `cierre`                                        INT(2) DEFAULT NULL,
            `id_cxc_cobro_resumen`                          INT(11) DEFAULT NULL,
            `id_fact_digital`                               VARCHAR(255) DEFAULT NULL,
            `fec_anula`                                     DATETIME DEFAULT NULL,
            `usu_anula`                                     VARCHAR(200) DEFAULT NULL,
            `mot_anula`                                     VARCHAR(500) DEFAULT NULL,
            `exento_bs`                                     $var_decimal DEFAULT NULL,
            `base_imp_bs`                                   $var_decimal DEFAULT NULL,
            `en_cola`                                       VARCHAR(10) DEFAULT NULL,
            `fecha_desde`                                   DATE DEFAULT NULL,
            `fecha_hasta`                                   DATE DEFAULT NULL,
            `fecha_factura_generada`                        DATE DEFAULT NULL,
            PRIMARY KEY (`id_ventas`),
            KEY `nro_factura` (`nro_factura`),
            KEY `corr_fiscal` (`corr_fiscal`),
            KEY `cod_cliente` (`cod_cliente`),
            KEY `nombre_cliente` (`nombre_cliente`),
            KEY `nro_control` (`nro_control`),
            KEY `rif_fiscal` (`rif_fiscal`),
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
            ) ENGINE=InnoDB AUTO_INCREMENT=704470 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_ventas_resumen_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        echo "🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...";
        echo '';
        $alter_ventas_resumen_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR, TEXT, MEDIUMTEXT)
            "MODIFY COLUMN `id_ventas`                      INT(20) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `nro_factura`                    VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `corr_fiscal`                    VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `cod_cliente`                    VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `nombre_cliente`                 VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `direccion`                      TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN `telefono`                       VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN `descripcion`                    MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN `fecha_emision`                  DATETIME NOT NULL",
            "MODIFY COLUMN `fecha_vencimiento`              DATE DEFAULT NULL",
            "MODIFY COLUMN `total_neto`                     $var_decimal NOT NULL",
            "MODIFY COLUMN `total_factura`                  $var_decimal NOT NULL",
            "MODIFY COLUMN `sub_total`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `abono`                          $var_decimal NOT NULL",
            "MODIFY COLUMN `saldo`                          $var_decimal NOT NULL",
            "MODIFY COLUMN `base_imp`                       $var_decimal NOT NULL",
            "MODIFY COLUMN `tasa_iva`                       $var_decimal NOT NULL",
            "MODIFY COLUMN `iva`                            $var_decimal NOT NULL",
            "MODIFY COLUMN `exento`                         $var_decimal NOT NULL",
            "MODIFY COLUMN `status`                         VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `descuento`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `t_descuento`                    $var_decimal NOT NULL",
            "MODIFY COLUMN `cantidad_renglon`               INT(10) NOT NULL",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal NOT NULL",
            "MODIFY COLUMN `total_bsd`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `total_fact_bsd`                 $var_decimal NOT NULL",
            "MODIFY COLUMN `nro_control`                    VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `fecha`                          DATETIME NOT NULL",
            "MODIFY COLUMN `ip_estacion`                    VARCHAR(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `usuario`                        VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `sucursal`                       VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `usr_nivel`                      VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `id_telecomunicaciones`          VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `rif_fiscal`                     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `razon_social`                   VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `ciudad`                         VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `iva_bs`                         $var_decimal NOT NULL",
            "MODIFY COLUMN `sub_total_bs`                   $var_decimal NOT NULL",
            "MODIFY COLUMN `fact_fiscal`                    VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL",
            "MODIFY COLUMN `id_cliente`                     INT(11) DEFAULT NULL",
            "MODIFY COLUMN `id_cliente_hijo`                INT(11) DEFAULT NULL",
            "MODIFY COLUMN `vuelto`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `cierre`                         INT(2) DEFAULT NULL",
            "MODIFY COLUMN `id_cxc_cobro_resumen`           INT(11) DEFAULT NULL",
            "MODIFY COLUMN `id_fact_digital`                VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN `fec_anula`                      DATETIME DEFAULT NULL",
            "MODIFY COLUMN `usu_anula`                      VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN `mot_anula`                      VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN `exento_bs`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `base_imp_bs`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `en_cola`                        VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN `fecha_desde`                    DATE DEFAULT NULL",
            "MODIFY COLUMN `fecha_hasta`                    DATE DEFAULT NULL",
            "MODIFY COLUMN `fecha_factura_generada`         DATE DEFAULT NULL"
        ];

        foreach ($alter_ventas_resumen_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }

        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';  
    }

    // CREANDO TRIGGERS - VENTAS RESUMEN
    // insert_fecha_factura
    $drop_trigger_ventas_resumen_insert_fecha_factura_sql = "DROP TRIGGER IF EXISTS `insert_fecha_factura`";
    $conn->query($drop_trigger_ventas_resumen_insert_fecha_factura_sql);

    $create_trigger_ventas_resumen_insert_fecha_factura_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `insert_fecha_factura` 
    BEFORE INSERT ON `$nombre_tabla` 
    FOR EACH ROW SET NEW.fecha_factura_generada = NEW.fecha_emision";
            
    $conn->query($create_trigger_ventas_resumen_insert_fecha_factura_sql);
    echo "✅ $nombre_tabla - Trigger 'insert_fecha_factura' creado correctamente....";
    echo '';

    // fecha_emision_insert_vent
    $drop_trigger_fecha_emision_insert_vent_sql = "DROP TRIGGER IF EXISTS `fecha_emision_insert_vent`";
    $conn->query($drop_trigger_fecha_emision_insert_vent_sql);

    $create_trigger_fecha_emision_insert_vent_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `fecha_emision_insert_vent` 
    BEFORE INSERT ON `ventas_resumen` 
    FOR EACH ROW IF TIME(NEW.fecha_emision) = '00:00:00' THEN
        SET NEW.fecha_emision = DATE(NEW.fecha_emision) + INTERVAL TIME(NOW()) HOUR_SECOND;
    END IF";
            
    $conn->query($create_trigger_fecha_emision_insert_vent_sql);
    echo "✅ $nombre_tabla - Trigger 'fecha_emision_insert_vent' creado correctamente....";
    echo '';

    // fecha_emis_no_mayor
    $drop_trigger_fecha_emis_no_mayor_sql = "DROP TRIGGER IF EXISTS `fecha_emis_no_mayor`";
    $conn->query($drop_trigger_fecha_emis_no_mayor_sql);

    $create_trigger_fecha_emis_no_mayor_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `fecha_emis_no_mayor` 
    BEFORE INSERT ON `ventas_resumen` FOR EACH ROW BEGIN
    IF DATE_FORMAT(NEW.fecha_emision, '%Y-%m-%d') > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'La fecha de emisión no puede ser mayor a la fecha actual.';
    END IF;
    END";
            
    $conn->query($create_trigger_fecha_emis_no_mayor_sql);
    echo "✅ $nombre_tabla - Trigger 'fecha_emis_no_mayor' creado correctamente....";
    echo '';

    // fecha_emision_no_mayor_edit
    $drop_trigger_fecha_emision_no_mayor_edit_sql = "DROP TRIGGER IF EXISTS `fecha_emision_no_mayor_edit`";
    $conn->query($drop_trigger_fecha_emision_no_mayor_edit_sql);

    $create_trigger_fecha_emision_no_mayor_edit_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `fecha_emision_no_mayor_edit` 
    BEFORE UPDATE ON `ventas_resumen` FOR EACH ROW BEGIN
    /*
        IF new.status = OLD.status THEN 
            IF DATE_FORMAT(NEW.fecha_emision, '%Y-%m-%d') > CURDATE() THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'La fecha de emisión no puede ser mayor a la fecha actual.';
            END IF;
        END IF;
        */
    END";
            
    $conn->query($create_trigger_fecha_emision_no_mayor_edit_sql);
    echo "✅ $nombre_tabla - Trigger 'fecha_emision_no_mayor_edit' creado correctamente....";
    echo '';

    // Actualizar_tasa_cxc
    $drop_trigger_Actualizar_tasa_cxc_sql = "DROP TRIGGER IF EXISTS `Actualizar_tasa_cxc`";
    $conn->query($drop_trigger_Actualizar_tasa_cxc_sql);

    $create_trigger_Actualizar_tasa_cxc_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `Actualizar_tasa_cxc` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
    IF DATE_FORMAT(NEW.fecha_emision, '%Y-%m-%d') > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'La fecha de emisión no puede ser mayor a la fecha actual.';
    END IF;
    END";
    
    $conn->query($create_trigger_Actualizar_tasa_cxc_sql);
    echo "✅ $nombre_tabla - Trigger 'Actualizar_tasa_cxc' creado correctamente....";
    echo '';

    // update_cola_tareas_recalcular_fact
    $drop_trigger_update_cola_tareas_recalcular_fact_sql = "DROP TRIGGER IF EXISTS `update_cola_tareas_recalcular_fact`";
    $conn->query($drop_trigger_update_cola_tareas_recalcular_fact_sql);

    $create_trigger_update_cola_tareas_recalcular_fact_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `update_cola_tareas_recalcular_fact` 
    AFTER UPDATE ON `$nombre_tabla` FOR EACH ROW BEGIN
    IF NEW.status = 'FACTURADO' OR NEW.status = 'ANULADO' OR OLD.saldo <> NEW.saldo THEN
        INSERT INTO cola_tareas (tipo, id_cliente, status, fecha, fecha_culminado, empresa, sucursal, usuario)
        VALUES ('recalcular_saldo',new.id_cliente, 'PENDIENTE', now(), null, new.empresa, new.sucursal,new.usuario);
    END IF;
    END";
        
    $conn->query($create_trigger_update_cola_tareas_recalcular_fact_sql);
    echo "✅ $nombre_tabla - Trigger 'update_cola_tareas_recalcular_fact' creado correctamente....";
    echo '';

    // anular_transaccion
    $drop_trigger_anular_transaccion_sql = "DROP TRIGGER IF EXISTS `anular_transaccion`";
    $conn->query($drop_trigger_anular_transaccion_sql);

    $create_trigger_anular_transaccion_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `anular_transaccion` 
    AFTER UPDATE ON `$nombre_tabla` FOR EACH ROW BEGIN
    IF NEW.status = 'ANULADO'  THEN
            UPDATE ventas_transacciones_detalles SET status = 'ANULADO' WHERE id_ventas_transacciones = OLD.id_ventas;
            UPDATE ventas_detalles SET status = 'ANULADO' WHERE id_detalle = OLD.id_ventas;
            UPDATE cxc_documentos SET estatus = 'ANULADO' WHERE id_ventas = OLD.id_ventas AND tipo_documento = 'FACTURV';
            END IF;
    END";
        
    $conn->query($create_trigger_anular_transaccion_sql);
    echo "✅ Trigger 'anular_transaccion' creado correctamente....";
    echo '';


    // --- VENTAS  DETALLES ---
   $nombre_tabla = 'ventas_detalles';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_ventas_detalles_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_detalle`                                    INT(11) NOT NULL,
            `iten`                                          INT(11) NOT NULL AUTO_INCREMENT,
            `nro_factura`                                   VARCHAR(10) NOT NULL,
            `codigo`                                        VARCHAR(12) NOT NULL,
            `descripcion`                                   LONGTEXT NOT NULL,
            `codigo_almacen`                                VARCHAR(200) DEFAULT NULL,
            `cantidad`                                      $var_decimal NOT NULL,
            `tipo_precio`                                   VARCHAR(20) DEFAULT NULL,
            `tipo_unidad`                                   VARCHAR(20) DEFAULT NULL,
            `precio_unitario`                               $var_decimal NOT NULL,
            `iva`                                           VARCHAR(6) NOT NULL,
            `total_iva`                                     $var_decimal NOT NULL,
            `t_descuento`                                   $var_decimal NOT NULL,
            `descuento`                                     $var_decimal NOT NULL,
            `sub_total`                                     $var_decimal NOT NULL,
            `costo`                                         $var_decimal NOT NULL,
            `tasa_cambio`                                   $var_decimal NOT NULL,
            `status`                                        VARCHAR(20) NOT NULL,
            `total_renglon`                                 $var_decimal NOT NULL,
            `usuario`                                       VARCHAR(200) NOT NULL,
            `empresa`                                       VARCHAR(20) NOT NULL,
            `sucursal`                                      VARCHAR(20) NOT NULL,
            `fecha`                                         DATE NOT NULL,
            `ip_estacion`                                   VARCHAR(60) NOT NULL,
            `id_servicio`                                   INT(11) DEFAULT NULL,
            `precio_unitario_bs`                            $var_decimal DEFAULT NULL,
            `total_iva_bs`                                  $var_decimal DEFAULT NULL,
            `sub_total_bs`                                  $var_decimal DEFAULT NULL,
            `total_renglon_bs`                              $var_decimal DEFAULT NULL,
            PRIMARY KEY (`iten`),
            KEY `item` (`iten`),
            KEY `codigo` (`codigo`),
            KEY `id_detalle` (`id_detalle`),
            KEY `ventas_detalles_id_detalle_IDX` (`id_detalle`) USING BTREE,
            KEY `empresa_sucursal` (`empresa`,`sucursal`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=665942 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_ventas_detalles_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        echo "🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...";
        echo '';

        $alter_ventas_detalles_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR, TEXT, MEDIUMTEXT)
            "MODIFY COLUMN `id_detalle`                     INT(11) NOT NULL",
            "MODIFY COLUMN `iten`                           INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `nro_factura`                    VARCHAR(10) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `codigo`                         VARCHAR(12) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `descripcion`                    LONGTEXT NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `codigo_almacen`                 VARCHAR(200) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cantidad`                       $var_decimal NOT NULL",
            "MODIFY COLUMN `tipo_precio`                    VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tipo_unidad`                    VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `precio_unitario`                $var_decimal NOT NULL",
            "MODIFY COLUMN `iva`                            VARCHAR(6) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `total_iva`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `t_descuento`                    $var_decimal NOT NULL",
            "MODIFY COLUMN `descuento`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `sub_total`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `costo`                          $var_decimal NOT NULL",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal NOT NULL",
            "MODIFY COLUMN `status`                         VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `total_renglon`                  $var_decimal NOT NULL",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `ip_estacion`                    VARCHAR(60) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_servicio`                    INT(11) DEFAULT NULL",
            "MODIFY COLUMN `precio_unitario_bs`             $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `total_iva_bs`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `sub_total_bs`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `total_renglon_bs`               $var_decimal DEFAULT NULL"
        ];

        foreach ($alter_ventas_detalles_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }
        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

// --- VENTAS TRANSACIONES DETALLES ---
   $nombre_tabla = 'ventas_transacciones_detalles';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_ventas_transacciones_detalles_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_ventas_transacciones_detalles`              INT(11) NOT NULL AUTO_INCREMENT,
            `id_ventas_transacciones`                       INT(11) DEFAULT NULL,
            `id_conciliacion`                               INT(11) DEFAULT NULL,
            `tipo_pago`                                     VARCHAR(30) DEFAULT NULL,
            `forma_pago`                                    VARCHAR(40) DEFAULT NULL,
            `tasa_cambio`                                   $var_decimal NOT NULL,
            `origen`                                        VARCHAR(30) NOT NULL,
            `cod_formas_pago`                               INT(6) NOT NULL,
            `referencia`                                    VARCHAR(255) NOT NULL,
            `descripcion`                                   VARCHAR(500) DEFAULT NULL,
            `monto`                                         $var_decimal NOT NULL,
            `monto_bs`                                      $var_decimal NOT NULL,
            `fecha_transaccion`                             DATE NOT NULL,
            `conciliado`                                    VARCHAR(3) DEFAULT NULL,
            `nro_conciliacion`                              VARCHAR(20) DEFAULT NULL,
            `revisado`                                      VARCHAR(3) NOT NULL,
            `status`                                        VARCHAR(10) NOT NULL,
            `tipo_conciliacion`                             VARCHAR(20) NOT NULL,
            `fecha`                                         DATE NOT NULL,
            `empresa`                                       VARCHAR(20) NOT NULL,
            `sucursal`                                      VARCHAR(20) NOT NULL,
            `usuario`                                       VARCHAR(200) NOT NULL,
            `usr_nivel`                                     VARCHAR(20) NOT NULL,
            `ip_estacion`                                   VARCHAR(60) NOT NULL,
            `id_cxc_cobro_resumen`                          INT(20) DEFAULT NULL,
            `id_cxc_documento`                              INT(20) DEFAULT NULL,
            `cierre`                                        INT(2) DEFAULT NULL,
            `numero_INTento`                                INT(4) DEFAULT NULL,
            `id_resumen_nota_entrega`                       INT(11) DEFAULT NULL,
            `monto_abonado`                                 $var_decimal DEFAULT NULL,
            `tipo_movimiento`                               VARCHAR(255) DEFAULT NULL,
            `fecha_aprobacion`                              VARCHAR(255) DEFAULT NULL,
            `usuario_aprobado`                              VARCHAR(255) DEFAULT NULL,
            `pre_revisado`                                  VARCHAR(2) DEFAULT NULL,
            `telefono`                                      VARCHAR(20) DEFAULT NULL,
            `id_banco`                                      INT(11) DEFAULT NULL,
            PRIMARY KEY (`id_ventas_transacciones_detalles`),
            KEY `empresa` (`empresa`),
            KEY `sucursal` (`sucursal`),
            KEY `fecha_transaccion` (`fecha_transaccion`),
            KEY `tipo_pago` (`tipo_pago`),
            KEY `forma_pago` (`forma_pago`),
            KEY `id_cxc_documento` (`id_cxc_documento`),
            KEY `id_cxc_cobro_resumen` (`id_cxc_cobro_resumen`),
            KEY `monto` (`monto`),
            KEY `referencia` (`referencia`),
            KEY `status` (`status`),
            KEY `id_ventas_detalles` (`id_ventas_transacciones_detalles`),
            KEY `fecha` (`fecha`) USING BTREE,
            KEY `tipo_forma` (`tipo_pago`,`forma_pago`,`empresa`,`sucursal`) USING BTREE,
            KEY `idx_ventas_transacciones_detalles_fecha` (`fecha`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1165413 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_ventas_transacciones_detalles_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        $alter_ventas_transacciones_detalles_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR)
            "MODIFY COLUMN `id_ventas_transacciones_detalles`   INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `id_ventas_transacciones`            INT(11) DEFAULT NULL",
            "MODIFY COLUMN `id_conciliacion`                    INT(11) DEFAULT NULL",
            "MODIFY COLUMN `tipo_pago`                          VARCHAR(30) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `forma_pago`                         VARCHAR(40) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tasa_cambio`                        $var_decimal NOT NULL",
            "MODIFY COLUMN `origen`                             VARCHAR(30) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cod_formas_pago`                    INT(6) NOT NULL",
            "MODIFY COLUMN `referencia`                         VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `descripcion`                        VARCHAR(500) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `monto`                              $var_decimal NOT NULL",
            "MODIFY COLUMN `monto_bs`                           $var_decimal NOT NULL",
            "MODIFY COLUMN `fecha_transaccion`                  DATE NOT NULL",
            "MODIFY COLUMN `conciliado`                         VARCHAR(3) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_conciliacion`                   VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `revisado`                           VARCHAR(3) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `status`                             VARCHAR(10) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tipo_conciliacion`                  VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fecha`                              DATE NOT NULL",
            "MODIFY COLUMN `empresa`                            VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                           VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usuario`                            VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usr_nivel`                          VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ip_estacion`                        VARCHAR(60) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_cxc_cobro_resumen`               INT(20) DEFAULT NULL",
            "MODIFY COLUMN `id_cxc_documento`                   INT(20) DEFAULT NULL",
            "MODIFY COLUMN `cierre`                             INT(2) DEFAULT NULL",
            "MODIFY COLUMN `numero_INTento`                     INT(4) DEFAULT NULL",
            "MODIFY COLUMN `id_resumen_nota_entrega`            INT(11) DEFAULT NULL",
            "MODIFY COLUMN `monto_abonado`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `tipo_movimiento`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fecha_aprobacion`                   VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usuario_aprobado`                   VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `pre_revisado`                       VARCHAR(2) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `telefono`                           VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_banco`                           INT(11) DEFAULT NULL"
        ];
        foreach ($alter_ventas_transacciones_detalles_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }
        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

    // CREANDO TRIGGERS - VENTAS TRANSACIONES DETALLES
    // validacion_referencias
    $drop_trigger_validacion_referencias_sql = "DROP TRIGGER IF EXISTS `validacion_referencias`";
    $conn->query($drop_trigger_validacion_referencias_sql);

    $create_trigger_validacion_referencias_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `validacion_referencias` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
    IF BINARY NEW.referencia <> LOWER(NEW.referencia) THEN
        SET NEW.referencia = LOWER(NEW.referencia);
            IF NEW.referencia != '0' THEN
            SET NEW.referencia = TRIM(LEADING '0' FROM NEW.referencia);
        END IF;
    END IF;
    END";
            
    $conn->query($create_trigger_validacion_referencias_sql);
    echo "✅ $nombre_tabla -Trigger 'validacion_referencias' creado correctamente....";
    echo '';

    // validar_fecha_aprobacion
    $drop_trigger_validar_fecha_aprobacion_sql = "DROP TRIGGER IF EXISTS `validar_fecha_aprobacion`";
    $conn->query($drop_trigger_validar_fecha_aprobacion_sql);

    $create_trigger_validar_fecha_aprobacion_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `validar_fecha_aprobacion` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
    IF BINARY NEW.referencia <> LOWER(NEW.referencia) THEN
        SET NEW.referencia = LOWER(NEW.referencia);
            IF NEW.referencia != '0' THEN
            SET NEW.referencia = TRIM(LEADING '0' FROM NEW.referencia);
        END IF;
    END IF;
    END";
            
    $conn->query($create_trigger_validar_fecha_aprobacion_sql);
    echo "✅ $nombre_tabla - Trigger 'validar_fecha_aprobacion' creado correctamente....";
    echo '';

    // insertar_id_banco
    $drop_trigger_insertar_id_banco_sql = "DROP TRIGGER IF EXISTS `insertar_id_banco`";
    $conn->query($drop_trigger_insertar_id_banco_sql);

    $create_trigger_insertar_id_banco_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `insertar_id_banco` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
    IF BINARY NEW.referencia <> LOWER(NEW.referencia) THEN
        SET NEW.referencia = LOWER(NEW.referencia);
            IF NEW.referencia != '0' THEN
            SET NEW.referencia = TRIM(LEADING '0' FROM NEW.referencia);
        END IF;
    END IF;
    END";
            
    $conn->query($create_trigger_insertar_id_banco_sql);
    echo "✅ $nombre_tabla - Trigger 'insertar_id_banco' creado correctamente....";
    echo '';

    // validar_referencia_forma_pago
    $drop_trigger_validar_referencia_forma_pago_sql = "DROP TRIGGER IF EXISTS `validar_referencia_forma_pago`";
    $conn->query($drop_trigger_validar_referencia_forma_pago_sql);

    $create_trigger_validar_referencia_forma_pago_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `validar_referencia_forma_pago` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
    DECLARE existe INT;
    SELECT COUNT(*) INTO existe
    FROM $nombre_tabla
    WHERE referencia = NEW.referencia AND NEW.sucursal = sucursal AND forma_pago = NEW.forma_pago AND 
    empresa = NEW.empresa AND NEW.referencia <> '' AND (status = 'FACTURADO' OR status = 'EN ESPERA') AND NEW.monto = monto;

    IF existe > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Ya existe una fila en la tabla $nombre_tabla con la misma combinación de valores en las
     columnas referencia y forma_pago.';
    END IF;

    END";
            
    $conn->query($create_trigger_validar_referencia_forma_pago_sql);
    echo "✅ $nombre_tabla - Trigger 'validar_referencia_forma_pago' creado correctamente....";
    echo '';
    
    // corr_monto_abonado
    $drop_trigger_corr_monto_abonado_sql = "DROP TRIGGER IF EXISTS `corr_monto_abonado`";
    $conn->query($drop_trigger_corr_monto_abonado_sql);

    $create_trigger_corr_monto_abonado_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `corr_monto_abonado` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
	IF NEW.monto_abonado > NEW.monto THEN 
		SET NEW.monto_abonado = NEW.monto;
	END IF;
    END";
            
    $conn->query($create_trigger_corr_monto_abonado_sql);
    echo "✅ $nombre_tabla - Trigger 'corr_monto_abonado' creado correctamente....";
    echo '';


    // monto_cero_insert
    $drop_trigger_monto_cero_insert_sql = "DROP TRIGGER IF EXISTS `monto_cero_insert`";
    $conn->query($drop_trigger_monto_cero_insert_sql);

    $create_trigger_monto_cero_insert_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `webservices`.`monto_cero_insert` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
    IF NEW.monto < 0.01  AND NEW.monto_bs < 0.01 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El monto no puede ser menor  a 0.01';
    END IF;
    END";
            
    $conn->query($create_trigger_monto_cero_insert_sql);
    echo "✅ $nombre_tabla - Trigger 'monto_cero_insert' creado correctamente....";
    echo '';

    // sincronizar_fecha_documento
    $drop_trigger_sincronizar_fecha_documento_sql = "DROP TRIGGER IF EXISTS `sincronizar_fecha_documento`";
    $conn->query($drop_trigger_sincronizar_fecha_documento_sql);

    $create_trigger_sincronizar_fecha_documento_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `sincronizar_fecha_documento` 
    BEFORE UPDATE ON `$nombre_tabla` FOR EACH ROW BEGIN
	UPDATE cxc_documentos SET fecha_emision = NEW.fecha_transaccion WHERE id_cxc_documentos = OLD.id_cxc_documento AND tipo_documento = 'ADEL';
    END";
            
    $conn->query($create_trigger_sincronizar_fecha_documento_sql);
    echo "✅ $nombre_tabla - Trigger 'sincronizar_fecha_documento' creado correctamente....";
    echo '';

   // monto_cero_udt
    $drop_trigger_monto_cero_udt_sql = "DROP TRIGGER IF EXISTS `monto_cero_udt`";
    $conn->query($drop_trigger_monto_cero_udt_sql);

    $create_trigger_monto_cero_udt_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `monto_cero_udt` 
    BEFORE UPDATE ON `$nombre_tabla` FOR EACH ROW BEGIN
    /*IF NEW.monto < 0.01   AND NEW.monto_bs < 0.01 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El monto no puede ser menor  a 0.01';
    END IF;*/
    END";
            
    $conn->query($create_trigger_monto_cero_udt_sql);
    echo "✅ $nombre_tabla - Trigger 'monto_cero_udt' creado correctamente....";
    echo '';

   // insert_cola_transacciones_banco_venta_insert
    $drop_trigger_insert_cola_transacciones_banco_venta_insert_sql = "DROP TRIGGER IF EXISTS `insert_cola_transacciones_banco_venta_insert`";
    $conn->query($drop_trigger_insert_cola_transacciones_banco_venta_insert_sql);

    $create_trigger_insert_cola_transacciones_banco_venta_insert_sql = "CREATE DEFINER=`scryptcase`@`%` 
    TRIGGER `insert_cola_transacciones_banco_venta_insert` AFTER INSERT ON `$nombre_tabla` 
    FOR EACH ROW BEGIN
        IF NEW.status = 'FACTURADO' AND NEW.tipo_pago <> 'RT' THEN 
            INSERT INTO cola_transacciones_bancos (
                id_relacion,
                origen,
                tipo,
                id_banco,
                monto,
                monto_bs,
                tasa_cambio,
                empresa,
                sucursal,
                status,
                mensaje,
                accion,
                fecha_transaccion,
                fecha_insertado
            ) VALUES (
                NEW.id_ventas_transacciones_detalles, 
                'VENTAS',
                'CREDITO',
                NEW.id_banco,
                NEW.monto,
                NEW.monto_bs,
                NEW.tasa_cambio,
                NEW.empresa,
                NEW.sucursal,
                'PENDIENTE',
                'Sin Procesar',
                'INSERCCION',
                NEW.fecha_transaccion,
                CURDATE()
            );
        END IF;
    END";
            
    $conn->query($create_trigger_insert_cola_transacciones_banco_venta_insert_sql);
    echo "✅ $nombre_tabla - Trigger 'insert_cola_transacciones_banco_venta_insert' creado correctamente....";
    echo '';

      // insert_cola_transacciones_banco_venta_update
    $drop_trigger_insert_insert_cola_transacciones_banco_venta_update_sql = "DROP TRIGGER IF EXISTS `insert_cola_transacciones_banco_venta_update`";
    $conn->query($drop_trigger_insert_insert_cola_transacciones_banco_venta_update_sql);

    $create_trigger_insert_cola_transacciones_banco_venta_update_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `insert_cola_transacciones_banco_venta_update` 
    AFTER UPDATE ON `$nombre_tabla` FOR EACH ROW BEGIN
    -- Insertar registro si el estatus cambia a 'PROCESADO'
    IF NEW.status = 'FACTURADO' AND OLD.status <> 'FACTURADO' AND NEW.tipo_pago <> 'RT' THEN
            INSERT INTO cola_transacciones_bancos (
                id_relacion,
                origen,
                tipo,
                id_banco,
                monto,
                monto_bs,
                tasa_cambio,
                empresa,
                sucursal,
                status,
                mensaje,
                accion,
                fecha_transaccion,
                fecha_insertado
            ) VALUES (
                NEW.id_ventas_transacciones_detalles, 
                'VENTAS',
                'CREDITO',
                NEW.id_banco,
                NEW.monto,
                NEW.monto_bs,
                NEW.tasa_cambio,
                NEW.empresa,
                NEW.sucursal,
                'PENDIENTE',
                'Sin Procesar',
                'INSERCCION',
                NEW.fecha_transaccion,
                CURDATE()
            );
        END IF;

        -- Insertar registro si el estatus cambia a 'ANULADO'
        IF NEW.status = 'ANULADO' AND OLD.status <> 'ANULADO' AND NEW.tipo_pago <> 'RT' THEN
            INSERT INTO cola_transacciones_bancos (
                id_relacion,
                origen,
                tipo,
                id_banco,
                monto,
                monto_bs,
                tasa_cambio,
                empresa,
                sucursal,
                status,
                mensaje,
                accion,
                fecha_transaccion,
                fecha_insertado
            ) VALUES (
                NEW.id_ventas_transacciones_detalles, 
                'VENTAS',
                'DEBITO',
                NEW.id_banco,
                NEW.monto,
                NEW.monto_bs,
                NEW.tasa_cambio,
                NEW.empresa,
                NEW.sucursal,
                'PENDIENTE',
                'Sin Procesar',
                'ANULACION',
                NEW.fecha_transaccion,
                CURDATE()
            );
        END IF;
    END";
            
    $conn->query($create_trigger_insert_cola_transacciones_banco_venta_update_sql);
    echo "✅ $nombre_tabla - Trigger 'insert_cola_transacciones_banco_venta_update' creado correctamente....";
    echo '';


   // --- VENTAS DEVOLUCIONES RESUMEN ---
   $nombre_tabla = 'ventas_devoluciones_resumen';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_ventas_devoluciones_resumen_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_ventas_devoluciones_resumen`                    INT(11) NOT NULL AUTO_INCREMENT,
            `id_ventas`                                         INT(11) NOT NULL,
            `descripcion`                                       LONGTEXT NOT NULL,
            `nro_devolucion`                                    VARCHAR(10) NOT NULL,
            `nro_control`                                       VARCHAR(10) NOT NULL,
            `id_cliente`                                        INT(11) NOT NULL,
            `fecha_emision`                                     timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `fecha_vencimiento`                                 DATE NOT NULL,
            `base_imp`                                          $var_decimal NOT NULL,
            `tasa_iva`                                          $var_decimal DEFAULT NULL,
            `iva`                                               $var_decimal NOT NULL,
            `sub_total`                                         $var_decimal NOT NULL,
            `total_neto`                                        $var_decimal NOT NULL,
            `status`                                            VARCHAR(20) NOT NULL,
            `cantidad_renglon`                                  INT(11) NOT NULL,
            `tasa_cambio`                                       $var_decimal NOT NULL,
            `id_telecomunicaciones`                             VARCHAR(20) NOT NULL,
            `fecha`                                             DATE NOT NULL,
            `usuario`                                           VARCHAR(200) NOT NULL,
            `ip_estacion`                                       VARCHAR(60) NOT NULL,
            `empresa`                                           VARCHAR(20) NOT NULL,
            `sucursal`                                          VARCHAR(20) NOT NULL,
            `cierre`                                            INT(2) DEFAULT NULL,
            `id_dev_token`                                      VARCHAR(255) DEFAULT NULL,
            `exento_bs`                                         $var_decimal DEFAULT NULL,
            `base_imp_bs`                                       $var_decimal DEFAULT NULL,
            `iva_bs`                                            $var_decimal DEFAULT NULL,
            `total_neto_bs`                                     $var_decimal DEFAULT NULL,
            `exento`                                            VARCHAR(13) DEFAULT NULL,
            PRIMARY KEY (`id_ventas_devoluciones_resumen`),
            KEY `id_ventas_devoluciones_resumen` (`id_ventas_devoluciones_resumen`) USING BTREE,
            KEY `id_cli` (`id_cliente`),
            KEY `id_vent` (`id_ventas`),
            KEY `cli_vent` (`id_ventas`,`id_cliente`)
            ) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_ventas_devoluciones_resumen_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {

        $alter_ventas_devoluciones_resumen_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR, LONGTEXT)
            "MODIFY COLUMN `id_ventas_devoluciones_resumen` INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `id_ventas`                      INT(11) NOT NULL",
            "MODIFY COLUMN `descripcion`                    LONGTEXT NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_devolucion`                 VARCHAR(10) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_control`                    VARCHAR(10) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_cliente`                     INT(11) NOT NULL",
            "MODIFY COLUMN `fecha_emision`                  TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()",
            "MODIFY COLUMN `fecha_vencimiento`              DATE NOT NULL",
            "MODIFY COLUMN `base_imp`                       $var_decimal NOT NULL",
            "MODIFY COLUMN `tasa_iva`                       $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `iva`                            $var_decimal NOT NULL",
            "MODIFY COLUMN `sub_total`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `total_neto`                     $var_decimal NOT NULL",
            "MODIFY COLUMN `status`                         VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cantidad_renglon`               INT(11) NOT NULL",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal NOT NULL",
            "MODIFY COLUMN `id_telecomunicaciones`          VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ip_estacion`                    VARCHAR(60) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cierre`                         INT(2) DEFAULT NULL",
            "MODIFY COLUMN `id_dev_token`                   VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `exento_bs`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `base_imp_bs`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `iva_bs`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `total_neto_bs`                  $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento`                         VARCHAR(13) DEFAULT NULL COLLATE utf8mb4_general_ci"
        ];
        foreach ($alter_ventas_devoluciones_resumen_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }
        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

   // --- VENTAS DEVOLUCIONES DETALLES ---
   $nombre_tabla = 'ventas_devoluciones_detalles';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_ventas_devoluciones_detalles_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_ventas_devoluciones_detalles`               INT(11) NOT NULL AUTO_INCREMENT,
            `id_ventas_devoluciones_resumen`                INT(11) NOT NULL,
            `codigo_producto`                               VARCHAR(20) NOT NULL,
            `codigo_almacen`                                VARCHAR(20) NOT NULL,
            `cantidad`                                      $var_decimal NOT NULL,
            `cantidad_venta`                                $var_decimal NOT NULL,
            `tipo_unidad`                                   VARCHAR(20) NOT NULL,
            `precio_unitario`                               $var_decimal NOT NULL,
            `porc_iva`                                      VARCHAR(11) NOT NULL,
            `iva`                                           $var_decimal NOT NULL,
            `sub_total`                                     $var_decimal NOT NULL,
            `total_renglon`                                 $var_decimal NOT NULL,
            `usuario`                                       VARCHAR(200) NOT NULL,
            `empresa`                                       VARCHAR(20) NOT NULL,
            `sucursal`                                      VARCHAR(20) NOT NULL,
            `fecha`                                         DATE NOT NULL,
            `ip_estacion`                                   VARCHAR(60) NOT NULL,
            PRIMARY KEY (`id_ventas_devoluciones_detalles`),
            KEY `id_ventas_devoluciones_detalles` (`id_ventas_devoluciones_detalles`) USING BTREE,
            KEY `codigo_producto` (`codigo_producto`) USING BTREE,
            KEY `empresa` (`empresa`) USING BTREE,
            KEY `sucursal` (`sucursal`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_ventas_devoluciones_detalles_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        $alter_ventas_devoluciones_detalles_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR)
            "MODIFY COLUMN `id_ventas_devoluciones_detalles`INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `id_ventas_devoluciones_resumen` INT(11) NOT NULL",
            "MODIFY COLUMN `codigo_producto`                VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `codigo_almacen`                 VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cantidad`                       $var_decimal NOT NULL",
            "MODIFY COLUMN `cantidad_venta`                 $var_decimal NOT NULL",
            "MODIFY COLUMN `tipo_unidad`                    VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `precio_unitario`                $var_decimal NOT NULL",
            "MODIFY COLUMN `porc_iva`                       VARCHAR(11) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `iva`                            $var_decimal NOT NULL",
            "MODIFY COLUMN `sub_total`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `total_renglon`                  $var_decimal NOT NULL",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `ip_estacion`                    VARCHAR(60) NOT NULL COLLATE utf8mb4_general_ci"
        ];
        foreach ($alter_ventas_devoluciones_detalles_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }
        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }


   // --- CXC DOCUMENTOS ---
   $nombre_tabla = 'cxc_documentos';
   $result = $conn->query("SHOW TABLES LIKE 'cxc_documentos'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_cxc_documentos_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_cxc_documentos`                             INT(10) NOT NULL AUTO_INCREMENT,
            `id_cliente`                                    INT(11) DEFAULT NULL,
            `tipo_documento`                                VARCHAR(20) NOT NULL,
            `numero_documento`                              VARCHAR(20) NOT NULL,
            `nro_fiscal`                                    VARCHAR(20) DEFAULT NULL,
            `nro_control`                                   VARCHAR(20) DEFAULT NULL,
            `descripcion`                                   LONGTEXT NOT NULL,
            `cod_cliente`                                   VARCHAR(20) NOT NULL,
            `sub_total`                                     $var_decimal NOT NULL,
            `total_neto`                                    $var_decimal NOT NULL,
            `saldo`                                         $var_decimal NOT NULL,
            `tasa_cambio`                                   $var_decimal NOT NULL,
            `fecha_emision`                                 DATE NOT NULL,
            `fecha_vencimiento`                             DATE NOT NULL,
            `tipo_documento_afect`                          VARCHAR(20) DEFAULT NULL,
            `numero_documento_afect`                        VARCHAR(20) DEFAULT NULL,
            `estatus`                                       VARCHAR(20) NOT NULL,
            `tipo`                                          VARCHAR(20) NOT NULL,
            `fecha`                                         DATE NOT NULL,
            `empresa`                                       VARCHAR(20) NOT NULL,
            `sucursal`                                      VARCHAR(20) NOT NULL,
            `usuario`                                       VARCHAR(200) NOT NULL,
            `id_servicio`                                   INT(11) DEFAULT NULL,
            `id_ventas`                                     INT(11) DEFAULT NULL,
            PRIMARY KEY (`id_cxc_documentos`) USING BTREE,
            KEY `IDX_cod_cliente` (`cod_cliente`),
            KEY `id_cxc_documentos` (`id_cxc_documentos`),
            KEY `empresa` (`empresa`),
            KEY `sucursal` (`sucursal`),
            KEY `estatus` (`estatus`),
            KEY `id_cliente` (`id_cliente`),
            KEY `numero_documento` (`numero_documento`),
            KEY `tipo_documento` (`tipo_documento`),
            KEY `id_servicio` (`id_servicio`),
            KEY `id_cxc_tipo` (`tipo_documento`,`id_cxc_documentos`),
            KEY `id_cxc_empresa` (`id_cxc_documentos`,`empresa`),
            KEY `fecha_empresa` (`fecha`,`empresa`),
            KEY `cruce` (`tipo_documento`,`saldo`,`estatus`,`id_servicio`,`id_cxc_documentos`) USING BTREE,
            KEY `cxc_documentos_id_cliente_IDX` (`id_cliente`,`estatus`) USING BTREE,
            KEY `saldo_cal_idx` (`id_cxc_documentos`,`tipo_documento`,`id_ventas`),
            KEY `saldo_cal_idx2` (`id_cliente`,`saldo`),
            KEY `id_status_tip` (`id_cliente`,`estatus`,`tipo`),
            KEY `tip_emp_suc` (`tipo_documento`,`empresa`,`sucursal`,`id_cliente`,`estatus`) USING BTREE,
            KEY `idx_cxc_documentos_tipo` (`id_cxc_documentos`,`tipo_documento`,`estatus`),
            KEY `cxc_venta_tipo` (`id_cxc_documentos`,`tipo_documento`,`id_ventas`),
            KEY `idx_cxc_empresa_sucursal_tipo_doc` (`id_cxc_documentos`,`tipo_documento`,`empresa`,`sucursal`,`id_servicio`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1016057 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_cxc_documentos_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        $alter_cxc_documentos_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR, LONGTEXT)
            "MODIFY COLUMN `id_cxc_documentos`              INT(10) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `id_cliente`                     INT(11) DEFAULT NULL",
            "MODIFY COLUMN `tipo_documento`                 VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `numero_documento`               VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_fiscal`                     VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_control`                    VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `descripcion`                    LONGTEXT NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cod_cliente`                    VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sub_total`                      $var_decimal NOT NULL",
            "MODIFY COLUMN `total_neto`                     $var_decimal NOT NULL",
            "MODIFY COLUMN `saldo`                          $var_decimal NOT NULL",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal NOT NULL",
            "MODIFY COLUMN `fecha_emision`                  DATE NOT NULL",
            "MODIFY COLUMN `fecha_vencimiento`              DATE NOT NULL",
            "MODIFY COLUMN `tipo_documento_afect`           VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `numero_documento_afect`         VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `estatus`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tipo`                           VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_servicio`                    INT(11) DEFAULT NULL",
            "MODIFY COLUMN `id_ventas`                      INT(11) DEFAULT NULL"
        ];
        foreach ($alter_cxc_documentos_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }
        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

    // CREANDO TRIGGERS - CXC DOCUMENTOS
    // id_ventas_insrt
    $drop_trigger_id_ventas_insrt_sql = "DROP TRIGGER IF EXISTS `id_ventas_insrt`";
    $conn->query($drop_trigger_id_ventas_insrt_sql);

    $create_trigger_id_ventas_insrt_sql = "CREATE DEFINER=`scryptcase`@`%` TRIGGER `id_ventas_insrt` 
    BEFORE INSERT ON `$nombre_tabla` FOR EACH ROW BEGIN
    DECLARE existe INT;
    IF NEW.tipo_documento = 'FACTURV' THEN
        SELECT COUNT(*) INTO existe
        FROM cxc_documentos
        WHERE id_ventas = NEW.id_ventas AND tipo_documento = 'FACTURV' AND id_ventas > 0;

        IF existe > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe la factura en documentos';
        END IF;
    END IF;
    END";
            
    $conn->query($create_trigger_id_ventas_insrt_sql);
    echo "✅ $nombre_tabla - Trigger 'id_ventas_insrt' creado correctamente....";
    echo '';


    // --- CXC COBRO RESUMEN ---
    $nombre_tabla = 'cxc_cobro_resumen';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_cxc_cobro_resumen_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_cxc_cobro_resumen`                          INT(20) NOT NULL AUTO_INCREMENT,
            `numero_documento`                              VARCHAR(50) NOT NULL,
            `cod_cliente`                                   VARCHAR(20) NOT NULL,
            `id_cliente`                                    INT(11) DEFAULT NULL,
            `fecha_emision`                                 DATE NOT NULL,
            `descripcion`                                   VARCHAR(100) NOT NULL,
            `estatus`                                       VARCHAR(20) NOT NULL,
            `tipo`                                          VARCHAR(20) NOT NULL,
            `saldo`                                         $var_decimal DEFAULT NULL,
            `saldo_bs`                                      $var_decimal NOT NULL,
            `tasa_cambio`                                   $var_decimal NOT NULL,
            `fecha`                                         DATE NOT NULL,
            `empresa`                                       VARCHAR(20) NOT NULL,
            `sucursal`                                      VARCHAR(20) NOT NULL,
            `usuario`                                       VARCHAR(200) NOT NULL,
            `ip_estacion`                                   VARCHAR(20) DEFAULT NULL,
            PRIMARY KEY (`id_cxc_cobro_resumen`),
            KEY `resumen` (`id_cxc_cobro_resumen`),
            KEY `cruce` (`id_cxc_cobro_resumen`,`estatus`),
            KEY `cxc_cobro_resumen_id_cxc_cobro_resumen_IDX` (`id_cxc_cobro_resumen`,`id_cliente`) USING BTREE,
            KEY `cxc_cobro_resumen_fecha_IDX` (`fecha`,`empresa`) USING BTREE,
            KEY `saldo_cal_idx` (`estatus`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1718292 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_cxc_cobro_resumen_sql);
        echo "✅ Tabla 'cxc_cobro_resumen' creada correctamente....";
        echo '';
    } else {
        $alter_cxc_cobro_resumen_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR)
            "MODIFY COLUMN `id_cxc_cobro_resumen`           INT(20) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `numero_documento`               VARCHAR(50) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cod_cliente`                    VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_cliente`                     INT(11) DEFAULT NULL",
            "MODIFY COLUMN `fecha_emision`                  DATE NOT NULL",
            "MODIFY COLUMN `descripcion`                    VARCHAR(100) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `estatus`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tipo`                           VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `saldo`                          $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `saldo_bs`                       $var_decimal NOT NULL",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal NOT NULL",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ip_estacion`                    VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci"
        ];

        foreach ($alter_cxc_cobro_resumen_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }
        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

   // --- CXC COBRO DETALLES ---
   $nombre_tabla = 'cxc_cobro_detalles';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_cxc_cobro_detalles_sql = "
                CREATE TABLE `$nombre_tabla` (
            `id_cxc_cobro_detalles`                         INT(20) NOT NULL AUTO_INCREMENT,
            `id_cxc_cobro_resumen`                          INT(20) NOT NULL,
            `id_cxc_documento`                              INT(20) NOT NULL,
            `cod_cliente`                                   VARCHAR(20) NOT NULL,
            `tipo_documento`                                VARCHAR(20) NOT NULL,
            `numero_documento`                              VARCHAR(20) NOT NULL,
            `estatus`                                       VARCHAR(20) NOT NULL,
            `total_neto`                                    $var_decimal NOT NULL,
            `saldo`                                         $var_decimal NOT NULL,
            `monto`                                         $var_decimal NOT NULL,
            `tasa`                                          $var_decimal NOT NULL,
            `fecha`                                         DATE NOT NULL,
            `empresa`                                       VARCHAR(20) NOT NULL,
            `sucursal`                                      VARCHAR(20) NOT NULL,
            `usuario`                                       VARCHAR(200) NOT NULL,
            `monto_bs`                                      $var_decimal DEFAULT NULL,
            PRIMARY KEY (`id_cxc_cobro_detalles`),
            KEY `resumen` (`id_cxc_cobro_resumen`),
            KEY `cxc_cobro_detalles_id_cxc_documento_IDX` (`id_cxc_documento`,`estatus`) USING BTREE,
            KEY `cxc_cobro_detalles_id_cxc_cobro_resumen_IDX` (`id_cxc_cobro_resumen`,`estatus`,`empresa`) USING BTREE,
            KEY `id_cxc_doc` (`id_cxc_documento`),
            KEY `saldo_cal` (`id_cxc_cobro_resumen`,`id_cxc_documento`),
            KEY `status` (`estatus`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3446948 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_cxc_cobro_detalles_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {      
        $alter_cxc_cobro_detalles_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR)
            "MODIFY COLUMN `id_cxc_cobro_detalles`          INT(20) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `id_cxc_cobro_resumen`           INT(20) NOT NULL",
            "MODIFY COLUMN `id_cxc_documento`               INT(20) NOT NULL",
            "MODIFY COLUMN `cod_cliente`                    VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tipo_documento`                 VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `numero_documento`               VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `estatus`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `total_neto`                     $var_decimal NOT NULL",
            "MODIFY COLUMN `saldo`                          $var_decimal NOT NULL",
            "MODIFY COLUMN `monto`                          $var_decimal NOT NULL",
            "MODIFY COLUMN `tasa`                           $var_decimal NOT NULL",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `monto_bs`                       $var_decimal DEFAULT NULL"
        ];

        foreach ($alter_cxc_cobro_detalles_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }
        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

   // --- RepLibroVentas ---
   $nombre_tabla = 'RepLibroVentas';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_RepLibroVentas_sql = "
                CREATE TABLE `$nombre_tabla` (
            `tipo_doc`                                      VARCHAR(255) DEFAULT NULL,
            `id_ventas`                                     INT(11) DEFAULT NULL,
            `fecha_emision`                                 DATE DEFAULT NULL,
            `nro_factura`                                   VARCHAR(255) DEFAULT NULL,
            `nro_control`                                   VARCHAR(255) DEFAULT NULL,
            `cod_cliente`                                   VARCHAR(255) DEFAULT NULL,
            `nombre_cliente`                                VARCHAR(255) DEFAULT NULL,
            `no_no_debito`                                  VARCHAR(255) DEFAULT NULL,
            `no_no_credito`                                 VARCHAR(255) DEFAULT NULL,
            `no_fac_afec`                                   VARCHAR(255) DEFAULT NULL,
            `ventas_c_iva`                                  $var_decimal DEFAULT NULL,
            `ventas_no_gra`                                 VARCHAR(255) DEFAULT NULL,
            `co_base_imponible_bs`                          $var_decimal DEFAULT NULL,
            `co_tasa_iva_bs`                                $var_decimal DEFAULT NULL,
            `co_impuesto_bs`                                $var_decimal DEFAULT NULL,
            `nco_base_imponible_bs`                         $var_decimal DEFAULT NULL,
            `nco_tasa_iva_bs`                               $var_decimal DEFAULT NULL,
            `nco_impuesto_bs`                               $var_decimal DEFAULT NULL,
            `co_base_imponible`                             $var_decimal DEFAULT NULL,
            `co_impuesto`                                   $var_decimal DEFAULT NULL,
            `nco_base_imponible`                            $var_decimal DEFAULT NULL,
            `nco_impuesto`                                  $var_decimal DEFAULT NULL,
            `tasa_cambio`                                   $var_decimal DEFAULT NULL,
            `exento`                                        $var_decimal DEFAULT NULL,
            `exento_bs`                                     $var_decimal DEFAULT NULL,
            `direccion`                                     VARCHAR(255) DEFAULT NULL,
            `telefono`                                      VARCHAR(255) DEFAULT NULL,
            `fact_fiscal`                                   VARCHAR(255) DEFAULT NULL,
            `empresa`                                       VARCHAR(255) DEFAULT NULL,
            `sucursal`                                      VARCHAR(255) DEFAULT NULL,
            `corr_fiscal`                                   VARCHAR(255) DEFAULT NULL,
            `no_com_ret`                                    VARCHAR(255) DEFAULT NULL,
            `ret_fac_afec`                                  VARCHAR(255) DEFAULT NULL,
            `iva_rete`                                      INT(11) DEFAULT NULL,
            `ciudad_cliente`                                VARCHAR(255) DEFAULT NULL,
            `ciudad_servicio`                               VARCHAR(255) DEFAULT NULL,
            `estado`                                        VARCHAR(255) DEFAULT NULL,
            `status`                                        VARCHAR(255) DEFAULT NULL,
            `fact_digital`                                  VARCHAR(255) DEFAULT NULL,
            `nro_retencion`                                 VARCHAR(255) DEFAULT NULL,
            `monto_retencion`                               $var_decimal DEFAULT NULL,
            `comision_retencion`                            VARCHAR(255) DEFAULT NULL,
            `id`                                            INT(11) NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=265667 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_RepLibroVentas_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        $alter_replibroventas_sqls = [
            "MODIFY COLUMN `tipo_doc`                       VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_ventas`                      INT(11) DEFAULT NULL",
            "MODIFY COLUMN `fecha_emision`                  DATE DEFAULT NULL",
            "MODIFY COLUMN `nro_factura`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_control`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cod_cliente`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nombre_cliente`                 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_no_debito`                   VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_no_credito`                  VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_fac_afec`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ventas_c_iva`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `ventas_no_gra`                  VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `co_base_imponible_bs`           $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_tasa_iva_bs`                 $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_impuesto_bs`                 $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_base_imponible_bs`          $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_tasa_iva_bs`                $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_impuesto_bs`                $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_base_imponible`              $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_impuesto`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_base_imponible`             $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_impuesto`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento_bs`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `direccion`                      VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `telefono`                       VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fact_fiscal`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `empresa`                        VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `corr_fiscal`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_com_ret`                     VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ret_fac_afec`                   VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `iva_rete`                       INT(11) DEFAULT NULL",
            "MODIFY COLUMN `ciudad_cliente`                 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ciudad_servicio`                VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `estado`                         VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `status`                         VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fact_digital`                   VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_retencion`                  VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `monto_retencion`                $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `comision_retencion`             VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id`                             INT(11) NOT NULL AUTO_INCREMENT"
        ];
        foreach ($alter_replibroventas_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }

        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

  // --- RepLibroVentasCiudadesDetalle ---
   $nombre_tabla = 'RepLibroVentasCiudadesDetalle';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_RepLibroVentasCiudadesDetalle_sql = "
                CREATE TABLE `$nombre_tabla` (
            `fecha_emision`                                 DATE DEFAULT NULL,
            `ciudad_cliente`                                VARCHAR(20) DEFAULT NULL,
            `corr_fiscal`                                   VARCHAR(11) DEFAULT NULL,
            `nro_factura`                                   VARCHAR(11) DEFAULT NULL,
            `nro_control`                                   VARCHAR(10) DEFAULT NULL,
            `cod_cliente`                                   VARCHAR(40) DEFAULT NULL,
            `nombre_cliente`                                VARCHAR(200) DEFAULT NULL,
            `direccion`                                     VARCHAR(300) DEFAULT NULL,
            `total_fact_bsd`                                $var_decimal DEFAULT NULL,
            `sub_total_bs`                                  $var_decimal DEFAULT NULL,
            `iva_bs`                                        $var_decimal DEFAULT NULL,
            `sub_total`                                     $var_decimal DEFAULT NULL,
            `iva`                                           $var_decimal DEFAULT NULL,
            `total_factura`                                 $var_decimal DEFAULT NULL,
            `exento`                                        $var_decimal DEFAULT NULL,
            `exento_bs`                                     $var_decimal DEFAULT NULL,
            `empresa`                                       VARCHAR(20) DEFAULT NULL,
            `sucursal`                                      VARCHAR(20) DEFAULT NULL,
            `ciudad_servicio`                               VARCHAR(20) DEFAULT NULL,
            `estado`                                        VARCHAR(20) DEFAULT NULL,
            `fact_fiscal`                                   VARCHAR(20) DEFAULT NULL,
            `status`                                        VARCHAR(50) DEFAULT NULL,
            `tasa_cambio`                                   $var_decimal DEFAULT NULL,
            `id`                                            INT(11) NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=462920 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_RepLibroVentasCiudadesDetalle_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        $alter_RepLibroVentasCiudadesDetalle_sqls = [
            "MODIFY COLUMN `fecha_emision`                  DATE DEFAULT NULL",
            "MODIFY COLUMN `ciudad_cliente`                 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `corr_fiscal`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_factura`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_control`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cod_cliente`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nombre_cliente`                 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `direccion`                      VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `total_fact_bsd`                 $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `sub_total_bs`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `iva_bs`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `sub_total`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `iva`                            $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `total_factura`                  $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento_bs`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ciudad_servicio`                VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `estado`                         VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fact_fiscal`                    VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `status`                         VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `id`                             INT(11) NOT NULL AUTO_INCREMENT"
        ];

        foreach ($alter_RepLibroVentasCiudadesDetalle_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }

        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

  // --- RepLibroVentasCiudadesResumen ---
    $nombre_tabla = 'RepLibroVentasCiudadesResumen';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_RepLibroVentasCiudadesResumen_sql = "
                CREATE TABLE `$nombre_tabla` (
            `fecha_emision`                                 date DEFAULT NULL,
            `ciudad_cliente`                                VARCHAR(20) DEFAULT NULL,
            `total_fact_bsd`                                $var_decimal DEFAULT NULL,
            `sub_total_bs`                                  $var_decimal DEFAULT NULL,
            `iva_bs`                                        $var_decimal DEFAULT NULL,
            `sub_total`                                     $var_decimal DEFAULT NULL,
            `iva`                                           $var_decimal DEFAULT NULL,
            `total_factura`                                 $var_decimal DEFAULT NULL,
            `exento`                                        $var_decimal DEFAULT NULL,
            `exento_bs`                                     decimal(25,4) DEFAULT NULL,
            `empresa`                                       VARCHAR(20) DEFAULT NULL,
            `sucursal`                                      VARCHAR(20) DEFAULT NULL,
            `corr_fiscal`                                   VARCHAR(11) DEFAULT NULL,
            `ciudad_servicio`                               VARCHAR(20) DEFAULT NULL,
            `estado`                                        VARCHAR(20) DEFAULT NULL,
            `fact_fiscal`                                   VARCHAR(20) DEFAULT NULL,
            `status`                                        VARCHAR(50) DEFAULT NULL,
            `tasa_cambio`                                   $var_decimal DEFAULT NULL,
            `id`                                            INT(11) NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=462917 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_RepLibroVentasCiudadesResumen_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        $alter_RepLibroVentasCiudadesResumen_sqls = [
            // --- Reconfirmación de definiciones para las columnas de la tabla YourTableName ---
            "MODIFY COLUMN `fecha_emision`                  DATE DEFAULT NULL",
            "MODIFY COLUMN `ciudad_cliente`                 VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `total_fact_bsd`                 $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `sub_total_bs`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `iva_bs`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `sub_total`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `iva`                            $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `total_factura`                  $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento_bs`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `corr_fiscal`                    VARCHAR(11) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ciudad_servicio`                VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `estado`                         VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fact_fiscal`                    VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `status`                         VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `id`                             INT(11) NOT NULL AUTO_INCREMENT"
        ];

        foreach ($alter_RepLibroVentasCiudadesResumen_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }

        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

   // --- RepLibroVentasResumen ---
   $nombre_tabla = 'RepLibroVentasResumen';
   $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '';
        $create_RepLibroVentasResumen_sql = "
                CREATE TABLE `$nombre_tabla` (
            `tipo_doc`                                      VARCHAR(3) DEFAULT NULL,
            `id_ventas`                                     INT(11) DEFAULT NULL,
            `fecha_emision`                                 date DEFAULT NULL,
            `nro_factura`                                   VARCHAR(11) DEFAULT NULL,
            `nro_control`                                   VARCHAR(10) DEFAULT NULL,
            `cod_cliente`                                   VARCHAR(40) DEFAULT NULL,
            `nombre_cliente`                                VARCHAR(200) DEFAULT NULL,
            `no_no_debito`                                  VARCHAR(1) DEFAULT NULL,
            `no_no_credito`                                 VARCHAR(10) DEFAULT NULL,
            `no_fac_afec`                                   VARCHAR(11) DEFAULT NULL,
            `ventas_c_iva`                                  $var_decimal DEFAULT NULL,
            `ventas_no_gra`                                 VARCHAR(1) DEFAULT NULL,
            `co_base_imponible_bs`                          $var_decimal DEFAULT NULL,
            `co_tasa_iva_bs`                                $var_decimal DEFAULT NULL,
            `co_impuesto_bs`                                $var_decimal DEFAULT NULL,
            `nco_base_imponible_bs`                         $var_decimal DEFAULT NULL,
            `nco_tasa_iva_bs`                               $var_decimal DEFAULT NULL,
            `nco_impuesto_bs`                               $var_decimal DEFAULT NULL,
            `co_base_imponible`                             $var_decimal DEFAULT NULL,
            `co_impuesto`                                   $var_decimal DEFAULT NULL,
            `nco_base_imponible`                            $var_decimal DEFAULT NULL,
            `nco_impuesto`                                  $var_decimal DEFAULT NULL,
            `tasa_cambio`                                   $var_decimal DEFAULT NULL,
            `exento`                                        $var_decimal DEFAULT NULL,
            `exento_bs`                                     $var_decimal DEFAULT NULL,
            `direccion`                                     VARCHAR(300) DEFAULT NULL,
            `telefono`                                      VARCHAR(100) DEFAULT NULL,
            `fact_fiscal`                                   VARCHAR(20) DEFAULT NULL,
            `empresa`                                       VARCHAR(20) DEFAULT NULL,
            `sucursal`                                      VARCHAR(20) DEFAULT NULL,
            `corr_fiscal`                                   VARCHAR(11) DEFAULT NULL,
            `no_com_ret`                                    VARCHAR(1) DEFAULT NULL,
            `ret_fac_afec`                                  VARCHAR(1) DEFAULT NULL,
            `iva_rete`                                      INT(11) DEFAULT NULL,
            `ciudad_cliente`                                VARCHAR(20) DEFAULT NULL,
            `ciudad_servicio`                               VARCHAR(20) DEFAULT NULL,
            `estado`                                        VARCHAR(20) DEFAULT NULL,
            `status`                                        VARCHAR(50) DEFAULT NULL,
            `fact_digital`                                  VARCHAR(2) DEFAULT NULL,
            `id`                                            INT(11) NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=264675 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $conn->query($create_RepLibroVentasResumen_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente....";
        echo '';
    } else {
        $alter_RepLibroVentasResumen_sqls = [
            // --- Reconfirmación of column definitions for YourTableName ---
            "MODIFY COLUMN `tipo_doc`                       VARCHAR(3) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id_ventas`                      INT(11) DEFAULT NULL",
            "MODIFY COLUMN `fecha_emision`                  DATE DEFAULT NULL",
            "MODIFY COLUMN `nro_factura`                    VARCHAR(11) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nro_control`                    VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cod_cliente`                    VARCHAR(40) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `nombre_cliente`                 VARCHAR(200) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_no_debito`                   VARCHAR(1) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_no_credito`                  VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_fac_afec`                    VARCHAR(11) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ventas_c_iva`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `ventas_no_gra`                  VARCHAR(1) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `co_base_imponible_bs`           $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_tasa_iva_bs`                 $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_impuesto_bs`                 $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_base_imponible_bs`          $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_tasa_iva_bs`                $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_impuesto_bs`                $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_base_imponible`              $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `co_impuesto`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_base_imponible`             $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `nco_impuesto`                   $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `tasa_cambio`                    $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento`                         $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `exento_bs`                      $var_decimal DEFAULT NULL",
            "MODIFY COLUMN `direccion`                      VARCHAR(300) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `telefono`                       VARCHAR(100) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fact_fiscal`                    VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `corr_fiscal`                    VARCHAR(11) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `no_com_ret`                     VARCHAR(1) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ret_fac_afec`                   VARCHAR(1) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `iva_rete`                       INT(11) DEFAULT NULL",
            "MODIFY COLUMN `ciudad_cliente`                 VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ciudad_servicio`                VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `estado`                         VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `status`                         VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fact_digital`                   VARCHAR(2) DEFAULT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `id`                             INT(11) NOT NULL AUTO_INCREMENT"
        ];

        foreach ($alter_RepLibroVentasResumen_sqls as $sql) {
            $conn->query("ALTER TABLE $nombre_tabla $sql");
        }

        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente...";
        echo '';
    }

    echo "✅ ✅ ESTRUCTURA BD PROCESADA CORRECTAMENTE ✅ ✅...";
    echo '';
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("❌ Error de conexión o SQL: " . $e->getMessage());
}
?>