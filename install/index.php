<?
IncludeModuleLangFile(__FILE__);

Class iggle_luckydiscount extends CModule
{
	var $MODULE_ID = "iggle.luckydiscount"; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("lucky.discount_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("lucky.discount_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("lucky.discount_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("lucky.discount_PARTNER_URI");
	}

	function InstallDB()
	{
		return true;
	}

	function UnInstallDB()
	{
		return true;
	}

	function DoInstall()
	{
		$this->InstallDB();
		CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/local/modules/iggle.luckydiscount/install/components/", $_SERVER['DOCUMENT_ROOT']."/local/components/iggle/", true, true);
		RegisterModule("iggle.luckydiscount");
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		UnRegisterModule("iggle.luckydiscount");
		DeleteDirFilesEx("/local/components/iggle/lucky.discount");
		unset($_SESSION["LUCKY_DISCOUNT"]);
	}
}
?>
