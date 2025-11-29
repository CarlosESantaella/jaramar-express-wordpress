<?php
/**
 * Script de prueba para verificar que la página home carga sin warnings
 */

define('WP_USE_THEMES', true);

// Capturar la salida
ob_start();
require_once(__DIR__ . '/index.php');
$output = ob_get_clean();

// Verificar si hay warnings de Deprecated
if (strpos($output, 'Deprecated') !== false) {
    echo "⚠️ ADVERTENCIA: Aún hay warnings de Deprecated en la salida\n\n";
    // Mostrar solo las primeras líneas con Deprecated
    $lines = explode("\n", $output);
    foreach ($lines as $line) {
        if (stripos($line, 'deprecated') !== false) {
            echo $line . "\n";
        }
    }
} else {
    echo "✓ ¡Perfecto! No hay warnings de Deprecated en la salida\n";
    echo "✓ El sitio debería verse correctamente ahora\n\n";

    // Verificar que hay contenido HTML
    if (strpos($output, '<html') !== false && strpos($output, '</html>') !== false) {
        echo "✓ HTML generado correctamente\n";
    }

    // Verificar que hay estilos de Elementor
    if (strpos($output, 'elementor') !== false) {
        echo "✓ Estilos de Elementor cargando\n";
    }

    echo "\nLongitud del HTML generado: " . strlen($output) . " bytes\n";
}
?>
