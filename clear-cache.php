<?php
/**
 * Script para limpiar cache de WordPress
 * Ejecutar desde: http://jaramar.test/clear-cache.php
 */

// Cargar WordPress
require_once(__DIR__ . '/wp-load.php');

echo "<h2>Limpieza de Cache de WordPress - Jaramar</h2>";

// 1. Limpiar transients expirados
echo "<p>Limpiando transients expirados...</p>";
delete_expired_transients();
echo "<p style='color:green;'>✓ Transients expirados eliminados</p>";

// 2. Limpiar cache de objetos
echo "<p>Limpiando cache de objetos...</p>";
wp_cache_flush();
echo "<p style='color:green;'>✓ Cache de objetos limpiado</p>";

// 3. Limpiar cache de Elementor si está activo
if (class_exists('\Elementor\Plugin')) {
    echo "<p>Limpiando cache de Elementor...</p>";
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "<p style='color:green;'>✓ Cache de Elementor limpiado</p>";
}

// 4. Limpiar cache de LiteSpeed si está activo
if (class_exists('LiteSpeed_Cache_API')) {
    echo "<p>Limpiando cache de LiteSpeed...</p>";
    LiteSpeed_Cache_API::purge_all();
    echo "<p style='color:green;'>✓ Cache de LiteSpeed limpiado</p>";
}

// 5. Limpiar carpetas de cache físicas
$cache_dirs = [
    WP_CONTENT_DIR . '/cache',
    WP_CONTENT_DIR . '/uploads/elementor/css',
    WP_CONTENT_DIR . '/et-cache',
    WP_CONTENT_DIR . '/litespeed',
];

foreach ($cache_dirs as $dir) {
    if (is_dir($dir)) {
        echo "<p>Limpiando directorio: {$dir}...</p>";
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "<p style='color:green;'>✓ Directorio limpiado</p>";
    }
}

// 6. Información del sitio
echo "<hr>";
echo "<h3>Información del sitio:</h3>";
echo "<ul>";
echo "<li><strong>URL del sitio:</strong> " . get_option('siteurl') . "</li>";
echo "<li><strong>URL de inicio:</strong> " . get_option('home') . "</li>";
echo "<li><strong>Tema activo:</strong> " . wp_get_theme()->get('Name') . "</li>";
echo "<li><strong>Versión de WordPress:</strong> " . get_bloginfo('version') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<p style='color:blue; font-weight:bold;'>¡Limpieza completada! Recarga tu navegador con Ctrl+F5 para ver los cambios.</p>";
?>
