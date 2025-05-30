import dotenv from 'dotenv';
dotenv.config();

import express from 'express';
import cors from 'cors';
import mysql from 'mysql2';

const app = express();
const port = process.env.PORT;

const empresa = 'icarosoft';
const sucursal = 'icarosofmcbo';

app.use(cors());

// Middleware para parsear cuerpos de solicitudes JSON
app.use(express.json()); // 👈 *** MODIFICACIÓN IMPORTANTE: Agregado este middleware ***

// Log cuando arranca el servidor
console.log('Iniciando servidor...');

// Conexión a la BD FACTURACION
const dbf = mysql.createConnection({
    host: process.env.DBF_HOST,
    user: process.env.DBF_USER,
    password: process.env.DBF_PASSWORD,
    database: process.env.DBF_NAME
});

// Conexión a la BD OTROS (Clientes, Productos)
const dbo = mysql.createConnection({
    host: process.env.DBO_HOST,
    user: process.env.DBO_USER,
    password: process.env.DBO_PASSWORD,
    database: process.env.DBO_NAME
});

const esquema_new = 'facturacion'; // Nombre de la Bsase de Datos
const esquema = 'webservices'; // Nombre de la Bsase de Datos

dbf.connect((err) => {
    if (err) {
        console.error('❌ Error de conexión a la BD:', err.message);
        return;
    }
    console.log('✅ Conectado a la base de datos');
});

// Middleware para loguear todas las peticiones
app.use((req, res, next) => {
    console.log(`➡️  ${req.method} ${req.url}`);
    next();
});


// Ruta: Obtener todos los documentos
app.get('/facturacion', (req, res) => { // Total de Ventas al Dia
    console.log('📥 Consultando documentos...');
    const condicion = `tipo_documento = 'FA' AND status = 'PROCESADO' AND empresa = '${empresa}'`;
    const query = `SELECT sum(total) as total_ventas FROM ${esquema_new}.documentos WHERE ${condicion}`;
    dbf.query(query, (err, results) => {
        if (err) {
            console.error('❌ Error en la consulta:', err.message);
            return res.status(500).json({ error: 'Error en la consulta' });
        }
        console.log(`📤 Total Ventas: ${results[0].total_ventas}`);
        res.json(results[0]);
    });
});

// Ruta: Obtener conteo de documentos
app.get('/conteo', (req, res) => { // Total de Facturas Emitidas
    console.log('📥 Consultando conteo de documentos...');
    const condicion = `tipo_documento = 'FA' and status = 'PROCESADO' and empresa = '${empresa}'`;
    const query = `SELECT count(*) AS total FROM ${esquema_new}.documentos WHERE ${condicion}`;

    dbf.query(query, (err, results) => {
        if (err) {
            console.error('❌ Error en la consulta:', err.message);
            return res.status(500).json({ error: 'Error en la consulta' });
        }
        console.log(`📤 Total de documentos: ${results[0].total}`);
        res.json(results[0]);
    });
});

// Ruta: Obtener conteo de documentos
app.get('/ultimas_facturas', (req, res) => { // Obteniendo las Ultimas 5 Facturas Emitidas
    console.log('📥 Consultando las últimas 5 facturas...');
    const condicion = `tipo_documento = 'FA' and status = 'PROCESADO' and empresa = '${empresa}' ORDER BY numero_control DESC LIMIT 5`;
    const query = `SELECT concat(tipo_documento,'-', LPAD(numero_control, 10, '0')) AS factura,
    razon_social AS cliente,
    DATE_FORMAT(fecha_emision, '%d-%m-%Y') AS fecha,
    total_general AS total,
    status AS estado FROM ${esquema_new}.documentos WHERE ${condicion}`;

    dbf.query(query, (err, results) => {
        if (err) {
            console.error('❌ Error en la consulta:', err.message);
            return res.status(500).json({ error: 'Error en la consulta' });
        }
        console.log(`📤 Facturas encontradas: ${results.length}`);
        res.json(results); // Devuelve todos los registros
    });
});

app.get('/buscar_clientes', (req, res) => { // Buscando Clientes
    console.log('📥 Consultando la lista de clientes...');
    const condicion = `empresa = '${empresa}' and sucursal = '${sucursal}' order by nombre_razon_social`;
    const query = `SELECT id_cliente as id, cod_cliente as ci_rif, nombre_cliente as nombre_razon_social, e_mail as email,
    telefono_movil as telefono, direccion_cliente as direccion, fecha_nacimiento, fecha_registro, ciudad, empresa, sucursal, usuario
    FROM ${esquema}.clientes_datos WHERE ${condicion}`;

    dbo.query(query, (err, results) => {
        if (err) {
            console.error('❌ Error en la consulta:', err.message);
            return res.status(500).json({ error: 'Error en la consulta' });
        }
        console.log(`📤 Clientes encontrados: ${results.length}`);
        res.json(results);
    });
});

app.get('/buscar_productos', (req, res) => { // Buscando Productos
    console.log('📥 Consultando la lista de productos...');
    const condicion = `empresa = '${empresa}' and sucursal = '${sucursal}' order by nombre_productos`;
    const query = `SELECT
            id_productos as id, codigo_productos as codigo, nombre_productos as nombre, ' ' as descripcion,
        costo_promedio_productos as costo, precio1_productos as precio, impuesto_productos as impuesto, 0 as stock,
        empresa, sucursal, usuario, fecha as fec_reg
    FROM
        ${esquema}.inventario_productos WHERE ${condicion}`;

    dbo.query(query, (err, results) => {
        if (err) {
            console.error('❌ Error en la consulta:', err.message);
            return res.status(500).json({ error: 'Error en la consulta' });
        }
        console.log(`📤 Productos encontrados: ${results.length}`);
        res.json(results);
    });
});

// ENDPOINT PARA INSERCCIONES Damian Diaz 30-05-2025
/*
app.post('/api/documentos', (req, res) => {
    const {
        tipo_documento,
        numero_documento,
        total_general,
        fecha_emision,
        razon_social,
        usuario,
        empresa,
        sucursal,
        status
        // Agrega aquí otros campos requeridos
    } = req.body;

    // Validaciones básicas
    if (!tipo_documento || !numero_documento || !total_general || !fecha_emision || !razon_social || !usuario || !empresa || !sucursal || !status) {
        return res.status(400).json({ error: 'Faltan campos obligatorios' });
    }

    const query = `
        INSERT INTO ${esquema_new}.documentos
        (tipo_documento, numero_documento, total_general, fecha_emision, razon_social, usuario, empresa, sucursal, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    dbf.query(
        query,
        [tipo_documento, numero_documento, total_general, fecha_emision, razon_social, usuario, empresa, sucursal, status],
        (err, result) => {
            if (err) {
                console.error('❌ Error insertando documento:', err.message);
                return res.status(500).json({ error: 'Error insertando documento' });
            }
            res.json({ success: true, id: result.insertId });
        }
    );
});
*/

app.post('/documentos', (req, res) => {
    const documentoData = req.body.documento;
    const detalles = req.body.detalles;

    if (!documentoData || !Array.isArray(detalles) || detalles.length === 0) {
        return res.status(400).json({ error: 'El campo "documento" y un array "detalles" son requeridos en el JSON' });
    }

    // Prepara los datos del documento principal
    const documentoInsert = {
        tipo_documento: documentoData.tipo_documento,
        numero_documento: documentoData.numero_documento,
        numero_control: documentoData.numero_control,
        fecha_emision: documentoData.fecha_emision ? new Date(documentoData.fecha_emision.split(' ')[0]) : null,
        hora_emision: documentoData.fecha_emision ? documentoData.fecha_emision.split(' ')[1] : new Date().toLocaleTimeString('en-US', { hour12: false }),
        tipo_pago: 'CONTADO',
        serie: documentoData.serie,
        tipo_venta: 'GENERAL',
        moneda_principal: documentoData.moneda_principal,
        registro_fiscal: documentoData.registro_fiscal,
        razon_social: documentoData.razon_social,
        direccion_fiscal: documentoData.direccion_fiscal,
        pais: 'VE',
        telefono: documentoData.telefono,
        e_mail: documentoData.e_mail,
        nroItems: detalles.length,
        base_imponible: documentoData.base_imponible,
        base_reducido: documentoData.base_reducido,
        monto_exento: documentoData.monto_exento,
        subtotal: documentoData.subtotal,
        porcentaje_iva: documentoData.porcentaje_iva,
        monto_iva: documentoData.monto_iva,
        porcentaje_iva_reducido: documentoData.porcentaje_iva_reducido,
        monto_iva_reducido: documentoData.monto_iva_reducido,
        balance_anterior: documentoData.balance_anterior,
        total: documentoData.total,
        base_igtf: documentoData.base_igtf,
        porcentaje_igtf: documentoData.porcentaje_igtf,
        monto_igtf: documentoData.monto_igtf,
        descripcion: documentoData.descripcion,
        total_general: documentoData.total_general,
        conversion_moneda: documentoData.conversion_moneda,
        tasa_cambio: documentoData.tasa_cambio,
        direccion_envio: documentoData.direccion_envio,
        serie_strong_id: documentoData.serie_strong_id,
        status: documentoData.status,
        nombre_empresa: 'NOMBRE_DE_TU_EMPRESA',
        rif_fiscal_empresa: 'RIF_DE_TU_EMPRESA',
        direccion_empresa: 'DIRECCION_DE_TU_EMPRESA',
        sucursal: 'PRINCIPAL',
        usuario: documentoData.usuario
    };

    const queryDocumento = `
        INSERT INTO ${esquema_new}.documentos (
            tipo_documento, numero_documento, numero_control, fecha_emision, hora_emision,
            tipo_pago, serie, tipo_venta, moneda_principal, registro_fiscal, razon_social,
            direccion_fiscal, pais, telefono, e_mail, nroItems, base_imponible, base_reducido,
            monto_exento, subtotal, porcentaje_iva, monto_iva, porcentaje_iva_reducido,
            monto_iva_reducido, balance_anterior, total, base_igtf, porcentaje_igtf,
            monto_igtf, descripcion, total_general, conversion_moneda, tasa_cambio,
            direccion_envio, serie_strong_id, status, nombre_empresa, rif_fiscal_empresa,
            direccion_empresa, sucursal, usuario
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;
    const valuesDocumento = [
        documentoInsert.tipo_documento,
        documentoInsert.numero_documento,
        documentoInsert.numero_control,
        documentoInsert.fecha_emision,
        documentoInsert.hora_emision,
        documentoInsert.tipo_pago,
        documentoInsert.serie,
        documentoInsert.tipo_venta,
        documentoInsert.moneda_principal,
        documentoInsert.registro_fiscal,
        documentoInsert.razon_social,
        documentoInsert.direccion_fiscal,
        documentoInsert.pais,
        documentoInsert.telefono,
        documentoInsert.e_mail,
        documentoInsert.nroItems,
        documentoInsert.base_imponible,
        documentoInsert.base_reducido,
        documentoInsert.monto_exento,
        documentoInsert.subtotal,
        documentoInsert.porcentaje_iva,
        documentoInsert.monto_iva,
        documentoInsert.porcentaje_iva_reducido,
        documentoInsert.monto_iva_reducido,
        documentoInsert.balance_anterior,
        documentoInsert.total,
        documentoInsert.base_igtf,
        documentoInsert.porcentaje_igtf,
        documentoInsert.monto_igtf,
        documentoInsert.descripcion,
        documentoInsert.total_general,
        documentoInsert.conversion_moneda,
        documentoInsert.tasa_cambio,
        documentoInsert.direccion_envio,
        documentoInsert.serie_strong_id,
        documentoInsert.status,
        documentoInsert.nombre_empresa,
        documentoInsert.rif_fiscal_empresa,
        documentoInsert.direccion_empresa,
        documentoInsert.sucursal,
        documentoInsert.usuario
    ];

    dbf.query(queryDocumento, valuesDocumento, (err, result) => {
        if (err) {
            console.error('Error al insertar el documento:', err);
            return res.status(500).json({ error: 'Error al insertar el documento en la base de datos' });
        }
        const id_documento = result.insertId;

        // Ahora insertamos los detalles
        const queryDetalle = `
            INSERT INTO ${esquema_new}.documento_detalle (
                id_documento, codigo, descripcion, cantidad, precio_unitario, monto, monto_total,
                monto_iva, monto_descuento, porcentaje_descuento, porcentaje_iva, es_exento
            ) VALUES ?
        `;

        // Preparamos los valores para el bulk insert
        const valuesDetalles = detalles.map(det => [
            id_documento,
            det.codigo,
            det.descripcion,
            det.cantidad,
            det.precio_unitario,
            det.monto,
            det.monto_total,
            det.monto_iva,
            det.monto_descuento,
            det.porcentaje_descuento,
            det.porcentaje_iva,
            det.es_exento ? 1 : 0
        ]);

        dbf.query(queryDetalle, [valuesDetalles], (err2) => {
            if (err2) {
                console.error('Error al insertar los detalles:', err2);
                return res.status(500).json({ error: 'Documento creado, pero error al insertar los detalles' });
            }
            res.status(201).json({ message: 'Documento y detalles insertados correctamente', id_documento });
        });
    });
});


// Inicio del servidor
app.listen(port, () => {
    console.log(`🚀 Servidor escuchando en http://localhost:${port}`);
});