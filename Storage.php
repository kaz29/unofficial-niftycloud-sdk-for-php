<?php
namespace decr_jp\NiftyCloud;

class Storage extends Base
{
	const ACL_PRIVATE             = 'private';
	const ACL_PUBLIC_PRIVATE      = 'public-read';
	const ACL_AUTHENTICATED_READ  = 'authenticated-read';

  public function __construct($params=null)
  {
    parent::__construct($params);
    
    $settings = array(
   		'api_hostname' => 'ncss.nifty.com',
   );

    $this->settings = array_merge($this->settings, $settings) ;
  }
  
  /**
   * undocumented function
   *
   * @return void
   * @author Kaz Watanabe
   **/
  protected function create_url($bucket_name=null, $object_name=null)
  {
    $url = 'https://';
    if ( !is_null($bucket_name) ) {
      $url .= $bucket_name.'.';
    }
    
    $url .= $this->settings['api_hostname'];
    
    return $url;
  }
  
	/**
	 * 認証用の署名を生成 
	 *
	 * @param array     $params     パラメータ
	 * @access private
	 * @return string		署名文字列
	 * @author Kaz Watanabe
	 **/
	protected function sign($method, $request_data)
	{
		$string_to_sign = $this->string_to_sign($method, $request_data);
		return base64_encode(hash_hmac('sha1', $string_to_sign, $this->settings['secret_key'], true));
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	private function string_to_sign($method, $request_data)
	{    
	  if ( !isset($request_data['path']) ) {
	    throw new \RuntimeException('path not defined', -1);
	  }
	  
	  $header_prefix = 'x-nifty-';
    $string_to_sign = "{$method}\n";
    foreach($request_data as $key => $value) {
      if ( is_null($value) ) {
        $value = '';
      }

			if ( strtolower($key) === 'content-md5' ||
				   strtolower($key) === 'content-type' ||
				   strtolower($key) === 'date') {
        $string_to_sign .= "{$value}\n";
      }
      
      if (strncmp($key, $header_prefix, count($header_prefix)) == 0) {
        $string_to_sign .= "{$key}:{$value}\n";
      }
    }
    
    $string_to_sign .= $request_data['path'];

    return $string_to_sign;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	protected function request($method, $params)
	{
	  $sign_date = $this->sign_date(Base::DATE_FORMAT_RFC2616);
	  $request = array(
	   'Content-MD5' => null,
	   'Content-Type' => 'application/octet-stream',
	   'Date' => $sign_date,
	  );
	  
	  $request = array_merge($request, $params);
    if (isset($request['bucket'])) {
      $url = "https://{$request['bucket']}.{$this->settings['api_hostname']}{$request['path']}";
      $request['path'] = "/{$request['bucket']}{$request['path']}";
    } else {
      $url = "https://{$this->settings['api_hostname']}/";
    }
	  	  
	  if (isset($request['acl'])) {
	    $request['x-nifty-acl'] = $request['acl'];
	    unset($request['acl']);
	  }
    uksort($request, 'strnatcmp');
	  $sigunature = $this->sign($method, $request);
	  $header = array(
	   "Content-Type: {$request['Content-Type']}",
	   "Date: $sign_date",
	   "Authorization: NIFTY {$this->settings['access_key']}:{$sigunature}",
	  );
	  
	  if (isset($request['Content-Length'])) {
	    $header[] = "Content-Length: {$request['Content-Length']}";
	  }
	  if (isset($request['x-nifty-acl'])) {
	    $header[] = "x-nifty-acl: {$request['x-nifty-acl']}";
	  }
    
		$fp = null;

    $this->is_error = true;
    
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header) ;
    switch(strtolower($method)) {
    case 'put':
      curl_setopt($ch, CURLOPT_PUT, true) ;
      if ( isset($request['body']) ) {
        $fp = tmpfile();
        fwrite($fp, $request['body']);
        fseek($fp, 0);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, strlen($request['body']));
      } else if ( isset($request['file']) ) {
        $fp = fopen($request['file'], 'r');
        if ( $fp === false ) {
          throw new \RuntimeException('Could not open file', -1);
        }
        fseek($fp, 0, SEEK_END);
        $filesize = ftell($fp);
        fseek($fp, 0, SEEK_SET);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $filesize);
      }
      break;
    case 'delete':
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE') ;
      break ;
    case 'head':
      curl_setopt($ch, CURLOPT_NOBODY, true) ;
      break ;
    default:
      curl_setopt($ch, CURLOPT_HTTPGET, true) ;
      break;
    }
		$this->response = curl_exec($ch);
		if (is_resource($fp)) {
      fclose($fp);
		}
		if ( $this->response === false ) {
			return false ;
		}
		
		$this->response_status  = curl_getinfo($ch);
		curl_close($ch);
		if ( (int)($this->response_status['http_code']/100) != 2 ) {
			return $this->analyze_response($this->response);
		}
		
		$this->is_error = false ;
		if ($this->response_status['http_code'] == 204) {
		  return true;
		} else {
		  $result = $this->analyze_response($this->response);
		  return $result;
		}
	}
	
	/**
	 * バケット一覧を取得
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function list_buckets()
	{
	  $request = array(
	   'path' => '/'
	  );
	  
	  $result = $this->request('GET', $request);
	  if ( $this->isError() === true ) {
	    return false ;
	  }
	  
	  $buckets = array();
    foreach($result->Buckets->Bucket as $item) {
      $buckets[] = new Storage\Bucket($this->settings, $item);
    }
	  
	  return $buckets;
	}
	
	/**
	 * オブジェクト一覧を取得
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function list_objects($bucket)
	{
	  $request = array(
	   'path'   => '/',
	   'bucket' => $bucket,
	  );
	  
	  $result = $this->request('GET', $request);
	  if ( $this->isError() === true ) {
	    return false ;
	  }

	  $objects = array();
	  $settings = array_merge($this->settings, $request);

    foreach($result->Contents as $item) {
      $objects[] = new Storage\Object($settings, $item);
    }
	  
	  return $objects;
	}
}