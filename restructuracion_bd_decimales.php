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

    $var_decimal = "DECIMAL(15,2)";

    // --- VENTAS  RESUMEN ---
    $result = $conn->query("SHOW TABLES LIKE 'ventas_resumen'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'ventas_resumen' no existe.  Creando...";
        echo '';
        $create_ventas_resumen_sql = "
                CREATE TABLE `ventas_resumen` (
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
        echo "✅ Tabla 'ventas_resumen' creada correctamente....";
        echo '';
    } else {
        echo "🛠 La tabla 'ventas_resumen' ya existe. Aplicando modificaciones...";
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
            $conn->query("ALTER TABLE ventas_resumen $sql");
        }

        echo "✅ Estructura de la tabla 'ventas_resumen' actualizada exitosamente...";
        echo '';
    }

    // --- VENTAS  DETALLES ---
   $result = $conn->query("SHOW TABLES LIKE 'ventas_detalles'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'ventas_detalles' no existe. Creando...";
        echo '';
        $create_ventas_detalles_sql = "
                CREATE TABLE `ventas_detalles` (
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
        echo "✅ Tabla 'ventas_detalles' creada correctamente....";
        echo '';
    } else {
        echo "🛠 La tabla 'ventas_detalles' ya existe. Aplicando modificaciones...";
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
            "MODIFY COLUMN `cantidad`                       DECIMAL(11,2) NOT NULL",
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
            $conn->query("ALTER TABLE ventas_detalles $sql");
        }
        echo "✅ Estructura de la tabla 'ventas_detalles' actualizada exitosamente...";
        echo '';
    }

// --- VENTAS TRANSACIONES DETALLES ---
   $result = $conn->query("SHOW TABLES LIKE 'ventas_transacciones_detalles'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'ventas_transacciones_detalles' no existe. Creando...";
        echo '';
        $create_ventas_transacciones_detalles_sql = "
                CREATE TABLE `ventas_transacciones_detalles` (
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
        echo "✅ Tabla 'ventas_transacciones_detalles' creada correctamente....";
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
            $conn->query("ALTER TABLE ventas_transacciones_detalles $sql");
        }
        echo "✅ Estructura de la tabla 'ventas_transacciones_detalles' actualizada exitosamente...";
        echo '';
    }

   // --- VENTAS DEVOLUCIONES RESUMEN ---
   $result = $conn->query("SHOW TABLES LIKE 'ventas_devoluciones_resumen'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'ventas_devoluciones_resumen' no existe. Creando...";
        echo '';
        $create_ventas_devoluciones_resumen_sql = "
                CREATE TABLE `ventas_devoluciones_resumen` (
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
        echo "✅ Tabla 'ventas_devoluciones_resumen' creada correctamente....";
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
            $conn->query("ALTER TABLE ventas_devoluciones_resumen $sql");
        }
        echo "✅ Estructura de la tabla 'ventas_devoluciones_resumen' actualizada exitosamente...";
        echo '';
    }

   // --- VENTAS DEVOLUCIONES DETALLES ---
   $result = $conn->query("SHOW TABLES LIKE 'ventas_devoluciones_detalles'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'ventas_devoluciones_detalles' no existe. Creando...";
        echo '';
        $create_ventas_devoluciones_detalles_sql = "
                CREATE TABLE `ventas_devoluciones_detalles` (
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
        echo "✅ Tabla 'ventas_devoluciones_detalles' creada correctamente....";
        echo '';
    } else {
        $alter_ventas_devoluciones_detalles_sqls = [
            // --- Reconfirmación de definiciones para todas las demás columnas ---
            // Incluye CHARSET y COLLATE solo para tipos de cadena (VARCHAR)
            "MODIFY COLUMN `id_ventas_devoluciones_detalles`INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `id_ventas_devoluciones_resumen` INT(11) NOT NULL",
            "MODIFY COLUMN `codigo_producto`                VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `codigo_almacen`                 VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `cantidad`                       DECIMAL(11,2) NOT NULL",
            "MODIFY COLUMN `cantidad_venta`                 DECIMAL(11,2) NOT NULL",
            "MODIFY COLUMN `tipo_unidad`                    VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `precio_unitario`                DECIMAL(11,2) NOT NULL",
            "MODIFY COLUMN `porc_iva`                       VARCHAR(11) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `iva`                            DECIMAL(11,2) NOT NULL",
            "MODIFY COLUMN `sub_total`                      DECIMAL(11,2) NOT NULL",
            "MODIFY COLUMN `total_renglon`                  DECIMAL(11,2) NOT NULL",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `ip_estacion`                    VARCHAR(60) NOT NULL COLLATE utf8mb4_general_ci"
        ];
        foreach ($alter_ventas_devoluciones_detalles_sqls as $sql) {
            $conn->query("ALTER TABLE ventas_devoluciones_detalles $sql");
        }
        echo "✅ Estructura de la tabla 'ventas_devoluciones_detalles' actualizada exitosamente...";
        echo '';
    }


   // --- CXC DOCUMENTOS ---
   $result = $conn->query("SHOW TABLES LIKE 'cxc_documentos'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'cxc_documentos' no existe. Creando...";
        echo '';
        $create_cxc_documentos_sql = "
                CREATE TABLE `cxc_documentos` (
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
        echo "✅ Tabla 'cxc_documentos' creada correctamente....";
        echo '';
    } else {
        $alter_cxc_documentos_sqls = $alter_cxc_documentos_sqls = [
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
            "MODIFY COLUMN `sub_total`                      DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `total_neto`                     DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `saldo`                          DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `tasa_cambio`                    DECIMAL(10,2) NOT NULL",
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
            $conn->query("ALTER TABLE cxc_documentos $sql");
        }
        echo "✅ Estructura de la tabla 'cxc_documentos' actualizada exitosamente...";
        echo '';
    }

    // --- CXC COBRO RESUMEN ---
   $result = $conn->query("SHOW TABLES LIKE 'cxc_cobro_resumen'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'cxc_cobro_resumen' no existe. Creando...";
        echo '';
        $create_cxc_cobro_resumen_sql = "
                CREATE TABLE `cxc_cobro_resumen` (
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
            "MODIFY COLUMN `saldo`                          DECIMAL(10,2) DEFAULT NULL",
            "MODIFY COLUMN `saldo_bs`                       DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `tasa_cambio`                    DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `ip_estacion`                    VARCHAR(20) DEFAULT NULL COLLATE utf8mb4_general_ci"
        ];

        foreach ($alter_cxc_cobro_resumen_sqls as $sql) {
            $conn->query("ALTER TABLE cxc_cobro_resumen $sql");
        }
        echo "✅ Estructura de la tabla 'cxc_cobro_resumen' actualizada exitosamente...";
        echo '';
    }

   // --- CXC COBRO DETALLES ---
   $result = $conn->query("SHOW TABLES LIKE 'cxc_cobro_detalles'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'cxc_cobro_detalles' no existe. Creando...";
        echo '';
        $create_cxc_cobro_detalles_sql = "
                CREATE TABLE `cxc_cobro_detalles` (
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
        echo "✅ Tabla 'cxc_cobro_detalles' creada correctamente....";
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
            "MODIFY COLUMN `total_neto`                     DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `saldo`                          DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `monto`                          DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `tasa`                           DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN `fecha`                          DATE NOT NULL",
            "MODIFY COLUMN `empresa`                        VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `sucursal`                       VARCHAR(20) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `usuario`                        VARCHAR(200) NOT NULL COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN `monto_bs`                       DECIMAL(20,2) DEFAULT NULL"
        ];

        foreach ($alter_cxc_cobro_resumen_sqls as $sql) {
            $conn->query("ALTER TABLE cxc_cobro_detalles $sql");
        }
        echo "✅ Estructura de la tabla 'cxc_cobro_detalles' actualizada exitosamente...";
        echo '';
    }

    echo "✅ ✅ ESTRUCTURA BD PROCESADA CORRECTAMENTE ✅ ✅...";
    echo '';
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("❌ Error de conexión o SQL: " . $e->getMessage());
}
?>