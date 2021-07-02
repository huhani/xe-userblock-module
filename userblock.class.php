<?php
/*! Copyright (C) 2016 BGM STORAGE. All rights reserved. */
/**
 * @class userblock
 * @author Huhani (mmia268@gmail.com)
 * @brief Userblock module high class.
 */

class userblock extends ModuleObject
{
	private $triggers = array(
		array( 'member.getMemberMenu',		'userblock',	'controller',	'triggerMemberMenu',						'after'	),
		array( 'member.insertMember',			'userblock',	'controller',	'triggerInsertMember',					'before'	),
		array( 'member.deleteMember',			'userblock',	'controller',	'triggerDeleteMember',					'after'	),
		array( 'member.doLogin',				'userblock',	'controller',	'triggerAfterLogin',						'after'	),
		array( 'document.insertDocument',	'userblock',	'controller',	'triggerBeforeInsertDocument',      'before'	),
		array( 'document.updateDocument',	'userblock',	'controller',	'triggerBeforeUpdateDocument',      'before'	),
		array( 'document.deleteDocument',	'userblock',	'controller',	'triggerBeforeDeleteDocument',      'before'	),
		array( 'document.getDocumentMenu',	'userblock',	'controller',	'triggerGetDocumentMenu',				'after'	),
		array( 'comment.insertComment',		'userblock',	'controller',	'triggerBeforeInsertComment',       'before'	),
		array( 'comment.updateComment',		'userblock',	'controller',	'triggerBeforeUpdateComment',       'before'	),
		array( 'comment.deleteComment',		'userblock',	'controller',	'triggerBeforeDeleteComment',       'before'	),
		array( 'comment.getCommentMenu',		'userblock',	'controller',	'triggerGetCommentMenu',				'after'	),
		array( 'file.downloadFile',			'userblock',	'controller',	'triggerBeforeDownloadFile',			'before'	),
		array( 'moduleHandler.init',			'userblock',	'controller',	'triggerBeforeModuleInit',				'before'	),
		array( 'moduleObject.proc',			'userblock',	'controller',	'triggerBeforeModuleProc',				'before'	),
		array( 'display',							'userblock',	'controller',	'triggerBeforeDisplay',					'before'	)
	);


	function moduleInstall()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		$userblock_info = $oModuleModel->getModuleInfoByMid('userblock');
		if(!$userblock_info->module_srl) {
			$args = new stdClass();
			$args->mid = 'userblock';
			$args->module = 'userblock';
			$args->browser_title = '유저 차단';
			$args->site_srl = 0;
			$args->skin = 'default';
			$oModuleController->insertModule($args);
		}

		$config = new stdClass();
		$default = new stdClass();

		$default->use_module = 'Y';
		$default->doc_write = 'Y';
		$default->doc_modify = 'Y';
		$default->doc_delete = 'N';
		$default->doc_voteup = 'Y';
		$default->doc_votedown = 'Y';
		$default->cmt_write = 'Y';
		$default->cmt_modify = 'Y';
		$default->cmt_delete = 'N';
		$default->cmt_voteup = 'Y';
		$default->cmt_votedown = 'Y';
		$default->file_download = 'N';


		$config->use = 'Y';
		$config->ip_priority = 'Y';
		$config->member_delete = 'N';
		$config->member_auto = 'N';
		$config->member_sign_up = 'Y';
		$config->use_javascript = 'N';
		$config->ban_day = '1';
		$config->ban_hour = '0';
		$config->ban_notice = '운영진에 의해 [time] 활동이 정지되었습니다.
차단 사유는 "[comment] ([type])"입니다.
해제 일자: [expdate]';
		$config->trash_reason = '차단에 의한 자동이동';

		$config->layout_srl = -1;
		//$config->mlayout_srl = -1;

		$config->default = $default;

		$oModuleController->insertModuleConfig('userblock', $config);


		foreach ($this->triggers as $trigger) {
			$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}

		return new BaseObject();
	}

	function moduleUninstall()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		foreach ($this->triggers as $trigger)
		{
			$oModuleController->deleteTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}

		return new BaseObject();
	}

	function checkUpdate()
	{
		$oModuleModel = getModel('module');
		foreach ($this->triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				return true;
			}
		}

		return false;
	}

	function moduleUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		foreach ($this->triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}

		return new BaseObject();

	}

}

/* End of file userblock.class.php */
/* Location: ./modules/userblock/userblock.class.php */
