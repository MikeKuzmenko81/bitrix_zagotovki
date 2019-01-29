<?
global $arSosedi;

CModule::IncludeModule("iblock");

$arFilterRooms = Array(
    "IBLOCK_ID"=>5,
    "PROPERTY_ROOMS"=>"5",
);
$restype = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilterRooms, Array("ID", "IBLOCK_ID", "PREVIEW_PICTURE", "PROPERTY_ROOMS"));
while($ar_fields = $restype->GetNext())
{
    $arTypes[] = $ar_fields["ID"];
}

/*echo "<pre>";
print_r($arTypes);
echo "</pre>";*/
$hash = explode('?',$_SERVER['REQUEST_URI']);$hash=MD5($hash[0]);
$_SESSION['filter'][$hash]['SECTION']=214;
$_SESSION['filter'][$hash]['ROOMS']=1;
$arSosedi = Array("SECTION_ID"=>214, "INCLUDE_SUBSECTIONS"=>"Y","ACTIVE"=>"Y");?> <section class="sec-lp-flats noslider lp-bg-top">
    <div class="container catalog-section">
        <div class="sec-title title-icon-left lp-icon-1">
            1-комн. квартиры ЖК "Белорусский квартал"
        </div>
        <?$APPLICATION->IncludeComponent(
            "ade:filter2",
            "oneroom",
            Array(
                "FILTER_NAME" => "arSosedi"
            )
        );?>
    </div>
</section>

//////////////////////////////////////////////////////////////

global $arSectionFilterShowroom;
$arSectionFilterShowroom= Array("SECTION_ID"=> $SECTION_CURRENT_ID, "PROPERTY_SHOWROOM"=>"1", "INCLUDE_SUBSECTIONS"=> "Y");
?> <?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "showrooms",
    Array(
            /*
        "89" => ,
		"90" => fals,
            */
		"ACTION_VARIABLE" => "",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"BACKGROUND_IMAGE" => "",
		"BASKET_URL" => "",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"COMPATIBLE_MODE" => "N",
		"COMPONENT_TEMPLATE" => "sosed",
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_COMPARE" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "",
		"ELEMENT_SORT_FIELD2" => "",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "",
		"ENLARGE_PRODUCT" => "STRICT",
		"FILTER_NAME" => "arSectionFilterShowroom",
		"HEADERTEXT" => "Наши шоу-румы",
		"IBLOCK_ID" => "16",
		"IBLOCK_TYPE" => "objects_catalog",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LABEL_PROP" => "",
		"LAZY_LOAD" => "N",
		"LINE_ELEMENT_COUNT" => "4",
		"LOAD_ON_SCROLL" => "N",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "",
		"MESS_BTN_BUY" => "",
		"MESS_BTN_DETAIL" => "",
		"MESS_BTN_SUBSCRIBE" => "",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_TITLE" => "",
		"PAGE_ELEMENT_COUNT" => "4",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => "",
		"PRICE_VAT_INCLUDE" => "N",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
		"PRODUCT_ID_VARIABLE" => "",
		"PRODUCT_PROPERTIES" => "",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_ROW_VARIANTS" => "[]",
		"PROPERTY_CODE" => array(0=>"OTDELKA",1=>"NUMBER",2=>"PRICE",3=>"AVALIABLE",4=>"TYPE",5=>"",),
		"PROPERTY_CODE_MOBILE" => "",
		"RCM_PROD_ID" => "",
		"RCM_TYPE" => "personal",
		"SECTION_CODE" => "",
		"SECTION_ID" => "0",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(0=>"",1=>"",),
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_PRICE_COUNT" => "",
		"SHOW_SLIDER" => "N",
		"TEMPLATE_THEME" => "",
		"USE_COMPARE_LIST" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N"
	)
);?> <br>
    <br>
    <br>
<?/* global $arZHKFilter;
$arZHKFilter = Array("PROPERTY_USER_TAGS"=>Array("48"), //"PROPERTY_ZHK"=>"2953");*/?> 
<section class="sec-home-news sec-lp-news sec-default sec-blog">
    <div class="container">
        <div class="sec-title">
            Наши Новости
        </div>
        <?$APPLICATION->IncludeComponent(
	"a-de:news.list", 
	"news-project", 
	array(
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COMPONENT_TEMPLATE" => "news-project",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "arZHKFilter",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "33",
		"IBLOCK_TYPE" => "news",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "N",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"TAGS_VIBOR" => "48"
	),
	false
);?> <br>
