<?
AddEventHandler('main', 'OnBeforeEventAdd', 'OnEventSend');
function OnEventSend(&$event, &$lid, &$arFields, &$message_id) {
CModule::IncludeModule('sale');
	if ($event == 'SALE_NEW_ORDER') {
        //define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log3.txt");
		/*
		CModule::IncludeModule('sale');

		$order = \Bitrix\Sale\Order::load($arFields["ORDER_ID"]);
		
		$shipmentCollection = $order->getShipmentCollection();
		
		foreach($shipmentCollection as $shipment){
			if(!$shipment->isSystem()){
				$shipment_nameDirty = $shipment->getDeliveryName(); 
				$pieces = explode("(", $shipment_nameDirty);
				if(trim($pieces[0]) == "eDost") $shipment_name = trim($pieces[1]); else  $shipment_name = trim($pieces[0]);
				$arFields["SHIPMENT_NAME"] =  $shipment_name;
			}
		}
		*/
		$intOrderID = $arFields["ORDER_ID"];
		//$order  = CSaleOrder::GetByID($arFields["ORDER_ID"]);
		//$order  = CSaleOrderPropsValue::GetByID($arFields["ORDER_ID"]);
		//$db_props  = CSaleOrderPropsValue::GetOrderProps($arFields["ORDER_ID"]);
		$dbOrderProps = CSaleOrderPropsValue::GetList(
    		array("SORT" => "ASC"),
        	array("ORDER_ID" => $intOrderID, "CODE"=>array("PHONE"))
    		);
    	while ($arOrderProps = $dbOrderProps->GetNext()){
            $arUserPhone = $arOrderProps;
    	}
		if ($arUserPhone){
		   $arFields["PHONE"] = $arUserPhone["VALUE_ORIG"];
		}



		//$order  = CSaleOrderUserPropsValue::GetByID($arFields["ORDER_ID"]);
		//$arUser = $order["USER_PROPS_ID"]


		/*
	  	$arFields["DELIVERY_ID"] = $order["DELIVERY_ID"];
		$arDeliv = CSaleDelivery::GetByID($order["DELIVERY_ID"]);
		if ($arDeliv){
		   $arFields["DELIVERY_NAME"] = $arDeliv["NAME"];
		}
		*/
		//AddMessage2Log($arFields);
    }
}

/*
AddEventHandler("main", "OnBeforeUserAdd", Array("UserRegisterEmailToLogin", "OnBeforeUserAddHandler"));

AddEventHandler("main", "OnBeforeUserUpdate", Array("UserRegisterEmailToLogin", "OnBeforeUserUpdateHandler"));

class UserRegisterEmailToLogin
{
    // создаем обработчик события "OnBeforeUserAdd"
    function OnBeforeUserAddHandler(&$arFields)
    {
		if($arFields["EMAIL"] && !$arFields["LOGIN"])
        {
           $arFields["LOGIN"] = $arFields["EMAIL"];
        }
		if(!$arFields["PASSWORD"])
        {
           $arFields["PASSWORD"] = randString(7);
		   $arFields["CONFIRM_PASSWORD"] = $arFields["PASSWORD"];
        }
    }
	 // создаем обработчик события "OnBeforeUserUpdate"
    function OnBeforeUserUpdateHandler(&$arFields)
    {
      if($arFields["EMAIL"] && !$arFields["LOGIN"])
        {
           $_REQUEST["LOGIN"] = $arFields["EMAIL"];
	   $arFields["LOGIN"] = $arFields["EMAIL"];

        }	
    }
}
*/

?>
