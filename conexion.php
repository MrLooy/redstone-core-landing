<?php
// ─── Carga de credenciales desde variables del entorno + archivo .env ──────────
// Precedencia: las variables YA definidas en el entorno real (getenv) mandan;
// el archivo .env (al lado de este script) solo completa lo que falte.
// Fail-closed: si falta una clave obligatoria, NUNCA se usan credenciales de
// texto plano hardcodeadas; el sitio termina con un mensaje amistoso.
$archivoEnv = __DIR__ . '/.env';
if (is_readable($archivoEnv)) {
    foreach (file($archivoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#') {
            continue;
        }
        [$clave, $valor] = array_pad(explode('=', $linea, 2), 2, '');
        $clave = trim($clave);
        $valor = trim(trim($valor), "\"'");
        if ($clave !== '' && getenv($clave) === false) {
            putenv($clave . '=' . $valor);
        }
    }
}

$host     = getenv('DB_HOST');
$dbname   = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

// Fallback local solo para el host (no es secreto). DB_NAME/DB_USER/DB_PASS
// jamás tienen fallback: si faltan, se corta la conexión.
if ($host === false || $host === '') {
    $host = 'localhost';
}

$faltantes = [];
if ($dbname === false || $dbname === '')   { $faltantes[] = 'DB_NAME'; }
if ($username === false || $username === '') { $faltantes[] = 'DB_USER'; }
if ($password === false || $password === '')   { $faltantes[] = 'DB_PASS'; }

if (!empty($faltantes)) {
    error_log('RedstoneCore: faltan variables de entorno del .env: ' . implode(', ', $faltantes));
    die('Lo sentimos, el sitio no está disponible en este momento. Intenta de nuevo más tarde. :(');
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // No exponer el mensaje real de PDO al visitante (puede revelar detalles internos).
    // El detalle completo queda en el log de errores del servidor para que el equipo lo revise.
    error_log('Error de conexion a la BD: ' . $e->getMessage());
    die('Lo sentimos, el sitio no está disponible en este momento. Intenta de nuevo más tarde. :(');
}
?>