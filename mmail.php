<?php

class Mmail{
	protected static $_swift = NULL;
	protected static $_smtp = NULL;
	protected static $_from = NULL;
	protected static $_lastresult = NULL;
	protected static $_attachments = array();
	protected static $_html_on = true;
	
	
	public static function init() {
		if(self::$_smtp === NULL) {
				$JConfig = new JConfig;
				self::$_from = $JConfig->smtpuser;
				self::$_smtp = Swift_SmtpTransport::newInstance($JConfig->smtphost, (int)$JConfig->smtpport, $JConfig->smtpsecure)
				->setUsername($JConfig->smtpuser)
				->setpassword($JConfig->smtppass);


		}
		if(self::$_swift === NULL) {
			self::$_swift = Swift_Mailer::newInstance(self::$_smtp);
			
		}
	}
	
	public static function set_attachment($attachment){
		self::$_attachments[] = $attachment;
	}
	
	public static function check_attachments() {
		$attachments = self::$_attachments;
		self::$_attachments = array();
		return $attachments;
	}
	


	public static function html_on(){
		self::$_html_on = true;
	}
	
	public static function html_off(){
		self:: $_html_on = false;
	}
	
	public static function mail($to, $subject, $body='', $headers='', $from='') {
		if(self::$_swift != NULL) {
			if(!$from) {
				
				$from = self::$_from;
			}
				//echo $from; exit;
			if(strpos($to,',')) {
				$to = str_replace(' ','', $to);
				$to = explode(',', $to);
			}
			else {
				$to = array($to);
			}
			

			$message = Swift_Message::newInstance($subject)
			  ->setFrom(array($from  => $from ))
			  ->setTo($to);
			
			
			if(self::$_html_on) {
				$message->setBody($body,'text/html');
			}
			else {
			  $message->setBody($body);
			}
			
			//$message->setBody($body,'text/html');
			if(sizeof(self::$_attachments)) {
				
			
				$attachments = self::check_attachments();
				
				foreach($attachments as $attachment) {
					$message->attach(Swift_Attachment::fromPath($attachment));
				}
			//$message = new Swift_Message($subject, $body);
			}
			try {
				//self::$_lastresult = self::$_smtp->send($message);
				self::$_lastresult = self::$_swift->send($message);
			}
			catch(Error $e) {
				self::$_lastresult = $e;
			}
			
		}
	
	}
	
	public static function lastresult() {
		print_r(self::$_lastresult);
	}
	
	//Connect to Gmail (PHP5)
	
	

}
?>