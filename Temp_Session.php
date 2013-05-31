<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * We sub-class CI's Session library here in order to add support for browser-session-expiring userdata
 */
class Temp_Session extends CI_Session
{
    var $session_key = null;

	//encapsulate token generation
	function generate_token(){
		$token = md5(uniqid(rand(),true));
		return $token;
	}

	//set userdata that will expire at the end of the current browser session
	public function set_temp_userdata($name, $data, $expire = 0){
		$this->CI->load->helper('cookie');

        if($this->session_key != null){
            $key = $this->session_key;
        }else{
		    $key = $this->CI->input->cookie('temp_session_key');
            if($key != FALSE){
                $this->session_key = $key;
            }
        }

        if($key == FALSE){
            //basically, we set another cookie to expire after the browser session ends
            //and add this key to the userdata array, which we will use for session validation 
            $session_key = $this->generate_token();
            $data['key'] = $session_key;
            $this->session_key = $session_key;
            $cookie = array(
                        'name'=>'temp_session_key',
                        'value'=>$session_key,
                        'path'=>'/',
                        'expire'=>$expire
                    );
            set_cookie($cookie);
        }else{
            $data['key'] = $key;
        }
		$this->userdata[$name] = $data;
		$this->sess_write();
	}

	//get temp userdata (to unset, just call regular unset_userdata)
	public function temp_userdata($name){
		$key = $this->CI->input->cookie('temp_session_key');
		if($key && isset($this->userdata[$name]) && isset($this->userdata[$name]['key'])){
			if($key == $this->userdata[$name]['key']){
				return $this->userdata[$name];
			}else{
				//session expired, invalidate
				$this->unset_userdata($name);
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
}
