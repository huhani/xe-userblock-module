<?php
/*! Copyright (C) 2016 BGM STORAGE. All rights reserved. */
/**
 * @class userblockAdminController
 * @author Huhani (mmia268@gmail.com)
 * @brief Userblock module admin controller class.
 */

class userblockAdminController extends userblock
{
	function init()
	{
		$this->setTemplatePath($this->module_path . 'tpl');
	}

	function procUserblockAdminConfig()
	{

		$oModuleController = getController('module');

		$default = Context::getRequestVars();
		getDestroyXeVars($default);
		unset($default->body);
		unset($default->_filter);
		unset($default->error_return_url);
		unset($default->act);
		unset($default->module);
		unset($default->ruleset);

		$config = new stdClass();
		$config->use = $default->use;
		$config->ip_priority = $default->ip_priority;
		$config->member_auto = $default->member_auto;
		$config->member_sign_up = $default->member_sign_up;
		$config->member_delete = $default->member_delete;
		$config->use_javascript = $default->use_javascript;
		$config->ban_day = $default->ban_day === null ? 0 : $default->ban_day;
		$config->ban_hour = $default->ban_hour === null ? 0 : $default->ban_hour;
		$config->ban_notice = $default->ban_notice;
		$config->trash_reason = $default->trash_reason;

		unset($default->use);
		unset($default->ip_priority);
		unset($default->member_auto);
		unset($default->member_sign_up);
		unset($default->member_delete);
		unset($default->ban_day);
		unset($default->ban_hour);
		unset($default->ban_notice);
		unset($default->trash_reason);
		unset($default->use_javascript);

		$config->default = $default;
		$output = $oModuleController->updateModuleConfig('userblock', $config);
		if (!$output->toBool())
		{
			return $output;
		}

		// 메시지 지정
		$this->setMessage('success_saved');

		// 저장 후 이동할 URL을 지정합니다
		$returnUrl = Context::get('mid') ? getNotEncodedUrl('', 'mid', 'userblock', 'act', 'dispUserblockAdminConfig') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispUserblockAdminConfig');
		$this->setRedirectUrl($returnUrl);
	}


	function procUserblockAdminMidConfig(){
		$module_srl = Context::get('module_srl');
		if(!$module_srl) return $this->stop('msg_invalid_request');


		$oModuleController = getController('module');

		$get_config = Context::getRequestVars();
		getDestroyXeVars($get_config);
		unset($get_config->body);
		unset($get_config->_filter);
		unset($get_config->error_return_url);
		unset($get_config->act);
		unset($get_config->module);
		unset($get_config->ruleset);
		unset($get_config->module_srl);


		$config->{$module_srl} = $get_config;
		$output = $oModuleController->updateModuleConfig('userblock', $config);
		if (!$output->toBool())
		{
			return $output;
		}

		// 메시지 지정
		$this->setMessage('success_saved');

		// 저장 후 이동할 URL을 지정합니다

		$returnUrl = Context::get('mid') ? getNotEncodedUrl('', 'mid', 'userblock', 'act', 'dispUserblockAdminMidConfig', 'module_srl', $module_srl) : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispUserblockAdminMidConfig', 'module_srl', $module_srl);
		$this->setRedirectUrl($returnUrl);

	}

	function procUserblockAdminSkinConfig() {

		$oModuleController = getController('module');

		$config = Context::getRequestVars();
		getDestroyXeVars($config);
		unset($config->body);
		unset($config->_filter);
		unset($config->error_return_url);
		unset($config->act);
		unset($config->module);
		unset($config->ruleset);

		$output = $oModuleController->updateModuleConfig('userblock', $config);
		if (!$output->toBool())
		{
			return $output;
		}

		// 메시지 지정
		$this->setMessage('success_saved');

		// 저장 후 이동할 URL을 지정합니다
		$returnUrl = Context::get('mid') ? getNotEncodedUrl('', 'mid', 'userblock', 'act', 'dispUserblockAdminSkin') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispUserblockAdminSkin');
		$this->setRedirectUrl($returnUrl);
	}


	function procUserblockAdminMidReset() {
		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();
		$module_srl = Context::get('module_srl');

		unset($config->{$module_srl});

		$oModuleController = getController('module');
		$output = $oModuleController->InsertModuleConfig('userblock', $config);
		if (!$output->toBool())
		{
			return $output;
		}

		// 메시지 지정
		$this->setMessage('success_reset');
	}


	function procUserblockAdminBanMember()	{

		$logged_info = Context::get('logged_info');
		$member_srl = $logged_info->member_srl;
		$nick_name = $logged_info->nick_name;

		$target_member_srl = Context::get('_member_srl');
		$comment = Context::get('comment');
		$day = Context::get('day');
		$hour = Context::get('hour');

		if(!$target_member_srl || !$comment || $day < 0 || $hour < 0 || $hour > 24) return $this->stop('msg_invalid_request');


		$oMemberModel = getModel('member');

		$args = new stdClass();
		$args->member_srl = $member_srl;
		$args->nick_name = $nick_name;
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		//target_user_info
		$args->target_srl = 0;
		$args->comment = $comment;
		$args->type = 0;
		$args->day = ($day == null || $day == "") ? 0 : $day;
		$args->hour = ($hour == null || $hour == "") ? 0 : $hour;
		$args->expdate = (($day == 0 || $day == null) && ($hour == 0 || $hour == null)) ? null : date("YmdHis", mktime(date('H') + $hour, date('i'), date('s'), date('m'), date('d')+$day, date('Y')));
		$args->regdate = date("YmdHis");

		$output = $oMemberModel->getMemberInfoByMemberSrl($target_member_srl);
		if ($output){
			$args->target_member_srl = $output->member_srl;
			$args->target_nick_name = $output->nick_name;
			$this->doUserblockBan($args);
		}

		Context::set('message', 'success_banned');
		$this->setTemplateFile('closePopup');

	}


	function procUserblockAdminBanDelete()
	{

		$logged_info = Context::get('logged_info');
		$member_srl = $logged_info->member_srl;
		$nick_name = $logged_info->nick_name;
		$target_member_srl = Context::get('member_srl');
		if(!$target_member_srl || $target_member_srl === 0) return $this->stop('msg_invalid_request');
		$args->member_srl = $member_srl;
		$args->nick_name = $nick_name;
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		$args->target_srl = 0;
		$args->type = "!";
		$args->regdate = date("YmdHis");

		$oMemberModel = getModel('member');
		$output = $oMemberModel->getMemberInfoByMemberSrl($target_member_srl);
		if ($output) {
			$args->target_member_srl = $output->member_srl;
			$args->target_nick_name = $output->nick_name;
			$this->doUserblockDeleteBan($args);
		}

		Context::set('message', 'success_delete_banned');

		$returnUrl = Context::get('mid') ? getNotEncodedUrl('', 'mid', 'userblock', 'act', '') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispUserblockAdminList');
		$this->setRedirectUrl($returnUrl);

	}


	function procUserblockAdminBanDeleteIP()
	{

		$logged_info = Context::get('logged_info');
		$member_srl = $logged_info->member_srl;
		$nick_name = $logged_info->nick_name;
		$id = Context::get('id');

		$args = new stdClass();
		$args->expdate = date("YmdHis");
		$args->id = $id;
		$args->action = "deleteBlockUser";
		$args->use_ipban = "N"; // 이미 등록된 경우 기존 업데이트는 묻어버리고 새로 등록.
		$output = executeQuery('userblock.updateMemberBanIP', $args);

		Context::set('message', 'success_delete_banned');

		$returnUrl = Context::get('mid') ? getNotEncodedUrl('', 'mid', 'userblock', 'act', '') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispUserblockAdminList');
		$this->setRedirectUrl($returnUrl);

	}


	function procUserblockAdminArrangeLog(){
		$args = new stdClass();
		$args->expdate = date("YmdHis");
		$output = executeQueryArray('userblock.deleteExpdateLog',$args);

		Context::set('message', 'success_deleted');
	}


	function procUserblockAdminBanComment(){

		$logged_info = Context::get('logged_info');
		$member_srl = $logged_info->member_srl;
		$nick_name = $logged_info->nick_name;
		$comment_srl = Context::get('comment_srl');

		$comment = Context::get('comment');
		$day = Context::get('day');
		$hour = Context::get('hour');

		if(!$comment_srl || !$comment || $day < 0 || $hour < 0 || $hour > 24) return $this->stop('msg_invalid_request');

		$oCommentModel = getModel('comment');
		$columnList = array('comment_srl', 'module_srl', 'member_srl', 'nick_name', 'ipaddress', 'content');
		$oComment = $oCommentModel->getComment($comment_srl, FALSE, $columnList);
		$target_member_srl = $oComment->get('member_srl');
		$target_nick_name = $oComment->get('nick_name');
		$target_ipaddress = $oComment->get('ipaddress');

		if($target_member_srl > 0){
			$use_ipban = Context::get('ban_ip') === "Y" ? "Y" : "N";
		} else {
			$use_ipban = "Y";
		}

		$args = new stdClass();
		$args->member_srl = $member_srl;
		$args->nick_name = $nick_name;
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		//target_user_info
		$args->target_srl = $comment_srl;
		$args->comment = $comment;
		$args->type = 2;
		$args->day = ($day == null || $day == "") ? 0 : $day;
		$args->hour = ($hour == null || $hour == "") ? 0 : $hour;
		$args->expdate = (($day == 0 || $day == null) && ($hour == 0 || $hour == null)) ? null : date("YmdHis", mktime(date('H') + $hour, date('i'), date('s'), date('m'), date('d')+$day, date('Y')));
		$args->regdate = date("YmdHis");
	
		$args->use_ipban = $use_ipban;

		$args->target_member_srl = $target_member_srl;
		$args->target_nick_name = $target_nick_name;
		$args->target_ipaddress = $target_ipaddress;

		$this->doUserblockBan($args);

		Context::set('message', 'success_banned');
		$this->setTemplateFile('closePopup');

	}


	function procUserblockAdminBanDocument(){

		$logged_info = Context::get('logged_info');
		$member_srl = $logged_info->member_srl;
		$nick_name = $logged_info->nick_name;
		$document_srl = Context::get('document_srl');
		$vote_type = Context::get('vote_type') ? Context::get('vote_type') : 0;
		$to_trash = Context::get('to_trash') ? Context::get('to_trash') : "N";

		$comment = Context::get('comment');
		$day = Context::get('day');
		$hour = Context::get('hour');
		if(!$document_srl || !$comment || $day < 0 || $hour < 0 || $hour > 24) return $this->stop('msg_invalid_request');
		$oDocumentModel = getModel('document');
		$columnList = array('document_srl', 'module_srl', 'member_srl', 'nick_name', 'ipaddress', 'title');
		$oDocument = $oDocumentModel->getDocument($document_srl, false, false, $columnList);

		if(!$oDocument->get('document_srl')) return $this->stop('msg_invalid_request');

		$target_module_srl = $oDocument->get('module_srl');
		$target_member_srl = $oDocument->get('member_srl');
		$target_nick_name = $oDocument->get('nick_name');
		$target_ipaddress = $oDocument->get('ipaddress');

		if($target_member_srl > 0){
			$use_ipban = Context::get('ban_ip') === "Y" ? "Y" : "N";
		} else {
			$use_ipban = "Y";
		}

		$args = new stdClass();
		$args->member_srl = $member_srl;
		$args->nick_name = $nick_name;
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		//target_user_info
		$args->target_srl = $document_srl;
		$args->comment = $comment;
		$args->type = Context::get('type') ? Context::get('type') : 1;
		$args->day = ($day == null || $day == "") ? 0 : $day;
		$args->hour = ($hour == null || $hour == "") ? 0 : $hour;
		$args->expdate = (($day == 0 || $day == null) && ($hour == 0 || $hour == null)) ? null : date("YmdHis", mktime(date('H') + $hour, date('i'), date('s'), date('m'), date('d')+$day, date('Y')));
		$args->regdate = date("YmdHis");
	
		$args->use_ipban = $use_ipban;

		if($vote_type != "0"){
			$vote_day = Context::get('vote_day');
			$vote_hour = Context::get('vote_hour');
			$vote_comment = Context::get('vote_comment');
			if(!$vote_comment || $vote_day < 0 || $vote_hour < 0 || $vote_hour > 24) return $this->stop('msg_invalid_request');

			$args->vote_type = $vote_type;
			$args->vote_day = ($vote_day == null || $vote_day == "") ? 0 : $vote_day;
			$args->vote_hour = ($vote_hour == null || $vote_hour == "") ? 0 : $vote_hour;
			$args->vote_expdate = (($vote_day == 0 || $vote_day == null) && ($vote_hour == 0 || $vote_hour == null)) ? null : date("YmdHis", mktime(date('H') + $vote_hour, date('i'), date('s'), date('m'), date('d')+$vote_day, date('Y')));
			$args->vote_use_ipban = Context::get('vote_use_ipban') ? Context::get('vote_use_ipban') : "N";
			$args->vote_comment = $vote_comment;

			$this->doUserblockVote($args);
		}

		$args->target_module_srl = $target_module_srl;
		$args->target_member_srl = $target_member_srl;
		$args->target_nick_name = $target_nick_name;
		$args->target_ipaddress = $target_ipaddress;

		$this->doUserblockBan($args);

		if($to_trash != "N"){
			$this->doUserblockMoveDocumentTrash($args);
		}

		Context::set('message', 'success_banned');
		$this->setTemplateFile('closePopup');

	}


	function doUserblockMoveDocumentTrash($obj){

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		$args = new stdClass;
		$args->document_srl = $obj->target_srl;
		$args->description = $config->trash_reason;
		
		$oDocumentController = getController('document');
		$oDocumentController->moveDocumentToTrash($args);
		
		if($obj->target_member_srl){
			$args = new stdClass;
			$args->module_srl = $obj->target_module_srl;
			$args->member_srl = $obj->target_member_srl;
			$args->uploaded_count = 0;
			$oPointController = getController('point');
			$oPointController->triggerDeleteDocument($args);
		}

	}


	function doUserblockVote($obj){ // document_srl, type(vote), member_srl, nick_name, target_member_srl(doc), comment(vote), day, hour, expdate
		$args = new stdClass();
		$args->document_srl = $obj->target_srl;
		$output = executeQueryArray('userblock.getVotedMemberList',$args);
		if(!$output->toBool()) return $output;

		$oMemberModel = getModel('member');
		if($output->data)
		{
			foreach($output->data as $k => $d)
			{
				$args1 = new stdClass();
				$args1->member_srl = $obj->member_srl;
				$args1->nick_name = $obj->nick_name;
				$args1->ipaddress = $obj->ipaddress;
				$args1->target_srl = $obj->target_srl;
				$args1->day = $obj->vote_day;
				$args1->hour = $obj->vote_hour;
				$args1->comment = $obj->vote_comment;
				$args1->expdate = $obj->vote_expdate;
				$args1->regdate = $obj->regdate;
				$args1->use_ipban = $obj->vote_use_ipban;

				$member_info = $oMemberModel->getMemberInfoByMemberSrl($d->member_srl);

				$args1->target_member_srl = $d->member_srl;
				$args1->target_nick_name = $d->member_srl == 0 ? "[@anon]" : !$member_info->nick_name ? "[@leave]" : $member_info->nick_name;
				$args1->target_ipaddress = $d->ipaddress;

				if($d->point > 0 && ($obj->vote_type == "1" || $obj->vote_type == "3")){ // 추천
					$args1->type = 3;
					$this->doUserblockBan($args1);

				}
				else if($d->point < 0 && ($obj->vote_type == "2" || $obj->vote_type == "3")){ // 비추천
					$args1->type = 4;
					$this->doUserblockBan($args1);
				}
			}
		}

	}


	function doUserblockDeleteBan($obj){
		$clone_obj = clone $obj;
		$check_exist = $this->checkUserblockExist($clone_obj->target_member_srl);
		if(!$check_exist){

			return false;

		} else {

			$args = new stdClass();
			$args->expdate = $obj->regdate;
			$args->target_member_srl = $obj->target_member_srl;
			$args->action = "deleteBlockUser";
			$args->use_ipban = "N"; // 이미 등록된 경우 기존 업데이트는 묻어버리고 새로 등록.
			$output = executeQuery('userblock.updateMemberBanIP', $args);

			$clone_obj->action = "deleteBlockUser";
			$clone_obj->id = getNextSequence();
			$output = executeQuery('userblock.deleteBlockMember', $clone_obj);
			$this->doUserblockLog($clone_obj);

		}
	
	}

	function doUserblockBan($obj){

		$obj->id = getNextSequence();
		$oMemberModel = getModel('member');

		$member_info = $oMemberModel->getMemberInfoByMemberSrl($obj->target_member_srl); // 탈퇴한 회원인지 체크

		if($obj->target_member_srl > 0 && $member_info->member_srl){ // 차단 대상이 회원일 경우
			if($obj->use_ipban === "Y"){	// IP차단을 사용하는지 체크
				$target_ipaddr_info = $this->getUserblockIP($obj->target_ipaddress);
				if(count($target_ipaddr_info) > 0){
					$args = new stdClass();
					$args->expdate = $obj->regdate;
					$args->target_ipaddress = $obj->target_ipaddress;
					$args->action = "deleteBlockUser";
					$args->use_ipban = "N"; // 이미 등록된 경우 기존 업데이트는 묻어버리고 새로 등록.
					$output = executeQuery('userblock.updateMemberBanIP', $args);

				}
			}

			$member_ban_info = $this->getUserblockInfo($obj->target_member_srl);
			if(count($member_ban_info) === 0){
			
				$obj->action = 'insertBlockUser';
				$output = executeQuery('userblock.insertMemberBanInfo', $obj);
				$this->doUserblockLog($obj);

			} else {
				//update 액션 추가

				if($member_ban_info->expdate > $obj->regdate){
					$obj->action = 'updateBlockUser'; //이미 차단중인 유저
				} else {
					$obj->action = 'insertBlockUser'; //차단이 끝난 유저
				}

				$output = executeQuery('userblock.updateMemberBanInfo', $obj);
				$this->doUserblockLog($obj);

			}

		} else { // 차단 대상이 비회원이고 탈퇴한 회원일 경우

			$target_ipaddr_info = $this->getUserblockIP($obj->target_ipaddress);	// 이미 IP주소가 차단등록되어있는지 검사. 
			if(count($target_ipaddr_info) > 0){

				$obj->action = "updateBlockUser";

				$args = new stdClass();
				$args->expdate = $obj->regdate;
				$args->target_ipaddress = $obj->target_ipaddress;
				$args->use_ipban = "N"; // 이미 등록된 경우 기존 업데이트는 묻어버리고 새로 등록.
				$output = executeQuery('userblock.updateMemberBanIP', $args);

			} else {
				$obj->action = "insertBlockUser"; // 등록이 되어있지 않은 경우
			}

			$this->doUserblockLog($obj);
		}
	
	}

	function doUserblockLog($obj){

		$output = executeQuery('userblock.insertMemberBanLog', $obj);
	}


	function getUserblockInfo($member_srl){

		$args = new stdClass();
		$args->target_member_srl = $member_srl;
		$output = executeQuery('userblock.getMemberBan', $args);
		return $output->data;

	}

	function getUserblockIP($ipaddress){

		$args = new stdClass();
		$args->target_ipaddress = $ipaddress;
		$args->expdate = date("YmdHis");
		$output = executeQuery('userblock.getMemberBanIP', $args);
		return $output->data;

	}


	function checkUserblockExist($member_srl){

		$args = new stdClass();
		$args->target_member_srl = $member_srl;
		$output = executeQuery('userblock.getMemberCheck', $args);
		return ($output->data->count) > 0 ? true : false;

	}


}

/* End of file userblock.admin.controller.php */
/* Location: ./modules/userblock/userblock.admin.controller.php */
