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
 * NiftyCloud APIクラス
 *
 * @package unofficial_niftycloudsdk
 * @author Kaz Watanabe
 **/
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'niftycloud.sdk.class.php');

class NiftyCloud extends NiftyCloudAPI
{
	const ACCOUNTING_TYPE_MONTH=1;
	const ACCOUNTING_TYPE_PAYPER=2;

	const DISK_TYPE_FAST=1;
	const DISK_TYPE_SLOW=2;

	const LOADBALANCE_TYPE_ROUNDROBIN=1;
	const LOADBALANCE_TYPE_LATESTCONNECTION=2;

	/**
	 * 指定したサーバーを起動
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function start_instances($options=array())
	{
		$params = array();
		if ( is_string($options) ) {
			$options = array(
				'InstanceId' => $options,
			);
		}
		
		if ( !isset($options['InstanceId']) ) {
			throw new NiftyCloud_Exception("The request must contain the parameter 'instanceId'.", -1);
		}

		if ( !is_array($options['InstanceId']) ) {
			$options['InstanceId'] = (array)$options['InstanceId'];
		}
		
		foreach($options['InstanceId'] as $key => $value) {
			$index = $key+1;
			$params["InstanceId.{$index}"] = $value;
			
			if ( isset($options['InstanceType'][$key]) ) {
				$params["InstanceType.{$index}"] = $options['InstanceType'][$key];
			}
			
			if ( isset($options['AccountingType'][$key]) ) {
				$params["AccountingType.{$index}"] = $options['AccountingType'][$key];
			}
		}

		return $this->request('StartInstances', $params);
	}
	
	/**
	 * 指定したサーバーを停止
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function stop_instances($options=array())
	{
		$params = array();
		if ( is_string($options) ) {
			$options = array(
				'InstanceId' => $options,
			);
		}
		
		if ( !isset($options['InstanceId']) ) {
			throw new NiftyCloud_Exception("The request must contain the parameter 'instanceId'.", -1);
		}

		if ( !is_array($options['InstanceId']) ) {
			$options['InstanceId'] = (array)$options['InstanceId'];
		}
		
		foreach($options['InstanceId'] as $key => $value) {
			$index = $key+1;
			$params["InstanceId.{$index}"] = $value;
		}

		if ( isset($options['Force']) ) {
			$options['Force'] = $options['Force'];
		}

		return $this->request('StopInstances', $params);
	}
	
	/**
	 * 指定したサーバーを再起動
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function reboot_instances($options=array())
	{
		$params = array();
		if ( is_string($options) ) {
			$options = array(
				'InstanceId' => $options,
			);
		}
		
		if ( !isset($options['InstanceId']) ) {
			throw new NiftyCloud_Exception("The request must contain the parameter 'instanceId'.", -1);
		}

		if ( !is_array($options['InstanceId']) ) {
			$options['InstanceId'] = (array)$options['InstanceId'];
		}
		
		foreach($options['InstanceId'] as $key => $value) {
			$index = $key+1;
			$params["InstanceId.{$index}"] = $value;
		}

		if ( isset($options['Force']) ) {
			$options['Force'] = $options['Force'];
		}

		return $this->request('RebootInstances', $params);
	}
	
	/**
	 * 指定したサーバーを削除
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function terminate_instances($options=array())
	{
		$params = array();
		if ( is_string($options) ) {
			$options = array(
				'InstanceId' => $options,
			);
		}
		
		if ( !isset($options['InstanceId']) ) {
			throw new NiftyCloud_Exception("The request must contain the parameter 'instanceId'.", -1);
		}

		if ( !is_array($options['InstanceId']) ) {
			$options['InstanceId'] = (array)$options['InstanceId'];
		}
		
		foreach($options['InstanceId'] as $key => $value) {
			$index = $key+1;
			$params["InstanceId.{$index}"] = $value;
		}

		$result = $this->request('TerminateInstances', $params);
		return $result;
	}
	
	/**
	 * サーバーを新規作成
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function run_instances($params=array())
	{
		$_defaults = array(
			'ImageId' => null,
			'KeyName' => null,
			'InstanceType' => null,
			'InstanceId' => null,
			'AccountingType' => NiftyCloud::ACCOUNTING_TYPE_PAYPER,
			'Admin' => null,
			'Password' => null,
		);

		$params = array_merge($_defaults, $params);

		if ( isset($params['SecurityGroup']) ) {
			if ( !is_array($params['SecurityGroup']) ) {
				$params['SecurityGroup'] = (array)$params['SecurityGroup'];
			}
			
			$index = 0 ;
			foreach($params['SecurityGroup'] as $key => $value) {
				$index = $key+1;
				$params["SecurityGroup.{$index}"] = $value;
			}
			
			unset($params['SecurityGroup']) ;
		}

		return $this->request('RunInstances', $params);
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

	/**
	 * 指定したサーバーのコピーを作成
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function copy_instances($params=array())
	{
		$_defaults = array(
			'InstanceId' => null,

			'CopyInstance.InstanceName' => null,
			'CopyInstance.InstanceType' => null,
			'CopyInstance.AccountingType' => NiftyCloud::ACCOUNTING_TYPE_PAYPER,
			'CopyCount' => 1,
		);

		$params = array_merge($_defaults, $params);

		return $this->request('CopyInstances', $params);
	}
	
	/**
	 * 指定したサーバーの作成（コピーによる作成）をキャンセル
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function cancel_copy_instances($instance_id)
	{
		$params = array(
			'InstanceId' => $instance_id,
		);

		return $this->request('CancelCopyInstances', $params);
	}
		
	/**
	 * 指定したサーバーの詳細情報を取得
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_instance_attribute($params=array())
	{
		$_defaults = array(
			'InstanceId' => null,
			'Attribute' => null,
		);
		
		$params = array_merge($_defaults, $params);
		return $this->request('DescribeInstanceAttribute', $params);
	}
	
	/**
	 * 指定したサーバーの詳細情報を更新
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function modify_instance_attribute($params=array())
	{
		$_defaults = array(
			'InstanceId' => null,
			'Attribute' => null,
			'Value' => null,
		);
		
		$params = array_merge($_defaults, $params);

		return $this->request('ModifyInstanceAttribute', $params);
	}
	
	/**
	 * 指定したディスクの情報を取得
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_volumes($volumes=array())
	{
		$params = array();
		if ( !is_array($volumes) ) {
			$volumes = (array)$volumes;
		}
		
		foreach($volumes as $key => $value) {
			$index = $key+1;
			$params["VolumeId.{$index}"] = $value;
		}
		return $this->request('DescribeVolumes', $params);
	}

	/**
	 * ディスクを新規作成
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function create_volume($volume_id, $disk_type, $size, $instance_id)
	{
		$params = array(
			'VolumeId' => $volume_id,
			'DiskType' => $disk_type,
			'Size' => $size,
			'InstanceId' => $instance_id,
		);

		return $this->request('CreateVolume', $params);
	}

	/**
	 * 指定したディスクとサーバーの接続を解除
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function detach_volume($volume_id, $instance_id=null)
	{
		$params = array(
			'VolumeId' => $volume_id,
			'InstanceId' => $instance_id,
		);

		return $this->request('DetachVolume', $params);
	}

	/**
	 * 指定したディスクをサーバーへ接続
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function attach_volume($volume_id, $instance_id)
	{
		$params = array(
			'VolumeId' => $volume_id,
			'InstanceId' => $instance_id,
		);

		return $this->request('AttachVolume', $params);
	}

	/**
	 * 指定したディスクを削除
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function delete_volume($volume_id)
	{
		$params = array(
			'VolumeId' => $volume_id,
		);

		return $this->request('DeleteVolume', $params);
	}
	
	/**
	 * 利用可能なゾーンの情報を取得
	 *
	 * @param mixed		$zonenames		ゾーン情報
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_availability_zones($zonenames=array())
	{
		$params = array();
		if ( !is_array($zonenames) ) {
			$zonenames = (array)$zonenames;
		}
		
		foreach($zonenames as $key => $value) {
			$index = $key+1;
			$params["ZoneName.{$index}"] = $value;
		}
		return $this->request('DescribeAvailabilityZones', $params);
	}
	
	/**
	 * 指定したSSHキーの情報を取得
	 *
	 * @param mixed		$keynames		SSHキー名
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_key_pairs($keynames=array())
	{
		$params = array();
		if ( !is_array($keynames) ) {
			$keynames = (array)$keynames;
		}
		
		foreach($keynames as $key => $value) {
			$index = $key+1;
			$params["KeyName.{$index}"] = $value;
		}
		return $this->request('DescribeKeyPairs', $params);
	}

	/**
	 * SSHキーを新規作成
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function create_key_pair($keyname, $password)
	{
		$params = array(
			'KeyName' => $keyname,
			'Password' => $password,
		);

		return $this->request('CreateKeyPair', $params);
	}

	/**
	 * SSHキーの情報を削除
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function delete_key_pair($keyname)
	{
		$params = array(
			'KeyName' => $keyname,
		);

		return $this->request('DeleteKeyPair', $params);
	}

	/**
	 * OSイメージの情報を取得
	 *
	 * @param mixed		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_images($options=array())
	{
		$params = array();
		if ( isset($options['ImageId']) ) {
			if ( !is_array($options['ImageId']) ) {
				$options['ImageId'] = (array)$options['ImageId'];
			}
			
			foreach($options['ImageId'] as $key => $value) {
				$index = $key+1;
				$params["ImageId.{$index}"] = $value;
			}
		}
		
		if ( isset($options['ImageName']) ) {
			if ( !is_array($options['ImageName']) ) {
				$options['ImageName'] = (array)$options['ImageName'];
			}
			
			foreach($options['ImageName'] as $key => $value) {
				$index = $key+1;
				$params["ImageName.{$index}"] = $value;
			}
		}
		
		if ( isset($options['Owner']) ) {
			if ( !is_array($options['Owner']) ) {
				$options['Owner'] = (array)$options['Owner'];
			}
			
			foreach($options['Owner'] as $key => $value) {
				$index = $key+1;
				$params["Owner.{$index}"] = $value;
			}
		}
		
		return $this->request('DescribeImages', $params);
	}

	/**
	 * 指定したサーバーをイメージ化し、カスタマイズイメージとして保存
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function create_image($instance_id, $imagename, $left_instance=true)
	{
		$params = array(
			'InstanceId' => $instance_id,
			'Name' => $imagename,
			'LeftInstance' => $left_instance,
		);

		return $this->request('CreateImage', $params);
	}

	/**
	 * 指定したサーバーをイメージ化し、カスタマイズイメージとして保存
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function modify_image_attribute($image_id, $attribute, $value)
	{
		$params = array(
			'ImageId' => $image_id,
			'Attribute' => $attribute,
			'Value' => $value,
		);

		return $this->request('ModifyImageAttribute', $params);
	}

	/**
	 * 指定したカスタマイズイメージを削除
	 *
	 * @param mixed		$volumes		ディスク名 
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function delete_image($image_id)
	{
		$params = array(
			'ImageId' => $image_id,
		);

		return $this->request('DeleteImage', $params);
	}
	
	/**
	 * 指定したロードバランサーの情報を取得
	 *
	 * @param mixed		$instanceids		ロードバランサー名
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_load_balancers($instanceids=array())
	{
		$params = array();
		if ( !is_array($instanceids) ) {
			$instanceids = (array)$instanceids;
		}
		
		foreach($instanceids as $key => $value) {
			$index = $key+1;
			$params["LoadBalancerNames.member.{$index}"] = $value;
		}
		return $this->request('DescribeLoadBalancers', $params);
	}
	
	/**
	 * 指定したロードバランサーを削除
	 *
	 * @param array		$options
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function delete_load_balancer($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
		);

		$params = array_merge($defaults, $params);

		return $this->request('DeleteLoadBalancer', $params);
	}
		
	/**
	 * ロードバランサーを作成
	 *
	 * @param array		$options
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function create_load_balancer($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'Protocol' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
			'BalancingType' => null,
			'NetworkVolume' => 10,
		);

		$params = array_merge($defaults, $params);
		
		$request_param = array(
			'LoadBalancerName' => $params['LoadBalancerName'],
			'NetworkVolume' => $params['NetworkVolume'],
		);
		
		if (  is_string($params['Protocol']) ) {
			$params['Protocol'] = (array)$params['Protocol'];
		}
		if ( is_array($params['Protocol']) ) {
			$index = 0;
			foreach($params['Protocol'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.Protocol"] = strtoupper($value);
			}
		}

		if ( is_int($params['LoadBalancerPort']) ) {
			$params['LoadBalancerPort'] = (array)$params['LoadBalancerPort'];
		}
		if ( is_array($params['LoadBalancerPort']) ) {
			$index = 0;
			foreach($params['LoadBalancerPort'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.LoadBalancerPort"] = $value;
			}
		}

		if ( is_int($params['InstancePort']) ) {
			$params['InstancePort'] = (array)$params['InstancePort'];
		}
		if ( is_array($params['InstancePort']) ) {
			$index = 0;
			foreach($params['InstancePort'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.InstancePort"] = $value;
			}
		}

		if ( is_int($params['BalancingType']) ) {
			$params['BalancingType'] = (array)$params['BalancingType'];
		}
		if ( is_array($params['BalancingType']) ) {
			$index = 0;
			foreach($params['BalancingType'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.BalancingType"] = $value;
			}
		}

		return $this->request('CreateLoadBalancer', $request_param);
	}
		
	/**
	 * ロードバランサーを作成
	 *
	 * @param array		$options
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function register_port_with_load_balancer($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'Protocol' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
			'BalancingType' => null,
			'NetworkVolume' => 10,
		);

		$params = array_merge($defaults, $params);
		
		$request_param = array(
			'LoadBalancerName' => $params['LoadBalancerName'],
			'NetworkVolume' => $params['NetworkVolume'],
		);
		
		if (  is_string($params['Protocol']) ) {
			$params['Protocol'] = (array)$params['Protocol'];
		}
		if ( is_array($params['Protocol']) ) {
			$index = 0;
			foreach($params['Protocol'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.Protocol"] = strtoupper($value);
			}
		}

		if ( is_int($params['LoadBalancerPort']) ) {
			$params['LoadBalancerPort'] = (array)$params['LoadBalancerPort'];
		}
		if ( is_array($params['LoadBalancerPort']) ) {
			$index = 0;
			foreach($params['LoadBalancerPort'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.LoadBalancerPort"] = $value;
			}
		}

		if ( is_int($params['InstancePort']) ) {
			$params['InstancePort'] = (array)$params['InstancePort'];
		}
		if ( is_array($params['InstancePort']) ) {
			$index = 0;
			foreach($params['InstancePort'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.InstancePort"] = $value;
			}
		}

		if ( is_int($params['BalancingType']) ) {
			$params['BalancingType'] = (array)$params['BalancingType'];
		}
		if ( is_array($params['BalancingType']) ) {
			$index = 0;
			foreach($params['BalancingType'] as $key => $value) {
				$index = $key+1;
				$request_param["Listeners.member.{$index}.BalancingType"] = $value;
			}
		}

		return $this->request('RegisterPortWithLoadBalancer', $request_param);
	}
	
	/**
	 * ロードバランサーにインスタンスを追加
	 *
	 * @param array		$options
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function register_instances_with_load_balancer($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
			'InstanceId' => null,
		);

		$params = array_merge($defaults, $params);
		
		$request_param = array(
			'LoadBalancerName' => $params['LoadBalancerName'],
			'LoadBalancerPort' => $params['LoadBalancerPort'],
			'InstancePort' => $params['InstancePort'],
		);
		
		if (  is_string($params['InstanceId']) ) {
			$params['InstanceId'] = (array)$params['InstanceId'];
		}
		if ( is_array($params['InstanceId']) ) {
			$index = 0;
			foreach($params['InstanceId'] as $key => $value) {
				$index = $key+1;
				$request_param["Instances.member.{$index}.InstanceId"] = $value;
			}
		}

		return $this->request('RegisterInstancesWithLoadBalancer', $request_param);
	}
		
	/**
	 * ロードバランサーにフィルターを追加
	 *
	 * @param array		$options
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function set_filter_for_load_balancer($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
			'IPAddresses' => null,
			'addOnFilter' => null,
			'FilterType' => null,
		);

		$params = array_merge($defaults, $params);
		
		$request_param = array(
			'LoadBalancerName' => $params['LoadBalancerName'],
			'LoadBalancerPort' => $params['LoadBalancerPort'],
			'InstancePort' => $params['InstancePort'],
			'FilterType' => $params['FilterType'],
		);
		
		if ( is_string($params['IPAddresses']) ) {
			$params['IPAddresses'] = (array)$params['IPAddresses'];
		}
		if ( is_array($params['IPAddresses']) ) {
			$index = 0;
			foreach($params['IPAddresses'] as $key => $value) {
				$index = $key+1;
				$request_param["IPAddresses.member.{$index}.IPAddress"] = $value;
			}
		}
		
		if ( is_bool($params['addOnFilter']) ) {
			$params['addOnFilter'] = (array)$params['addOnFilter'];
		}
		if ( is_array($params['addOnFilter']) ) {
			$index = 0;
			foreach($params['addOnFilter'] as $key => $value) {
				$index = $key+1;
				$request_param["IPAddresses.member.{$index}.addOnFilter"] = $value;
			}
		}

		return $this->request('SetFilterForLoadBalancer', $request_param);
	}
	
	/**
	 * ロードバランサーからインスタンスを削除
	 *
	 * @param array		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function deregister_instances_from_load_balancer($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
			'InstanceId' => null,
		);

		$params = array_merge($defaults, $params);
		
		$request_param = array(
			'LoadBalancerName' => $params['LoadBalancerName'],
			'LoadBalancerPort' => $params['LoadBalancerPort'],
			'InstancePort' => $params['InstancePort'],
		);
		
		if (  is_string($params['InstanceId']) ) {
			$params['InstanceId'] = (array)$params['InstanceId'];
		}
		if ( is_array($params['InstanceId']) ) {
			$index = 0;
			foreach($params['InstanceId'] as $key => $value) {
				$index = $key+1;
				$request_param["Instances.member.{$index}.InstanceId"] = $value;
			}
		}

		return $this->request('DeregisterInstancesFromLoadBalancer', $request_param);
	}
	
	/**
	 * 指定したロードバランサーに設定されている、サーバーのヘルスチェック結果を取得
	 *
	 * @param array		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_instance_health($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
			'InstanceId' => null,
		);

		$params = array_merge($defaults, $params);
		
		$request_param = array(
			'LoadBalancerName' => $params['LoadBalancerName'],
			'LoadBalancerPort' => $params['LoadBalancerPort'],
			'InstancePort' => $params['InstancePort'],
		);
		
		if ( is_string($params['InstanceId']) ) {
			$params['InstanceId'] = (array)$params['InstanceId'];
		}
		if ( is_array($params['InstanceId']) ) {
			$index = 0;
			foreach($params['InstanceId'] as $key => $value) {
				$index = $key+1;
				$request_param["Instances.member.{$index}.InstanceId"] = $value;
			}
		}

		return $this->request('DescribeInstanceHealth', $request_param);
	}
	
	/**
	 * 指定したロードバランサーのヘルスチェックの設定を変更
	 *
	 * @param array		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function configure_health_check($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'LoadBalancerPort' => null,
			'InstancePort' => null,
			'Target' => null,
			'Interval' => 10,
			'UnhealthyThreshold' => 3,
			'HealthyThreshold' => 1,
		);

		$params = array_merge($defaults, $params);
		
		$params = array(
			'LoadBalancerName' => $params['LoadBalancerName'],
			'LoadBalancerPort' => $params['LoadBalancerPort'],
			'InstancePort' => $params['InstancePort'],
			'HealthCheck.Target' => $params['Target'],
			'HealthCheck.Interval' => $params['Interval'],
			'HealthCheck.UnhealthyThreshold' => $params['UnhealthyThreshold'],
			'HealthCheck.HealthyThreshold' => $params['HealthyThreshold'],
		);
		
		return $this->request('ConfigureHealthCheck', $params);
	}
	
	/**
	 * 指定したロードバランサーのヘルスチェックの設定を変更
	 *
	 * @param array		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function update_load_balancer($params=array())
	{
		$defaults = array(
			'LoadBalancerName' => null,
			'ListenerUpdate.LoadBalancerPort' => null,
			'ListenerUpdate.InstancePort' => null,
			'ListenerUpdate.Listener.Protocol' => null,
			'ListenerUpdate.Listener.LoadBalancerPort' => null,
			'ListenerUpdate.Listener.InstancePort' => null,
			'ListenerUpdate.Listener.BalancingType' => null,
			'NetworkVolumeUpdate' => null,
		);

		$params = array_merge($defaults, $params);
		
		return $this->request('UpdateLoadBalancer', $params);
	}
	
	/**
	 * 指定したファイアウォールグループの設定情報を取得する
	 *
	 * @param array		$params
	 * @access public
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function describe_security_groups($params=array())
	{
		$request_param = array();
		
		if ( isset($params['GroupName']) ) {
			if ( !is_array($params['GroupName']) ) {
				$params['GroupName'] = (array)$params['GroupName'];
			}
			
			$index = 0;
			foreach($params['GroupName'] as $key => $group_name) {
				$index = $key+1;
				$request_param["GroupName.{$index}"] = $group_name;
			}
		}
		
		if ( isset($params['Filter']) && is_array($params['Filter']) ) {
			$n = 0;
			foreach($params['Filter'] as $name => $values) {
				$n++ ;
				if ( !is_array($values) ) {
					$values = (array)$values;
				}
				
				$request_param["Filter.{$n}.Name"] = $name;
				$m = 0;
				foreach($values as $value) {
					$m++;
					$request_param["Filter.{$n}.Value.{$m}"] = $value;
				}
			}
		}
		
		return $this->request('DescribeSecurityGroups', $request_param);
	}
	
	/**
	 * ファイアウォールグループを新規に作成する
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function create_security_group($params=array())
	{
		$defaults = array(
			'GroupName' => null,
			'GroupDescription' => null,
		);

		$params = array_merge($defaults, $params);

		return $this->request('CreateSecurityGroup', $params);
	}

	/**
	 * ファイアウォールグループを削除する
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function delete_security_group($params=array())
	{
		$defaults = array(
			'GroupName' => null,
		);

		$params = array_merge($defaults, $params);

		return $this->request('DeleteSecurityGroup', $params);
	}

	/**
	 * ファイアウォールグループの設定情報を更新する
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function update_security_group($params=array())
	{
		$defaults = array(
			'GroupName' => null,
		);

		$params = array_merge($defaults, $params);

		return $this->request('UpdateSecurityGroup', $params);
	}
	
	/**
	 * 指定したファイアウォールグループに許可ルールを追加する
	 *
	 * @return void
	 * @author Kaz Watanabe
	 **/
	public function authorize_security_group_ingress($params=array())
	{
		$request_params = array(
			'GroupName' => null,
		);

		if ( !isset($params['GroupName']) || !isset($params['IpPermissions']) || !is_array($params['IpPermissions']) ) {
			return false;
		}
		
		$request_params['GroupName'] = $params['GroupName'];
		$n = 0;
		foreach($params['IpPermissions'] as $ip_permission) {
			$n++;
			
			$proto = strtolower($ip_permission['IpProtocol']);
			$request_params["IpPermissions.{$n}.IpProtocol"] = strtoupper($proto);
			switch($proto) {
			case 'tcp': 
			case 'udp':
				$request_params["IpPermissions.{$n}.FromPort"] = $ip_permission['FromPort'];
				break ;
			default:
				break ;
			}
			
			if ( isset($ip_permission['InOut']) ) {
				$request_params["IpPermissions.{$n}.InOut"] = $ip_permission['InOut'];
			}
			
			$m = 0;
			if ( isset($ip_permission['CidrIp']) ) {
				if ( is_string($ip_permission['CidrIp']) ) {
					$ip_permission['CidrIp'] = (array)$ip_permission['CidrIp'];
				}
				foreach($ip_permission['CidrIp'] as $value) {
					$m++ ;
					$request_params["IpPermissions.{$n}.IpRanges.{$m}.CidrIp"] = $value;
				}
			}	

			if ( isset($ip_permission['GroupName']) ) {
				if ( is_string($ip_permission['GroupName']) ) {
					$ip_permission['GroupName'] = (array)$ip_permission['GroupName'];
				}
				foreach($ip_permission['GroupName'] as $value) {
					$m++ ;
					$request_params["ipPermissions.{$n}.Groups.{$m}.GroupName"] = $value;
				}
			}	
		}
		
		return $this->request('AuthorizeSecurityGroupIngress', $request_params);
	}
} // END class NiftyCloud extends NiftyCloudAPI_Base