<?php
namespace decr_jp\NiftyCloud;

class Base
{
	const DATE_FORMAT_RFC2616 = 'D, d M Y H:i:s \G\M\T';

	public $settings = array(
		'secret_key'  => null,
		'access_key'  => null,
		'time_zone'   => 'Asia/Tokyo',
	);

	protected $version = '2.0a';
	protected $request_params = null;
	protected $request_url = null;
	protected $response_status = null;
	protected $is_error = true;

  public function __construct($params=null)
  {
    if ( is_array($params) ) {
      $this->settings = array_merge($this->settings, $params);
    }
  }

  public static function autoload($className)
  {
    $path = dirname(__FILE__);

    $items = explode('\\', $className);
    if (count($items) > 1) {
      $class = $items[count($items)-1];
      if ( $items[count($items)-2] === 'Cloud' ||
           $items[count($items)-2] === 'Storage' ) {
        $path .= DIRECTORY_SEPARATOR.$items[count($items)-2];
      }
    } else {
      $class = $items[0];
    }

    if ( !file_exists($path.DIRECTORY_SEPARATOR.$class.'.php') ) {
      throw new \RuntimeException('Class file not found.', -1);
    }
    require_once($path.DIRECTORY_SEPARATOR.$class.'.php');
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
	 * 設定情報をを取得
	 *
	 * @param void
	 * @access public
	 * @return string
	 * @author Kaz Watanabe
	 **/
	public function settings()
	{
		return $this->settings;
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
	 * レスポンス文字列を解析し
	 *
	 * @param string		$response		レスポンスbody
	 * @access private
	 * @return SimpleXML Object
	 * @author Kaz Watanabe
	 **/
	protected function analyze_response($response)
	{
		list($response_header, $response_body) = explode("\r\n\r\n", $response, 2);
	    // HTTP/1.1 100 Continue の対応
		for(;;) {
  		if (strncmp($response_body, 'HTTP/', 5) != 0)
  		  break ;
		  list($response_header, $response_body) = explode("\r\n\r\n", $response_body, 2);
		}
		
		$content_type = $this->response_status['content_type'];
		if (strpos($content_type, ';') !== false) {
		  list($content_type, $charset) = explode(';', $content_type);
		}
		
		if ( $content_type === 'application/xml') {
		  return new \SimpleXMLElement($response_body);
    } else {
      return $response_body;
    }
	}
	
	/**
	 * APIリクエスト用のQuery Stringを作成
	 *
	 * @param array   $params リクエストパラメータ
	 * @access private
	 * @return string リクエストURL
	 * @author Kaz Watanabe
	 **/
	protected function create_query_string($params)
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
	protected function sign($method_suffix, $sign_date)
	{
		$method_name = "sign_{$method_suffix}";

    if ( !method_exists($this, $method_name) ) {
			throw new \RuntimeException('Signature Method not defined.', -1);
    }

		return $this->{$method_name}($sign_date);
	}
	
	/**
	 * 認証に使用する日時を取得する
	 *
	 * @param int $time		timestamp
	 * @access private
	 * @return string			署名用日付文字列
	 * @author Kaz Watanabe
	 **/
	protected function sign_date($format=null, $time=null)
	{
	  $format = (is_null($format))?'Y-m-d\TH:i:s\Z':$format;
		return (is_null($time))?date($format):date($format, $time);
	}
	
	public function __get($name)
	{
	  return (isset($this->{$name}))?$this->{$name}:null;
	}
}

spl_autoload_register(array('\decr_jp\NiftyCloud\Base','autoload'));
