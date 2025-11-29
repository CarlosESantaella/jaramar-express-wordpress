<?php
/**
 * Forzar regeneración de CSS de Elementor para todas las páginas
 * Ejecutar: php force-regenerate-css.php
 */

define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

if (!class_exists('\Elementor\Plugin')) {
    die("Error: Elementor no está activo\n");
}

echo "=== Regeneración forzada de CSS de Elementor ===\n\n";

// Obtener todas las páginas con Elementor
global $wpdb;
$posts = $wpdb->get_results(
    "SELECT p.ID, p.post_title
     FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
     WHERE pm.meta_key = '_elementor_edit_mode'
     AND pm.meta_value = 'builder'
     AND p.post_status = 'publish'
     ORDER BY p.ID"
);

echo "Encontradas " . count($posts) . " páginas con Elementor\n\n";

$success = 0;
$errors = 0;

foreach ($posts as $post) {
    echo "Procesando #{$post->ID}: {$post->post_title}... ";

    try {
        // Eliminar CSS anterior
        delete_post_meta($post->ID, '_elementor_css');

        // Regenerar CSS
        $css_file = new \Elementor\Core\Files\CSS\Post($post->ID);
        $css_file->update();

        // Verificar si se generó
        $css_meta = get_post_meta($post->ID, '_elementor_css', true);

        if (!empty($css_meta)) {
            echo "✓ OK\n";
            $success++;
        } else {
            echo "⚠ No se generó meta CSS\n";
            $errors++;
        }

    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        $errors++;
    }

    flush();
}

// Regenerar CSS global
echo "\nRegenerando CSS global... ";
try {
    delete_option('_elementor_global_css');
    $global_css = new \Elementor\Core\Files\CSS\Global_CSS('global.css');
    $global_css->update();
    echo "✓ OK\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Verificar archivos generados
echo "\n=== Archivos CSS generados ===\n";
$css_dir = WP_CONTENT_DIR . '/uploads/elementor/css/';
if (is_dir($css_dir)) {
    $files = glob($css_dir . '*.css');
    echo "Total de archivos: " . count($files) . "\n";

    if (!empty($files)) {
        echo "\nPrimeros 10 archivos:\n";
        foreach (array_slice($files, 0, 10) as $file) {
            $size = filesize($file);
            echo "  - " . basename($file) . " (" . number_format($size) . " bytes)\n";
        }
    }
} else {
    echo "⚠ Carpeta CSS no existe\n";
}

echo "\n=== Resumen ===\n";
echo "Exitosos: $success\n";
echo "Errores: $errors\n";
echo "\n¡Completado! Recarga tu navegador con Ctrl+F5\n";
?>
