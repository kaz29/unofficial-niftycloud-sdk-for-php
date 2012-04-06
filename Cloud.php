<?php
namespace decr_jp\NiftyCloud;

class Cloud extends Base
{
  public function __construct($params=null)
  {
    parent::__construct($params);
    
    $settings = array(
   		'api_url' => 'https://cp.cloud.nifty.com/api/',
  		'SignatureMethod' => 'HmacSHA256',
  		'SignatureVersion' => 'v2',
   );

    $this->settings = array_merge($this->settings, $settings) ;
  }

	/**
	 * 認証用の署名を生成 (signature version:0)
	 *
	 * @param void
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	protected function sign_v0()
	{
		unset($this->request_params['SignatureMethod']);

		$string_to_sign = $this->request_params['Action'].$this->request_params['Timestamp'];
		return base64_encode(hash_hmac('sha1', $string_to_sign, $this->settings['secret_key'], true));
	}
  
	/**
	 * 認証用の署名を生成 (signature version:1)
	 *
	 * @param void
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	protected function sign_v1()
	{
		unset($this->request_params['SignatureMethod']);
		$params = $this->request_params;
    uksort($params, 'strnatcmp');
		$string_to_sign = "";
		foreach($params as $key => $value) {
			$string_to_sign .= $key.$value;
		}
		
		return base64_encode(hash_hmac('sha1', $string_to_sign, $this->settings['secret_key'], true));
	}

	/**
	 * 認証用の署名を生成 (signature version:2)
	 *
	 * @param array     $params     パラメータ
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	protected function sign_v2($params=array())
	{
		$params = $this->request_params;
    uksort($params, 'strnatcmp');

		$url_hash = parse_url($this->settings['api_url']);

		$string_to_sign = "GET\n{$url_hash['host']}\n{$url_hash['path']}\n";
		$string_to_sign .= $this->create_query_string($params);
		$method = strtolower(substr($params['SignatureMethod'], 4));

		return base64_encode(hash_hmac($method, $string_to_sign, $this->settings['secret_key'], true));
	}

	/**
	 * APIリクエストを送信する
	 *
	 * @param string    $method     リクエストするメッド名
	 * @param array     $params     パラメータ
	 * @access public
	 * @return mixed
	 * @author Kaz Watanabe
	 **/
	public function request($method, $params=array())
	{
		$this->is_error = true ;
		$this->response_status = null;
		$this->request_params = null;
		$this->request_url = null;
		
		$sign_date = $this->sign_date();

		$this->request_params = array_merge(
			array(
				'Action' => $method,
				'AccessKeyId' => $this->settings['access_key'],
				'SignatureVersion' => substr($this->settings['SignatureVersion'], 1),
				'SignatureMethod' => $this->settings['SignatureMethod'],
				'Timestamp' => $sign_date,
			),
			$params
		);
		
		$this->request_params['Signature'] = $this->sign($this->settings['SignatureVersion'], $sign_date);
		$this->request_url = "{$this->settings['api_url']}?".$this->create_query_string($this->request_params);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_URL, $this->request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		if ( $response === false ) {
			return false ;
		}

		$this->response_status  = curl_getinfo($ch);
		if ( (int)$this->response_status['http_code']/100 != 2 ) {
			return $this->analyze_response($response);
		}
		
		$result = $this->analyze_response($response);
		curl_close($ch);
		
		$this->is_error = false ;
		return $result;
	}

	/**
	 * 指定したサーバーの情報を取得
	 *
	 * @param mixed		$instanceids		サーバー名
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_instances($instanceids=array())
	{
		$params = array();
		if ( !is_array($instanceids) ) {
			$instanceids = (array)$instanceids;
		}
		
		$index = 0;
		foreach($instanceids as $key => $value) {
			$index = $key+1;
			$params["InstanceId.{$index}"] = $value;
		}
		return $this->request('DescribeInstances', $params);
	}
}