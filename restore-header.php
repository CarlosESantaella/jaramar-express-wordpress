<?php
/**
 * Restaurar header desde la última revisión válida
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Restauración del Header ===\n\n";

// Buscar la revisión más reciente con widget de navegación
global $wpdb;
$revisions = $wpdb->get_results(
    "SELECT p.ID, p.post_modified, pm.meta_value
     FROM {$wpdb->posts} p
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
     WHERE p.post_parent = 25
     AND p.post_type = 'revision'
     AND pm.meta_key = '_elementor_data'
     AND pm.meta_value LIKE '%navigation-menu%'
     ORDER BY p.post_modified DESC
     LIMIT 1"
);

if ($revisions && !empty($revisions)) {
    $revision = $revisions[0];
    echo "✓ Revisión encontrada: ID {$revision->ID}\n";
    echo "  Fecha: {$revision->post_modified}\n\n";

    // Copiar _elementor_data de la revisión al post principal
    $elementor_data = $revision->meta_value;

    echo "Restaurando datos de Elementor...\n";
    update_post_meta(25, '_elementor_data', $elementor_data);

    // Copiar otros metadatos relevantes
    $metas_to_copy = ['_elementor_version', '_elementor_page_settings', '_elementor_css'];

    foreach ($metas_to_copy as $meta_key) {
        $meta_value = get_post_meta($revision->ID, $meta_key, true);
        if ($meta_value) {
            update_post_meta(25, $meta_key, $meta_value);
            echo "  ✓ Copiado: {$meta_key}\n";
        }
    }

    // Limpiar CSS para regenerar
    delete_post_meta(25, '_elementor_css');

    // Limpiar cache
    wp_cache_flush();

    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
        echo "  ✓ Cache de Elementor limpiado\n";
    }

    echo "\n✓ Header restaurado exitosamente!\n";
    echo "\nAhora regenerando CSS del header...\n";

    if (class_exists('\Elementor\Plugin')) {
        $css_file = new \Elementor\Core\Files\CSS\Post(25);
        $css_file->update();
        echo "✓ CSS regenerado\n";
    }

    echo "\n¡Proceso completado! El menú debería aparecer ahora.\n";
} else {
    echo "✗ No se encontraron revisiones válidas con widget de navegación\n";
}
?>
