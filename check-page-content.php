<?php
/**
 * Verificar contenido de las páginas principales
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Verificación de Contenido de Páginas ===\n\n";

global $wpdb;

// Obtener todas las páginas publicadas
$pages = $wpdb->get_results(
    "SELECT ID, post_title, post_name, post_status, post_modified
     FROM {$wpdb->posts}
     WHERE post_type = 'page'
     AND post_status = 'publish'
     ORDER BY ID ASC"
);

echo "Total de páginas publicadas: " . count($pages) . "\n\n";

// Función para contar widgets de texto
function count_text_widgets($elements, &$text_count = 0, &$heading_count = 0) {
    foreach ($elements as $element) {
        if (isset($element['widgetType'])) {
            if (in_array($element['widgetType'], ['text-editor', 'theme-post-content'])) {
                $text_count++;
            }
            if ($element['widgetType'] === 'heading') {
                $heading_count++;
            }
        }

        if (isset($element['elements']) && is_array($element['elements'])) {
            count_text_widgets($element['elements'], $text_count, $heading_count);
        }
    }
}

foreach ($pages as $page) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: {$page->ID}\n";
    echo "Título: {$page->post_title}\n";
    echo "Slug: {$page->post_name}\n";
    echo "Última modificación: {$page->post_modified}\n";

    // Verificar si tiene datos de Elementor
    $elementor_data = get_post_meta($page->ID, '_elementor_data', true);

    if ($elementor_data) {
        $data = json_decode($elementor_data, true);
        echo "✓ Tiene datos de Elementor: " . count($data) . " elementos\n";

        $text_count = 0;
        $heading_count = 0;
        count_text_widgets($data, $text_count, $heading_count);

        echo "  - Widgets de texto/contenido: {$text_count}\n";
        echo "  - Widgets de encabezados: {$heading_count}\n";

        // Verificar si tiene CSS generado
        $elementor_css = get_post_meta($page->ID, '_elementor_css', true);
        if ($elementor_css) {
            echo "  - CSS generado: ✓\n";
        } else {
            echo "  - CSS generado: ✗ (FALTA)\n";
        }

    } else {
        // Ver si tiene contenido normal de WordPress
        $post = get_post($page->ID);
        if (!empty($post->post_content)) {
            echo "Tiene contenido WordPress estándar\n";
            echo "Longitud: " . strlen($post->post_content) . " caracteres\n";
        } else {
            echo "⚠ NO TIENE CONTENIDO\n";
        }
    }

    echo "\n";
}

echo "✓ Verificación completada\n";
?>
