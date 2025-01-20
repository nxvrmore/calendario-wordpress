<?php
/*
Plugin Name: Calendario Plugin
Description: Un plugin para mostrar un calendario con 31 días y permitir añadir texto en cada día.
Version: 1.0
Author: BIERZOSEO
*/

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Registrar el shortcode
function calendario_shortcode() {
    ob_start();
    mostrar_calendario();
    return ob_get_clean();
}
add_shortcode('calendario', 'calendario_shortcode');

// Función para mostrar el calendario
function mostrar_calendario() {
    // Obtener los textos guardados
    $textos = get_option('calendario_textos', array());

    // Estructura del calendario
    echo '<div class="calendario-container">';
    for ($i = 1; $i <= 31; $i++) {
        echo '<div class="calendario-dia">';
        echo '<span class="dia-numero">' . $i . '</span>';
        echo '<div class="dia-texto">' . (isset($textos[$i]) ? esc_html($textos[$i]) : '') . '</div>';
        echo '</div>';
    }
    echo '</div>';
}

function calendario_estilos() {
    echo '
    <style>
        .calendario-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px;
            background-color: transparent; /* Fondo transparente */
            border-radius: 10px;
        }
        .calendario-dia {
            flex: 1 1 calc(100% / 7 - 20px);
            box-sizing: border-box;
            border: 1px solid #e0e0e0;
            padding: 10px;
            text-align: center;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            background-color: #957dad3f; /* Fondo semitransparente */
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }
        .calendario-dia:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .dia-numero {
            font-size: 1em;
            font-weight: bold;
            color: #957dad;
            position: absolute;
            bottom: 5px;
            left: 5px;
        }
        .dia-texto {
            margin-top: 10px;
            font-size: 1em;
            color: #333;
            font-weight: 500;
            text-align: left;
            width: 100%;
        }
        @media (max-width: 768px) {
            .calendario-dia {
                flex: 1 1 calc(100% / 4 - 20px);
            }
        }
        @media (max-width: 480px) {
            .calendario-dia {
                flex: 1 1 calc(100% / 2 - 20px);
            }
        }
    </style>
    ';
}
add_action('wp_head', 'calendario_estilos');

// Añadir menú de administración
function calendario_menu_admin() {
    add_menu_page(
        'Calendario',
        'Calendario',
        'manage_options',
        'calendario-admin',
        'calendario_admin_page',
        'dashicons-calendar',
        6
    );
}
add_action('admin_menu', 'calendario_menu_admin');

// Página de administración
function calendario_admin_page() {
    // Guardar los textos si se ha enviado el formulario
    if (isset($_POST['calendario_textos']) && check_admin_referer('guardar_calendario_textos', 'calendario_nonce')) {
        $textos = array();
        foreach ($_POST['calendario_textos'] as $dia => $texto) {
            $textos[intval($dia)] = sanitize_text_field($texto);
        }
        update_option('calendario_textos', $textos);
        echo '<div class="updated"><p>Textos guardados correctamente.</p></div>';
    }

    // Obtener los textos guardados
    $textos = get_option('calendario_textos', array());

    // Depuración: Mostrar los textos guardados
    echo '<pre style="display:none;">';
    print_r($textos);
    echo '</pre>';

    // Formulario de administración
    echo '<div class="wrap">';
    echo '<h1>Configuración del Calendario</h1>';
    echo '<form method="post" action="">';
    wp_nonce_field('guardar_calendario_textos', 'calendario_nonce');
    for ($i = 1; $i <= 31; $i++) {
        echo '<div>';
        echo '<label for="calendario_textos[' . $i . ']">Día ' . $i . ':</label>';
        echo '<input type="text" id="calendario_textos[' . $i . ']" name="calendario_textos[' . $i . ']" value="' . (isset($textos[$i]) ? esc_attr($textos[$i]) : '') . '" style="width: 300px; margin-bottom: 10px;">';
        echo '</div>';
    }
    echo '<input type="submit" class="button-primary" value="Guardar">';
    echo '</form>';
    echo '</div>';
}