<?php
namespace decr_jp\NiftyCloud\Storage;

class Bucket extends \decr_jp\NiftyCloud\Storage
{
  protected  $name=null;
  protected  $creation_date=null;
  
  public function __construct($settings, $params=null) 
  {
    parent::__construct($settings);
    
    if (is_object($params) ) {
      $this->name = (string)$params->Name;
      
      $this->creation_date = new \DateTime((string)$params->CreationDate);
      $this->creation_date->setTimezone(new \DateTimeZone($this->settings['time_zone']));
    }
  }
  
  /**
   * 新規バケットを作成
   *
   * @return mixed
   * @author Kaz Watanabe
   **/
  public function create($params)
  {
    $body = <<<EOT
<CreateBucketConfiguration xmlns="http://doc.ncss.nifty.com/2011-09-29">
<LocationConstraint>ap-japan-1a</LocationConstraint>
</CreateBucketConfiguration>
EOT;

    $_params = array(
      'acl'   => \decr_jp\NiftyCloud\Storage::ACL_PRIVATE,
      'path'  => '/',
      'body'  => $body,
    );
    
    $_params = array_merge($_params, $params);
    
    $result = $this->request('PUT', $_params);
	  if ( $this->isError() === true ) {
	    return false ;
	  }

    return true;
  }
  
  /**
   * バケットを削除
   *
   * @return mixed
   * @author Kaz Watanabe
   **/
  public function drop($params)
  {
    $_params = array(
      'path'  => '/',
    );
    
    $_params = array_merge($_params, $params);
    
    $result = $this->request('DELETE', $_params);
	  if ( $this->isError() === true ) {
	    return false ;
	  }

    return true;
  }
}