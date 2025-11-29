<?php
/**
 * Verificar URLs de archivos multimedia
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Verificación de URLs de Medios ===\n\n";

global $wpdb;

// Obtener todos los attachments (imágenes, videos, etc.)
$attachments = $wpdb->get_results(
    "SELECT ID, post_title, post_name, guid, post_mime_type
     FROM {$wpdb->posts}
     WHERE post_type = 'attachment'
     ORDER BY ID DESC
     LIMIT 20"
);

echo "Últimos 20 archivos multimedia:\n\n";

foreach ($attachments as $attachment) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: {$attachment->ID}\n";
    echo "Título: {$attachment->post_title}\n";
    echo "Tipo: {$attachment->post_mime_type}\n";
    echo "URL (guid): {$attachment->guid}\n";

    // Obtener la URL real del attachment
    $url = wp_get_attachment_url($attachment->ID);
    echo "URL (wp_get_attachment_url): {$url}\n";

    // Verificar si el archivo existe
    $upload_dir = wp_upload_dir();
    $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);

    if (file_exists($file_path)) {
        echo "Archivo: ✓ EXISTE ({$file_path})\n";
        echo "Tamaño: " . filesize($file_path) . " bytes\n";
    } else {
        echo "Archivo: ✗ NO EXISTE ({$file_path})\n";
    }

    echo "\n";
}

// Verificar configuración de uploads
echo "\n=== Configuración de Uploads ===\n";
$upload_dir = wp_upload_dir();
echo "Base URL: {$upload_dir['baseurl']}\n";
echo "Base DIR: {$upload_dir['basedir']}\n";
echo "Directorio existe: " . (is_dir($upload_dir['basedir']) ? "✓ SÍ" : "✗ NO") . "\n";

echo "\n✓ Verificación completada\n";
?>
