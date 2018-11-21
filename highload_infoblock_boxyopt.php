<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$hlbl = 1;// - "ID  Highload инфоблока" (я его выношу в параметры компонента обычно).
$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
// get entity
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

//Получение списка:
if (CModule::IncludeModule('highloadblock')) {
    $rsData = $entity_data_class::getList(array(
        'select' => array('ID','UF_NAME','UF_DESCRIPTION','UF_XML_ID'),
        'order' => array('ID' => 'ASC'),
        'limit' => '50',
    ));
    while ($arItem = $rsData->Fetch()) {
        $arItems[$arItem["UF_XML_ID"]] = $arItem;
    }
}


/*echo "<pre style='display: none'>";
print_r($arItems);
echo "</pre>";*/


function getBrandName($elementID){

    CModule::IncludeMOdule('iblock');

    $result=CIBlockElement::GetElementGroups($elementID,false,array("ID","IBLOCK_ID","NAME"));
    while($ar_group = $result->Fetch())
        $arSections[]=array("ID"=>$ar_group["ID"],"IBLOCK_ID"=>$ar_group["IBLOCK_ID"]);
    foreach($arSections as $k=>$section){
        $brandName = isBrand($section["ID"], $section["IBLOCK_ID"]);
        if ($brandName) $res = $brandName;
    }
    return $res;
}

// определяем какой из разделов имеет свойство "Бренд" и получаем его имя
function isBrand($SECTION_ID, $IBLOCK_ID){
    $arFilter=array("ID"=>$SECTION_ID, "IBLOCK_ID"=>$IBLOCK_ID);
    $arSelect=array("ID","NAME","IBLOCK_SECTION_ID","UF_BRAND");
    $res = CIBlockSection::GetList(Array("SORT"=>"ASC"),$arFilter,false,$arSelect,false);
    while($ar_result = $res->GetNext())
    {
        if($ar_result["UF_BRAND"])
            $brand=$ar_result["NAME"];
        else{
            if ($ar_result["IBLOCK_SECTION_ID"]>0) $brand=isBrand($ar_result["IBLOCK_SECTION_ID"], $IBLOCK_ID); else return false;
        }
    }
    if($brand) return $brand; else return false;
}





if ($USER->IsAuthorized()) {
    $USER_ID = CUser::GetID();
    $arGroups = CUser::GetUserGroup($USER_ID);

    foreach($arGroups as $GROUP_ID) :
        if($GROUP_ID == 3) $OPT=1;
        else if ($GROUP_ID == 8)  $GUEST=1;
        else if ($GROUP_ID == 1)  $ADM=1;
    endforeach;

    if(($GUEST==1) || ($OPT==1) || ($ADM == 1)) $arResult["SHOW_BUY"] = "Y";
}

/*

$obCache = new CPHPCache();
$cacheLifetime = 86400; $cacheID = 'mphotos'; $cachePath = '/'.$cacheID;
if( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) )
{
   $vars = $obCache->GetVars();
   $arAllPhotos = $vars['mphotos'];
}
elseif( $obCache->StartDataCache()  )
{
	*/

if (!$arResult['MODULES']['catalog'])
    return;
$arFilterWater = Array(Array("name" => "sharpen", "precision" => 10), Array("name" => "watermark", "position" => "center", "size"=>"real", "file"=>$_SERVER['DOCUMENT_ROOT']."/img/wlogo-s666.png"));


foreach($arResult["ITEMS"] as $ke=>$arItem) {

    $arResult["ITEMS"][$ke]["BRANDNAME"] = getBrandName($arItem["ID"]);


    $arEmptyPreview = false;
    $strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
    if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
    {
        $arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
        if (!empty($arSizes))
        {
            $arEmptyPreview = array(
                'SRC' => $strEmptyPreview,
                'WIDTH' => intval($arSizes[0]),
                'HEIGHT' => intval($arSizes[1])
            );
        }
        unset($arSizes);
    }
    unset($strEmptyPreview);



    $arSelect = Array("ID", "ACTIVE", "IBLOCK_ID", "DETAIL_PICTURE", "PROPERTY_SIZES_CLOTHES", "PROPERTY_COLOR_REF");
    $arFilter = Array("IBLOCK_ID"=>IntVal($arItem["OFFERS"][0]["IBLOCK_ID"]), /*"!DETAIL_PICTURE"=>false, */"PROPERTY_CML2_LINK"=>$arItem["ID"]);
    $res = CIBlockElement::GetList(Array("SORT"=>"ASC", "PROPERTY_COLOR_REF"=>"ASC"), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    while($arPh = $res->GetNext()){
        if($arPh["ACTIVE"] == "Y") $arSizes[$arPh["PROPERTY_SIZES_CLOTHES_VALUE"]] = $arPh["PROPERTY_SIZES_CLOTHES_VALUE"];
        if($arPh["DETAIL_PICTURE"]) {/*$arFileTmpSuperbig = CFile::ResizeImageGet(
												$arPh["DETAIL_PICTURE"],
												array("width" => 1500, "height" => 1500),
												BX_RESIZE_IMAGE_PROPORTIONAL,
												true, $arFilterWaterBig, false, 100
											);*/



            $arFilterImages = Array("IBLOCK_ID"=>IntVal(16), "PROPERTY_PROD_ID"=>$arPh["ID"]);
            $resImages = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilterImages, false, Array("nPageSize"=>50), Array("ID", "NAME", "DETAIL_PICTURE", "PREVIEW_PICTURE"));
            if (intval($resImages->SelectedRowsCount())>0) {

                while($arImages = $resImages->GetNext()){

                    $rsFileImg = CFile::GetByID($arImages["PREVIEW_PICTURE"]);
                    $arFileImg = $rsFileImg->Fetch();
                    $arFileTmp["src"] = "/upload/".$arFileImg["SUBDIR"]."/".$arFileImg["FILE_NAME"];
                    $arFileTmp["width"] = $arFileImg["WIDTH"];
                    $arFileTmp["height"] = $arFileImg["HEIGHT"];

                    $rsFileImg = CFile::GetByID($arImages["DETAIL_PICTURE"]);
                    $arFileImg = $rsFileImg->Fetch();
                    $arFileTmpBig["src"] = "/upload/".$arFileImg["SUBDIR"]."/".$arFileImg["FILE_NAME"];
                    $arFileTmpBig["width"] = $arFileImg["WIDTH"];
                    $arFileTmpBig["height"] = $arFileImg["HEIGHT"];




                }

            }
            else {

                $arFileTmpBig = CFile::ResizeImageGet(
                    $arPh["DETAIL_PICTURE"],
                    array("width" => 440, "height" => 660),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true, $arFilterWater, false, 100
                );
                $arFileTmp = CFile::ResizeImageGet(
                    $arPh["DETAIL_PICTURE"],
                    array("width" => 110, "height" => 165),
                    BX_RESIZE_IMAGE_EXACT,
                    true, $arFilterWater, false, 100
                );
                $elImg = new CIBlockElement;

                $PROP = array();
                $PROP[107] = $arPh["ID"];

                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                    "IBLOCK_ID"      => 16,
                    "NAME"           => "Фото для ".$arPh["ID"],
                    "PROPERTY_VALUES"=> $PROP,
                    "ACTIVE"         => "Y",            // активен
                    "PREVIEW_PICTURE" => CFile::MakeFileArray($arFileTmp["src"]),
                    "DETAIL_PICTURE" => CFile::MakeFileArray($arFileTmpBig["src"])
                    //"DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/image.gif")
                );

                $prod_pict_id = $elImg->Add($arLoadProductArray);
                if(!$prod_pict_id) echo "Error: ".$elImg->LAST_ERROR;

            }


            $arPict = array(
                "OFFER_ID" =>0,
                "SRC" => $arFileTmp["src"],
                "WIDTH" => $arFileTmp["width"],
                "HEIGHT" => $arFileTmp["height"],
                "BIGSRC" => $arFileTmpBig["src"],
                "BIGWIDTH" => $arFileTmpBig["width"],
                "BIGHEIGHT" => $arFileTmpBig["height"],
                /*"SUPERBIGSRC" => $arFileTmpSuperbig["src"],
                "SUPERBIGWIDTH" => $arFileTmpSuperbig["width"],
                "SUPERBIGHEIGHT" => $arFileTmpSuperbig["height"],*/
            );
            $arItem["MORE_PHOTO_OSN"][] = $arPict;
        }

    }
    $arAllPhotos[$ke]["MORE_PHOTO"] = $arItem["MORE_PHOTO_OSN"];
    ksort($arSizes);
    $str_size = '';
    $aa = 0;
    foreach($arSizes as $s) {$aa++; if($aa>1) $str_size.=","; $str_size .=$s;}
    $arResult["ITEMS"][$ke]["SIZES"] = $str_size;

}


/*

   $obCache->EndDataCache(array('mphotos' => $arAllPhotos));
}*/

$arResult["MOREPHOTOSAR"] = $arAllPhotos;

foreach ($arResult["ITEMS"] as &$item){
    if(count($item["OFFERS"]) > 0){
        foreach ($item["OFFERS"] as &$offers){


            if($arItems[$offers["DISPLAY_PROPERTIES"]["COLOR_REF"]["VALUE"]]["UF_XML_ID"] == $offers["DISPLAY_PROPERTIES"]["COLOR_REF"]["VALUE"]){
                $offers["DISPLAY_PROPERTIES"]["COLOR_REF"]["COLOR_HEX"] = $arItems[$offers["DISPLAY_PROPERTIES"]["COLOR_REF"]["VALUE"]]["UF_DESCRIPTION"];
            }


            $arColors[$offers["DISPLAY_PROPERTIES"]["COLOR_REF"]["VALUE"]] =array(
                "DISPLAY_VALUE" => $offers["DISPLAY_PROPERTIES"]["COLOR_REF"]["DISPLAY_VALUE"],
                "COLOR_HEX" => $offers["DISPLAY_PROPERTIES"]["COLOR_REF"]["COLOR_HEX"],
            );
        }
    }
    $item["COLORS_OFFERS"] = $arColors;
}

/*
echo "<pre style='display: none'>";
print_r($arResult);
echo "</pre>";
*/


?>
