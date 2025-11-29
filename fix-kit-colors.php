<?php
/**
 * Script para forzar regeneración del Default Kit con colores correctos
 */

define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

if (!class_exists('\Elementor\Plugin')) {
    die("Error: Elementor no está activo\n");
}

echo "=== Regeneración del Default Kit (ID: 6) ===\n\n";

// Eliminar CSS antiguo
delete_post_meta(6, '_elementor_css');
delete_option('_elementor_global_css');
echo "✓ CSS antiguo eliminado\n";

// Forzar regeneración del Kit
try {
    $kit_css = new \Elementor\Core\Files\CSS\Post(6);
    $kit_css->update();
    echo "✓ CSS del Default Kit regenerado\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Regenerar CSS global
try {
    $global_css = new \Elementor\Core\Files\CSS\Global_CSS('global.css');
    $global_css->update();
    echo "✓ CSS Global regenerado\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Limpiar cache
wp_cache_flush();
\Elementor\Plugin::$instance->files_manager->clear_cache();
echo "✓ Cache limpiado\n\n";

// Verificar colores en el CSS generado
$css_file = WP_CONTENT_DIR . '/uploads/elementor/css/post-6.css';
if (file_exists($css_file)) {
    $css_content = file_get_contents($css_file);
    echo "=== Verificación de Colores ===\n";

    if (strpos($css_content, '#FF5100') !== false) {
        echo "✓ Color Primary correcto: #FF5100 (naranja)\n";
    } else {
        echo "✗ Color Primary NO encontrado\n";
        if (preg_match('/--e-global-color-primary:(#[0-9A-F]{6})/i', $css_content, $matches)) {
            echo "  Valor actual: " . $matches[1] . "\n";
        }
    }

    if (strpos($css_content, '#001B71') !== false) {
        echo "✓ Color Secondary correcto: #001B71 (azul)\n";
    } else {
        echo "✗ Color Secondary NO encontrado\n";
    }

    if (strpos($css_content, 'gotham') !== false || strpos($css_content, 'Gotham') !== false) {
        echo "✓ Fuente Gotham encontrada\n";
    } else {
        echo "✗ Fuente Gotham NO encontrada\n";
    }

    echo "\nTamaño del archivo: " . number_format(filesize($css_file)) . " bytes\n";
} else {
    echo "✗ Archivo CSS del Kit no existe\n";
}

echo "\n¡Proceso completado!\n";
?>
