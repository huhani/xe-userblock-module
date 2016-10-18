<?php
/*! Copyright (C) 2016 BGM STORAGE. All rights reserved. */
/**
 * @class userblockView
 * @author Huhani (mmia268@gmail.com)
 * @brief Userblock module view class.
 */

class userblockView extends userblock
{
	function init()
	{

		$oUserblockModel = getModel('userblock');
		$config = $oUserblockModel->getConfig();

		// 템플릿에서 쓸 수 있도록 Context::set()
		Context::set('config', $config);
		$this->module_info->layout_srl = $config->layout_srl;
		$this->setTemplatePath($this->module_path.'tpl');

	}

	function dispUserblockMemberRedirect(){

		$url = getUrl('', 'mid', 'userblock');
		Context::set('url', $url);
		header('Location: '.$url);
		$this->setTemplateFile('redirect');
	}

}

/* End of file userblock.view.php */
/* Location: ./modules/userblock/userblock.view.php */
