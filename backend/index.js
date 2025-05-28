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

// Log cuando arranca el servidor
console.log('Iniciando servidor...');

// Conexión a la base de datos
const db = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME
});

 const esquema_new = 'facturacion'; // Nombre de la Bsase de Datos
 const esquema = 'webservices'; // Nombre de la Bsase de Datos

db.connect((err) => {
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
    db.query(query, (err, results) => {
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

    db.query(query, (err, results) => {
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

    db.query(query, (err, results) => {
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

    db.query(query, (err, results) => {
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
    const condicion = `empresa = '${empresa}' and sucursal = '${sucursal}' oorder by nombre_productos;`;
    const query = `SELECT
            id_productos as id, codigo_productos as codigo, nombre_productos as nombre, ' ' as descripcion,
        costo_promedio_productos as costo, precio1_productos as precio, impuesto_productos as impuesto, 0 as stock, empresa, sucursal, usuario, fecha as fec_reg
    FROM
        ${esquema}.inventario_productos WHERE ${condicion}`;

    db.query(query, (err, results) => {
        if (err) {
            console.error('❌ Error en la consulta:', err.message);
            return res.status(500).json({ error: 'Error en la consulta' });
        }
        console.log(`📤 Productos encontrados: ${results.length}`);
        res.json(results);
    });
});

app.put('/actualizar_producto/:id', (req, res) => {
    const { id } = req.params;
    const { codigo, nombre, descripcion, costo, precio, impuesto, stock, usuario } = req.body;
    const query = `
        UPDATE ${esquema}.inventario_productos
        SET 
            codigo = ?, 
            nombre = ?, 
            descripcion = ?, 
            costo = ?, 
            precio = ?, 
            impuesto = ?, 
            stock = ?, 
            usuario = ?
        WHERE id = ? AND empresa = ?
    `;
    db.query(
        query,
        [codigo, nombre, descripcion, costo, precio, impuesto, stock, usuario, id, empresa],
        (err, result) => {
            if (err) {
                console.error('❌ Error actualizando producto:', err.message);
                return res.status(500).json({ error: 'Error actualizando producto' });
            }
            res.json({ success: true });
        }
    );
});


// Inicio del servidor
app.listen(port, () => {
    console.log(`🚀 Servidor escuchando en http://localhost:${port}`);
});
