<?php
/**
 * Verificar configuración específica de sticky y colores
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Verificación Detallada de Sticky y Colores ===\n\n";

function check_container_details($header_id, $header_name) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "{$header_name} (ID: {$header_id})\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $elementor_data = get_post_meta($header_id, '_elementor_data', true);

    if ($elementor_data) {
        $data = json_decode($elementor_data, true);

        // Analizar containers principales
        foreach ($data as $idx => $element) {
            if ($element['elType'] === 'container') {
                echo "Container #{$idx} (ID: {$element['id']})\n";

                $settings = $element['settings'] ?? [];

                // Mostrar TODAS las configuraciones del container
                echo "  Todas las configuraciones:\n";
                foreach ($settings as $key => $value) {
                    if (is_array($value)) {
                        echo "    - {$key}: " . json_encode($value, JSON_UNESCAPED_SLASHES) . "\n";
                    } else {
                        echo "    - {$key}: {$value}\n";
                    }
                }

                echo "\n";
            }
        }
    }
}

check_container_details(1002, "Header ESPAÑOL");
check_container_details(25, "Header INGLÉS");

echo "✓ Verificación completada\n";
?>
