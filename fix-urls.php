<?php
/**
 * WordPress URL Search & Replace Script
 *
 * IMPORTANTE: Ejecutar este script UNA SOLA VEZ y luego eliminarlo
 */

// Configuración de la base de datos
define('DB_NAME', 'jaramar');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_PREFIX', 'wp_');

// URLs a reemplazar (incluyendo versión con barras escapadas para JSON)
$old_url = 'https://jaramar-express-wordpress.test';
$new_url = 'http://jaramar.test';

// Conectar a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

echo "<h2>Buscando y reemplazando URLs...</h2>\n";
echo "<p>De: <strong>$old_url</strong></p>\n";
echo "<p>A: <strong>$new_url</strong></p>\n";
echo "<hr>\n";

// Función para actualizar datos serializados
function update_serialized_data($data, $old, $new) {
    if (is_serialized($data)) {
        $unserialized = @unserialize($data);
        if ($unserialized !== false) {
            $unserialized = str_replace_recursive($old, $new, $unserialized);
            return serialize($unserialized);
        }
    }
    return str_replace($old, $new, $data);
}

function str_replace_recursive($old, $new, $data) {
    if (is_string($data)) {
        return str_replace($old, $new, $data);
    } elseif (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = str_replace_recursive($old, $new, $value);
        }
    } elseif (is_object($data)) {
        foreach ($data as $key => $value) {
            $data->$key = str_replace_recursive($old, $new, $value);
        }
    }
    return $data;
}

function is_serialized($data) {
    if (!is_string($data)) return false;
    $data = trim($data);
    if ('N;' == $data) return true;
    if (!preg_match('/^([adObis]):/', $data, $badions)) return false;
    switch ($badions[1]) {
        case 'a':
        case 'O':
        case 's':
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) return true;
            break;
        case 'b':
        case 'i':
        case 'd':
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) return true;
            break;
    }
    return false;
}

// Obtener todas las tablas
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$total_updates = 0;

foreach ($tables as $table) {
    echo "<h3>Procesando tabla: $table</h3>\n";

    // Obtener columnas de la tabla
    $columns = [];
    $result = $conn->query("DESCRIBE `$table`");
    while ($row = $result->fetch_assoc()) {
        // Solo procesar columnas de texto
        if (strpos($row['Type'], 'text') !== false ||
            strpos($row['Type'], 'char') !== false ||
            strpos($row['Type'], 'blob') !== false) {
            $columns[] = $row['Field'];
        }
    }

    if (empty($columns)) {
        echo "<p>No hay columnas de texto para procesar.</p>\n";
        continue;
    }

    // Obtener nombre de la columna primaria
    $primary_key = null;
    $result = $conn->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
    if ($row = $result->fetch_assoc()) {
        $primary_key = $row['Column_name'];
    }

    if (!$primary_key) {
        echo "<p>⚠️ No se encontró clave primaria, omitiendo tabla.</p>\n";
        continue;
    }

    // Buscar y reemplazar en cada columna
    $table_updates = 0;
    foreach ($columns as $column) {
        $query = "SELECT `$primary_key`, `$column` FROM `$table` WHERE `$column` LIKE '%" . $conn->real_escape_string($old_url) . "%'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $old_value = $row[$column];
                $new_value = update_serialized_data($old_value, $old_url, $new_url);

                if ($old_value !== $new_value) {
                    $update_query = "UPDATE `$table` SET `$column` = '" . $conn->real_escape_string($new_value) . "' WHERE `$primary_key` = '" . $conn->real_escape_string($row[$primary_key]) . "'";
                    if ($conn->query($update_query)) {
                        $table_updates++;
                        $total_updates++;
                    }
                }
            }
        }
    }

    if ($table_updates > 0) {
        echo "<p style='color: green;'>✓ Actualizadas $table_updates filas</p>\n";
    } else {
        echo "<p>No se encontraron coincidencias.</p>\n";
    }

    flush();
}

echo "<hr>\n";
echo "<h2 style='color: green;'>✓ Proceso completado!</h2>\n";
echo "<p><strong>Total de actualizaciones: $total_updates</strong></p>\n";
echo "<p style='color: red;'><strong>IMPORTANTE:</strong> Elimina este archivo (fix-urls.php) después de usarlo.</p>\n";

$conn->close();
?>
