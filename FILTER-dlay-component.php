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
