<?php
/**
 * Unofficial NiftyCloud SDK for PHP.
 *
 * PHP versions 5
 *
 * https://github.com/kaz29/aws-datasource-for-cakephp
 * Copyright 2011, Kazuhiro Watanabe a.k.a kaz29(http://d.hatena.ne.jp/kaz_29/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Kazuhiro Watanabe a.k.a kaz29(http://d.hatena.ne.jp/kaz_29/)
 * @package       unofficial_niftycloudsdk
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * NiftyCloud API用例外クラス
 *
 * @package unofficial_niftycloudsdk
 * @author Kaz Watanabe
 **/
class NiftyCloud_Exception extends Exception
{

}

/**
 * NiftyCloud API用基底クラス
 *
 * @package unofficial_niftycloudsdk
 * @author Kaz Watanabe
 **/
class NiftyCloudAPI
{
	public $settings = array(
		'secret_key' => null,
		'access_key' => null,
		'api_url' => 'https://cp.cloud.nifty.com/api/',
		'SignatureMethod' => 'HmacSHA256',
		'SignatureVersion' => 2,
	);

	private $version = '0.1a';
	private $request_params=null;
	private $request_url=null;
	private $response_status=null;
	private $is_error = true;
	
	/**
	 * コンストラクタ
	 *
	 * @param array   $settings 設定情報
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function __construct($settings=array())
	{
		$this->settings($settings);
	}

	/**
	 * 設定情報の取得/再設定
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function settings($settings=null)
	{
		if ( is_null($settings) )
			return $this->settings ;
		
		$this->settings = array_merge($this->settings, $settings);
		return $this->settings;
	}
	/**
	 * バージョン番号を取得
	 *
	 * @param void
	 * @access public
	 * @return string
	 * @author Kaz Watanabe
	 **/
	public function version()
	{
		return "{$this->version}";
	}
	
	/**
	 * エラーが発生状況を取得
	 *
	 * @param void
	 * @access public
	 * @return bool
	 * @author Kaz Watanabe
	 **/
	public function isError()
	{
		return $this->is_error;
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
				'SignatureVersion' => $this->settings['SignatureVersion'],
				'SignatureMethod' => $this->settings['SignatureMethod'],
				'Timestamp' => $sign_date,
			),
			$params
		);
		
		$this->request_params['Signature'] = $this->sign($method, $sign_date);
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
	 * リクエストURLを取得
	 *
	 * @param void
	 * @access public
	 * @return string
	 * @author Kaz Watanabe
	 **/
	public function getRequestURL()
	{
		return $this->request_url;
	}
	
	/**
	 * リクエストパラメータを取得
	 *
	 * @param void
	 * @access public
	 * @return array
	 * @author Kaz Watanabe
	 **/
	public function getRequestParams()
	{
		return $this->request_params;
	}
	
	/**
	 * レスポンスデータを取得
	 *
	 * @param void
	 * @access public
	 * @return array
	 * @author Kaz Watanabe
	 **/
	public function getResponseStatus()
	{
		return $this->response_status;
	}
	
	/**
	 * レスポンス文字列を解析し
	 *
	 * @param string		$response		レスポンスbody
	 * @access private
	 * @return SimpleXML Object
	 * @author Kaz Watanabe
	 **/
	private function analyze_response($response)
	{
		list($response_header, $response_body) = explode("\r\n\r\n", $response, 2);
		$result = new SimpleXMLElement($response_body);
		return $result;
	}
	
	/**
	 * APIリクエスト用のQuery Stringを作成
	 *
	 * @param array   $params リクエストパラメータ
	 * @access private
	 * @return string リクエストURL
	 * @author Kaz Watanabe
	 **/
	private function create_query_string($params)
	{
		$param_string = "";
		foreach ($params as $key => $value) {
			if ( strlen($param_string) > 0 ) 
				$param_string .= "&";
			$param_string .= rawurlencode($key) . '=' . rawurlencode($value);
		}

		return $param_string;
	}

	/**
	 * 認証用の署名を生成
	 *
	 * @param string    $method     リクエストするメッド名
	 * @param string    $sign_date  処理日時
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	private function sign($method, $sign_date)
	{
		if ( !isset($this->settings['SignatureVersion']) ) {
			throw new NiftyCloud_Exception('Signature Version not defined.', -1);
		}

		if ( $this->settings['SignatureVersion'] < 0 || $this->settings['SignatureVersion'] > 2) {
			throw new NiftyCloud_Exception('Invalid signature version.', -1);
		}

		$method_name = "sign_v{$this->settings['SignatureVersion']}";

		return $this->{$method_name}();
	}
  
	/**
	 * 認証用の署名を生成 (signeture version:0)
	 *
	 * @param void
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	private function sign_v0()
	{
		unset($this->request_params['SignatureMethod']);

		$string_to_sign = $this->request_params['Action'].$this->request_params['Timestamp'];
		return base64_encode(hash_hmac('sha1', $string_to_sign, $this->settings['secret_key'], true));
	}
  
	/**
	 * 認証用の署名を生成 (signeture version:1)
	 *
	 * @param void
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	private function sign_v1()
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
	 * 認証用の署名を生成 (signeture version:2)
	 *
	 * @param array     $params     パラメータ
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	private function sign_v2($params=array())
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
	 * 認証に使用する日時を取得する
	 *
	 * @param int $time		timestamp
	 * @access private
	 * @return string			署名用日付文字列
	 * @author Kaz Watanabe
	 **/
	private function sign_date($time=null)
	{
		return date('Y-m-d\TH:i:s\Z', $time);
	}
} // END class NiftyCloudAPI_Base