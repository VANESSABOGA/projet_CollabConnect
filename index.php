<?php

/**
 * Created by PhpStorm.
 * User: bogav
 * Date: 14/04/24
 * Time: 00:24
 */

header('Content-type:text/plain;charset=ISO-8859-1');
include_once 'autoload.php';

$canal = "USSD";
$service = new Central($_REQUEST, $canal);

$next = $service->getNext(0, true);
print_r($next);


#if($next!=null and $next->diff<60 and $next->next!='' and $_REQUEST['next']!="menu" and $next->sessionId==$brvm->sessionId()){

// if (isset($next) && ($next) != null && $next->diff < 60 && $next->next != '' && $next != "menu") {
if (isset($next) && ($next) != null && $next->next != '' && $next != "menu") {
    $next = $next->next;
    // $next = $next ?? '';

    if ($next !== '' && preg_match('/^menu_groupe(_\d+){2}$/i', $next)) {
        $service->getGroupement();
    } elseif ($next !== '' && preg_match('/^menuReservation_(OUI|NON|NO)(_\d+){1}$/i', $next)) {
        $service->getReserverRM();
    } elseif ($next !== '' && preg_match('/^menuDelaiReservation_(OUI|NON|NO)/i', $next)) {
        $service->getDelaiReservation();
    } elseif ($next !== '' && preg_match('/^menuChoixDelaiReservation_/i', $next)) {
        $service->getChoixDelaiReservation();
    } elseif ($next !== '' && preg_match('/^menuModeFacturation_/i', $next)) {
        $service->getChoixModeFacturation();
    } elseif ($next !== '' && preg_match('/^menuReservationAfficheDev_/i', $next)) {
        $service->getChoixReservationAfficheDev();
    } elseif ($next !== '' && preg_match('/^menuReserverElect_/i', $next)) {
        $service->getReserverElectricite();
    } elseif ($next !== '' && preg_match('/^menuSpecialiteElect_/i', $next)) {
        $service->getReserverSpecialiteElectricite();
    } elseif ($next !== '' && preg_match('/^menuConfirSpecialiteElect_/i', $next)) {
        $service->getConfirSpecialiteElect();
    } elseif ($next !== '' && preg_match('/^menuInviteAM_/i', $next)) {
        $service->getConfirmationPaiementAM();
    } elseif ($next !== '' && preg_match('/^genererFacture/i', $next)) {
        $service->getGenererFacture();
    } elseif ($next !== '' && preg_match('/^getGenererFactureService_/i', $next)) {
        $service->getMenuGenererFacture();
    } elseif ($next !== '' && preg_match('/^designationPrestation/i', $next)) {
        $service->getMenuDesignationPrestation();
    } elseif ($next !== '' && preg_match('/^getConfirmationDesignationPrestation_/i', $next)) {
        $service->getMenuConfirmationDesignationPrestation();
    } elseif ($next !== '' && preg_match('/^designationFacturePrestation_/i', $next)) {
        $service->getMenuDesignationFacturePrestation();
    } elseif ($next !== '' && preg_match('/^payerFacture/i', $next)) {
        $service->getMenuInvitPayerFacture();
    } elseif ($next !== '' && preg_match('/^listFacture_/i', $next)) {
        $service->getMenuListFacture();
    } elseif ($next !== '' && preg_match('/^confirmerFacture_/i', $next)) {
        $service->getMenuConfirmerFacture();
    } elseif ($next !== '' && preg_match('/^paiementFacture_/i', $next)) {
        $service->getMenuPaiementFacture();
    } else {
        $service->flowContinueMain();
    }
} else {

    print_r("hhhhhhhhhhhhhhhhhhhh");
    $service->flowContinueMain();
}
