<?php 

class IGtBaseTemplate{
	var $appId;
	var $appkey;

	
	function get_transparent() {
		$transparent = new Transparent();
		$transparent->set_id('');
		$transparent->set_messageId('');
		$transparent->set_taskId('');
		$transparent->set_action('pushmessage');
		$transparent->set_pushInfo($this->get_pushInfo());
		$transparent->set_appId($this->appId);
		$transparent->set_appKey($this->appkey);

		$actionChainList = $this->getActionChain();
		
		foreach($actionChainList as $index=>$actionChain){
			$transparent->add_actionChain();
			$transparent->set_actionChain($index,$actionChain);
		}
		return $transparent->SerializeToString();
	}

	function  get_transmissionContent() {
		return null;
	}
	
	function  get_pushType() {
		return null;
	}

	function get_actionChain() {
		return null;
	}

	function get_pushInfo() {
		$pushInfo = new PushInfo();
		$pushInfo->set_actionKey('');
		$pushInfo->set_badge('');
		$pushInfo->set_message('');
		$pushInfo->set_sound('');

		return $pushInfo;
	}

	function  set_appId($appId) {
		$this->appId = $appId;
	}

	function  set_appkey($appkey) {
		$this->appkey = $appkey;
	}
}