<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Oneapi
 * @subpackage Oneapi/admin
 * @author     OneAPI <info@oneapi.ru>
 */
class Services_Oneapi {


    public function Oneapi_send_sms($appkey, $appsecret, $from, $to, $body) {
					ini_set('date.timezone', 'UTC');
					$nonce = "OneAPINonce";
					$created = date('Y-m-d\TH:i:s').'Z';
					ini_set('date.timezone', 'UTC');
					$wssssw_in = base64_encode(hash('sha256', $nonce.$created.$appsecret, 'true'));
					$wssssw = "UsernameToken Username="."\"".$appkey."\"".", PasswordDigest="."\"".$wssssw_in."\"".", Nonce="."\"".$nonce."\"".", Created="."\"".$created."\"";

					$url = 'http://api.oneapi.ru/sms/sendSms/v1';
					$ch = curl_init($url);
					$headers = array( 
								"Content-Type:application/x-www-form-urlencoded", 
								"X-WSSE:".$wssssw,
								"Authorization: WSSE realm=\"SDP\", profile=\"UsernameToken\", type=\"AppKey\""	);

					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, 'from='.$from.'&to='.$to.'&body='.$body);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					$log = new Oneapi_Logger();
					$log->add('Oneapi SMS', '1'.$appkey);
					$log->add('Oneapi SMS', '1'.$appsecret);
					$log->add('Oneapi SMS', '1'.$from);
					$log->add('Oneapi SMS', '1'.$to);
					$log->add('Oneapi SMS', '1'.$body);
					$response = curl_exec($ch);
					curl_close($ch);
    }
}
