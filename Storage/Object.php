<?php
namespace decr_jp\NiftyCloud\Storage;

class Object extends \decr_jp\NiftyCloud\Storage
{
  protected  $name=null;
  protected  $size=null;
  protected  $last_modified=null;
  protected  $etag=null;
  
  public function __construct($settings, $attrs=null) 
  {
    parent::__construct($settings);
    
    if (is_object($attrs) ) {
      $this->name = (string)$attrs->Key;
      $this->size = (int)$attrs->Size;
      $this->last_modified = new \DateTime((string)$attrs->LastModified);
      $this->last_modified->setTimezone(new \DateTimeZone($this->settings['time_zone']));
      $this->etag = (string)$attrs->ETag;
    } else if (is_array($attrs)) {
      $_settings = array(
        'bucket' => $attrs['bucket'],
        'path' => $attrs['path'],
      );
      
      $this->settings = array_merge($this->settings, $_settings);
      $this->name = $attrs['name'];
    }
  }

	/**
	 * オブジェクトの存在確認
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function head($params)
	{
	  $request = array(
	   'path'   => $params['path'].$params['name'],
	   'bucket' => $params['bucket'],
	  );
	  
	  $result = $this->request('HEAD', $request);
	  if ( $this->isError() === true ) {
	    return false ;
	  }
	  
	  return true;
	}

	/**
	 * オブジェクトを取得
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function get()
	{
	  $request = array(
	   'path'   => $this->settings['path'].$this->name,
	   'bucket' => $this->settings['bucket'],
	  );
	  
	  $result = $this->request('GET', $request);
	  if ( $this->isError() === true ) {
	    return false ;
	  }
	  
	  return $result;
	}
  
  /**
   * オブジェクトを削除
   *
   * @return mixed
   * @author Kaz Watanabe
   **/
  public function drop($params)
  {
    $_params = array(
    );
    
    $_params = array_merge($_params, $params);
    $_params['path'] .= $_params['name'];
    $result = $this->request('DELETE', $_params);
	  if ( $this->isError() === true ) {
	    return false ;
	  }

    return true;
  }

	/**
	 * オブジェクトの生成
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function create($params)
	{
	  $name = $params['name'];
	  
	  if ($name[strlen($name)-1] === '/') {
	    return $this->create_directory($params);
	  } else {
	    return $this->create_object($params);
	  }
  }
  
  /**
   * ディレクトリを作成
   *
   * @return void
   * @author Kaz Watanabe
   **/
  private function create_directory($params)
  {
    $_params = array(
      'Content-Type'    => 'application/x-directory',
      'Content-Length'  => 0,
      'acl'             => \decr_jp\NiftyCloud\Storage::ACL_PRIVATE,
    );
    
    $_params = array_merge($_params, $params);
    $_params['path'] .= $_params['name'];
    $result = $this->request('PUT', $_params);
	  if ( $this->isError() === true ) {
	    return false ;
	  }

    return true;
  }
  
  /**
   * オブジェクトを作成
   *
   * @return void
   * @author Kaz Watanabe
   **/
  private function create_object($params)
  {
    $_params = array(
      'acl'             => \decr_jp\NiftyCloud\Storage::ACL_PRIVATE,
    );
    
    $_params = array_merge($_params, $params);
    $_params['path'] .= $_params['name'];
    $result = $this->request('PUT', $_params);
	  if ( $this->isError() === true ) {
	    return false ;
	  }

    return true;
  }
}