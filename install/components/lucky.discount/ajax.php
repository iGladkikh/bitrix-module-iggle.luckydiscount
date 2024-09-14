<?
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);
define("SITE_ID", $_REQUEST["SITE_ID"]);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent(
	"iggle:lucky.discount",
	"",
	array(
		"DISCOUNT_VALUE_TYPE" => $_REQUEST["DISCOUNT_VALUE_TYPE"],
		"CURRENCY" => $_REQUEST["CURRENCY"],
		"ONE_TIME_COUPON" => $_REQUEST["ONE_TIME_COUPON"],
		"AJAX" => "Y"
	), 
	false, 
	array("HIDE_ICONS" => "Y")
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
