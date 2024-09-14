<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog"))
	return;

global $USER;
$arUserGroups = $USER->GetUserGroupArray();
if (!$arParams["AJAX"] && !array_intersect($arParams["USER_GROUPS"], $arUserGroups))
	return;

$arParams["MAX_TRYES"] = intval($arParams["MAX_TRYES"]);
$arParams["MIN_DISCOUNT_VALUE"] = intval($arParams["MIN_DISCOUNT_VALUE"]);
$arParams["MAX_DISCOUNT_VALUE"] = intval($arParams["MAX_DISCOUNT_VALUE"]);
$arParams["DISCOUNT_VALUE_TYPE"] = strip_tags($arParams["DISCOUNT_VALUE_TYPE"]);
$arParams["DISCOUNT_STEP"] = intval($arParams["DISCOUNT_STEP"]) > 1 ? intval($arParams["DISCOUNT_STEP"]) : 1;
$arParams["CURRENCY"] = strip_tags($arParams["CURRENCY"]);
$arParams["ONE_TIME_COUPON"] = "O";
$arParams["LAST_DISCOUNT_VALUE"] = intval($_REQUEST["DISCOUNT_VALUE"]);
$arParams["DISCOUNT_VALUE"] = intval($_SESSION["LUCKY_DISCOUNT"]["DISCOUNT_VALUE"]);
$arParams["GET_COUPON"] = isset($_REQUEST["GET_COUPON"]) ? true : false;

if (
	!$arParams["AJAX"]
	&&
	(
		($_SESSION["LUCKY_DISCOUNT"]["DISCOUNT_VALUE_TYPE"] && $_SESSION["LUCKY_DISCOUNT"]["DISCOUNT_VALUE_TYPE"] != $arParams["DISCOUNT_VALUE_TYPE"])
		||
		($_SESSION["LUCKY_DISCOUNT"]["CURRENCY"] && $_SESSION["LUCKY_DISCOUNT"]["CURRENCY"] != $arParams["CURRENCY"])
	)
)
	unset($_SESSION["LUCKY_DISCOUNT"]);

$_SESSION["LUCKY_DISCOUNT"]["DISCOUNT_VALUE_TYPE"] = $arParams["DISCOUNT_VALUE_TYPE"];
$_SESSION["LUCKY_DISCOUNT"]["CURRENCY"] = $arParams["CURRENCY"];

if ($arParams["MAX_DISCOUNT_VALUE"] < $arParams["MIN_DISCOUNT_VALUE"])
	$arParams["MAX_DISCOUNT_VALUE"] = $arParams["MIN_DISCOUNT_VALUE"];

if ($arParams["LAST_DISCOUNT_VALUE"] > $arParams["DISCOUNT_VALUE"])
	$_SESSION["LUCKY_DISCOUNT"]["DISCOUNT_VALUE"] = $arParams["LAST_DISCOUNT_VALUE"];

if ($arParams["DISCOUNT_VALUE_TYPE"] == "P")
{
	$discount_simbol = "%.";
}
else
{
	$obCache = new CPHPCache();
	$CACHE_TIME = is_set($arParams, "CACHE_TIME") ? intval($arParams["CACHE_TIME"]) : 86400;
	$CACHE_ID = SITE_ID . "|" . $componentName . "|" . $arParams["CURRENCY"];
	$CACHE_PATH = "/" . SITE_ID . CComponentEngine::MakeComponentPath($componentName);
	if ($obCache->InitCache($CACHE_TIME, $CACHE_ID, $CACHE_PATH))
	{
		$discount_simbol = $obCache->GetVars();
	}
	elseif ($obCache->StartDataCache())
	{
		$db_currency_lang = CCurrencyLang::GetByID($arParams["CURRENCY"], LANGUAGE_ID);
		$discount_simbol = str_replace(array("#", "."), "", $db_currency_lang["FORMAT_STRING"]) . ".";
		$obCache->EndDataCache($discount_simbol);
	}
}

if ($arParams["GET_COUPON"] && $arParams["DISCOUNT_VALUE"] > 0)
{

	$arParams["DISCOUNT_NAME"] = GetMessage("COMPONENT_NAME") . " " . $arParams["DISCOUNT_VALUE"] . $discount_simbol;

	$arFilter = array(
		"SITE_ID" => SITE_ID,
		"ACTIVE" => "Y",
		"PRIORITY" => $arParams["DISCOUNT_VALUE"],
		"NAME" => $arParams["DISCOUNT_NAME"],
		"VALUE" => $arParams["DISCOUNT_VALUE"],
		"MAX_DISCOUNT" => $arParams["DISCOUNT_VALUE"],
		"VALUE_TYPE" => $arParams["DISCOUNT_VALUE_TYPE"],
		"CURRENCY" => $arParams["CURRENCY"],
	);

	$dbProductDiscounts = CCatalogDiscount::GetList(
		array(),
		$arFilter,
		false,
		false,
		array("ID")
	);

	if ($arDiscount = $dbProductDiscounts->Fetch())
	{
		$discountId = $arDiscount["ID"];
	}
	else
	{
		$arFields = array(
			"RENEWAL" => "N",
			"LAST_DISCOUNT" => "N",
		);

		$discountId = CCatalogDiscount::Add(array_merge($arFilter, $arFields));
	}

	if ($discountId > 0)
	{
		$COUPON = CatalogGenerateCoupon();

		$arCouponFields = array(
			"DISCOUNT_ID" => $discountId,
			"ACTIVE" => "Y",
			"ONE_TIME" => $arParams["ONE_TIME_COUPON"],
			"COUPON" => $COUPON,
			"DATE_APPLY" => false
		);

		$CID = intval(CCatalogDiscountCoupon::Add($arCouponFields));

		if ($CID > 0)
			$_SESSION["LUCKY_DISCOUNT"]["COUPON"] = $COUPON;
	}
}

if (isset($_REQUEST["DISCOUNT_VALUE"]))
{
	$_SESSION["LUCKY_DISCOUNT"]["TRYES"] = intval($_SESSION["LUCKY_DISCOUNT"]["TRYES"]) + 1;
	$_SESSION["LUCKY_DISCOUNT"]["LAST_DISCOUNT_VALUE"] = $arParams["LAST_DISCOUNT_VALUE"];
}

$arJsFields = array(
	"GET_COUPON" => "Y",
	"SITE_ID" => SITE_ID,
	"DISCOUNT_VALUE_TYPE" => $arParams["DISCOUNT_VALUE_TYPE"],
	"CURRENCY" => $arParams["CURRENCY"],
	"ONE_TIME_COUPON" => $arParams["ONE_TIME_COUPON"],
);

$arResult = array(
	"COUPON" => $_SESSION["LUCKY_DISCOUNT"]["COUPON"],
	"DISCOUNT_VALUE" => intval($_SESSION["LUCKY_DISCOUNT"]["DISCOUNT_VALUE"]),
	"LAST_DISCOUNT_VALUE" => intval($_SESSION["LUCKY_DISCOUNT"]["LAST_DISCOUNT_VALUE"]),
	"DISCOUNT_SIMBOL" => $discount_simbol,
	"TRYES" => $_SESSION["LUCKY_DISCOUNT"]["LAST_DISCOUNT_VALUE"] < $arParams["MAX_DISCOUNT_VALUE"] ? intval($arParams["MAX_TRYES"] - $_SESSION["LUCKY_DISCOUNT"]["TRYES"]) : 0,
	"COUNTER_URI" => $this->__path . "/ajax.php",
	"JS_PARAMS" => CUtil::PhpToJSObject($arJsFields),
);
CJSCore::Init("jquery");
$this->IncludeComponentTemplate();
?>