<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
session_start();
CModule::IncludeModule('iblock');
CModule::IncludeModule("catalog");
$IBLOCK_ID_MAIN = 3;
$IBLOCK_ID_TIME = 23;
$todayDay = 20;//date("d");
$todayMounth = 10;//date("m");
$todayYear = date("Y");

if(!(array_key_exists("CUR_STEP", $_GET))){
    $cur_step = 0;
}else{
    $cur_step = $_GET['CUR_STEP'];
}

//если первый шаг то парсим xml
if($cur_step == 0) {
    //забираем файл
    $text = file_get_contents('ftp://mteplo:Y4i7O3e1@home.m-teploset.ru/m-teploset.ru/www/docs/OAO/xml/ARGO_F1_' . $todayYear . '.' . $todayMounth . '.' . $todayDay . '.00.00.00_1.xml');
    file_put_contents("counters.xml", $text);
    //запускаем разбор
    if (!$xml = simplexml_load_file('counters.xml')) {
        die('Ошибка!!! Не удалось получить XML файл');
    }
    if (!$arData = parceXml($xml)) {
        die('Разбор XML не произошел');
    }
    $_SESSION["NEW_EL"] = 0;
    $_SESSION["UPDATE_EL"] = 0;
    $_SESSION["AR_DATA"] = $arData;
    unset($xml);
}

$arData = $_SESSION["AR_DATA"];

//Записсываем в базу элементы удаляя их из массива
$lenghtData = importAllElements($arData, $IBLOCK_ID_MAIN, $IBLOCK_ID_TIME);

$page = $APPLICATION->GetCurPage(false);
if($lenghtData > 0){
    $cur_step++;
    header("Location: $page?CUR_STEP=$cur_step");
}

function parceXml($xml){
    $cPoint = 0;
    $cParam = 0;
    $arXML = array();
    $_arData = array();

    foreach ($xml->points->point as $point) {
        $cPoint++;
        $attributes = $point->attributes();
        $xmlId = (string)$attributes['id'];
        $name = mb_convert_encoding($attributes['name'], "UTF-8");
        foreach ($point->params->param as $param){
            $attributes = $param->attributes();
            $type = mb_convert_encoding($attributes['type'], "UTF-8");
            $typeId = (string)$attributes['typeid'];

            $attributes = $param->firstdata->attributes();
            $value = (string)$attributes['value'];
            $time = (string)$attributes['time'];
            list($year, $month, $day, $hour, $minute, $second) = explode('.', $time);

            if (($typeId == "49") || ($typeId == "50") || ($typeId == "51") || ($typeId == "62") || ($typeId == "63") || ($typeId == "81") || ($typeId == "102")) {
                $arXML[$typeId] = array(
                    'type' => $type,
                    'typeId' => $typeId,
                    'value' => $value,
                );
            }
            $cParam++;
        }
        $_arData[$cPoint] = array(
            //'count' => $cPoint,
            'xmlId' => $xmlId,
            //'time' => $time,
            //'time' => $day . "." . $month . "." . $year . " " . $hour . ":" . $minute . ":" . $second,
            'time' => $day . "." . $month . "." . $year,
            'name' => $name,
            'params' => $arXML,
        );
        unset($arXML);
    }
    return $_arData;
}

function getElementIDByXMLID($_iblockId, $_xmlId)
{
    $arFilter = Array('IBLOCK_ID' => $_iblockId, 'XML_ID' => $_xmlId);
    $arSelect = Array('ID', 'XML_ID');
    $db_list = CIBlockElement::GetList(Array(), $arFilter, $arSelect);
    if ($ar_result = $db_list->GetNext()) {
        return $ar_result['ID'];
    } else {
        return false;
    }
}

function getElementIDByActiveFrom($_iblockId, $_xmlId, $_activeFrom)
{
    $arFilter = Array('IBLOCK_ID' => $_iblockId, 'XML_ID' => $_xmlId, 'DATE_ACTIVE_FROM' => $_activeFrom);
    $arSelect = Array('ID', 'XML_ID');
    $db_list = CIBlockElement::GetList(Array(), $arFilter, $arSelect);
    if ($ar_result = $db_list->GetNext()) {
        return $ar_result['ID'];
    } else {
        return false;
    }
}

function importAllElements($_arData, $_iblockIdMain, $_iblockIdTime){
    $oElement = new CIBlockElement();
    $count = 0;
    $newEl = 0;
    $updateEl = 0;
    foreach ($_arData as $key => $data) {
        $arProp = array();
        $isNoData = false;

        $arFields = array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $_iblockIdMain,
            "DATE_ACTIVE_FROM" => $data["time"],
            "IBLOCK_SECTION_ID" => "",
            "NAME" => $data["name"],
            "XML_ID" => $data["xmlId"],
            "PROPERTY_VALUES" => $arProp,
        );

        /*if (($data["params"]["49"]["value"] == "") || ($data["params"]["49"]["value"] == "NODATA")) $isNoData = true;
        if (($data["params"]["49"]["value"] == "") || ($data["params"]["50"]["value"] == "NODATA")) $isNoData = true;
        if (($data["params"]["49"]["value"] == "") || ($data["params"]["51"]["value"] == "NODATA")) $isNoData = true;
        if (($data["params"]["49"]["value"] == "") || ($data["params"]["62"]["value"] == "NODATA")) $isNoData = true;
        if (($data["params"]["49"]["value"] == "") || ($data["params"]["63"]["value"] == "NODATA")) $isNoData = true;
        if (($data["params"]["49"]["value"] == "") || ($data["params"]["81"]["value"] == "NODATA")) $isNoData = true;
        if (($data["params"]["49"]["value"] == "") || ($data["params"]["102"]["value"] == "NODATA")) $isNoData = true;*/

        if((($data["params"]["49"]["value"] == "") || ($data["params"]["49"]["value"] == "NODATA") || ($data["params"]["49"]["value"] == 0)) &&
            (($data["params"]["50"]["value"] == "") || ($data["params"]["50"]["value"] == "NODATA") || ($data["params"]["50"]["value"] == 0))&&
            (($data["params"]["51"]["value"] == "") || ($data["params"]["51"]["value"] == "NODATA") || ($data["params"]["51"]["value"] == 0))&&
            (($data["params"]["62"]["value"] == "") || ($data["params"]["62"]["value"] == "NODATA") || ($data["params"]["62"]["value"] == 0))&&
            (($data["params"]["63"]["value"] == "") || ($data["params"]["63"]["value"] == "NODATA") || ($data["params"]["63"]["value"] == 0))&&
            (($data["params"]["81"]["value"] == "") || ($data["params"]["81"]["value"] == "NODATA") || ($data["params"]["81"]["value"] == 0))&&
            (($data["params"]["102"]["value"] == "") || ($data["params"]["102"]["value"] == "NODATA") || ($data["params"]["102"]["value"] == 0))){
            $isNoData = true;
        }


        if (!$isNoData) {
            $ELEMENT_ID_TO_UPDATE = getElementIDByXMLID($_iblockIdMain, $data["xmlId"]);

            $lastError = false;
            if ($ELEMENT_ID_TO_UPDATE) {
                if ($ELEMENT_ID = $oElement->Update($ELEMENT_ID_TO_UPDATE, $arFields)) {
                    //echo 'UPDATE ID: ' . $ELEMENT_ID;
                    $updateEl++;
                } else {
                    $lastError = true;
                    //echo 'Error UPDATE: ' . $oElement->LAST_ERROR;
                }
            } else {
                if ($ELEMENT_ID = $oElement->Add($arFields)) {
                    //echo 'New ID: ' . $ELEMENT_ID;
                    $newEl++;
                } else {
                    $lastError = true;
                    //echo 'Error ADD: ' . $oElement->LAST_ERROR;
                }
            }

            foreach ($data["params"] as $k => $v) {
                $vol = "VOLUME_" . $v["typeId"];
                $arProp[$vol] = $v["value"];
                CIBlockElement::SetPropertyValues($ELEMENT_ID_TO_UPDATE, $_iblockIdMain, $v["value"], $vol);
            }

            $ELEMENT_ID_EXIST = getElementIDByActiveFrom($_iblockIdTime, $data["xmlId"], $data["time"]);
            if ((!$lastError) && (!$ELEMENT_ID_EXIST)) {
                $arFields["IBLOCK_ID"] = $_iblockIdTime;
                $arFields['DATE_ACTIVE_FROM'] = $data["time"];
                $arProp['MAIN_ELEMENT'] = $ELEMENT_ID_TO_UPDATE;
                $arFields['PROPERTY_VALUES'] = $arProp;
                //print_r($arFields);die();
                $oElement->Add($arFields);
                $lastError = false;
            }
        }

        unset($arProp);
        unset($arFields);

        $count++;
        unset($_arData[$key]);
        if ($count == 500) {
            break;
        }
    }
    $_SESSION["NEW_EL"] += $newEl;
    $_SESSION["UPDATE_EL"] += $updateEl;
    $_SESSION["AR_DATA"] = $_arData;
    return count($_arData);
}

/*echo "<pre>";
print_r($_GET);
print_r($_SESSION);
echo "</pre>";*/

echo "Добавлено " . $_SESSION["NEW_EL"] . " новых элементов.<br>";
echo "Обновлено" . $_SESSION["UPDATE_EL"] . " элементов.<br>";
