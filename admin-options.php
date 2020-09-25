<?php
class WooQuickPhoneOrderAdminPage
{
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'mw_enqueue_color_picker'));
    }

    public function mw_enqueue_color_picker() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'my-script-handle', plugins_url('js/quick-order.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }

    public function add_plugin_page() {
        add_options_page(
            'Settings Admin',
            'Woo Quick Phone Order Settings',
            'manage_options',
            'woo-quick-phone-order',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('wqpo_options');
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php
                settings_fields('wqpo_options_group');
                do_settings_sections('woo-quick-phone-order');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init()
    {
        register_setting(
            'wqpo_options_group', // Option group
            'wqpo_options', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Woo Quick Phone Order Settings', // Title
            array($this, 'print_section_info'), // Callback
            'woo-quick-phone-order' // Page
        );

        add_settings_field(
            'button_color_callback', // ID
            'ID Number', // Title
            array($this, 'button_color_callback'), // Callback
            'woo-quick-phone-order', // Page
            'setting_section_id', // Section
            array('class' => 'button_color', 'label_for' => 'button_color_callback')
        );

        add_settings_field(
            'box_color_callback', // ID
            'ID Number', // Title
            array($this, 'box_color_callback'), // Callback
            'woo-quick-phone-order', // Page
            'setting_section_id', // Section
            array('class' => 'box_color','label_for' => 'box_color_callback')
        );
    }

    public function sanitize($input)
    {
        $new_input = array();

        if(isset($input['button_color']))
            $new_input['button_color'] = sanitize_text_field($input['button_color']);

        if(isset($input['box_color']))
            $new_input['box_color'] = sanitize_text_field($input['box_color']);

        return $new_input;
    }

    public function print_section_info() {
        print 'Change background colors of form and button';
    }

    public function button_color_callback() {
        printf(
            '<input type="text" id="button_color" name="wqpo_options[button_color]" value="%s" />',
            isset($this->options['button_color']) ? esc_attr($this->options['button_color']) : ''
        );
    }

    public function box_color_callback()
    {
        printf(
            '<input type="text" id="box_color" name="wqpo_options[box_color]" value="%s" />',
            isset($this->options['box_color']) ? esc_attr($this->options['box_color']) : ''
        );
    }
}

