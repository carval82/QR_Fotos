<?php
/**
 * QR Fotos - Verificador de Instalación
 * Creado por LC Design
 * 
 * Ejecuta este archivo para verificar que todo esté configurado correctamente.
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>QR Fotos - Verificar Instalación</title>
    <style>
        :root { --bg:#0b1220; --card:#0f1a30; --text:#e8eefc; --muted:#a9b7d6; --line:#203050; --accent:#4f8cff; --success:#28c76f; --danger:#ff5a5f; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); padding: 24px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: var(--accent); margin-bottom: 8px; }
        .subtitle { color: var(--muted); margin-bottom: 24px; }
        .card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .card h3 { margin: 0 0 16px; color: var(--text); }
        .check { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--line); }
        .check:last-child { border-bottom: none; }
        .check-icon { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .check-ok { background: rgba(40,199,111,.2); color: var(--success); }
        .check-fail { background: rgba(255,90,95,.2); color: var(--danger); }
        .check-warn { background: rgba(255,159,67,.2); color: #ff9f43; }
        .check-text { flex: 1; }
        .check-detail { font-size: 12px; color: var(--muted); }
        .btn { display: inline-block; padding: 12px 20px; border-radius: 10px; background: rgba(79,140,255,.18); border: 1px solid rgba(79,140,255,.35); color: var(--text); text-decoration: none; font-weight: 600; margin-top: 16px; }
        .btn:hover { background: rgba(79,140,255,.28); }
        code { background: rgba(255,255,255,.1); padding: 2px 6px; border-radius: 4px; font-size: 13px; }
        .instructions { background: rgba(79,140,255,.1); border: 1px solid rgba(79,140,255,.25); border-radius: 10px; padding: 16px; margin-top: 16px; }
        .instructions h4 { margin: 0 0 12px; color: var(--accent); }
        .instructions ol { margin: 0; padding-left: 20px; line-height: 1.8; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔍 Verificar Instalación</h1>
    <p class='subtitle'>QR Fotos - Sistema de subida de fotos por QR</p>";

$checks = [];

// 1. Verificar PHP
$checks[] = [
    'name' => 'PHP Version',
    'status' => version_compare(PHP_VERSION, '8.1.0', '>=') ? 'ok' : 'fail',
    'detail' => 'Versión actual: ' . PHP_VERSION . ' (requiere 8.1+)'
];

// 2. Verificar extensiones
$requiredExtensions = ['pdo', 'pdo_sqlite', 'fileinfo', 'gd'];
foreach ($requiredExtensions as $ext) {
    $checks[] = [
        'name' => "Extensión: $ext",
        'status' => extension_loaded($ext) ? 'ok' : 'fail',
        'detail' => extension_loaded($ext) ? 'Instalada' : 'No instalada'
    ];
}

// 3. Verificar archivo .env
$envPath = __DIR__ . '/../.env';
$checks[] = [
    'name' => 'Archivo .env',
    'status' => file_exists($envPath) ? 'ok' : 'fail',
    'detail' => file_exists($envPath) ? 'Existe' : 'No existe - copia .env.example a .env'
];

// 4. Verificar base de datos SQLite
$dbPath = __DIR__ . '/../database/database.sqlite';
$checks[] = [
    'name' => 'Base de datos SQLite',
    'status' => file_exists($dbPath) ? 'ok' : 'warn',
    'detail' => file_exists($dbPath) ? 'Existe (' . round(filesize($dbPath)/1024, 1) . ' KB)' : 'No existe - ejecuta: php artisan migrate'
];

// 5. Verificar storage link
$storageLinkPath = __DIR__ . '/../public/storage';
$checks[] = [
    'name' => 'Storage Link',
    'status' => (is_link($storageLinkPath) || is_dir($storageLinkPath)) ? 'ok' : 'fail',
    'detail' => (is_link($storageLinkPath) || is_dir($storageLinkPath)) ? 'Configurado' : 'No existe - ejecuta: php artisan storage:link'
];

// 6. Verificar permisos de escritura
$storagePath = __DIR__ . '/../storage';
$checks[] = [
    'name' => 'Permisos de storage/',
    'status' => is_writable($storagePath) ? 'ok' : 'fail',
    'detail' => is_writable($storagePath) ? 'Escritura permitida' : 'Sin permisos de escritura'
];

// 7. Verificar hosts file
$hostsFile = 'C:\\Windows\\System32\\drivers\\etc\\hosts';
$hostsContent = @file_get_contents($hostsFile);
$hasQrFotosEntry = $hostsContent && strpos($hostsContent, 'qrfotos.local') !== false;
$checks[] = [
    'name' => 'Entrada en archivo hosts',
    'status' => $hasQrFotosEntry ? 'ok' : 'warn',
    'detail' => $hasQrFotosEntry ? 'qrfotos.local configurado' : 'Falta agregar: 127.0.0.1 qrfotos.local'
];

// 8. Verificar virtual host config
$vhostsFile = 'C:\\xampp\\apache\\conf\\extra\\httpd-vhosts.conf';
$vhostsContent = @file_get_contents($vhostsFile);
$hasVhostEntry = $vhostsContent && strpos($vhostsContent, 'qrfotos.local') !== false;
$checks[] = [
    'name' => 'Virtual Host en Apache',
    'status' => $hasVhostEntry ? 'ok' : 'warn',
    'detail' => $hasVhostEntry ? 'Configurado en httpd-vhosts.conf' : 'Falta configurar virtual host'
];

// Mostrar resultados
echo "<div class='card'><h3>Estado de la Instalación</h3>";

$allOk = true;
$needsHostsConfig = false;

foreach ($checks as $check) {
    $icon = $check['status'] === 'ok' ? '✓' : ($check['status'] === 'warn' ? '!' : '✗');
    $class = 'check-' . $check['status'];
    
    if ($check['status'] !== 'ok') $allOk = false;
    if (strpos($check['name'], 'hosts') !== false && $check['status'] !== 'ok') {
        $needsHostsConfig = true;
    }
    
    echo "<div class='check'>
        <div class='check-icon {$class}'>{$icon}</div>
        <div class='check-text'>
            <div>{$check['name']}</div>
            <div class='check-detail'>{$check['detail']}</div>
        </div>
    </div>";
}

echo "</div>";

// Instrucciones si falta configurar hosts
if ($needsHostsConfig) {
    echo "<div class='instructions'>
        <h4>📋 Configurar archivo hosts</h4>
        <ol>
            <li>Ejecuta como Administrador: <code>setup/configurar_hosts.bat</code></li>
            <li>O manualmente: abre Notepad como Admin y edita <code>C:\\Windows\\System32\\drivers\\etc\\hosts</code></li>
            <li>Agrega la línea: <code>127.0.0.1 qrfotos.local</code></li>
            <li>Reinicia Apache desde el panel de XAMPP</li>
        </ol>
    </div>";
}

// Botones de acción
echo "<div style='margin-top: 24px;'>";
if ($allOk) {
    echo "<a href='/admin/events' class='btn'>✓ Ir al Panel de Admin</a>";
} else {
    echo "<a href='' class='btn' onclick='location.reload(); return false;'>🔄 Verificar de nuevo</a>";
}
echo "</div>";

echo "</div></body></html>";
