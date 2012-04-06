<?php
require_once 'Base.php';
class StorageTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->settings = include('settings.php');
		$this->ncs = new decr_jp\NiftyCloud\Storage($this->settings);
	}

	public function tearDown()
	{
		parent::tearDown();
		
		$this->nc = null;
	}
	
	private function filename($file)
	{
	  return dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'datas'.DIRECTORY_SEPARATOR.$file;
	}
	private function getfile($file)
	{
	  return file_get_contents($this->filename($file));
	}
	/**
	 * @test
	 */
	public function testバケット一覧が正しく取得できること()
	{
	  $results = $this->ncs->list_buckets();
	  
	  $count = 0 ;
	  foreach($results as $bucket) {
	    $this->assertTrue(is_string($bucket->name)) ;
	    
	    if ( $bucket->name === 's3sync.rb' ) {
	      $this->assertEquals('2011/12/29 11:23:31', $bucket->creation_date->format('Y/m/d H:i:s'));
	    } else if ($bucket->name === 'your-first-bucket' ) {
	      $this->assertEquals('2011/12/31 08:18:51', $bucket->creation_date->format('Y/m/d H:i:s'));
	    } else {
	      continue ;
	    }

      $count++;
	  }
	  
	  $this->assertEquals(2, $count, '規定のバケットが取得できていること');
	}

	/**
	 * @test
	 */
	public function testバケット内のファイル一覧が正しく取得できること()
	{
	  $results = $this->ncs->list_objects('s3sync.rb');
	  $this->assertEquals(9, count($results), 'オブジェクトの数が9件であること');
	  
	  $this->assertEquals('cakephp2_startup.sh', $results[0]->name);
	  $this->assertEquals(483, $results[0]->size);
	  $this->assertEquals('2012/01/08 13:58:02', $results[0]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('centos-fluent.sh', $results[1]->name);
	  $this->assertEquals(1991, $results[1]->size);
	  $this->assertEquals('2012/01/12 09:38:02', $results[1]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('mysql_for_cakephp2.sh', $results[2]->name);
	  $this->assertEquals(1510, $results[2]->size);
	  $this->assertEquals('2012/01/08 14:19:12', $results[2]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('ncss.conf', $results[3]->name);
	  $this->assertEquals(637, $results[3]->size);
	  $this->assertEquals('2011/12/30 16:22:58', $results[3]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('ncss.patch', $results[4]->name);
	  $this->assertEquals(10929, $results[4]->size);
	  $this->assertEquals('2012/01/24 02:10:36', $results[4]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('nginx.sh', $results[5]->name);
	  $this->assertEquals(699, $results[5]->size);
	  $this->assertEquals('2011/12/30 16:22:48', $results[5]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('s3sync_ncss_patch.sh', $results[6]->name);
	  $this->assertEquals(447, $results[6]->size);
	  $this->assertEquals('2011/12/29 12:17:46', $results[6]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('td-agetnt.sh', $results[7]->name);
	  $this->assertEquals(1982, $results[7]->size);
	  $this->assertEquals('2012/01/14 07:47:48', $results[7]->last_modified->format('Y/m/d H:i:s'));

	  $this->assertEquals('test.jpeg', $results[8]->name);
	  $this->assertEquals(15391, $results[8]->size);
	  $this->assertEquals('2011/12/30 11:20:39', $results[8]->last_modified->format('Y/m/d H:i:s'));
  }

	/**
	 * @test
	 */
	public function testオブジェクトを正しく取得できること()
	{
	  $params = array(
	    'bucket' => 's3sync.rb',
	    'path' => '/',
	    'name' => 'cakephp2_startup.sh',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings, $params);
	  $actual = $obj->get();
	  $this->assertEquals($this->getfile('cakephp2_startup.sh'), $actual);
	  
	  $params = array(
	    'bucket' => 's3sync.rb',
	    'path' => '/',
	    'name' => 'test.jpeg',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings, $params);
	  $actual = $obj->get();
	  $this->assertEquals($this->getfile('test.jpeg'), $actual);
  }

	/**
	 * @test
	 */
	public function testバケットを正しく作成できること()
	{
	  $params = array(
	    'bucket' => 'niftycloud-php-sdk-test',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Bucket($this->settings);
	  
	  $actual = $obj->create($params);
	  $this->assertTrue($actual);
	}
	
	/**
	 * @test
	 */
	public function test正常にディレクトリを作成できること()
	{
	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/',
	    'name'    => 'first-dir/',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  
	  $actual = $obj->create($params);
	  $this->assertTrue($actual);

	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/first-dir/',
	    'name'    => 'second-dir/',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  
	  $actual = $obj->create($params);
	  $this->assertTrue($actual);
	}
	
	/**
	 * @test
	 */
	public function test正常にオブジェクトを作成できること()
	{
	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/',
	    'name'    => 'UPLOAD_IMAGE.JPEG',
	    'file'    => $this->filename('upload_image.jpg')
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  
	  $actual = $obj->create($params);
	  $this->assertTrue($actual);

	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path' => '/',
	    'name' => 'UPLOAD_IMAGE.JPEG',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings, $params);
	  $actual = $obj->get();
	  $this->assertEquals($this->getfile('upload_image.jpg'), $actual);

	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/first-dir/second-dir/',
	    'name'    => 'UPLOAD_IMAGE-2.JPEG',
	    'file'    => $this->filename('upload_image.jpg')
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  
	  $actual = $obj->create($params);
	  $this->assertTrue($actual);

	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/first-dir/second-dir/',
	    'name' => 'UPLOAD_IMAGE-2.JPEG',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings, $params);
	  $actual = $obj->get();
	  $this->assertEquals($this->getfile('upload_image.jpg'), $actual);
	}

	
	/**
	 * @test
	 */
	public function testオブジェクトの存在確認が正常にできること()
	{
	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/',
	    'name'    => 'first-dir/',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  $actual = $obj->head($params);

	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/',
	    'name'    => 'UPLOAD_IMAGE.JPEG',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  $actual = $obj->head($params);

	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/first-dir/second-dir/',
	    'name'    => 'UPLOAD_IMAGE-2.JPEG',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  $actual = $obj->head($params);
  }
	/**
	 * @test
	 */
	public function testオブジェクトが存在する場合、バケット削除時にエラーになること()
	{
	  $params = array(
	    'bucket' => 'niftycloud-php-sdk-test',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Bucket($this->settings);
	  
	  $actual = $obj->drop($params);
	  $this->assertFalse($actual);
	}

	/**
	 * @test
	 */
	public function test最上位のディレクトリを削除できること()
	{
	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/',
	    'name'    => 'first-dir/',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  
	  $actual = $obj->drop($params);
	  $this->assertTrue($actual);
	}

	/**
	 * @test
	 */
	public function test最上位のオブジェクトを削除できること()
	{
	  $params = array(
	    'bucket'  => 'niftycloud-php-sdk-test',
	    'path'    => '/',
	    'name'    => 'UPLOAD_IMAGE.JPEG',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Object($this->settings);
	  
	  $actual = $obj->drop($params);
	  $this->assertTrue($actual);
	}

	/**
	 * @test
	 */
	public function testバケットを正しく削除できること()
	{
	  $params = array(
	    'bucket' => 'niftycloud-php-sdk-test',
	  );
	  $obj = new \decr_jp\NiftyCloud\Storage\Bucket($this->settings);
	  
	  $actual = $obj->drop($params);
	  $this->assertTrue($actual);
	}
}
