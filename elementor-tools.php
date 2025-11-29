<?php
/**
 * Herramientas de Elementor para Jaramar
 * Acceder desde: http://jaramar.test/elementor-tools.php
 */

require_once(__DIR__ . '/wp-load.php');

// Verificar que el usuario esté logueado (seguridad)
if (!is_user_logged_in()) {
    auth_redirect();
    exit;
}

echo "<style>
body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
.success { color: green; }
.error { color: red; }
.warning { color: orange; }
.info { color: blue; }
button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; margin: 10px 5px; }
button:hover { background: #005177; }
</style>";

echo "<h1>Herramientas de Elementor - Jaramar</h1>";
echo "<hr>";

// Si se presionó el botón de regenerar
if (isset($_POST['regenerate_css'])) {
    if (class_exists('\Elementor\Plugin')) {
        echo "<h2>Regenerando CSS de Elementor...</h2>";

        // Limpiar cache
        \Elementor\Plugin::$instance->files_manager->clear_cache();
        echo "<p class='success'>✓ Cache limpiado</p>";

        // Obtener posts de Elementor
        global $wpdb;
        $posts = $wpdb->get_results(
            "SELECT p.ID, p.post_title
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE pm.meta_key = '_elementor_edit_mode'
             AND pm.meta_value = 'builder'
             AND p.post_status = 'publish'
             LIMIT 50"
        );

        echo "<p class='info'>Regenerando CSS para " . count($posts) . " páginas...</p>";

        foreach ($posts as $post) {
            try {
                $css_file = new \Elementor\Core\Files\CSS\Post($post->ID);
                $css_file->update();
                echo "<p>✓ {$post->post_title}</p>";
                flush();
            } catch (Exception $e) {
                echo "<p class='warning'>⚠ Error en {$post->post_title}: " . $e->getMessage() . "</p>";
            }
        }

        // CSS Global
        try {
            $global_css = new \Elementor\Core\Files\CSS\Global_CSS('global.css');
            $global_css->update();
            echo "<p class='success'>✓ CSS Global regenerado</p>";
        } catch (Exception $e) {
            echo "<p class='warning'>⚠ Error en CSS global: " . $e->getMessage() . "</p>";
        }

        wp_cache_flush();
        echo "<p class='success'><strong>✓ Proceso completado!</strong></p>";
    } else {
        echo "<p class='error'>❌ Elementor no está activo</p>";
    }
}

// Información del sistema
echo "<hr>";
echo "<h2>Información del Sistema</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";

// Método de CSS
global $wpdb;
$css_method = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'elementor_css_print_method'");
echo "<tr><td><strong>Método CSS de Elementor</strong></td><td>{$css_method}</td></tr>";

// Archivos CSS
$css_dir = WP_CONTENT_DIR . '/uploads/elementor/css/';
if (is_dir($css_dir)) {
    $files = glob($css_dir . '*.css');
    $total_size = 0;
    foreach ($files as $file) {
        $total_size += filesize($file);
    }
    echo "<tr><td><strong>Archivos CSS generados</strong></td><td>" . count($files) . " archivos (" . size_format($total_size) . ")</td></tr>";
} else {
    echo "<tr><td><strong>Archivos CSS generados</strong></td><td class='warning'>0 archivos (carpeta no existe)</td></tr>";
}

// Posts con Elementor
$total_posts = $wpdb->get_var(
    "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_edit_mode' AND meta_value = 'builder'"
);
echo "<tr><td><strong>Páginas con Elementor</strong></td><td>{$total_posts}</td></tr>";

// URL del sitio
echo "<tr><td><strong>URL del sitio</strong></td><td>" . get_option('siteurl') . "</td></tr>";

echo "</table>";

// Formulario
echo "<hr>";
echo "<h2>Acciones</h2>";
echo "<form method='post'>";
echo "<button type='submit' name='regenerate_css'>Regenerar CSS de Elementor (50 páginas)</button>";
echo "</form>";

echo "<p class='info'><strong>Nota:</strong> Después de regenerar, presiona Ctrl+F5 en tu navegador.</p>";
?>
