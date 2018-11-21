<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>

<?
$IBLOCK_ID = 6;
$arSelect = Array("ID", "NAME", "PROPERTY_CLEAN_ART", "PROPERTY_CODE_FROM_ED_1C", "PROPERTY_ED_ID");
$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "SECTION_ID" => 33846, "INCLUDE_SUBSECTIONS" => "Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement())
{
    $arFields[] = $ob->GetFields();
    //print_r($arFields);
}

echo '<pre style="display: none">';
print_r($arResult);
echo "</pre>";

$arArticuls = array();
foreach ($arFields as $item){
    $arArticuls[$item["PROPERTY_CLEAN_ART_VALUE"]][$item["ID"]] = array(
                "ED_ID" => $item["PROPERTY_ED_ID_VALUE"],
                "CODE_FROM_ED_1C" => $item["PROPERTY_CODE_FROM_ED_1C_VALUE"],
    );
}
/*
echo "<pre>";
print_r($arArticuls);
echo "</pre>";
*/
$ed_id;
$ed_1c;
$id_for_del;
$id_for_save;
$count = 0;
foreach ($arArticuls as $key => $val){
    foreach ($val as $id => $arts){
        if($arts["ED_ID"] && $arts["CODE_FROM_ED_1C"]){
            $ed_id = $arts["ED_ID"];
            $ed_1c = $arts["CODE_FROM_ED_1C"];
            $id_for_del = $id;
        }else{
            $id_for_save = $id;
        }
    }
    echo "<br>Данные из элемента с ID = " . $id_for_del . ", >> Записываем в элемент с ID = " . $id_for_save . "<br>";
    echo "Данные для записи ED_ID = " . $ed_id . " | CODE_FROM_ED_1C = " . $ed_1c . "<br>";

    CIBlockElement::SetPropertyValues($id_for_save, $IBLOCK_ID, $ed_1c, "CODE_FROM_ED_1C");
    CIBlockElement::SetPropertyValues($id_for_save, $IBLOCK_ID, $ed_id, "ED_ID");
    if(CIBlockElement::Delete($id_for_del)){
        echo "Удален элемент ID = " . $id_for_del . "<br>";
    }

    //if ($count == 0){die();}
    //$count++;
}

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
