<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
CModule::IncludeModule('iblock');
CModule::IncludeModule("catalog");

$pathToGoods = $_SERVER['DOCUMENT_ROOT'] . "/import/goods.xml";
$pathToPricies = $_SERVER['DOCUMENT_ROOT'] . "/import/pricetails.xml";
$IBLOCK_ID = 110;

//начинаем работать с файлом "/import/goods.xml"
if (!$xml = simplexml_load_file($pathToGoods)){
    //$path может быть локальный или удаленный (с html://)
    die('Ошибка!!! Не удалось получить XML файл');
}

//берем из XML массив разделов
foreach ($xml->group_list->group as $group) {
    $attributes = $group->attributes();
    // массив атрибутов групп

    $code = (string)mb_convert_encoding(trim($attributes['code']), "CP1251"); //(int)$attributes['code'];
    $name = mb_convert_encoding($attributes['name'], "CP1251");
    $parent = (string)mb_convert_encoding(trim($attributes['parent']), "CP1251");

    $arXML['group'][$code] = array(
        'code' => $code,
        'name' => $name,
        'parent' => $parent,
    );
}

//берем из XML массив элементов без цен
foreach ($xml->product_list->product as $product){
    $attributes = $product->attributes();
    // массив атрибутов товаров

    $code = (string)mb_convert_encoding(trim($attributes['code']), "CP1251");
    $name = mb_convert_encoding($attributes['name'], "CP1251");
    $group = (string)mb_convert_encoding(trim($attributes['group']), "CP1251");
    $article = mb_convert_encoding($attributes['article'], "CP1251");
    $image = (string)$attributes['image'];
    $description = mb_convert_encoding(base64_decode($attributes['description']), "CP1251");
    $descriptionOnHTML = (string)$attributes['descriptionOnHTML'];

    $arXML['product'][$code] = array(
        'code' => $code,
        'name' => $name,
        'group' => $group,
        'article' => $article,
        'image' => $image,
        //'description' => mb_convert_encoding($attributes['description'], "BASE64"),
        'description' => $description,
        'descriptionOnHTML' => $descriptionOnHTML,
    );
}

unset($xml);//Переменная $xml больше не нужна, освободим память

//начинаем работать с файлом /import/pricetails.xml"
if (!$xml = simplexml_load_file($pathToPricies)){
    //$path может быть локальный или удаленный (с html://)
    die('Ошибка!!! Не удалось получить XML файл');
}
//берем из XML массив цен для элементов
foreach ($xml->price_product as $priceProduct){
    $attributes = $priceProduct->attributes();
    // массив атрибутов товаров
    $code = (string)mb_convert_encoding(trim($attributes['code']), "CP1251");
    $price = (float)$attributes['price'];
    $price_ = (float)$attributes['price_'];

    $attributes = $priceProduct->quantity->attributes();
    $instock = (string)$attributes['instock'];

    $attributes = $priceProduct->quantity->orders->attributes();
    $dateorder = (string)$attributes['dateorder'];
    $transit = (string)$attributes['transit'];

    $arXML['priceProduct'][$code] = array(
        'code' => $code,
        'price' => $price,
        'price_' => $price_,
        'instock' => $instock,
        'dateorder' => $dateorder,
        'transit' => $transit,
    );
}
unset($xml);//Переменная $xml больше не нужна, освободим память

//объединяем массивы цен и элементов в новый массив
foreach ($arXML['priceProduct'] as $prod){
    $arXML['merge'][$prod['code']] = array_merge($prod, $arXML['product'][$prod['code']]);
}

unset($arXML['priceProduct']);
unset($arXML['product']);


//построим все разделы в "Импорт из 1С"
foreach ($arXML["group"] as $item)
{
    $arParams = array("replace_space"=>"-","replace_other"=>"-");
    $trans = Cutil::translit($item['name'],"ru",$arParams);
    $trans .= $item['code'];//rand(1111,9999);

    $bs = new CIBlockSection;
    $arFields = Array(
        "IBLOCK_SECTION_ID" => 2492,
        "IBLOCK_ID" => $IBLOCK_ID,
        "EXTERNAL_ID" => $item['code'],
        "CODE" => $trans,
        "NAME" => $item['name'],
    );

    $ID = $bs->Add($arFields);
    $res = ($ID>0);

    if(!$res) {
        //echo $bs->LAST_ERROR;
    }
}

//разложим разделы которые сделали в "Импорт из 1С" по родителям в разделе "Каталог товаров"
$arRootSec = array();
foreach ($arXML["group"] as $item)
{
   if($item["parent"]){
       $PARENT_SECTION_ID = getSectionIDByXMLID($item["parent"]);
   }
       else {
       $PARENT_SECTION_ID = 2246;
       $arRootSec[] = $item['code'];
   }
   $SECTION_ID_TO_UPDATE = getSectionIDByXMLID($item["code"]);
   $arAllSections[$item["code"]] = $SECTION_ID_TO_UPDATE;
    //echo ' раздел ['.$item["code"].']  $SECTION_ID_TO_UPDATE = ' . $SECTION_ID_TO_UPDATE . ' кладем в раздел  ['.$item["parent"].']  $PARENT_SECTION_ID = ' . $PARENT_SECTION_ID . "<br>";

   if($SECTION_ID_TO_UPDATE>0) {
       $bs = new CIBlockSection;
       $arFields = Array(
           "IBLOCK_SECTION_ID" => $PARENT_SECTION_ID,
       );

       $res = $bs->Update($SECTION_ID_TO_UPDATE, $arFields);

       if (!$res) {
           //echo $bs->LAST_ERROR;
       }
   }

}

function getSectionIDByXMLID($code){
    $arFilter = Array('IBLOCK_ID'=>110, 'GLOBAL_ACTIVE'=>'Y', 'XML_ID' => $code);
    $arSelect = Array('ID');
    $db_list = CIBlockSection::GetList(Array(), $arFilter, true, $arSelect);
    if($ar_result = $db_list->GetNext()){
        return $ar_result['ID'];
    }
    else{
        return false;
    }
}

//получаем ID корневых разделов в которых будем обновлять элементы
$arFilter = Array('IBLOCK_ID'=>$IBLOCK_ID, 'GLOBAL_ACTIVE'=>'Y', 'SECTION_ID' => 2246, 'XML_ID' => $arRootSec);
$arSelect = Array('ID','XML_ID');
$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, $arSelect);
while($ar_result = $db_list->GetNext())
{
    $arRootSection[$ar_result['XML_ID']] = $ar_result['ID'];
}

//смотрим какие элементы в корневых разделах уже есть
$arFilter = Array('IBLOCK_ID'=>$IBLOCK_ID, 'GLOBAL_ACTIVE'=>'Y', 'SECTION_ID' => $arAllSections);
$arSelect = Array('ID', 'XML_ID');
$db_list = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ar_result = $db_list->GetNext())
{
    $arElement[$ar_result['ID']] = $ar_result['XML_ID'];
}

/*echo "<pre>";
print_r($arAllSections);
echo "</pre>";*/
/*echo "=================";*/
echo "<pre>";
print_r($arElement);
echo "</pre>";

/*die('Пока ВСЕ!!!');*/

function setPrice($id, $priceType, $priceVal, $currency){
    $PRODUCT_ID = $id;
    $PRICE_TYPE_ID = $priceType;

    $arFields = Array(
        "PRODUCT_ID" => $PRODUCT_ID,
        "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
        "PRICE" => $priceVal,
        "CURRENCY" => $currency
    );

    $res = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID" => $PRODUCT_ID,
            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
        )
    );

    if ($arr = $res->Fetch())
    {
        CPrice::Update($arr["ID"], $arFields);
    }
    else
    {
        CPrice::Add($arFields);
    }
}


$counter = 0;
foreach ($arXML["merge"] as $item)
{
    $arParams = array("replace_space"=>"-","replace_other"=>"-");
    $trans = Cutil::translit($item['name'],"ru",$arParams);
    $trans .= $item['code'];//rand(1111,9999);

    $element = new CIBlockElement;
    $arLoadProductArray  = Array(
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => $IBLOCK_ID,
        "EXTERNAL_ID" => $item['code'],
        "CODE" => $trans,
        "NAME" => $item['name'],
        "PROPERTY_VALUES" => array("CML2_ARTICLE" => $item['article']),
        //??"DETAIL_PICTURE" => $item['image'],
        /*"PREVIEW_PICTURE" => CFile::MakeFileArray('http://www.avt-1c.ru/images/inet1c.JPG'),
        "DETAIL_PICTURE" => CFile::MakeFileArray('http://www.avt-1c.ru/images/inet1c.JPG'),*/
        "DETAIL_TEXT" => $item['description'],
        "IBLOCK_SECTION" => $arAllSections[$item["group"]],
    );

    $PRODUCT_ID = array_search($item['code'], $arElement);

    if($PRODUCT_ID)
    {
        if($res = $element->Update($PRODUCT_ID, $arLoadProductArray)) {
            $product_prop = array(
                "ID" => $PRODUCT_ID,
                "VAT_ID" => 1,               // выставляем тип ндс (задается в админке)
                "VAT_INCLUDED" => "Y",         // НДС входит в стоимость
                "QUANTITY" => "10",            // Количество в региональном складе
                "CAN_BUY_ZERO" => "Y",         // Покупа при нуле
                "NEGATIVE_AMOUNT_TRACE" => "Y",   // Покупа при нуле
                //"PURCHASING_PRICE" => "100",   // закупочная цена
                //"PURCHASING_CURRENCY" => "RUB",   // валюта закупочной цены
                "MEASURE" => "5",            // ID единицы измерения
                "PRICE_TYPE" => "S",         // Покупа при нуле
            );
            if (CCatalogProduct::Add($product_prop)) {

                setPrice($PRODUCT_ID,1 , $item['price'], "RUB");
                setPrice($PRODUCT_ID,11 , $item['price_'], "RUB");

                $product_prop_sklad = array(
                    "PRODUCT_ID" => $PRODUCT_ID,
                    "STORE_ID" => 1,         // Номер склада
                    "AMOUNT" => $item['instock'],
                );
                CCatalogStoreProduct::Add($product_prop_sklad);
            }
        }

    }
    else
    {
        if($PRODUCT_ID = $element->Add($arLoadProductArray)) {
            $product_prop = array(
                "ID" => $PRODUCT_ID,
                "VAT_ID" => 1,               // выставляем тип ндс (задается в админке)
                "VAT_INCLUDED" => "Y",         // НДС входит в стоимость
                "QUANTITY" => "10",            // Количество в региональном складе
                "CAN_BUY_ZERO" => "Y",         // Покупа при нуле
                "NEGATIVE_AMOUNT_TRACE" => "Y",   // Покупа при нуле
                //"PURCHASING_PRICE" => "100",   // закупочная цена
                //"PURCHASING_CURRENCY" => "RUB",   // валюта закупочной цены
                "MEASURE" => "5",            // ID единицы измерения
                "PRICE_TYPE" => "S",         // Покупа при нуле
            );
            if (CCatalogProduct::Add($product_prop)) {

                setPrice($PRODUCT_ID,1 , $item['price'], "RUB");
                setPrice($PRODUCT_ID,11 , $item['price_'], "RUB");

                $product_prop_sklad = array(
                    "PRODUCT_ID" => $PRODUCT_ID,
                    "STORE_ID" => 1,         // Номер склада
                    "AMOUNT" => $item['instock'],
                );
                CCatalogStoreProduct::Add($product_prop_sklad);
            }
            $res = ($PRODUCT_ID > 0);
        }
    }

    if(!$res)
        echo $element->LAST_ERROR;

    $counter++;
    if($counter == 1000) break;
}

echo 'Изменено ' . $counter . 'элементов';


/*echo "<pre>";
print_r($arXML);
echo "</pre>";*/

