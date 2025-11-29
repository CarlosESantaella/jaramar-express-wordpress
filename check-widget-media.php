<?php
/**
 * Verificar configuración de widgets de video e imagen
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Verificación de Configuración de Widgets Multimedia ===\n\n";

// Función para buscar widgets de video e imagen
function find_media_widgets($elements, $depth = 0) {
    $media_widgets = [];

    foreach ($elements as $element) {
        if (isset($element['widgetType'])) {
            $widget_type = $element['widgetType'];

            // Buscar widgets de video, imagen, etc.
            if (in_array($widget_type, ['video', 'image', 'eael-image-accordion', 'eael-filterable-gallery'])) {
                $media_widgets[] = [
                    'type' => $widget_type,
                    'id' => $element['id'] ?? 'N/A',
                    'settings' => $element['settings'] ?? [],
                    'depth' => $depth
                ];
            }
        }

        if (isset($element['elements']) && is_array($element['elements'])) {
            $child_widgets = find_media_widgets($element['elements'], $depth + 1);
            $media_widgets = array_merge($media_widgets, $child_widgets);
        }
    }

    return $media_widgets;
}

// Verificar página Home
$page_id = 13;
$page = get_post($page_id);

echo "Página: {$page->post_title} (ID: {$page_id})\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$elementor_data = get_post_meta($page_id, '_elementor_data', true);

if ($elementor_data) {
    $data = json_decode($elementor_data, true);
    $media_widgets = find_media_widgets($data);

    echo "Total de widgets multimedia: " . count($media_widgets) . "\n\n";

    foreach ($media_widgets as $widget) {
        echo "Widget: {$widget['type']} (ID: {$widget['id']})\n";
        echo "Profundidad: {$widget['depth']}\n";

        // Mostrar configuración relevante según el tipo
        if ($widget['type'] === 'video') {
            echo "Configuración de Video:\n";
            echo "  - video_type: " . ($widget['settings']['video_type'] ?? 'N/A') . "\n";

            if (isset($widget['settings']['youtube_url'])) {
                echo "  - youtube_url: {$widget['settings']['youtube_url']}\n";
            }
            if (isset($widget['settings']['vimeo_url'])) {
                echo "  - vimeo_url: {$widget['settings']['vimeo_url']}\n";
            }
            if (isset($widget['settings']['insert_url'])) {
                echo "  - insert_url: {$widget['settings']['insert_url']}\n";
            }
            if (isset($widget['settings']['hosted_url'])) {
                if (is_array($widget['settings']['hosted_url'])) {
                    echo "  - hosted_url: " . json_encode($widget['settings']['hosted_url']) . "\n";
                } else {
                    echo "  - hosted_url: {$widget['settings']['hosted_url']}\n";
                }
            }
        } elseif ($widget['type'] === 'image') {
            echo "Configuración de Imagen:\n";
            if (isset($widget['settings']['image'])) {
                if (is_array($widget['settings']['image'])) {
                    echo "  - image: " . json_encode($widget['settings']['image']) . "\n";
                } else {
                    echo "  - image: {$widget['settings']['image']}\n";
                }
            }
        } elseif ($widget['type'] === 'eael-image-accordion') {
            echo "Configuración de Image Accordion:\n";
            if (isset($widget['settings']['eael_img_accordion_content'])) {
                $items = $widget['settings']['eael_img_accordion_content'];
                echo "  - Total de items: " . count($items) . "\n";
                foreach ($items as $idx => $item) {
                    echo "    Item " . ($idx + 1) . ":\n";
                    if (isset($item['eael_accordion_bg'])) {
                        echo "      bg: " . json_encode($item['eael_accordion_bg']) . "\n";
                    }
                }
            }
        } elseif ($widget['type'] === 'eael-filterable-gallery') {
            echo "Configuración de Filterable Gallery:\n";
            if (isset($widget['settings']['eael_fg_gallery_items'])) {
                $items = $widget['settings']['eael_fg_gallery_items'];
                echo "  - Total de items: " . count($items) . "\n";
            }
        }

        echo "\n";
    }
} else {
    echo "✗ No tiene datos de Elementor\n";
}

echo "✓ Verificación completada\n";
?>
