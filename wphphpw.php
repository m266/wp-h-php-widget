<?php

/*
Plugin Name: WP H-PHP-Widget
Plugin URI: https://github.com/m266/wp-h-php-widget
Description: Dieses Plugin f&uuml;gt ein Widget zum Einf&uuml;gen von PHP-Code im Dashboard hinzu.
Version: 1.0
Date: 2017-08-16
Text Domain: php-widget
Author: Hans M. Herbrand
Author URI: http://www.web266.de
Credits: http://ottopress.com/wordpress-plugins/php-code-widget/
https://www.drweb.de/magazin/so-einfach-erstellst-du-ein-eigenes-wordpress-widget-69104/
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
// Externer Zugriff verhindern
defined('ABSPATH') || exit();
// Plugin-Updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://github.com/m266/wp-h-php-widget', __FILE__, 'wp-h-php-widget');
class PHP_Widget extends WP_Widget {
// Frontend-Design Funktionen
  public function __construct() {
    load_plugin_textdomain('wp-h-php-widget', false, dirname(plugin_basename(__FILE__)));
    $widget_ops = array('classname' => 'widget_execphp', 'description' => __('Beliebiger PHP-Code. ', 'wp-h-php-widget'));
    $control_ops = array('width' => 400, 'height' => 350);
    parent::__construct('execphp', __('PHP', 'wp-h-php-widget'), $widget_ops, $control_ops);
  }
  public function widget($args, $instance) {
// Funktionen für die Ausgabe des Codes auf der Website
    extract($args);
    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
    $text = apply_filters('widget_execphp', $instance['text'], $instance);
    echo $before_widget;
    if (!empty($title)) {
      echo $before_title . $title . $after_title;
    }
    ob_start();
    eval('?>' . $text);
    $text = ob_get_contents();
    ob_end_clean();
    ?>
      <div class="execphpwidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
                                <?php
                                echo $after_widget;
                              }
                              public function form($instance) {
                            // Erstellt das Kontroll-Formular im WP-Dashboard
                                $instance = wp_parse_args((array) $instance, array('title' => '', 'text' => ''));
                                $title = strip_tags($instance['title']);
                                $text = format_to_edit($instance['text']);
                                ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-h-php-widget'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

    <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

    <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatisch Abs&auml;tze hinzuf&uuml;gen.', 'wp-h-php-widget'); ?></label></p>
                            <?php
                          }
                        // Updating der Widget-Funktionen
                          public function update($new_instance, $old_instance) {
                            $instance = $old_instance;
                            $instance['title'] = strip_tags($new_instance['title']);
                            if (current_user_can('unfiltered_html'))
                              $instance['text'] = $new_instance['text'];
                            else
                              $instance['text'] = stripslashes(wp_filter_post_kses($new_instance['text']));
                            $instance['filter'] = isset($new_instance['filter']);
                            return $instance;
                          }
                        }
                        // Registrierung Widget
                        function php_widget_init() {
                          register_widget('php_widget');
                        }
                        add_action('widgets_init', 'php_widget_init');