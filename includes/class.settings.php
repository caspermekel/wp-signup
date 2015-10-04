<?php
class Qinvoice_Signup_Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function load()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }


    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Qinvoice Signup', 
            'Qinvoice Signup', 
            'manage_options', 
            'qinvoice-signup', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'qinvoice-signup-settings' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Qinvoice Signup</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'qinvoice-signup-settings' );   
                do_settings_sections( 'qinvoice-signup-settings' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }


    /**
     * Validate options.
     *
     * @param  array $input options to valid.
     *
     * @return array        validated options.
     */
    public function validate_options( $input ) {
        // Create our array for storing the validated options.
        $output = array();
    
        // Loop through each of the incoming options.
        foreach ( $input as $key => $value ) {
    
            // Check to see if the current option has a value. If so, process it.
            if ( isset( $input[$key] ) ) {
                // Strip all HTML and PHP tags and properly handle quoted strings.
                if ( is_array( $input[$key] ) ) {
                    foreach ( $input[$key] as $sub_key => $sub_value ) {
                        $output[$key][$sub_key] = strip_tags( $input[$key][$sub_key] );
                    }

                } else {
                    $output[$key] = strip_tags( $input[$key] );
                }
            }
        }
    
        // Return the array processing any additional functions filtered by this action.
        return apply_filters( 'qs_validate_input', $output, $input );
    }


    /**
     * Register and add settings
     */
    public function page_init()
    {        
        $option = 'qinvoice-signup-settings';

        register_setting(
            'qinvoice-signup-settings', // Option group
            'qinvoice-signup-settings', // Option name
            array( $this, 'validate_options' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'qinvoice-signup-settings' // Page
        ); 

        add_settings_field(
            'channel_id', // ID
            'Channel ID', // Title 
            array( $this, 'text_element_callback' ), // Callback
            'qinvoice-signup-settings', // Page
            'setting_section_id', // Section           
            array(
                'menu'          => $option,
                'id'            => 'channel_id',
                'size'          => '40',
                'description'   => __( 'Your channel ID, found in partner environment','qinvoice-signup' )
            )
        );  

         add_settings_field(
            'enable_referrer', // ID
            'Enable referrer?', // Title 
            array( $this, 'checkbox_element_callback' ), // Callback
            'qinvoice-signup-settings', // Page
            'setting_section_id', // Section           
            array(
                'menu'          => $option,
                'id'            => 'enable_referrer',
                'size'          => '40',
                'value'         => 1,
                'description'   => __( 'Enable this to allow referrers to be set using url/r?=1234, it will overwrite your channel id.','qinvoice-signup' )
            )
        );   

        add_settings_field(
            'language', // ID
            'Website language', // Title 
            array( $this, 'text_element_callback' ), // Callback
            'qinvoice-signup-settings', // Page
            'setting_section_id', // Section           
            array(
                'menu'          => $option,
                'id'            => 'language',
                'size'          => '40',
                'description'   => __( 'Main language on this website. E.g. nl_NL. Also used for user account','qinvoice-signup' )
            )
        );  

        add_settings_field(
            'termsconditions_url', // ID
            'Terms & Conditions URL', // Title 
            array( $this, 'select_element_callback' ), // Callback
            'qinvoice-signup-settings', // Page
            'setting_section_id', // Section           
            array(
                'menu'          => $option,
                'id'            => 'termsconditions_url',
                'size'          => '40',
                'description'   => __( 'URL to terms and conditions page.','qinvoice-signup' ),
                'options'       => $this->list_pages()
            )
        );  

         add_settings_field(
            'login_url', // ID
            'Login link URL', // Title 
            array( $this, 'text_element_callback' ), // Callback
            'qinvoice-signup-settings', // Page
            'setting_section_id', // Section           
            array(
                'menu'          => $option,
                'id'            => 'login_url',
                'size'          => '40',
                'description'   => __( 'URL for login link.','qinvoice-signup' )
            )
        );  

        add_settings_field(
            'test_mode', // ID
            'Test mode?', // Title 
            array( $this, 'checkbox_element_callback' ), // Callback
            'qinvoice-signup-settings', // Page
            'setting_section_id', // Section           
            array(
                'menu'          => $option,
                'id'            => 'test_mode',
                'size'          => '40',
                'value'         => 1,
                'description'   => __( 'Account creation will be simulated. No accounts will be created!','qinvoice-signup' )
            )
        );  

        
        
        
    }

    public function list_pages(){
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        ); 
        $pages = get_pages( $args );
        foreach ( $pages as $page ) {
            $return[ $page->ID ] =  $page->post_title;
        }
        return $return;
    }


    
    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        //print 'Enter your settings below:';
    }

  
        // Text element callback.
    public function text_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
        $size = isset( $args['size'] ) ? $args['size'] : '25';
    
        $options = get_option( $menu );
    
        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }

    
        $html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" size="%4$s"/>', $id, $menu, $current, $size );
    
        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<span class="description">%s</span>', $args['description'] );
        }
    
        echo $html;
    }
    
    // Text element callback.
    public function textarea_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
        $width = $args['width'];
        $height = $args['height'];
    
        $options = get_option( $menu );
    
        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }
    
        $html = sprintf( '<textarea id="%1$s" name="%2$s[%1$s]" cols="%4$s" rows="%5$s"/>%3$s</textarea>', $id, $menu, $current, $width, $height );
    
        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }
    
        echo $html;
    }


    /**
     * Checkbox field callback.
     *
     * @param  array $args Field arguments.
     *
     * @return string     Checkbox field.
     */
    public function checkbox_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
        
    
        $options = get_option( $menu );
    
        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }
    
        $html = sprintf( '<label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1" %3$s />%4$s</label>', $id, $menu, checked( 1, $current, false ), $label );
    
        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }
    
        echo $html;
    }
    
    /**
     * Multiple Checkbox field callback.
     *
     * @param  array $args Field arguments.
     *
     * @return string     Checkbox field.
     */
    public function multiple_checkbox_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
    
        $options = get_option( $menu );
    
    
        foreach ( $args['options'] as $key => $label ) {
            $current = ( isset( $options[$id][$key] ) ) ? $options[$id][$key] : '';
            printf( '<input type="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="1"%4$s /> %5$s<br/>', $menu, $id, $key, checked( 1, $current, false ), $label );
        }

        // Displays option description.
        if ( isset( $args['description'] ) ) {
            printf( '<p class="description">%s</p>', $args['description'] );
        }
    }

    /**
     * Select element callback.
     *
     * @param  array $args Field arguments.
     *
     * @return string     Select field.
     */
    public function select_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
    
        $options = get_option( $menu );
    
        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }
    
        $html = sprintf( '<select id="%1$s" name="%2$s[%1$s]">', $id, $menu );

        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
        }

        $html .= '</select>';
    
        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }
    
        echo $html;
    }

    /**
     * Select element callback.
     *
     * @param  array $args Field arguments.
     *
     * @return string     Select field.
     */
    public function button_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
        $description = $args['description'];
    
        $options = get_option( $menu );
    
        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }
    
        $html = sprintf( '<button id="%1$s" name="%2$s[%1$s]">', $id, $menu );
        $html .= $description;
        

        $html .= '</button>';
    
    
        echo $html;
    }

    /**
     * Displays a radio settings field
     *
     * @param array   $args settings field args
     */
    public function radio_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
    
        $options = get_option( $menu );
    
        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }

        $html = '';
        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $menu, $id, $key, checked( $current, $key, false ) );
            $html .= sprintf( '<label for="%1$s[%2$s][%3$s]"> %4$s</label><br>', $menu, $id, $key, $label);
        }
        
        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }

        echo $html;
    }
}
