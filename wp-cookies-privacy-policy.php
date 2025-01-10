<?php
/**
 * Plugin Name: WP Cookies & Privacy Policy
 * Plugin URI: https://github.com/nakharinit/WP-Cookies-Privacy-Policy
 * Description: A simple plugin to display a WP Cookies & Privacy Policy banner.
 * Version: 1.2
 * Author: Your Name
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Enqueue styles and scripts
function cpp_enqueue_assets() {
    wp_enqueue_style('cpp-google-font', 'https://fonts.googleapis.com/css2?family=Noto+Serif+Thai:wght@400;700&display=swap', false);
    wp_enqueue_style('cpp-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('cpp-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
    wp_localize_script('cpp-script', 'cpp_ajax_url', array('url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'cpp_enqueue_assets');

// Add the WP Cookies & Privacy Policy banner
function cpp_display_banner() {
    if (!isset($_COOKIE['cpp_accepted'])) {
        echo '<div id="cpp-banner" class="cpp-banner">
                <p>เราใช้คุกกี้สำหรับการวิเคราะห์เพื่อเพิ่มประสิทธิภาพในการใช้งานของคุณ ตรวจสอบรายละเอียดทั้งหมดได้ที่ <a href="/privacy-policy">นโยบายความเป็นส่วนตัว</a>.</p>
                <div class="cpp-buttons">
                    <button id="cpp-settings" class="cpp-button">ตั้งค่าคุกกี้</button>
                    <button id="cpp-accept" class="cpp-button accept">ยอมรับ</button>
                </div>
              </div>';

        // Add the settings popup
        echo '<div id="cpp-settings-popup" class="cpp-popup" style="display: none;">
                <div class="cpp-popup-content">
                    <h2>ตั้งค่าคุกกี้</h2>
                    <p>คุณสามารถปรับแต่งประเภทของคุกกี้ที่คุณต้องการใช้งานได้:</p>
                    <label><input type="checkbox" id="analytics-cookies" checked> คุกกี้วิเคราะห์</label><br>
                    <label><input type="checkbox" id="functional-cookies" checked> คุกกี้เพื่อการทำงาน</label><br>
                    <div class="cpp-popup-buttons">
                        <button id="cpp-save-settings" class="cpp-button">บันทึก</button>
                        <button id="cpp-close-settings" class="cpp-button">ปิด</button>
                    </div>
                </div>
              </div>';
    }
}
add_action('wp_footer', 'cpp_display_banner');

// Handle AJAX request for user consent
function cpp_handle_consent() {
    if (isset($_POST['consent'])) {
        $consent = sanitize_text_field($_POST['consent']);
        if ($consent === 'accept') {
            setcookie('cpp_accepted', 'yes', time() + (365 * 24 * 60 * 60), '/'); // 1 year
        } elseif ($consent === 'settings') {
            // Handle settings logic here
        }
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action('wp_ajax_cpp_consent', 'cpp_handle_consent');
add_action('wp_ajax_nopriv_cpp_consent', 'cpp_handle_consent');

// Create CSS file
file_put_contents(plugin_dir_path(__FILE__) . 'css/style.css', "#cpp-banner { position: fixed; bottom: 0; width: 100%; background: #f5f6fb; color: #333; text-align: center; padding: 20px 10px; z-index: 9999; font-family: 'Noto Serif Thai', serif; border-top: 1px solid #ddd; }
#cpp-banner p { margin: 0; font-size: 14px; }
#cpp-banner a { color: #0066cc; text-decoration: none; font-weight: bold; }
.cpp-buttons { margin-top: 10px; }
.cpp-button { background-color: #fff; border: 1px solid #0066cc; color: #0066cc; padding: 8px 15px; font-size: 14px; border-radius: 5px; cursor: pointer; margin-right: 10px; }
.cpp-button.accept { background-color: #0066cc; color: #fff; border: none; }
.cpp-button:hover { opacity: 0.9; }
.cpp-popup { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; border: 1px solid #ddd; padding: 20px; z-index: 10000; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); font-family: 'Noto Serif Thai', serif; }
.cpp-popup-content h2 { margin-top: 0; }
.cpp-popup-buttons { margin-top: 10px; text-align: right; }
");

// Create JavaScript file
file_put_contents(plugin_dir_path(__FILE__) . 'js/script.js', "jQuery(document).ready(function($) {
    $('#cpp-accept, #cpp-settings').on('click', function() {
        var consent = $(this).attr('id') === 'cpp-accept' ? 'accept' : 'settings';
        if (consent === 'settings') {
            $('#cpp-settings-popup').fadeIn();
        } else {
            $.post(cpp_ajax_url.url, { action: 'cpp_consent', consent: consent }, function() {
                $('#cpp-banner').fadeOut();
            });
        }
    });
    $('#cpp-save-settings').on('click', function() {
        // Logic to save settings can go here
        alert('การตั้งค่าคุกกี้ของคุณถูกบันทึกแล้ว!');
        $('#cpp-settings-popup').fadeOut();
    });
    $('#cpp-close-settings').on('click', function() {
        $('#cpp-settings-popup').fadeOut();
    });
});");
