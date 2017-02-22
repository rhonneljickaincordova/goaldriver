<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Encryption_lib {
    
    var $skey     = "e784d84183281c3f6bbd701508ba7e52"; // you can change it
    var $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->skey = "e784d84183281c3f6bbd701508ba7e52";
    }
    public  function safe_b64encode($string) {
    
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
 
    public function safe_b64decode($string,$return=false) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data,$return);
    }
    
    public  function encode($value){ 
        
        if(!$value){return false;}
        $text = $value;
        $hash = md5($text);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }
    
    public function decode($value){
        
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}