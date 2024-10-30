<?php
if(!class_exists('WP_CHC_captcha')){
	class WP_CHC_captcha {
		
		function __construct() {
			if(isset($_SESSION))
				session_start();


	        include_once(WP_CHC()->plugin_path('custom-captcha/phptextClass.php'));

	        $this->captcha_option = array(
	        	'textColor' 	=> get_option( 'chc-textColor' ),
				'backgroundColor' => get_option( 'chc-backgroundColor' ),
				'imgWidth' 		=> get_option( 'chc-imgWidth' ),
				'imgHeight' 	=> get_option( 'chc-imgHeight' ),
				'noiceLines' 	=> get_option( 'chc-noiceLines' ),
				'noiceDots' 	=> get_option( 'chc-noiceDots' ),
				'noiceColor' 	=> get_option( 'chc-noiceColor' ),
	        	);

	        $this->captcha_option = array_merge(array(
				'textColor' 	=> '#000',
				'backgroundColor' 	=> '#fff',
				'imgWidth' 		=> 120,
				'imgHeight' 	=> 40,
				'noiceLines' 	=> 10,
				'noiceDots' 	=> 25,
				'noiceColor' 	=> '#162453',
			), array_filter($this->captcha_option));
	    }
		
		function get_captcha($args = array(), $key="chc_captcha"){
			$phptextObj = new phptextClass();	
			$phptextObj->set_session_key($key);
			
			$this->captcha_option = array_merge($this->captcha_option, $args);

			extract($this->captcha_option);


			ob_start();
			$phptextObj->phpcaptcha($textColor,$backgroundColor,$imgWidth,$imgHeight,$noiceLines,$noiceDots,$noiceColor);

			return "data:image/jpeg;base64," . base64_encode (ob_get_clean());
		}
		
		function is_captcha_valid($captcha, $key='chc_captcha', $case = true){
			@session_start();
			if(is_bool($key)){
				$case = $key;
				$key = 'chc_captcha';
			}
			if(empty($key)){
				$key = 'chc_captcha';
			}

			if(!$case){
				$_session = strtolower($_SESSION[$key]);
				$captcha = strtolower($captcha);
				return (isset($_SESSION[$key]) && ($_session == $captcha));
			}
			return (isset($_SESSION[$key]) && ($_SESSION[$key] == $captcha));
		}
		
	}
}
return new WP_CHC_captcha();