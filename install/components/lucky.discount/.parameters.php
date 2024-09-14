<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog"))
	return;

$arDiscountType = array("P" => GetMessage("DISCOUNT_VALUE_TYPE_P"), "F" => GetMessage("DISCOUNT_VALUE_TYPE_F"));

$arCurrency = array();
$rsCurrency = CCurrency::GetList(($by="SORT"), ($order="ASC"));
while($arr = $rsCurrency->Fetch()) 
{
	$arCurrency[$arr["CURRENCY"]] = $arr["FULL_NAME"];
}


$arGroups = array();
$rsGroups = CGroup::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"));
while ($arr = $rsGroups->Fetch())
{
	$arGroups[$arr["ID"]] = $arr["NAME"];
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"DISCOUNT_VALUE_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DISCOUNT_VALUE_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arDiscountType,
			"DEFAULT" => "P",
		),
		"MIN_DISCOUNT_VALUE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MIN_DISCOUNT_VALUE"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"MAX_DISCOUNT_VALUE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MAX_DISCOUNT_VALUE"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"DISCOUNT_STEP" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DISCOUNT_STEP"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"CURRENCY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CURRENCY"),
			"TYPE" => "LIST",
			"VALUES" => $arCurrency,
			"DEFAULT" => CCurrency::GetBaseCurrency(),
		),
		"MAX_TRYES" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MAX_TRYES"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"USER_GROUPS" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("USER_GROUPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"SIZE" => "7",
			"VALUES" => $arGroups,
			"DEFAULT" => array(0 => "2"),
		),
	),
);
?>