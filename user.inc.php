<?php
	namespace itlife\user;
	infra_require('*session/session.php');
	infra_require('*aws.inc.php');

	
	class user {
		static function isAdmin(){
			$email=infra_session_getEmail();
			if(!$email)return false;
			$verify=infra_session_getVerify();
			if(!$verify)return false;
			$conf=infra_config();
			return in_array($email,$conf['user']['admins']);
		}
		static function get(){
			$json='*user/user.php';
			$user=infra_loadJSON($json);
			return $user;
		}
		static function sentEmail($email,$tpl,$data){
			$to='user';// manager
			if(function_exists('aws_mail')){
				return aws_mail($to,$email,$mailroot,$data);
			}else{
				return user_mail($to,$email,$mailroot,$data);
			}
		}
		static function getEmail(){
			return infra_session_getEmail();
		}
		static function checkData($str, $type='value'){
			switch($type){
				case 'radio': return !!$str;
				case 'value': return $str&&strlen($str)>1;
				case 'password': return $str&&strlen($str)>5;
				case 'email': return $str&&preg_match('/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/',$str);
			}
		}
		static function mail($to,$email,$mailroot, $data=array()){
			if(!$email){
				if($to=='manager')$email='noreplay@'.$_SERVER['HTTP_HOST'];
				else return 'Wrong email.';
			}
			if(!$mailroot)return;//Когда нет указаний в конфиге... ничего такого...
			$tpl='*user/user.mail.tpl';

			$data['host']=infra_view_getHost();
			$data['path']=infra_view_getRoot();
			$data['email']=$email;
			$data['time']=time();
			$data["site"]=$data['host'].'/'.$data['path'];

			$subject = infra_template_parse($tpl,$data,$mailroot.'-subject');
			$body = infra_template_parse($tpl,$data,$mailroot);
			//return $body;
			if($to=='user')return infra_mail_fromAdmin($subject,$email,$body);
			if($to=='manager')return infra_mail_toAdmin($subject,$email,$body);
		}
	}
?>