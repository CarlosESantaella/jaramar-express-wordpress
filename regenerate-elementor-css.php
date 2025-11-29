<?php
/**
 * Script para regenerar CSS de Elementor
 * Ejecutar desde: http://jaramar.test/regenerate-elementor-css.php
 */

require_once(__DIR__ . '/wp-load.php');

echo "<h2>Regeneración de CSS de Elementor</h2>";
echo "<hr>";

// Verificar que Elementor esté activo
if (!class_exists('\Elementor\Plugin')) {
    echo "<p style='color:red;'>❌ Error: Elementor no está activo</p>";
    exit;
}

echo "<p>✓ Elementor detectado correctamente</p>";

// 1. Limpiar cache de Elementor
echo "<h3>1. Limpiando cache de Elementor...</h3>";
\Elementor\Plugin::$instance->files_manager->clear_cache();
echo "<p style='color:green;'>✓ Cache de Elementor limpiado</p>";

// 2. Eliminar meta _elementor_css para forzar regeneración
echo "<h3>2. Eliminando archivos CSS antiguos...</h3>";
global $wpdb;
$deleted = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_css'");
echo "<p style='color:green;'>✓ Eliminados {$deleted} registros de CSS antiguos</p>";

// 3. Eliminar opción de CSS global
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = '_elementor_global_css'");
echo "<p style='color:green;'>✓ CSS global eliminado</p>";

// 4. Obtener todos los posts que usan Elementor
echo "<h3>3. Regenerando CSS para cada página de Elementor...</h3>";
$posts = $wpdb->get_results(
    "SELECT p.ID, p.post_title
     FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
     WHERE pm.meta_key = '_elementor_edit_mode'
     AND pm.meta_value = 'builder'
     AND p.post_status = 'publish'"
);

if (empty($posts)) {
    echo "<p style='color:orange;'>⚠️ No se encontraron páginas de Elementor</p>";
} else {
    echo "<p>Encontradas " . count($posts) . " páginas de Elementor</p>";

    foreach ($posts as $post) {
        // Regenerar CSS para cada post
        $css_file = new \Elementor\Core\Files\CSS\Post($post->ID);
        $css_file->update();
        echo "<p>✓ Regenerado CSS para: <strong>{$post->post_title}</strong> (ID: {$post->ID})</p>";
        flush();
    }
}

// 5. Regenerar CSS global
echo "<h3>4. Regenerando CSS global...</h3>";
$global_css = new \Elementor\Core\Files\CSS\Global_CSS('global.css');
$global_css->update();
echo "<p style='color:green;'>✓ CSS global regenerado</p>";

// 6. Limpiar cache de WordPress
echo "<h3>5. Limpiando cache de WordPress...</h3>";
wp_cache_flush();
delete_expired_transients();
echo "<p style='color:green;'>✓ Cache de WordPress limpiado</p>";

// 7. Verificar archivos generados
echo "<h3>6. Verificando archivos CSS generados...</h3>";
$css_dir = WP_CONTENT_DIR . '/uploads/elementor/css/';
if (is_dir($css_dir)) {
    $files = glob($css_dir . '*.css');
    echo "<p style='color:green;'>✓ Generados " . count($files) . " archivos CSS</p>";
    echo "<ul>";
    foreach (array_slice($files, 0, 10) as $file) {
        $size = filesize($file);
        echo "<li>" . basename($file) . " (" . number_format($size) . " bytes)</li>";
    }
    if (count($files) > 10) {
        echo "<li>... y " . (count($files) - 10) . " archivos más</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:orange;'>⚠️ Carpeta CSS no existe aún</p>";
}

echo "<hr>";
echo "<h2 style='color:green;'>✓ Proceso completado!</h2>";
echo "<p style='color:blue; font-weight:bold;'>Presiona Ctrl+F5 en tu navegador para recargar completamente la página.</p>";
echo "<p style='color:red;'><strong>IMPORTANTE:</strong> Elimina este archivo después de usarlo.</p>";
?>
