<?php
class OSSCube_Booster_Adminhtml_BoosterpanelController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Magento Booster"));
	   $this->renderLayout();
    }
	
	public function boostAction()
	{
		$enable_compilation = $_POST['enable_compilation'];
		$enable_cache = $_POST['enable_cache'];
		$merge_css_js_files = $_POST['merge_css_js_files'];
		$enable_full_page_cache = $_POST['enable_full_page_cache'];
		$enable_log_cleaning = $_POST['enable_log_cleaning'];
		$increase_memory_limit = $_POST['increase_memory_limit'];
		$enable_flat_data = $_POST['enable_flat_data'];
		$enable_htaccess = $_POST['enable_htaccess'];
		$cron_index_setting = $_POST['cron_index_setting'];
		$current_time = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
		$data = $this->getData();
		$model = $data->load(1);
		
		if($enable_compilation && $data->getEnableCompilation() != $enable_compilation)
		{
			$this->_getCompiler()->clear();
			$this->enableCompilation();
		}
		elseif(!$enable_compilation && $data->getEnableCompilation() != $enable_compilation)
		{
			
			$this->disableCompilation();
		}
		if($enable_cache && $data->getEnableCache() != $enable_cache)
		{
			$this->enableCache();
		}
		elseif(!$enable_cache && $data->getEnableCache() != $enable_cache)
		{
			$this->disableCache();
		}
		if($merge_css_js_files && $data->getMergeCssJsFiles() != $merge_css_js_files)
		{
			$this->enableMerging();
		}
		elseif(!$merge_css_js_files && $data->getMergeCssJsFiles() != $merge_css_js_files)
		{
			$this->disableMerging();
		}
		
		if($enable_log_cleaning && $data->getEnableLogCleaning() != $enable_log_cleaning)
		{
			$this->cleanLog();
		}
		elseif(!$enable_log_cleaning && $data->getEnableLogCleaning() != $enable_log_cleaning)
		{
			$this->disableLogcleanup();
		}
		
		if($increase_memory_limit && $data->getIncreaseMemoryLimit() != $increase_memory_limit)
		{
			$this->increaseMemoryLimit();
		}
		
		if($enable_flat_data && $data->getEnableFlatData() != $enable_flat_data)
		{
			$this->enableFlatData();
		}
		elseif(!$enable_flat_data && $data->getEnableFlatData() != $enable_flat_data)
		{
			$this->disableFlatData();
		}
		
		if($enable_htaccess && $data->getEnableHtaccess() != $enable_htaccess)
		{
			$this->enableHtaccess();
		}		
		
		if($model)
		{	
			$data = $model;
		}
		
		$data->setEnableCompilation($enable_compilation);
		$data->setEnableCache($enable_cache);
		$data->setMergeCssJsFiles($merge_css_js_files);
		$data->setEnableFullPageCache($enable_full_page_cache);
		$data->setEnableLogCleaning($enable_log_cleaning);
		$data->setIncreaseMemoryLimit($increase_memory_limit);
		$data->setEnableFlatData($enable_flat_data);
		$data->setEnableHtaccess($enable_htaccess);
		$data->setCronIndexSetting($cron_index_setting);
		$data->setProfileRunAt($current_time);
		$data->save();
		
		
		
		
		$this->_redirect('*/*/');
	}
	
	
	public function getData()
	{
		$model = Mage::getModel('booster/booster');
		return $model;
	}
	protected function _getCompiler()
    {
        if ($this->_compiler === null) {
            $this->_compiler = Mage::getModel('compiler/process');
        }
        return $this->_compiler;
    }
	
	public function enableCompilation()
	{
		$this->_getCompiler()->run();
		Mage::getSingleton('adminhtml/session')->addSuccess
		(
            Mage::helper('compiler')->__('The compilation has been enabled.')
        );
	}
	
	public function disableCompilation()
	{
		$this->_getCompiler()->registerIncludePath(false);
		Mage::getSingleton('adminhtml/session')->addSuccess
		(
            Mage::helper('compiler')->__('The compilation has been disabled.')
        );
		return;
	}
	
	public function enableCache()
	{
		$model = Mage::getModel('core/cache');
		$options = $model->canUse();
		foreach($options as $option=>$value) 
		{
			$options[$option] = 1;
		}
		$model->saveOptions($options);
	}
	
	public function disableCache()
	{
		$model = Mage::getModel('core/cache');
		$options = $model->canUse();
		foreach($options as $option=>$value) 
		{
			$options[$option] = 0;
		}
		$model->saveOptions($options);
	}
	public function getConfig()
	{
		$config = new Mage_Core_Model_Config();
		return $config;
	}
	
	public function enableMerging()
	{
		$config = $this->getConfig();
		$config ->saveConfig('dev/js/merge_files', "1", 'default', 0);
		$config ->saveConfig('dev/css/merge_css_files', "1", 'default', 0);
	}
	
	public function disableMerging()
	{
		$config = $this->getConfig();
		$config ->saveConfig('dev/js/merge_files', "0", 'default', 0);
		$config ->saveConfig('dev/css/merge_css_files', "0", 'default', 0);
	}
	
	public function cleanLog()
	{	
		$config = $this->getConfig();
		$config ->saveConfig('system/log/clean_after_day', "1", 'default', 0);
		$config ->saveConfig('system/log/enabled', "1", 'default', 0);
		$config ->saveConfig('system/log/frequency', "D", 'default', 0);
		$log = Mage::getModel('log/log');
		$log->clean();
	}
	
	public function disableLogcleanup()
	{
		$config = $this->getConfig();
		$config ->saveConfig('system/log/enabled', "0", 'default', 0);
	}
	
	public function increaseMemoryLimit()
	{
		$fileLocation = getenv("DOCUMENT_ROOT") . "mywork/php.ini";
		$file = fopen($fileLocation,"w");
		$content = "extension=pdo.so\n extension=pdo_sqlite.so \n extension=sqlite.so \n extension=pdo_mysql.so \n memory_limit: 512M";
		fwrite($file,$content);
		fclose($file);
	}
	
	public function enableFlatData()
	{
		$config = $this->getConfig();
		$config ->saveConfig('catalog/frontend/flat_catalog_category', "1", 'default', 0);
		$config ->saveConfig('catalog/frontend/flat_catalog_product', "1", 'default', 0);
	}
	
	public function disableFlatData()
	{
		$config = $this->getConfig();
		$config ->saveConfig('catalog/frontend/flat_catalog_category', "0", 'default', 0);
		$config ->saveConfig('catalog/frontend/flat_catalog_product', "0", 'default', 0);
	}
	
	public function enableHtaccess()
	{
		$root = getcwd();
		rename($root."/.htaccess.sample", $root."/.htaccess");
	}
	
	public function disableHtaccess()
	{
		$root = getcwd();
		rename($root."/.htaccess", $root.".htaccess.sample");
	}
}
