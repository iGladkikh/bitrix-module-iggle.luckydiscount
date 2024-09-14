<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
if(!$arParams["AJAX"] && method_exists($this, 'createFrame'))
{ 
	$this->createFrame()->begin();
}

if ($arParams["AJAX"])
{
	echo $arResult["COUPON"];
	return false;
}
?>
<div><?=GetMessage("COMPONENT_NAME")?></div>
<div id="SLOT">
	<div id="SLOT1">
		<div class="scroll">
			<div class="item">
				<div class="pic orange"></div>
				<div class="val">10</div>
			</div>
		</div>
	</div>
	<div id="SLOT2">
		<div class="scroll">
			<div class="item">
				<div class="pic lime"></div>
				<div class="val">-15</div>
			</div>
		</div>
	</div>
		<div id="SLOT3">
		<div class="scroll">
			<div class="item">
				<div class="pic strawberries"></div>
				<div class="val">5</div>
			</div>
		</div>
	</div>
</div>
<div id="RESULT">
	<div class="game_result"><?=GetMessage("WIN")?> <span id="discount_val"><?=GetMessage("WIN_START")?></span></div>
	<div class="game_try"><?=GetMessage("TRYES")?> <span id="try"><?=$arResult["TRYES"]?></span></div>
	<div id="record" class="game_record hide"><?=GetMessage("YOURS")?> <span id="max_discount_info" title="<?=GetMessage("DO_NOT_WORRY")?>"><?=GetMessage("DISCOUNT")?></span> &mdash; <b><span id="max_val"><?=$arResult["DISCOUNT_VALUE"]?></span><?=$arResult["DISCOUNT_SIMBOL"]?></b> <span id="get_discount_link" class="hide"><?=GetMessage("GET_COUPON")?></span></div>
	<div id="coupon" class="hide"><?=GetMessage("COUPON")?> <span id="coupon_val"><?=$arResult["COUPON"]?></span></div>
</div>
<div id="NAV">
	<button><?=GetMessage("BUTTON_START")?></button>
</div>


<script type="text/javascript">

function getRandomInt(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getRandomDiscount(min, max, step) {
	if(+step <= 0)
		var step = 1;
	return Math.floor((Math.random() * (max - min + 1) + min)/step)*step;
}

function setStartDiscount(discount) {
	var minDiscount = "<?=$arParams["MIN_DISCOUNT_VALUE"]?>";
	var maxDiscount = "<?=$arParams["MAX_DISCOUNT_VALUE"]?>";
	var style = ["seven", "banana", "cherry", "lemon", "blueberries", "plum", "watermelon", "orange", "strawberries"];

	var discountChars = [];
	discountChars[0] = getRandomInt(-maxDiscount, maxDiscount);
	discountChars[1] = getRandomInt(-maxDiscount, maxDiscount);
	var summ = discountChars[0] + discountChars[1];
	discountChars[2] =  (summ >= 0) ? discount - summ :  Math.abs(summ - discount);

	$(".scroll").each(function(x, y) {
		$(this).children().children().last().html(discountChars[x]);
		if (discount == maxDiscount)
			$(this).children().children().first().removeClass().addClass("pic seven");
		else
			$(this).children().children().first().removeClass().addClass("pic " + getRandomMess(style));
	});
}

function getRandomMess(arMess) {
	return arMess[Math.floor(Math.random() * arMess.length)];
}

function setRandomMess(discount, minDiscount, maxDiscount, discountSimbol) {

	var messMax = [<?=GetMessage("messMax")?>];
	var messNearMax = [<?=GetMessage("messNearMax")?>];
	var messPreMax = [<?=GetMessage("messPreMax")?>];
	var messPre34 = [<?=GetMessage("messPre34")?>];
	var messPre23 = [<?=GetMessage("messPre23")?>];
	var mess12 = [<?=GetMessage("mess12")?>];
	var messPre12 = [<?=GetMessage("messPre12")?>];
	var messPre13 = [<?=GetMessage("messPre13")?>];
	var messPre14 = [<?=GetMessage("messPre14")?>];
	var messNearMin = [<?=GetMessage("messNearMin")?>];
	var mess0 = [<?=GetMessage("mess0")?>];

	if (discount == maxDiscount) 
			discountMess = discount + discountSimbol + " " + getRandomMess(messMax);
	if (discount == maxDiscount - 1)
		discountMess = discount + discountSimbol + " " + getRandomMess(messNearMax);
	if (discount < maxDiscount-1)
		discountMess = discount + discountSimbol + " " + getRandomMess(messPreMax);
	if (discount < maxDiscount*3/4)
		discountMess = discount + discountSimbol + " " + getRandomMess(messPre34);
	if (discount < maxDiscount*2/3)
		discountMess = discount + discountSimbol + " " + getRandomMess(messPre23);
	if (discount == maxDiscount/2)
		discountMess = discount + discountSimbol + " " + getRandomMess(mess12);
	if (discount < maxDiscount/2)
		discountMess =  discount + discountSimbol + " " + getRandomMess(messPre12);
	if (discount < maxDiscount*1/3)
		discountMess = discount + discountSimbol + " " + getRandomMess(messPre13);
	if (discount < maxDiscount/4)
		discountMess = discount + discountSimbol + " " + getRandomMess(messPre14);
	if (discount == minDiscount + 1)
		discountMess = discount + discountSimbol + " " + getRandomMess(messNearMin);
	if (discount == 0)
		discountMess = getRandomMess(mess0);

	$("#discount_val").html(discountMess);
	$("#NAV button").text("<?=GetMessage("BUTTON_PROGRESS")?>");
}

$("#NAV button").on("click", function() {
	if ($(this).hasClass("submitted") || $("#try").text() == 0)
		return false;
	$(this).addClass("submitted");
	startSlot();
});

$("#get_discount_link").one("click", function() {
	$.get(
		"<?=$arResult["COUNTER_URI"]?>", 
		<?=$arResult["JS_PARAMS"]?>, 
		function(data) {
			$("#coupon_val").text(data);
			$("#coupon").removeClass("hide");
			$("#get_discount_link").remove();
		}
	);
});

function startSlot(){

	var step = +$("#try").text();
	var record = +$("#max_val").text();

	var fruit = ["seven", "banana", "cherry", "lemon", "blueberries", "plum", "watermelon", "orange", "strawberries"];

	var minDiscount = "<?=$arParams["MIN_DISCOUNT_VALUE"]?>";
	var maxDiscount = "<?=$arParams["MAX_DISCOUNT_VALUE"]?>";
	var discountStep = "<?=$arParams["DISCOUNT_STEP"]?>";
	var discountSimbol = "<?=$arResult["DISCOUNT_SIMBOL"]?>";
	var discount = getRandomDiscount(+minDiscount, +maxDiscount, +discountStep);

	if (discount == maxDiscount) 
		step = 1;

	var discountChars = [];
	discountChars[0] = getRandomInt(-maxDiscount, +maxDiscount);
	discountChars[1] = getRandomInt(-maxDiscount, +maxDiscount);
	var summ = discountChars[0] + discountChars[1];
	discountChars[2] =  (summ >= 0) ? discount - summ :  Math.abs(summ - discount);

	$.get("<?=$arResult["COUNTER_URI"]?>?DISCOUNT_VALUE=" + discount);

	$(".scroll").each(function(x, y) {

		for (var i = 0; i <= getRandomInt(2, 200); i++) {
			var val = getRandomInt(-maxDiscount, maxDiscount);
			if(val == 0)
				val++;
			var newOb = $(this).children().eq(0).clone();
			$(newOb).children().first().removeClass().addClass("pic " + getRandomMess(fruit));
			$(newOb).children().last().html(val);
			$(this).prepend(newOb);
		}

		if (discount == maxDiscount)
			$(newOb).children().first().removeClass().addClass("pic seven");
		$(newOb).children().last().html(discountChars[x]);
		$(this).css({"top": -$(this).height() + newOb.height()});
		$(this).animate(
			{top: 0}, 
			1500,  
			function() {
				$(this).children().not(newOb).remove();

				if (x == 2) {
					if (discount != maxDiscount)
						$("#NAV button").removeClass("submitted");

					setRandomMess(discount, minDiscount, maxDiscount, discountSimbol);

					$("#try").text(--step);

					if (discount > record) {
						$("#max_val").text(discount);
						$("#record").removeClass("hide");
					}
					if (step == 0) {
						$("#NAV button").text("<?=GetMessage("BUTTON_FINISH")?>").attr({"disabled": "disabled"});
						$("#get_discount_link").removeClass("hide");
						$("#max_discount_info").removeAttr("id").removeAttr("title");
					}
				}
			}
		);
	});
}

setStartDiscount(<?=$arResult["DISCOUNT_VALUE"]?>);

<?if ($arResult["LAST_DISCOUNT_VALUE"]):?>
	setRandomMess("<?=$arResult["LAST_DISCOUNT_VALUE"]?>", "<?=$arParams["MIN_DISCOUNT_VALUE"]?>", "<?=$arParams["MAX_DISCOUNT_VALUE"]?>", "<?=$arResult["DISCOUNT_SIMBOL"]?>");
<?endif?>
<?if ($arResult["DISCOUNT_VALUE"]):?>
	$("#record").removeClass("hide");
<?endif?>
<?if ($arResult["TRYES"] == 0):?>
	$("#NAV button").text("<?=GetMessage("BUTTON_FINISH")?>").attr({"disabled": "disabled"});
	$("#max_discount_info").removeAttr("id").removeAttr("title");
<?endif?>
<?if (!$arResult["COUPON"] && $arResult["TRYES"] == 0):?>
	$("#get_discount_link").removeClass("hide");
<?elseif ($arResult["COUPON"]):?>
	$("#coupon").removeClass("hide");
<?endif?>
</script>