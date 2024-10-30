<?php
if(!class_exists('WP_CHC_contact_from_captcha')){
    class WP_CHC_contact_from_captcha extends WP_CHC_Form_Field{
    	private $enabled_catpacha = 'ch-captcha';
    	private $captcha;
    	function __construct() {


            parent::__construct();

    		if(isset($_SESSION))
    			session_start();
            
    		//add_filter( 'wpcf7_form_elements', 'cf7sr_wpcf7_form_elements' );
    		add_shortcode( 'ch-captcha', 'get_captcha_shortcode' );



            /* Shortcode handler */

            add_action( 'wpcf7_admin_init', array($this,'wpcf7_add_tag_generator_captcha'), 46 );

            /* Tag genrater */
            add_action( 'wpcf7_init', array($this,'wpcf7_add_shortcode_captcha') );

            /* Validation filter */
            add_filter( 'wpcf7_validate_ch_captcha', array($this,'wpcf7_captcha_validation_filter'), 10, 2 );

            /* Ajax echo filter */
            add_filter( 'wpcf7_ajax_onload', array($this,'wpcf7_captcha_ajax_refill') );
            add_filter( 'wpcf7_ajax_json_echo', array($this,'wpcf7_captcha_ajax_refill') );


            add_action( 'wp_ajax_reload_ch_captcha', array($this,'wpcf7_captcha_ajax_reload') );
            add_action( 'wp_ajax_nopriv_reload_ch_captcha', array($this,'wpcf7_captcha_ajax_reload') );


            add_action( 'wp_head', array($this,'script') ,9999);

            /* Messages */
            add_filter( 'wpcf7_messages', array($this,'wpcf7_captcha_messages') );

            /* Warning message */
            add_action( 'wpcf7_admin_notices', array($this,'wpcf7_captcha_display_warning_message') );



    		
            $this->captcha = include(dirname(__FILE__)."/"."custom-captcha.php");

        }


        function wpcf7_add_tag_generator_captcha() {
            $tag_generator = WPCF7_TagGenerator::get_instance();
            $tag_generator->add('ch_captcha',
                __( 'Ch Captcha', 'contact-form-7' ),
                array($this,'wpcf7_tag_generator_captcha') );
        }
        function wpcf7_tag_generator_captcha( $contact_form, $args = '' ) {
            $args = wp_parse_args( $args, array() );

            $image_fields = array(
                            array(
                                    'type'      => 'color_picker',
                                    'label'     => 'Text Color',
                                    'name'      => 'textColor',
                                    'default'   => '#000',
                                    'change'    => '$("#imgWidth").trigger("change");'
                                ),
                            array(
                                    'type'      => 'color_picker',
                                    'label'     => 'Background Color',
                                    'name'      => 'backgroundColor',
                                    'default'   => '#56aad8',
                                    'change'    => '$("#imgWidth").trigger("change");'
                                ),
                            array(
                                    'type'      => 'color_picker',
                                    'label'     => 'Noice Color',
                                    'name'      => 'noiceColor',
                                    'default'   => '#162453',
                                    'change'    => '$("#imgWidth").trigger("change");'
                                ),
                            array(
                                    'type'      => 'text',
                                    'label'     => 'Image Width',
                                    'name'      => 'imgWidth',
                                    'default'   => '120',
                                ),
                            array(
                                    'type'      => 'text',
                                    'label'     => 'Image Height',
                                    'name'      => 'imgHeight',
                                    'default'   => '50',
                                ),
                            array(
                                    'type'      => 'text',
                                    'label'     => 'Noice Lines',
                                    'name'      => 'noiceLines',
                                    'default'   => '10',
                                ),
                            array(
                                    'type'      => 'text',
                                    'label'     => 'Noice Dots',
                                    'name'      => 'noiceDots',
                                    'default'   => '25',
                                ),
                            );
        ?>
        <div class="control-box">
        <fieldset>

        <table class="form-table">
        <tbody>
            <tr>
            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
            <td><input readonly type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
            </tr>
        </tbody>
        </table>

        <table class="form-table scope captchac">
        <caption><?php echo esc_html( __( "Image settings", 'contact-form-7' ) ); ?></caption>
        <tbody>
        <?php
            foreach ($image_fields as $key => $value) {
                if(!isset($value['value'])){
                    $value['value'] = get_option($value['name']);
                    $value['class'] = 'idvalue oneline option';
                }
                echo $this->get_field($value);
            }
        ?>

        </tbody>
        </table>

        <table class="form-table scope ch-captchr">
            <caption><?php echo esc_html( __( "Input field settings", 'contact-form-7' ) ); ?></caption>
            <tbody>
                <tr>
                <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-ch-captcha-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
                <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-ch-captcha-id' ); ?>" /></td>
                </tr>

                <tr>
                <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-ch-captcha-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
                <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-ch-captcha-class' ); ?>" /></td>
                </tr>
            </tbody>
        </table>
        </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="ch_captcha" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
            <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
            </div>
        </div>
        <?php
        }
        function wpcf7_add_shortcode_captcha() {
            wpcf7_add_shortcode('ch_captcha',
                array($this,'wpcf7_captcha_shortcode_handler'), true );
        }

        function wpcf7_captcha_shortcode_handler( $tag ) {
            $tag = new WPCF7_Shortcode( $tag );

            //if ( 'captchac' == $tag->type && ! class_exists( 'ReallySimpleCaptcha' ) )
            //    return '<em>' . __( 'To use CAPTCHA, you need <a href="http://wordpress.org/extend/plugins/really-simple-captcha/">Really Simple CAPTCHA</a> plugin installed.', 'contact-form-7' ) . '</em>';


            $validation_error = wpcf7_get_validation_error( $tag->name );

            $class = wpcf7_form_controls_class( $tag->type );

            if ( 'ch_captcha' == $tag->type ) {
                $class .= ' wpcf7-captcha-' . $tag->name;

                $atts = array();

                $atts['class'] = $tag->get_class_option( $class );
                $atts['id'] = $tag->get_id_option();


                $op = array( // Default
                    'textColor' =>'#162453',
                    'backgroundColor' =>'#ffffff',
                    'imgWidth' => 120,
                    'imgHeight' => 50,
                    'noiceLines' => 10,
                    'noiceDots' => 25 
                );

                $op = array_merge( $op, $this->wpcf7_captchac_options( $tag->options ) );

                $atts = array_merge($op, $atts);

                if (strpos($atts['textColor'], '#') === false) {
                    $atts['textColor'] = "#".$atts['textColor'];
                }

                if (strpos($atts['backgroundColor'],'#') === false) {
                    $atts['backgroundColor'] = "#".$atts['backgroundColor'];
                }

                //$atts = wpcf7_format_atts( $atts );

                $prefix = substr( $filename, 0, strrpos( $filename, '.' ) );

                ob_start();
                $captcha_image = $this->captcha->get_captcha($op, $tag->name);
                
                ?>
                    <span class="ch-captcha-image-container">
                        <img src="<?php echo $captcha_image ?>" id="<?php echo $tag->name."-img"; ?>" class="<?php echo $tag->name."-img"; ?>">
                        <span class="refresh-link">Can't read the image? click <a href="javascript:" onclick="refreshCaptcha(this);">here</a> to refresh.</span>
                    </span>

                    <span class="ch-captcha-input-container wpcf7-form-control-wrap <?php echo sanitize_html_class( $tag->name ); ?>"><input type="text" name="_wpcf7_<?php echo $tag->name; ?>" class="ch-captcha-input <?php echo $atts["class"]?>"></span>
                <?php
                $html = ob_get_clean();
                $html = apply_filters('ch_captcha_contact_form_html', $html, $atts, $tag);
                return $html;

            }
        }

        function script(){
            ?>
            <script type="text/javascript">
                function refreshCaptcha(ele){

                    var form = jQuery(ele).closest('form');
                    var form_id = jQuery(form).find(':input[name="_wpcf7"]').val();

                    jQuery.ajax({
                        url : '<?php echo admin_url('admin-ajax.php') ?>',
                        data : {action : 'reload_ch_captcha', form_id : form_id},
                        type : 'post',
                        method : 'post',
                        dataType : 'json',
                        success : function(data){
                            jQuery.each(data.captcha, function(i, n) {
                                form.find(':input[name="_wpcf7_' + i + '"]').clearFields();
                                form.find('img.' + i+'-img').attr('src', n);
                            });
                        }
                    })
                }

                jQuery(document).ready(function(){
                    jQuery.fn.wpcf7RefillCaptcha = function(captcha) {
                        return this.each(function() {
                            var form = jQuery(this);
                            jQuery.each(captcha, function(i, n) {
                                form.find(':input[name="_wpcf7_' + i + '"]').clearFields();
                                form.find('img.' + i+'-img').attr('src', n);
                            });
                        });
                    };
                });
            </script> 
            <?php
        }


        function wpcf7_captcha_validation_filter( $result, $tag ) {
            $tag = new WPCF7_Shortcode( $tag );
            $type = $tag->type;
            $name = $tag->name;

            $captcha = '_wpcf7_' . $name;
            $captcha = isset( $_REQUEST[$captcha] ) ? (string) $_REQUEST[$captcha] : '';
            $response = 'Captcha';
            $response = wpcf7_canonicalize( $response );

            $is_valid = $this->captcha->is_captcha_valid($captcha, $tag->name);
            if ( 0 == strlen( $captcha ) || ! $is_valid) {
                $result->invalidate( $tag, wpcf7_get_message( 'captcha_not_match' ) );
            }

            if ( 0 != strlen( $captcha ) ) {
                wpcf7_remove_captcha( $captcha );
            }
            return $result;
        }

        function wpcf7_captcha_ajax_reload(){
            $form = wpcf7_contact_form( $_REQUEST['form_id'] );
            $item = $this->wpcf7_captcha_ajax_refill(null);
            echo json_encode($item);
            exit();
        }

        function wpcf7_captcha_ajax_refill( $items ) {
            $fes = wpcf7_scan_shortcode( array( 'type' => 'ch_captcha' ) );
            if ( empty( $fes ) )
                return $items;

            $refill = array();

            foreach ( $fes as $fe ) {
                $name = $fe['name'];
                $options = $fe['options'];

                 $op = array( // Default
                    'textColor' =>'#162453',
                    'backgroundColor' =>'#ffffff',
                    'imgWidth' => 120,
                    'imgHeight' => 50,
                    'noiceLines' => 10,
                    'noiceDots' => 25 
                );

                $options = array_merge( $op, $this->wpcf7_captchac_options( $options ) );

                if ( empty( $name ) )
                    continue;
             
                $captcha_image = $this->captcha->get_captcha($options, $name);
                $refill[$name] = $captcha_image;
              
            }

            if ( ! empty( $refill ) )
                $items['captcha'] = $refill;

            return $items;
        }


        function wpcf7_captcha_messages( $messages ) {
            return array_merge( $messages, array( 'captcha_not_match' => array(
                'description' => __( "The code that sender entered does not match the CAPTCHA", 'contact-form-7' ),
                'default' => __( 'Your entered code is incorrect.', 'contact-form-7' )
            ) ) );
        }


        function wpcf7_captcha_display_warning_message() {
            if ( ! $contact_form = wpcf7_get_current_contact_form() ) {
                return;
            }

            $has_tags = (bool) $contact_form->form_scan_shortcode(
                array( 'type' => array( 'ch-captcha' ) ) );

            if ( ! $has_tags ) {
                return;
            }

            if ( ! function_exists( 'imagecreatetruecolor' ) || ! function_exists( 'imagettftext' ) ) {
                $message = __( 'This contact form contains CAPTCHA fields, but the necessary libraries (GD and FreeType) are not available on your server.', 'contact-form-7' );

                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
            }
        }
    
        function wpcf7_captchac_options($options){
            $args = array();
            foreach ($options as $key => $value) {
                $value = explode(':', $value);
                $args[$value[0]] = $value[1];
            }
            return $args;
        }
    }

}
new WP_CHC_contact_from_captcha();
