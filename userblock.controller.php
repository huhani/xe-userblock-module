<?php
/*! Copyright (C) 2016 BGM STORAGE. All rights reserved. */
/**
 * @class userblockController
 * @author Huhani (mmia268@gmail.com)
 * @brief Userblock module controller class.
 */

class userblockController extends userblock
{
	function init()
	{
		Context::loadLang('./modules/userblock/lang/');
	}


	function triggerDeleteMember(&$obj)
	{

		$args = new stdClass();
		$args->target_member_srl = $obj->member_srl;
		$output = executeQuery('userblock.deleteBlockMember', $args);

		$logged_info = Context::get('logged_info');
		if($logged_info->is_admin == 'Y') return new Object();

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($config->use === "N" || $config->member_delete === "N") {
			return new Object();
		} else {

			$args1 = new stdClass();
			$args1->expdate = date("YmdHis");
			$args1->target_member_srl = $obj->member_srl;
			$args1->use_ipban = "N";
			$output = executeQuery('userblock.updateMemberBanIP', $args1);	

		}

		return new Object();
	}


	function triggerMemberMenu()
	{
		$member_srl = Context::get('target_srl');
		$logged_info = Context::get('logged_info');

		if(!$logged_info) return new Object();

		$oModuleModel = getModel('module');
		$mid_info = $oModuleModel->getModuleInfoByMid("userblock");
		$is_module_admin = false;

		if($logged_info->is_admin == 'Y') $is_module_admin = true;
		else {
			$admin_member = $oModuleModel->getAdminId($mid_info->module_srl);
			if(!empty($admin_member)){
				foreach($admin_member as $value){
					if($value->member_srl === $logged_info->member_srl){
						$is_module_admin = true;
						break;
					}
				}
			}
			if(!$is_module_admin) {

				$getGrant = $this->getUserblockAdminGroup($mid_info);
				$member_group_list = $logged_info->group_list;
				foreach($getGrant as $value){
					if(isset($member_group_list[$value])) $is_module_admin = true;
				}
			}
		}

		if ($logged_info->member_srl != $member_srl && $is_module_admin)
		{
			$oMemberController = getController('member');
			$oMemberController->addMemberPopupMenu(getUrl('', 'mid', 'userblock', 'act', 'dispUserblockAdminPopup', '_member_srl', $member_srl), 'cmd_userblock_management', '', 'popup');
		}

		return new Object();
	}

	function triggerBeforeInsertDocument(&$obj)
	{
		$logged_info = Context::get('logged_info');
		if($logged_info->is_admin == 'Y') return new Object();

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($obj->module_srl && $config->{$obj->module_srl}) {
			$set = $config->{$obj->module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->doc_write === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}

		return new Object();
	}


	function triggerBeforeUpdateDocument(&$obj)
	{
		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($obj->module_srl && $config->{$obj->module_srl}) {
			$set = $config->{$obj->module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->doc_modify === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}

		return new Object();
	}

	function triggerBeforeDeleteDocument(&$obj)
	{
		if(!$obj->module_srl) return new Object();	//admin모듈에서 삭제할때 에러남
		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($obj->module_srl && $config->{$obj->module_srl}) {
			$set = $config->{$obj->module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->doc_delete === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}

		return new Object();
	}

	function triggerBeforeInsertComment(&$obj)
	{
		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($obj->module_srl && $config->{$obj->module_srl}) {
			$set = $config->{$obj->module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->cmt_write === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}

		return new Object();
	}

	function triggerBeforeUpdateComment(&$obj)
	{
		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($obj->module_srl && $config->{$obj->module_srl}) {
			$set = $config->{$obj->module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->cmt_modify === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}

		return new Object();
	}


	function triggerBeforeDeleteComment(&$obj)
	{
		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($obj->module_srl && $config->{$obj->module_srl}) {
			$set = $config->{$obj->module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->cmt_delete === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}

		return new Object();
	}


	function triggerGetCommentMenu(&$menu_list) {

		if(!Context::get('is_logged')) {
			return new Object();
		}

		$logged_info = Context::get('logged_info');
		$comment_srl = Context::get('target_srl');
		if(!$logged_info || !$comment_srl) return new Object();

		$oModuleModel = getModel('module');
		$mid_info = $oModuleModel->getModuleInfoByMid("userblock");
		$is_module_admin = false;

		if($logged_info->is_admin == 'Y') $is_module_admin = true;
		else {
			$admin_member = $oModuleModel->getAdminId($mid_info->module_srl);
			foreach($admin_member as $value){
				if($value->member_srl === $logged_info->member_srl){
					$is_module_admin = true;
					break;
				}
			}
			if(!$is_module_admin) {

				$getGrant = $this->getUserblockAdminGroup($mid_info);
				$member_group_list = $logged_info->group_list;
				foreach($getGrant as $value){
					if(isset($member_group_list[$value])) $is_module_admin = true;
				}
			}
		}

		if(!$is_module_admin) return new Object();

		$oCommentModel = getModel('comment');
		$columnList = array('comment_srl', 'module_srl', 'member_srl', 'ipaddress');
		$oComment = $oCommentModel->getComment($comment_srl, FALSE, $columnList);
		$member_srl = $oComment->get('member_srl');

		if ($member_srl != $logged_info->member_srl){

			$oCommentController = getController('comment');
			$url = getUrl('','mid','userblock','act','dispUserblockAdminCommentPopup','comment_srl', $comment_srl);
			$oCommentController->addCommentPopupMenu($url,'cmd_userblock_cmt_manager','','popup');

		}

		return new Object();
	}


	function triggerGetDocumentMenu(&$menu_list)	{

		if(!Context::get('is_logged')) {
			return new Object();
		}

		$logged_info = Context::get('logged_info');
		$document_srl = Context::get('target_srl');

		if(!$logged_info || !$document_srl) return new Object();

		$oModuleModel = getModel('module');
		$mid_info = $oModuleModel->getModuleInfoByMid("userblock");
		$admin_member = $oModuleModel->getAdminId($mid_info->module_srl);
		$is_module_admin = false;

		if($logged_info->is_admin == 'Y') $is_module_admin = true;
		else {
			foreach($admin_member as $value){
				if($value->member_srl === $logged_info->member_srl){
					$is_module_admin = true;
					break;
				}
			}

			if(!$is_module_admin) {

				$getGrant = $this->getUserblockAdminGroup($mid_info);
				$member_group_list = $logged_info->group_list;
				foreach($getGrant as $value){
					if(isset($member_group_list[$value])) $is_module_admin = true;
				}
			}
		}

		if(!$is_module_admin) return new Object();

		$oDocumentModel = getModel('document');
		$columnList = array('document_srl', 'module_srl', 'member_srl', 'ipaddress');
		$oDocument = $oDocumentModel->getDocument($document_srl, false, false, $columnList);
		$member_srl = $oDocument->get('member_srl');

		if ($member_srl != $logged_info->member_srl){

			$oDocumentController = getController('document');
			$url = getUrl('','mid','userblock','act','dispUserblockAdminDocumentPopup','doc_srl', $document_srl);
			$oDocumentController->addDocumentPopupMenu($url,'cmd_userblock_doc_manager','','popup');

			$oDocument = $oDocumentModel->getDocument($document_srl);
			$file_list = $oDocument->getUploadedFiles();
			if(count($file_list) > 0){
				$url = getUrl('','mid','userblock','act','dispUserblockAdminFilePopup','doc_srl', $document_srl);
				$oDocumentController->addDocumentPopupMenu($url,'cmd_userblock_file_manager','','popup');
			}

		}

		return new Object();
	}

	function triggerInsertMember(&$obj)
	{

		$logged_info = Context::get('logged_info');
		if($logged_info->is_admin == 'Y') return new Object();

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($config->use === "N" || $config->member_sign_up === "N") {
			return new Object();
		} else {

			$args = new stdClass();
			$args->target_ipaddress = $_SERVER['REMOTE_ADDR'];
			$args->expdate = date("YmdHis");
			$output = executeQuery('userblock.getMemberBanIP', $args);
			$ban_info = $output->data;
			if(count($output->data)){
				$message = $this->getBanMessage($config->ban_notice, $ban_info);
				return new Object(-1,sprintf('%s',$message));
			}

	
		}

		return new Object();
	}


	function triggerAfterLogin(&$obj)
	{

		//debugPrint($obj);

		return new Object();
	}


	function triggerBeforeDownloadFile(&$obj)
	{

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($obj->module_srl && $config->{$obj->module_srl}) {
			$set = $config->{$obj->module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->file_download === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}

		return new Object();
	}

	function _triggerBeforeDocVoteUp($module_srl){

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($module_srl && $config->{$module_srl}) {
			$set = $config->{$module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->doc_voteup === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}
		return new Object();
	}

	function _triggerBeforeDocVoteDown($module_srl){

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($module_srl && $config->{$module_srl}) {
			$set = $config->{$module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->doc_votedown === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}
		return new Object();
	}


	function _triggerBeforeCmtVoteUp($module_srl){

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($module_srl && $config->{$module_srl}) {
			$set = $config->{$module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->cmt_voteup === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}
		return new Object();
	}

	function _triggerBeforeCmtVoteDown($module_srl){

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($module_srl && $config->{$module_srl}) {
			$set = $config->{$module_srl};
		} else {
			$set = $config->default;
		}

		if($config->use === "N" || $set->use_module === "N" || $set->cmt_votedown === "N") {
			return new Object();
		} else {
			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}
		return new Object();
	}

	function _triggerDispBoardWrite($module_srl){

		$oUserblockModel = getModel('userblock');

		$config = $oUserblockModel->getConfig();

		if($module_srl && $config->{$module_srl}) {
			$set = $config->{$module_srl};
		} else {
			$set = $config->default;
		}

		$document_srl = Context::get('document_srl');
		if($config->use === "N" || $set->use_module === "N" || ( (!$document_srl && $set->doc_write === "N") || ($document_srl && $set->doc_modify === "N") )) {
			return new Object();
		} else {

			$member_info = $this->checkMemberBanned(null, null, $config->ip_priority);
			if($member_info->is_banned){

				$message = $this->getBanMessage($config->ban_notice, $member_info);
				return new Object(-1,sprintf('%s',$message));
			}
	
		}
		return new Object();
	}


	function triggerBeforeModuleInit(&$obj){

		if(!Context::get('is_logged')) {
			return new Object();
		}

		$member_srl = Context::get('target_srl');
		$logged_info = Context::get('logged_info');

		$oModuleModel = getModel('module');
		$mid_info = $oModuleModel->getModuleInfoByMid("userblock");
		$admin_member = $oModuleModel->getAdminId($mid_info->module_srl);
		$is_module_admin = false;

		if($logged_info->is_admin == 'Y') $is_module_admin = true;
		else {
			if(!empty($admin_member)){
				foreach($admin_member as $value){
					if($value->member_srl === $logged_info->member_srl){
						$is_module_admin = true;
						break;
					}
				}
			}
			if(!$is_module_admin) {

				$getGrant = $this->getUserblockAdminGroup($mid_info);
				$member_group_list = $logged_info->group_list;
				foreach($getGrant as $value){
					if(isset($member_group_list[$value])) $is_module_admin = true;
				}
			}
		}

		if ($logged_info->member_srl != $member_srl && $is_module_admin){
			$oMemberController = getController('member');
			$oMemberController->addMemberMenu('dispUserblockMemberRedirect', 'userblock_manager');
		}

		return new Object();

	}

	function triggerBeforeModuleProc(&$obj){
		switch($obj->act){

			case "procDocumentVoteUp":
				return $this->_triggerBeforeDocVoteUp($obj->module_srl);
			break;

			case "procDocumentVoteDown":
				return $this->_triggerBeforeDocVoteDown($obj->module_srl);
			break;

			case "procCommentVoteUp":
				return $this->_triggerBeforeCmtVoteUp($obj->module_srl);
			break;

			case "procCommentVoteDown":
				return $this->_triggerBeforeCmtVoteDown($obj->module_srl);
			break;

			case "dispBoardWrite":
				return $this->_triggerDispBoardWrite($obj->module_srl);
			break;

			default:
			break;
		}

		return new Object();
	}




	function triggerBeforeDisplay(&$output){

		if (Context::getResponseMethod() == 'HTML') {
			$mid = Context::get('mid');
			if(!$mid) return new Object();
			if ($mid){
				$oModuleModel = getModel('module');
				$mid_info = $oModuleModel->getModuleInfoByMid($mid);
				$module_srl = $mid_info->module_srl;
			}

			$logged_info = Context::get('logged_info');
			if(!$module_srl) return new Object();

			$oUserblockModel = getModel('userblock');

			$config = $oUserblockModel->getConfig();
			if($config->{$module_srl}) {
				$set = $config->{$module_srl};
			} else {
				$set = $config->default;
			}

			if($config->use === "N" || $config->use_javascript === "N" || $set->use_module === "N") {
				return new Object();
			} else {
				$member_info = $this->checkMemberBanned($logged_info->member_srl, null, $config->ip_priority);
				if($member_info->is_banned){

					$message = $this->getBanMessage($config->ban_notice, $member_info);
					Context::set('config', $set);				
					Context::set('msg', $message);		

					$oTemplate = TemplateHandler::getInstance();
					$temlate_path = $this->module_path . 'tpl';
					$compile = $oTemplate->compile($temlate_path, 'javascriptNotice');
					$output .= $compile;
				}
	
			}
		}

		return new Object();
	}

	function getUserblockInfo($member_srl){

		$args = new stdClass();
		$args->target_member_srl = $member_srl;
		$args->expdate = date("YmdHis");
		$output = executeQuery('userblock.getMemberCheckBan', $args);
		return $output->data;

	}

	function getUserblockAdminGroup($module_info){

		if(!$module_srl){
			$oModuleModel = getModel('module');
			$module_info = $oModuleModel->getModuleInfoByMid("userblock");
		}

		$args = new stdClass();
		$args->module_srl = $module_info->module_srl;
		$output = executeQueryArray('module.getModuleGrants', $args);

		$oMemberModel = getModel('member');
		$group_list = $oMemberModel->getGroups($module_info->site_srl);

		$adminGroup_array = array();

		foreach($output->data as $manager_group){
			if($manager_group->name === "manager"){
				foreach($group_list as $val){
					if($val->group_srl === $manager_group->group_srl){
						//$adminGroup_array[$manager_group->group_srl] = $val->title;
						array_push($adminGroup_array, $manager_group->group_srl);
					}
				}
			}
		}

		return $adminGroup_array;

	}

	function getUserblockIP($ipaddress){

		$args = new stdClass();
		$args->target_ipaddress = $ipaddress;
		$args->use_ipban = "Y";
		$args->expdate = date("YmdHis");
		$output = executeQuery('userblock.getMemberBanIP', $args);
		return $output->data;

	}

	function checkMemberBanned($member_srl, $ipaddress, $ip_priority){	// 리턴해야할 값 : is_banned, type, comment, day, hour, expdate

		$args = new stdClass();
		$args->is_banned = false;

		if(!$member_srl){
			$logged_info = Context::get('logged_info');
			$member_srl = $logged_info->member_srl;
		}

		if(!$ipaddress){
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}

		if(!$ip_priority) {
			$oUserblockModel = getModel('userblock');

			$config = $oUserblockModel->getConfig();
			$ip_priority = $config->ip_priority;
		}

		if($member_srl > 0) { //회원인 경우 차단체킹

			$member_info = $this->getUserblockInfo($member_srl);
			if(count($member_info)){
				$args->is_banned = true;
				$args->type = $member_info->type;
				$args->comment = $member_info->comment;
				$args->day = $member_info->day;
				$args->hour = $member_info->hour;
				$args->expdate = $member_info->expdate;

				return $args;
			}

		}

		if($ip_priority == "Y" || !$member_srl){ //비회원이거나 IP차단우선기능을 사용하는 경우

			$ip_info = $this->getUserblockIP($ipaddress);
			if(count($ip_info)){
				$args->is_banned = true;
				$args->type = $ip_info->type;
				$args->comment = $ip_info->comment;
				$args->day = $ip_info->day;
				$args->hour = $ip_info->hour;
				$args->expdate = $ip_info->expdate;

				return $args;
			}

		}

		return $args;

	}

	function getBanMessage($notice, $obj){


		if($obj->day == 0 && $obj->hour == 0) $time = "영구";
		else {
			$ban_time = ($obj->day) * 24 + $obj->hour;
			if($ban_time <= 96) {
				$time = $ban_time."시간";
			} else {	
				$time = $obj->day > 0 ? ($obj->day."일") : null;
				$time .= ($obj->day && $obj->hour) ? " " : "";
				$time .= $obj->hour > 0 ? ($obj->hour."시간") : null;
			}
		}


		switch($obj->type){

			case 0:
				$type = "기타";
			break;

			case 1:
				$type = "게시글 작성";
			break;

			case 2:
				$type = "댓글 작성";
			break;

			case 3:
				$type = "게시글 추천";
			break;

			case 4:
				$type = "게시글 비추천";
			break;

			case 5:
				$type = "파일";
			break;

			default:
				$type = "예외";
			break;
		}
		$expdate = ($obj->day == 0 && $obj->hour == 0) ? "무기한" : zdate($obj->expdate,"Y-m-d H:i");

		$notice = preg_replace('/\[time\]/', $time, $notice);
		$notice = preg_replace('/\[comment\]/', $obj->comment, $notice);
		$notice = preg_replace('/\[type\]/', $type, $notice);
		$notice = preg_replace('/\[expdate\]/', $expdate, $notice);

		return $notice;

	}


}

/* End of file userblock.controller.php */
/* Location: ./modules/userblock/userblock.controller.php */
