<?php
/*! Copyright (C) 2016 BGM STORAGE. All rights reserved. */
/**
 * @class userblockAdminView
 * @author Huhani (mmia268@gmail.com)
 * @brief Userblock 모듈의 admin.view class
 **/

class userblockAdminView extends userblock
{
	/**
	 * 초기화 시 실행되는 코드
	 */
	public function init()
	{
		$module_name = Context::get('module');
		$act =  Context::get('act');

		if($module_name !== 'admin' && !strpos($act, 'Popup')){
			$oUserblockModel = getModel('userblock');
			$config = $oUserblockModel->getConfig();
			$this->module_info->layout_srl = $config->layout_srl;
			Context::loadFile('/modules/admin/tpl/css/admin.bootstrap.css', 'head');
			Context::loadFile('/modules/admin/tpl/css/admin.css', 'head');
			Context::loadFile('/modules/admin/tpl/css/admin_ko.css', 'head');
			Context::loadFile('/modules/admin/tpl/js/admin.js', 'head');
			Context::loadFile('/modules/admin/tpl/js/jquery.tmpl.js', 'head');
			Context::loadFile('/modules/admin/tpl/js/jquery.jstree.js', 'head');
		}


		Context::set('is_admin_page', $this->is_admin_page = $module_name === 'admin' ? true : false);
		// 템플릿 폴더 지정
		$this->setTemplatePath($this->module_path.'tpl');
	}



	function dispUserblockAdminList() {

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		// setup the board module general information
		$args = new stdClass();
		$args->sort_index = "regdate";
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$args->order_type = 'desc';
		
		$columnList = array('target_nick_name', 'target_member_srl', 'regdate', 'expdate', 'comment');
		$search_target = Context::get('search_target');
		if($search_target && in_array($search_target, $columnList)) {

			$args->{"s_".Context::get('search_target')} = Context::get('search_keyword') ? Context::get('search_keyword') : null;
		}

		$output = executeQueryArray('userblock.getMemberBlockList', $args);


		Context::set('config', $config);
		Context::set('date', date("YmdHis"));

		// use context::set to setup variables on the templates
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('ban_member_list');
	}

	function dispUserblockAdminIPList()
	{
		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		// setup the board module general information
		$args = new stdClass();
		$args->sort_index = "regdate";
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$args->order_type = 'desc';
		$args->expdate = date("YmdHis");

		$columnList = array('target_ipaddress', 'target_nick_name', 'target_member_srl', 'regdate', 'expdate', 'comment');
		$search_target = Context::get('search_target');
		if($search_target && in_array($search_target, $columnList)) {

			$args->{"s_".Context::get('search_target')} = Context::get('search_keyword') ? Context::get('search_keyword') : null;
		}

		$output = executeQueryArray('userblock.getMemberBanIPList', $args);

		foreach($output->data as $value){

			$oMemberModel = getModel('member');
			if($value->member_srl){
				$is_deleted = $oMemberModel->getMemberInfoByMemberSrl($value->target_member_srl);
			}
			$value->is_deleted = $is_deleted ? false : true;

		}

		Context::set('config', $config);
		Context::set('date', date("YmdHis"));

		// use context::set to setup variables on the templates
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('banIPList');

	}

	function dispUserblockAdminMemberView()
	{
		$args = new stdClass();
      $args->target_member_srl = Context::get('member_srl');
      $args->id = Context::get('id');
		$output = executeQueryArray($args->id ? 'userblock.getBlockMemberViewID' : 'userblock.getBlockMemberView', $args);

		Context::set('data', $output->data[0]);

		$this->setTemplateFile('blockMemberView');
	}


	function dispUserblockAdminLog() {

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		// setup the board module general information
		$args = new stdClass();
		$args->sort_index = "regdate";
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$args->order_type = 'desc';

		$columnList = array('target_nick_name', 'target_member_srl', 'nick_name', 'member_srl', 'target_srl', 'regdate', 'expdate', 'comment', 'action');
		$search_target = Context::get('search_target');
		$search_keyword = Context::get('search_keyword');
		if($search_target && in_array($search_target, $columnList)) {
			$args->{"s_".$search_target} = $search_keyword ? $search_keyword : null;
		}


		$output = executeQueryArray('userblock.getMemberBlockLog', $args);

		Context::set('config', $config);
		Context::set('date', date("YmdHis"));

		// use context::set to setup variables on the templates
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('actionLog');
	}



	function dispUserblockAdminContent() {

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		// setup the board module general information
		$args = new stdClass();
		$args->sort_index = "module_srl";
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$args->s_module_category_srl = Context::get('module_category_srl');

		$output = executeQueryArray('board.getBoardList', $args);


		foreach($output->data as &$value){
			$value->config_status = $config->{$value->module_srl} ? true : false;
		}

		Context::set('config', $config);

		// use context::set to setup variables on the templates
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('board_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('index');
	}

	function dispUserblockAdminMidConfig()
	{
		$module_srl = Context::get('module_srl');
		if(!$module_srl) return $this->stop('msg_invalid_request');
		else {
			$oModuleModel = getModel('module');
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			if($module_info->module_srl != $module_srl) return $this->stop('msg_invalid_request');
		}


		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();
		$config_status = $config->{$module_srl} ? true : false;
		$config = $config_status ? $config->{$module_srl} : $config->default;

		Context::set('config_status', $config_status);
		Context::set('config', $config);
		Context::set('module_info', $module_info);

		$this->setTemplateFile('midConfig');
	}


	function dispUserblockAdminConfig()
	{
		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		Context::set('config', $config);
		$this->setTemplateFile('config');
	}

	function dispUserblockAdminGrantInfo()
	{
		// get the grant infotmation from admin module
		$oModuleAdminModel = getAdminModel('module');

		$oModuleModel = getModel('module');
		$this->mid_info = $oModuleModel->getModuleInfoByMid("userblock");

		$admin_member = $oModuleModel->getAdminId($this->mid_info->module_srl);
		$grant_content = $oModuleAdminModel->getModuleGrantHTML($this->mid_info->module_srl, $this->xml_info->grant);
		Context::set('grant_content', $grant_content);

		$this->setTemplateFile('authConfig');
	}

	function dispUserblockAdminSkin()
	{
		// loginlogModel 객체 생성 
		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		Context::set('config', $config);

		// moduleModel 객체 생성
		$oModuleModel = getModel('module');

		$oLayoutModel = getModel('layout');
		$layout_list = $oLayoutModel->getLayoutList();
		$mlayout_list = $oLayoutModel->getLayoutList(0, 'M');

		Context::set('layout_list', $layout_list);
		Context::set('mlayout_list', $mlayout_list);

		// 스킨 목록을 가져옴
		$skin_list = $oModuleModel->getSkins($this->module_path);
		Context::set('skin_list', $skin_list);

		// 모바일 스킨 목록을 가져옴
		$mskin_list = $oModuleModel->getSkins($this->module_path, 'm.skins');
		Context::set('mskin_list', $mskin_list);

		// 템플릿 파일 지정
		$this->setTemplateFile('skin');
	}

	function dispUserblockAdminPopup(){

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();
		Context::set('config', $config);

		$member_srl = Context::get('_member_srl');

		$oMemberModel = getModel('member');

		$output = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
		if ($output)
		{
			$oMemberController = getController('userblock');
			$member_block_info = $oMemberController->getUserblockInfo($member_srl);
			$is_banned = false;

			if(count($member_block_info) > 0){
				$is_banned = true;
			}
			$member_info = $output;
			$member_info->is_banned = $is_banned;
			$member_info->day = $is_banned ? $member_block_info->day : null;
			$member_info->hour = $is_banned ? $member_block_info->hour : null;
		}
		Context::set('target_member_srl', $member_srl);
		Context::set('member_info', $member_info);

		$this->setTemplateFile('userManagePopup');

	}


	function dispUserblockAdminCommentPopup(){

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();
		Context::set('config', $config);


		$target_srl = Context::get('comment_srl');

		$oCommentModel = getModel('comment');
		$columnList = array('comment_srl', 'module_srl', 'document_srl', 'member_srl', 'nick_name', 'ipaddress', 'content');
		$oComment = $oCommentModel->getComment($target_srl, FALSE, $columnList);
		$member_srl = $oComment->get('member_srl');

		if($oComment->document_srl){
			$oDocumentModel = getModel('document');
			$columnList = array('document_srl', 'title');
			$oDocument = $oDocumentModel->getDocument($oComment->document_srl, FALSE, $columnList);
		}

		$is_banned = false;
		$oUserblockController = getController('userblock');
		$block_ip_info = $oUserblockController->getUserblockIP($oComment->get('ipaddress'));

		if($member_srl > 0){
			// 회원 차단 체크
			$oMemberModel = getModel('member');
			$member_block_info = $oUserblockController->getUserblockInfo($member_srl);
			$is_deleted = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
			$member_is_deleted = $is_deleted ? false : true;

			if(count($member_block_info) > 0){
				$is_banned = true;
				$member_info = $output;
				$member_info->is_banned = $is_banned;
				$member_info->is_ip_banned = count($block_ip_info) > 0 ? true : false;
				$member_info->day = $is_banned ? $member_block_info->day : null;
				$member_info->hour = $is_banned ? $member_block_info->hour : null;
			}

		} else {
			// 비회원 IP차단유무
			$member_info = new stdClass();
			if(count($block_ip_info) > 0) {
				$is_banned = true;
			}
			$member_info->is_banned = $is_banned ? true : false;
			$member_info->is_ip_banned = $is_banned ? true : false;
			$member_info->day = $is_banned ? $block_ip_info->day : null;
			$member_info->hour = $is_banned ? $block_ip_info->hour : null;

		}

		$oModuleModel = getModel('module');
		$module_info = $oModuleModel->getModuleInfoByModuleSrl($oComment->module_srl);

		$content = $oComment->get('content');
		$content = cut_str($content, 200);
		$content = preg_replace("/<br[^>]*>/i", " ", $content);

		Context::set('module_info', $module_info);
		Context::set('member_info', $member_info);
		Context::set('member_is_deleted', $member_is_deleted);
		Context::set('content', strip_tags($content));
		Context::set('comment', $oComment);
		Context::set('document', $oDocument);
		$this->setTemplateFile('commentManagePopup');

	}


	function dispUserblockAdminDocumentPopup(){

		$target_srl = Context::get('doc_srl');
		if(!$target_srl) return new Object();

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();
		Context::set('config', $config);

		$oDocumentModel = getModel('document');
		$oDocument = $oDocumentModel->getDocument($target_srl);
		$member_srl = $oDocument->get('member_srl');

		if(!$oDocument->get('document_srl'))  return $this->stop('msg_invalid_request');

		$is_banned = false;
		$oUserblockController = getController('userblock');
		$block_ip_info = $oUserblockController->getUserblockIP($oDocument->get('ipaddress'));

		if($member_srl > 0){
			$oMemberModel = getModel('member');
			$member_block_info = $oUserblockController->getUserblockInfo($member_srl);
			$is_deleted = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
			$member_is_deleted = $is_deleted ? false : true;

			if(count($member_block_info) > 0){
				$is_banned = true;
				$member_info = $output;
				$member_info->is_banned = $is_banned;
				$member_info->is_ip_banned = count($block_ip_info) > 0 ? true : false;
				$member_info->day = $is_banned ? $member_block_info->day : null;
				$member_info->hour = $is_banned ? $member_block_info->hour : null;
			}

		} else {
			$member_info = new stdClass();
			if(count($block_ip_info) > 0) {
				$is_banned = true;
			}
			$member_info->is_banned = $is_banned ? true : false;
			$member_info->is_ip_banned = $is_banned ? true : false;
			$member_info->day = $is_banned ? $block_ip_info->day : null;
			$member_info->hour = $is_banned ? $block_ip_info->hour : null;
		}

		$oModuleModel = getModel('module');
		$module_info = $oModuleModel->getModuleInfoByModuleSrl($oDocument->get('module_srl'));

		Context::set('module_info', $module_info);
		Context::set('member_info', $member_info);
		Context::set('member_is_deleted', $member_is_deleted);
		Context::set('document', $oDocument);
		$this->setTemplateFile('documentManagePopup');


	}

	function dispUserblockAdminFilePopup(){

		$target_srl = Context::get('doc_srl');
		if(!$target_srl) return new Object();

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();
		Context::set('config', $config);

		$oDocumentModel = getModel('document');
		$oDocument = $oDocumentModel->getDocument($target_srl);
		$member_srl = $oDocument->get('member_srl');

		if(!$oDocument->get('document_srl'))  return $this->stop('msg_invalid_request');

		$is_banned = false;
		$oUserblockController = getController('userblock');
		$block_ip_info = $oUserblockController->getUserblockIP($oDocument->get('ipaddress'));

		if($member_srl > 0){
			$oMemberModel = getModel('member');
			$member_block_info = $oUserblockController->getUserblockInfo($member_srl);
			$is_deleted = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
			$member_is_deleted = $is_deleted ? false : true;

			if(count($member_block_info) > 0){
				$is_banned = true;
				$member_info = $output;
				$member_info->is_deleted = $is_deleted ? false : true;
				$member_info->is_banned = $is_banned;
				$member_info->is_ip_banned = count($block_ip_info) > 0 ? true : false;
				$member_info->day = $is_banned ? $member_block_info->day : null;
				$member_info->hour = $is_banned ? $member_block_info->hour : null;
			}

		} else {
			$member_info = new stdClass();
			if(count($block_ip_info) > 0) {
				$is_banned = true;
			}
			$member_info->is_banned = $is_banned ? true : false;
			$member_info->is_ip_banned = $is_banned ? true : false;
			$member_info->day = $is_banned ? $block_ip_info->day : null;
			$member_info->hour = $is_banned ? $block_ip_info->hour : null;
		}

		$oModuleModel = getModel('module');
		$module_info = $oModuleModel->getModuleInfoByModuleSrl($oDocument->get('module_srl'));

		Context::set('module_info', $module_info);
		Context::set('member_info', $member_info);
		Context::set('member_is_deleted', $member_is_deleted);
		Context::set('document', $oDocument);
		Context::set('file_list',$oDocument->getUploadedFiles());
		$this->setTemplateFile('fileManagePopup');


	}



}

/* End of file : userblock.admin.view.php */
/* Location : ./modules/userblock/userblock.admin.view.php */
