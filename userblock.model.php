<?php
/*! Copyright (C) 2016 BGM STORAGE. All rights reserved. */
/**
 * @class userblockdModel
 * @author Huhani (mmia268@gmail.com)
 * @brief Userblock module model class.
 */

class userblockModel extends userblock
{
	function init()
	{
	}

	function getConfig()
	{
		static $config = null;
		if(is_null($config))
		{
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('userblock');
			if(!$config)
			{
				$config = new stdClass;
			}

			unset($config->body);
			unset($config->_filter);
			unset($config->error_return_url);
			unset($config->act);
			unset($config->module);
		}

		return $config;
	}


}

/* End of file userblock.model.php */
/* Location: ./modules/userblock/userblock.model.php */
