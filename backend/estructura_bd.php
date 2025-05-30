<?php
$host = '10.10.10.114';
$user = 'remote';
$pass = 'Mt*1329*--1';
$target_db = 'ddiazbd';

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
    $result = $conn->query("SHOW TABLES LIKE 'clientes'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'clientes' no existe. Creando...";

        $create_clientes_sql = "
            CREATE TABLE clientes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ci_rif VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                nombre_razon_social VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                email VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                telefono VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                direccion TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                empresa VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                sucursal VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                usuario VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                fec_reg DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_clientes_sql);
        echo "✅ Tabla 'clientes' creada correctamente....";
    } else {
        echo "🛠 La tabla 'clientes' ya existe. Aplicando modificaciones...";

        $alter_clientes_sqls = [
            "MODIFY COLUMN ci_rif VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN nombre_razon_social VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN email VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN telefono VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN direccion TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL",
            "MODIFY COLUMN empresa VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN sucursal VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN usuario VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci",
            "MODIFY COLUMN fec_reg DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($alter_clientes_sqls as $sql) {
            $conn->query("ALTER TABLE clientes $sql");
        }

        echo "✅ Estructura de la tabla 'clientes' actualizada exitosamente...";
    }

    // --- PRODUCTOS ---
    $result = $conn->query("SHOW TABLES LIKE 'productos'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'productos' no existe. Creando...";

        $create_productos_sql = "
            CREATE TABLE productos (
                id INT(11) NOT NULL AUTO_INCREMENT,
                codigo VARCHAR(50) NOT NULL,
                nombre VARCHAR(100) NOT NULL,
                descripcion TEXT DEFAULT NULL,
                costo DECIMAL(10,2) NOT NULL,
                precio DECIMAL(10,2) NOT NULL,
                impuesto DECIMAL(10,2) NOT NULL,
                stock INT(11) NOT NULL DEFAULT 0,
                empresa VARCHAR(50) NOT NULL,
                sucursal VARCHAR(50) NOT NULL,
                usuario VARCHAR(50) NOT NULL,
                fec_reg DATETIME NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_productos_sql);
        echo "✅ Tabla 'productos' creada correctamente<br>";
    } else {
        echo "🛠 La tabla 'productos' ya existe. Aplicando modificaciones...";

        $alter_productos_sqls = [
            "MODIFY COLUMN codigo VARCHAR(50) NOT NULL",
            "MODIFY COLUMN nombre VARCHAR(100) NOT NULL",
            "MODIFY COLUMN descripcion TEXT DEFAULT NULL",
            "MODIFY COLUMN costo DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN precio DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN impuesto DECIMAL(10,2) NOT NULL",
            "MODIFY COLUMN stock INT(11) NOT NULL DEFAULT 0",
            "MODIFY COLUMN empresa VARCHAR(50) NOT NULL",
            "MODIFY COLUMN sucursal VARCHAR(50) NOT NULL",
            "MODIFY COLUMN usuario VARCHAR(50) NOT NULL",
            "MODIFY COLUMN fec_reg DATETIME NOT NULL DEFAULT current_timestamp()"
        ];

        foreach ($alter_productos_sqls as $sql) {
            $conn->query("ALTER TABLE productos $sql");
        }

        echo "✅ Estructura de la tabla 'productos' actualizada exitosamente...";
    }

     // --- CORRELATIVOS ---
    $result = $conn->query("SHOW TABLES LIKE 'correlativos'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'correlativos' no existe. Creando.....";

        $create_table_sql = "
            CREATE TABLE correlativos (
                id INT(11) NOT NULL AUTO_INCREMENT,
                tipo_documento ENUM('FA','ND','NC','CT','FC') NOT NULL,
                serie VARCHAR(255) NOT NULL,
                ultimo_documento INT(11) NOT NULL DEFAULT 0,
                ultimo_control VARCHAR(11) DEFAULT NULL,
                empresa VARCHAR(50) NOT NULL,
                usuario VARCHAR(50) NOT NULL,
                fecha_reg DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uc_tipo_serie_empresa (tipo_documento, serie, empresa)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_table_sql);
        echo "✅ Tabla 'correlativos' creada correctamente<br>";
    } else {
        echo "🛠 La tabla 'correlativos' ya existe. Aplicando modificaciones...";

        $alter_sqls = [
            "MODIFY COLUMN tipo_documento ENUM('FA','ND','NC','CT','FC') NOT NULL",
            "MODIFY COLUMN serie VARCHAR(255) NOT NULL",
            "MODIFY COLUMN ultimo_documento INT(11) NOT NULL DEFAULT 0",
            "MODIFY COLUMN ultimo_control VARCHAR(11) DEFAULT NULL",
            "MODIFY COLUMN empresa VARCHAR(50) NOT NULL",
            "MODIFY COLUMN usuario VARCHAR(50) NOT NULL",
            "MODIFY COLUMN fecha_reg DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "DROP INDEX uc_tipo_serie_empresa",
            "ADD UNIQUE KEY uc_tipo_serie_empresa (tipo_documento, serie, empresa)"
        ];

        foreach ($alter_sqls as $sql) {
            try {
                $conn->query("ALTER TABLE correlativos $sql");
            } catch (mysqli_sql_exception $e) {
                // Ignore errors for DROP INDEX if it doesn't exist
                if (strpos($sql, 'DROP INDEX') === false) {
                    throw $e;
                }
            }
        }

        echo "✅ Estructura de la tabla 'correlativos' actualizada exitosamente...";
    }
    // --- TIPO_DOCUMENTO ---
    $result = $conn->query("SHOW TABLES LIKE 'tipo_documento'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'tipo_documento' no existe. Creando...";

        $create_tipo_documento_sql = "
            CREATE TABLE tipo_documento (
                id_tipo_documento INT(11) NOT NULL AUTO_INCREMENT,
                tipo_documento VARCHAR(10) NOT NULL,
                desc_tipo_documento VARCHAR(255) DEFAULT NULL,
                tipo_movimiento VARCHAR(10) DEFAULT NULL,
                feg_reg DATETIME NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (id_tipo_documento),
                UNIQUE KEY tipo_documento (tipo_documento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_tipo_documento_sql);
        echo "✅ Tabla 'tipo_documento' creada correctamente<br>";
    } else {
        echo "🛠 La tabla 'tipo_documento' ya existe. Aplicando modificaciones...";

        $alter_tipo_documento_sqls = [
            "MODIFY COLUMN tipo_documento VARCHAR(10) NOT NULL",
            "MODIFY COLUMN desc_tipo_documento VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN tipo_movimiento VARCHAR(10) DEFAULT NULL",
            "MODIFY COLUMN feg_reg DATETIME NOT NULL DEFAULT current_timestamp()"
        ];

        foreach ($alter_tipo_documento_sqls as $sql) {
            $conn->query("ALTER TABLE tipo_documento $sql");
        }

        // Verifica si el índice único ya existe antes de crearlo
        $indexExists = false;
        $res = $conn->query("SHOW INDEX FROM tipo_documento WHERE Key_name = 'tipo_documento'");
        if ($res && $res->num_rows > 0) {
            $indexExists = true;
        }
        if (!$indexExists) {
            $conn->query("ALTER TABLE tipo_documento ADD UNIQUE KEY tipo_documento (tipo_documento)");
        }

        echo "✅ Estructura de la tabla 'tipo_documento' actualizada exitosamente...";
    }

    // --- DOCUMENTOS ---
    $result = $conn->query("SHOW TABLES LIKE 'documentos'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'documentos' no existe. Creando...";

        $create_documentos_sql = "
            CREATE TABLE documentos (
                id_documento INT(11) NOT NULL AUTO_INCREMENT,
                tipo_documento VARCHAR(10) NOT NULL,
                numero_documento VARCHAR(10) NOT NULL,
                numero_control VARCHAR(10) NOT NULL,
                fecha_emision DATE DEFAULT NULL,
                hora_emision TIME NOT NULL DEFAULT current_timestamp(),
                fecha_vencimiento DATE DEFAULT NULL,
                tipo_pago VARCHAR(20) NOT NULL,
                serie VARCHAR(10) DEFAULT NULL,
                tipo_venta VARCHAR(20) NOT NULL,
                moneda_principal VARCHAR(10) DEFAULT NULL,
                codigo_vendedor VARCHAR(10) DEFAULT NULL,
                nombre_vendedor TINYINT(100) DEFAULT NULL,
                numero_cajero VARCHAR(3) DEFAULT NULL,
                registro_fiscal VARCHAR(20) DEFAULT NULL,
                razon_social VARCHAR(255) DEFAULT NULL,
                direccion_fiscal VARCHAR(255) DEFAULT NULL,
                pais VARCHAR(10) DEFAULT NULL,
                telefono VARCHAR(50) DEFAULT NULL,
                e_mail VARCHAR(255) DEFAULT NULL,
                nroItems INT(4) DEFAULT NULL,
                base_imponible DECIMAL(12,2) DEFAULT NULL,
                base_reducido DECIMAL(12,2) DEFAULT NULL,
                monto_exento DECIMAL(12,2) DEFAULT NULL,
                subtotal DECIMAL(12,2) DEFAULT NULL,
                porcentaje_iva DECIMAL(5,2) DEFAULT NULL,
                monto_iva DECIMAL(12,2) DEFAULT NULL,
                porcentaje_iva_reducido DECIMAL(5,2) DEFAULT NULL,
                monto_iva_reducido DECIMAL(12,2) DEFAULT NULL,
                balance_anterior DECIMAL(12,2) DEFAULT NULL,
                total DECIMAL(12,2) DEFAULT NULL,
                base_igtf DECIMAL(12,2) DEFAULT NULL,
                porcentaje_igtf DECIMAL(5,2) DEFAULT NULL,
                monto_igtf DECIMAL(12,2) DEFAULT NULL,
                descripcion VARCHAR(255) DEFAULT NULL,
                total_general DECIMAL(12,2) DEFAULT NULL,
                conversion_moneda VARCHAR(10) DEFAULT NULL,
                tasa_cambio DECIMAL(12,8) DEFAULT NULL,
                direccion_envio TEXT DEFAULT NULL,
                serie_strong_id VARCHAR(50) DEFAULT NULL,
                status VARCHAR(20) DEFAULT NULL,
                motivo_anulacion TEXT DEFAULT NULL,
                fecha_insertado TIMESTAMP NOT NULL DEFAULT current_timestamp(),
                nombre_empresa VARCHAR(255) NOT NULL,
                rif_fiscal_empresa VARCHAR(15) DEFAULT NULL,
                direccion_empresa VARCHAR(255) DEFAULT NULL,
                saldo DECIMAL(11,2) DEFAULT NULL,
                tipo_documento_afectado VARCHAR(2) DEFAULT NULL,
                numero_documento_afectado VARCHAR(10) DEFAULT NULL,
                empresa VARCHAR(50) DEFAULT NULL,
                sucursal VARCHAR(50) NOT NULL,
                usuario VARCHAR(200) DEFAULT NULL,
                fecha_reg DATETIME NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (id_documento),
                UNIQUE KEY uq_tipo_doc_num_doc_empresa (tipo_documento, numero_documento, empresa) USING BTREE,
                CONSTRAINT fk_tipo_documento FOREIGN KEY (tipo_documento) REFERENCES tipo_documento (tipo_documento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_documentos_sql);
        echo "✅ Tabla 'documentos' creada correctamente<br>";
    } else {
        echo "🛠 La tabla 'documentos' ya existe. Aplicando modificaciones...";

        $alter_documentos_sqls = [
            "MODIFY COLUMN tipo_documento VARCHAR(10) NOT NULL",
            "MODIFY COLUMN numero_documento VARCHAR(10) NOT NULL",
            "MODIFY COLUMN numero_control VARCHAR(10) NOT NULL",
            "MODIFY COLUMN fecha_emision DATE DEFAULT NULL",
            "MODIFY COLUMN hora_emision TIME NOT NULL DEFAULT current_timestamp()",
            "MODIFY COLUMN fecha_vencimiento DATE DEFAULT NULL",
            "MODIFY COLUMN tipo_pago VARCHAR(20) NOT NULL",
            "MODIFY COLUMN serie VARCHAR(10) DEFAULT NULL",
            "MODIFY COLUMN tipo_venta VARCHAR(20) NOT NULL",
            "MODIFY COLUMN moneda_principal VARCHAR(10) DEFAULT NULL",
            "MODIFY COLUMN codigo_vendedor VARCHAR(10) DEFAULT NULL",
            "MODIFY COLUMN nombre_vendedor TINYINT(100) DEFAULT NULL",
            "MODIFY COLUMN numero_cajero VARCHAR(3) DEFAULT NULL",
            "MODIFY COLUMN registro_fiscal VARCHAR(12) DEFAULT NULL",
            "MODIFY COLUMN razon_social VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN direccion_fiscal VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN pais VARCHAR(10) DEFAULT NULL",
            "MODIFY COLUMN telefono VARCHAR(50) DEFAULT NULL",
            "MODIFY COLUMN e_mail VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN nroItems INT(4) DEFAULT NULL",
            "MODIFY COLUMN base_imponible DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN base_reducido DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN monto_exento DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN subtotal DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN porcentaje_iva DECIMAL(5,2) DEFAULT NULL",
            "MODIFY COLUMN monto_iva DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN porcentaje_iva_reducido DECIMAL(5,2) DEFAULT NULL",
            "MODIFY COLUMN monto_iva_reducido DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN balance_anterior DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN total DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN base_igtf DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN porcentaje_igtf DECIMAL(5,2) DEFAULT NULL",
            "MODIFY COLUMN monto_igtf DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN descripcion VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN total_general DECIMAL(12,2) DEFAULT NULL",
            "MODIFY COLUMN conversion_moneda VARCHAR(10) DEFAULT NULL",
            "MODIFY COLUMN tasa_cambio DECIMAL(12,8) DEFAULT NULL",
            "MODIFY COLUMN direccion_envio TEXT DEFAULT NULL",
            "MODIFY COLUMN serie_strong_id VARCHAR(50) DEFAULT NULL",
            "MODIFY COLUMN status VARCHAR(20) DEFAULT NULL",
            "MODIFY COLUMN motivo_anulacion TEXT DEFAULT NULL",
            "MODIFY COLUMN fecha_insertado TIMESTAMP NOT NULL DEFAULT current_timestamp()",
            "MODIFY COLUMN nombre_empresa VARCHAR(255) NOT NULL",
            "MODIFY COLUMN rif_fiscal_empresa VARCHAR(15) DEFAULT NULL",
            "MODIFY COLUMN direccion_empresa VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN saldo DECIMAL(11,2) DEFAULT NULL",
            "MODIFY COLUMN tipo_documento_afectado VARCHAR(2) DEFAULT NULL",
            "MODIFY COLUMN numero_documento_afectado VARCHAR(10) DEFAULT NULL",
            "MODIFY COLUMN empresa VARCHAR(50) DEFAULT NULL",
            "MODIFY COLUMN sucursal VARCHAR(50) NOT NULL",
            "MODIFY COLUMN usuario VARCHAR(200) DEFAULT NULL",
            "MODIFY COLUMN fecha_reg DATETIME NOT NULL DEFAULT current_timestamp()"
        ];

        foreach ($alter_documentos_sqls as $sql) {
            try {
                $conn->query("ALTER TABLE documentos $sql");
            } catch (mysqli_sql_exception $e) {
                // Ignorar errores para DROP INDEX si no existe
                throw $e;
            }
        }

        // Verifica si el índice único ya existe antes de crearlo
        $indexExists = false;
        $res = $conn->query("SHOW INDEX FROM documentos WHERE Key_name = 'uq_tipo_doc_num_doc_empresa'");
        if ($res && $res->num_rows > 0) {
            $indexExists = true;
        }
        if (!$indexExists) {
            // Intentar eliminar el índice si existe con otro nombre
            try {
                $conn->query("ALTER TABLE documentos DROP INDEX uq_tipo_doc_num_doc_empresa");
            } catch (mysqli_sql_exception $e) {
                // Ignorar si no existe
            }
            $conn->query("ALTER TABLE documentos ADD UNIQUE KEY uq_tipo_doc_num_doc_empresa (tipo_documento, numero_documento, empresa) USING BTREE");
        }

        echo "✅ Estructura de la tabla 'documentos' actualizada exitosamente...";
    }
    
    // --- DOCUMENTO_DETALLE ---
    $result = $conn->query("SHOW TABLES LIKE 'documento_detalle'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'documento_detalle' no existe. Creando...";

        $create_documento_detalle_sql = "
            CREATE TABLE documento_detalle (
                id INT(11) NOT NULL AUTO_INCREMENT,
                id_documento INT(11) NOT NULL,
                codigo VARCHAR(255) NOT NULL,
                descripcion TEXT DEFAULT NULL,
                cantidad INT(11) NOT NULL,
                precio_unitario DECIMAL(15,2) NOT NULL,
                monto DECIMAL(15,2) NOT NULL,
                monto_total DECIMAL(15,2) NOT NULL,
                cod_impues VARCHAR(3) DEFAULT NULL,
                monto_iva DECIMAL(15,2) NOT NULL,
                monto_descuento DECIMAL(15,2) NOT NULL,
                porcentaje_descuento DECIMAL(5,2) NOT NULL,
                porcentaje_iva DECIMAL(5,2) NOT NULL,
                es_exento TINYINT(1) NOT NULL,
                empresa VARCHAR(255) NOT NULL,
                fec_reg DATETIME NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (id),
                KEY id_documento (id_documento),
                CONSTRAINT documento_detalle_ibfk_1 FOREIGN KEY (id_documento) REFERENCES documentos (id_documento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_documento_detalle_sql);
        echo "✅ Tabla 'documento_detalle' creada correctamente<br>";
    } else {
        echo "🛠 La tabla 'documento_detalle' ya existe. Aplicando modificaciones...";

        $alter_documento_detalle_sqls = [
            "MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN id_documento INT(11) NOT NULL",
            "MODIFY COLUMN codigo VARCHAR(255) NOT NULL",
            "MODIFY COLUMN descripcion TEXT DEFAULT NULL",
            "MODIFY COLUMN cantidad INT(11) NOT NULL",
            "MODIFY COLUMN precio_unitario DECIMAL(15,2) NOT NULL",
            "MODIFY COLUMN monto DECIMAL(15,2) NOT NULL",
            "MODIFY COLUMN monto_total DECIMAL(15,2) NOT NULL",
            "MODIFY COLUMN cod_impues VARCHAR(3) DEFAULT NULL",
            "MODIFY COLUMN monto_iva DECIMAL(15,2) NOT NULL",
            "MODIFY COLUMN monto_descuento DECIMAL(15,2) NOT NULL",
            "MODIFY COLUMN porcentaje_descuento DECIMAL(5,2) NOT NULL",
            "MODIFY COLUMN porcentaje_iva DECIMAL(5,2) NOT NULL",
            "MODIFY COLUMN es_exento TINYINT(1) NOT NULL",
            "MODIFY COLUMN empresa VARCHAR(255) NOT NULL",
            "MODIFY COLUMN fec_reg DATETIME NOT NULL DEFAULT current_timestamp()"
        ];

        foreach ($alter_documento_detalle_sqls as $sql) {
            try {
                $conn->query("ALTER TABLE documento_detalle $sql");
            } catch (mysqli_sql_exception $e) {
                throw $e;
            }
        }

        // Verifica si la clave foránea existe antes de agregarla
        $fkExists = false;
        $res = $conn->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'documento_detalle' AND CONSTRAINT_NAME = 'documento_detalle_ibfk_1' AND TABLE_SCHEMA = '$target_db'");
        if ($res && $res->num_rows > 0) {
            $fkExists = true;
        }
        if (!$fkExists) {
            // Elimina la FK si existe con otro nombre
            try {
                $conn->query("ALTER TABLE documento_detalle DROP FOREIGN KEY documento_detalle_ibfk_1");
            } catch (mysqli_sql_exception $e) {
                // Ignorar si no existe
            }
            $conn->query("ALTER TABLE documento_detalle ADD CONSTRAINT documento_detalle_ibfk_1 
            FOREIGN KEY (id_documento) REFERENCES documentos (id_documento)");
        }

        echo "✅ Estructura de la tabla 'documento_detalle' actualizada exitosamente...";
    }
    
    // --- DOCUMENTO_PAGOS ---
    $result = $conn->query("SHOW TABLES LIKE 'documento_pagos'");
    if ($result->num_rows == 0) {
        echo "🆕 Tabla 'documento_pagos' no existe. Creando...";

        $create_documento_pagos_sql = "
            CREATE TABLE documento_pagos (
                id INT(11) NOT NULL AUTO_INCREMENT,
                id_documento INT(11) NOT NULL,
                tipo_pago VARCHAR(20) NOT NULL,
                forma_pago VARCHAR(20) NOT NULL,
                monto DECIMAL(15,2) NOT NULL,
                fecha_pago DATETIME NOT NULL,
                usuario VARCHAR(255) NOT NULL,
                tasa_cambio DECIMAL(15,6) NOT NULL,
                moneda VARCHAR(10) NOT NULL,
                referencia VARCHAR(255) DEFAULT NULL,
                banco VARCHAR(255) DEFAULT NULL,
                status VARCHAR(50) NOT NULL,
                empresa VARCHAR(255) NOT NULL,
                fec_reg DATETIME NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (id),
                KEY id_documento (id_documento),
                CONSTRAINT documento_pagos_ibfk_1 FOREIGN KEY (id_documento) REFERENCES documentos (id_documento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        $conn->query($create_documento_pagos_sql);
        echo "✅ Tabla 'documento_pagos' creada correctamente<br>";
    } else {
        echo "🛠 La tabla 'documento_pagos' ya existe. Aplicando modificaciones...";

        $alter_documento_pagos_sqls = [
            "MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN id_documento INT(11) NOT NULL",
            "MODIFY COLUMN tipo_pago VARCHAR(20) NOT NULL",
            "MODIFY COLUMN forma_pago VARCHAR(20) NOT NULL",
            "MODIFY COLUMN monto DECIMAL(15,2) NOT NULL",
            "MODIFY COLUMN fecha_pago DATETIME NOT NULL",
            "MODIFY COLUMN usuario VARCHAR(255) NOT NULL",
            "MODIFY COLUMN tasa_cambio DECIMAL(15,6) NOT NULL",
            "MODIFY COLUMN moneda VARCHAR(10) NOT NULL",
            "MODIFY COLUMN referencia VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN banco VARCHAR(255) DEFAULT NULL",
            "MODIFY COLUMN status VARCHAR(50) NOT NULL",
            "MODIFY COLUMN empresa VARCHAR(255) NOT NULL",
            "MODIFY COLUMN fec_reg DATETIME NOT NULL DEFAULT current_timestamp()"
        ];

        foreach ($alter_documento_pagos_sqls as $sql) {
            try {
                $conn->query("ALTER TABLE documento_pagos $sql");
            } catch (mysqli_sql_exception $e) {
                throw $e;
            }
        }

        // Verifica si la clave foránea existe antes de agregarla
        $fkExists = false;
        $res = $conn->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'documento_pagos' AND CONSTRAINT_NAME = 'documento_pagos_ibfk_1' AND TABLE_SCHEMA = '$target_db'");
        if ($res && $res->num_rows > 0) {
            $fkExists = true;
        }
        if (!$fkExists) {
            // Elimina la FK si existe con otro nombre
            try {
                $conn->query("ALTER TABLE documento_pagos DROP FOREIGN KEY documento_pagos_ibfk_1");
            } catch (mysqli_sql_exception $e) {
                // Ignorar si no existe
            }
            $conn->query("ALTER TABLE documento_pagos ADD CONSTRAINT documento_pagos_ibfk_1 
            FOREIGN KEY (id_documento) REFERENCES documentos (id_documento)");
        }

        echo "✅ Estructura de la tabla 'documento_pagos' actualizada exitosamente...";
    }
     echo "✅ ✅ ESTRUCURA BD PROCESO CORRECTAMENTE ✅ ✅...";

    // CREANDO TRIGGERS
     // --- TRIGGER saldoDocumentoInsert ---
    // Elimina el trigger si ya existe
    try {
        $conn->query("DROP TRIGGER IF EXISTS saldoDocumentoInsert");
    } catch (mysqli_sql_exception $e) {
        // Ignorar si no existe
    }

    $trigger_sql = "
    CREATE TRIGGER saldoDocumentoInsert
    AFTER INSERT ON documento_pagos
    FOR EACH ROW
    BEGIN
        DECLARE pagos DECIMAL(13,2);
        SELECT SUM(monto) INTO pagos FROM documento_pagos WHERE id_documento = NEW.id_documento;
        IF pagos IS NOT NULL THEN
            UPDATE documentos SET saldo = total_general - pagos WHERE id_documento = NEW.id_documento;
        END IF;
    END;
    ";

    try {
        $conn->query($trigger_sql);
        echo "✅ Trigger 'saldoDocumentoInsert' creado correctamente...";
    } catch (mysqli_sql_exception $e) {
        echo "❌ Error creando trigger 'saldoDocumentoInsert': " . $e->getMessage();
    }

     // --- TRIGGER saldoDocumentoUpdate ---
    // Elimina el trigger si ya existe
    try {
        $conn->query("DROP TRIGGER IF EXISTS saldoDocumentoUpdate");
    } catch (mysqli_sql_exception $e) {
        // Ignorar si no existe
    }

    // Crea el trigger
    $trigger_update_sql = "
    CREATE TRIGGER saldoDocumentoUpdate
    AFTER UPDATE ON documento_pagos
    FOR EACH ROW
    BEGIN
        DECLARE pagos DECIMAL(13,2);
        SELECT SUM(monto) INTO pagos FROM documento_pagos WHERE id_documento = NEW.id_documento;
        IF pagos IS NOT NULL THEN
            UPDATE documentos SET saldo = total_general - pagos WHERE id_documento = NEW.id_documento;
        END IF;
    END;
    ";

    try {
        $conn->query($trigger_update_sql);
        echo "✅ Trigger 'saldoDocumentoUpdate' creado correctamente...";
    } catch (mysqli_sql_exception $e) {
        echo "❌ Error creando trigger 'saldoDocumentoUpdate': " . $e->getMessage();
    }

       // --- TRIGGER saldo_insert ---
    // Elimina el trigger si ya existe
    try {
        $conn->query("DROP TRIGGER IF EXISTS saldo_insert");
    } catch (mysqli_sql_exception $e) {
        // Ignorar si no existe
    }

    // Crea el trigger
    $trigger_saldo_insert_sql = "
    CREATE TRIGGER saldo_insert
    BEFORE INSERT ON documentos
    FOR EACH ROW
    BEGIN
        SET NEW.saldo = NEW.total_general;
    END;
    ";

    try {
        $conn->query($trigger_saldo_insert_sql);
        echo "✅ Trigger 'saldo_insert' creado correctamente...";
    } catch (mysqli_sql_exception $e) {
        echo "❌ Error creando trigger 'saldo_insert': " . $e->getMessage();
    }

    echo "✅ ✅ ESTRUCURA TRIGGERS PROCESO CORRECTAMENTE ✅ ✅...";
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("❌ Error de conexión o SQL: " . $e->getMessage());
}
/*?>*/