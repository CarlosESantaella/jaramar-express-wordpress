<?php
/**
 * Análisis detallado de widgets en las páginas
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Análisis Detallado de Widgets ===\n\n";

// Función para listar todos los widgets
function list_all_widgets($elements, $depth = 0) {
    $widgets_found = [];

    foreach ($elements as $element) {
        if (isset($element['widgetType'])) {
            $widget_type = $element['widgetType'];
            $widgets_found[] = [
                'type' => $widget_type,
                'depth' => $depth,
                'id' => $element['id'] ?? 'N/A'
            ];
        }

        if (isset($element['elements']) && is_array($element['elements'])) {
            $child_widgets = list_all_widgets($element['elements'], $depth + 1);
            $widgets_found = array_merge($widgets_found, $child_widgets);
        }
    }

    return $widgets_found;
}

// Analizar páginas principales
$main_pages = [13, 15, 1038]; // Home, About us, Home spanish

foreach ($main_pages as $page_id) {
    $page = get_post($page_id);
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Página: {$page->post_title} (ID: {$page_id})\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $elementor_data = get_post_meta($page_id, '_elementor_data', true);

    if ($elementor_data) {
        $data = json_decode($elementor_data, true);
        $widgets = list_all_widgets($data);

        if (count($widgets) > 0) {
            echo "Total de widgets: " . count($widgets) . "\n\n";
            foreach ($widgets as $widget) {
                echo str_repeat("  ", $widget['depth']) . "- {$widget['type']} (ID: {$widget['id']})\n";
            }
        } else {
            echo "⚠ No se encontraron widgets\n";
        }
    } else {
        echo "✗ No tiene datos de Elementor\n";
    }

    echo "\n";
}

echo "✓ Análisis completado\n";
?>
