<?php

class Central extends MENU
{


    var $init;
    var $text;
    var $newNext;
    var $result;
    var $freeFlow;

    var $mes_abonnements = null;
    var $callers = null;
    var $callerId = null;

    public $menuUssd = null;

    public $link = null;
    public $menu = null;

    public $menuSpecialite = null;
    public $tableauSpecialite = null;
    public $agent = null;


    function __construct($request, $canal)
    {

        $this->link = new dbAccess();
        parent::__construct($request, $canal, $this->link);


        $this->fonction = new Fonction($request, $canal, $this->link);
        $this->menuUssd = new MenUssd($this->link);
        $this->menuSpecialite = new MenuServiceSpecialite();

        $this->agent = $this->fonction->retourneAgent();

        $this->tableauSpecialite = array(
            "electricite" => array(

                1 => array(
                    "libelle" => "Installation",
                    "keyword" => "installation",
                    "code_service" => 1,
                    "url" => ""
                ),

                2 => array(
                    "libelle" => "Reinstallation",
                    "keyword" => "reinstallation",
                    "code_service" => 2,
                    "url" => ""
                ),
                3 => array(
                    "libelle" => "Maintenance",
                    "keyword" => "maintenance",
                    "code_service" => 3,
                    "url" => ""
                )
            )
        );
        // $this->etat = $this->fonction->getAbonnementTelephone();

    }


    public function menuErreurService()
    {
        $pourAfficher = new EtatLecture(1, "desole une erreur est survenue veuillez ressayer", "");
        $this->setResponse($pourAfficher, $pourAfficher, "menu", "FB");
    }

    public function menuFinParcours()
    {
        $pourAfficher = new EtatLecture(1, "Merci d'avoir utilise le service, revenez Bientot", "");
        $this->setResponse($pourAfficher, $pourAfficher, "menu", "FB");
    }


    public function flowMenuPrincipal($id_context = null, $page = 1)
    {

        $pourAfficher = $this->menuUssd->menuGroupe($id_context, $page);
        $this->setResponse($pourAfficher, $pourAfficher, "menu_groupe_" . $id_context . "_" . $page);
    }

    public function flowContinueMain($code_service = null, $id_context = null)
    {

        if ($code_service == NULL) {
             
            $this->flowMenuPrincipal(0, 1);
        } else {

            if ($code_service == "2") {
                $pourAfficher = new EtatLecture(1, "Generer Facture{CR}Saisir le numero du client", "0.Retour");
                $this->setResponse(__FUNCTION__, $pourAfficher, "genererFacture");
            } elseif ($code_service == "8") {

                $this->getMenuAfficherFacture();
            } else {
                $service = $this->retourneService($code_service);
                if ($service != null) {
                    $this->getRedirectionMenuGroupe($service);
                } else {
                    $this->menuFinParcours();
                }
            }
            
        }
    }

    public function getRedirectionMenuGroupe(Service $service)
    {
        //$this->LOG(__FUNCTION__ . "  Executing Main menu rule");
        switch (strtolower($service->keyword)) {
            case "repassage":
                $pourAfficher = $this->messager->libelleGetReservation($service->libelle);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReservation_" . $service->specialite . "_{$service->code_service}");
                break;
            case "menage":
                $pourAfficher = $this->messager->libelleGetReservation($service->infos);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReservation_" . $service->specialite . "_{$service->code_service}");
                break;
            case "affiche":
            case "informatique":
                $pourAfficher = $this->messager->libelleGetDelaiReservationAfficheDev($service, TARIF_RESERVATION);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReservationAfficheDev" . "_{$service->code_service}");
                break;
            case "electricite":
                $pourAfficher = $this->messager->libelleGetDelaiReservationElectricite($service);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReserverElect_" . $service->specialite . "_{$service->code_service}");
                break;
        }
    }

    public function getReserverRM()
    {

        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();

        $code_service = $etal->page;
        $specialite = $etal->id_consultation;
        $service = $this->retourneService($code_service);
        if ($service != null) {
            switch ($this->content) {
                case '0':
                    $this->flowMenuPrincipal($service->precedent, 1);
                    break;
                case '1':
                    $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReservation_" . $service->specialite . "_{$service->code_service}");
                    break;
                default:
                    $this->getRedirectionMenuGroupe($service);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
        exit;
    }

    // public function getReserver()
    // {
    //     //$this->LOG(__FUNCTION__."  Executing Main menu rule");
    //     $etal = $this->getEtatLecture();
    //     print_r($etal);

    //     $page = $etal->page;
    //     $code_service = $etal->id_consultation;
    //     $specialite = $etal->context;

    //     $params = "";
    //     $params2 = "";
    //     $service = $this->retourneService($code_service);
    //     if ($service != null) {

    //         switch ($this->content) {
    //             case '0':
    //                 $this->flowContinueMain();
    //                 break;
    //             // case '0':
    //             //     // $groupement = $this->menuUssd->retourneGroupement($code_service);
    //             //     // print_r($groupement);
    //             //     $pourAfficher = $this->menuUssd->menuGroupe($code_service, $page - 1, $params);
    //             //     print_r($pourAfficher);
    //             //    // $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
    //             //     break;
    //             case '1':
    //                 $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
    //                 $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReserver_" . $service->specialite . "_{$service->code_service}_1");

    //                 break;
    //             default:
    //                 $this->getRedirectionMenuGroupe($service);
    //                 break;
    //         }
    //         // $tableauSpecialite = $this->tableauSpecialite[$service->keyword];
    //         // $retMenu =  $this->menuSpecialite->getMenu($tableauSpecialite);
    //         // $menu = $service->libelle . "{CR}" . $retMenu;
    //         // $pourAfficher = new EtatLecture(1, $service->libelle, $retMenu);
    //         // $this->setResponse($pourAfficher, $pourAfficher, "menuSpecialite_" . $service->keyword . "_{$service->code_service}_1");
    //     } else {
    //         $this->menuErreurService();
    //     }
    // }



    public function getDelaiReservation()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $code_service = $etal->page;
        $specialite = $etal->id_consultation;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->getRedirectionMenuGroupe($service);
                    break;
                case '1':
                    $this->getMenuReserverChoixDelai($service, "J2");
                    break;
                case '2':
                    $this->getMenuReserverChoixDelai($service, "J3");
                    break;
                case '3':
                    $this->getMenuReserverChoixDelai($service, "J7");
                    break;

                default:
                    $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReservation_" . $service->specialite . "_{$service->code_service}");
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }


    public function getMenuReserverChoixDelai(Service $service, $delai)
    {
        $montant = TARIF_RESERVATION;
        $pourAfficher = $this->messager->libelleChoixForfaitReservation($service, $montant, $delai);
        $this->setResponse($pourAfficher, $pourAfficher, "menuChoixDelaiReservation_" . $delai . "_{$service->code_service}");
    }

    public function getMenuModeFacturation(Service $service, $delai, $choix = 0)
    {
        $montant = TARIF_RESERVATION;
        $pourAfficher = $this->messager->libelleModeFacturation($service, $montant, $delai);
        $this->setResponse($pourAfficher, $pourAfficher, "menuModeFacturation_" . $delai . "_{$service->code_service}_{$choix}");
    }



    public function getChoixDelaiReservation()
    {
        $etal = $this->getEtatLecture();
        $code_service = $etal->page;
        $delai = $etal->id_consultation;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReservation_" . $service->specialite . "_{$service->code_service}");
                    break;
                case '1':
                    $this->getMenuModeFacturation($service, $delai);
                    break;
                case '2':
                    $this->menuFinParcours();
                    break;
                default:
                    $this->getMenuReserverChoixDelai($service, $delai);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getChoixModeFacturation()
    {
        $etal = $this->getEtatLecture();
        $delai = $etal->context;
        $code_service = $etal->id_consultation;
        $choix = $etal->page;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    if ($choix == 0)
                        $this->getMenuReserverChoixDelai($service, $delai);
                    else
                        $this->getMenuReserverChoixDelai($service, $delai);
                    break;
                case '1':

                    $pourAfficher = $this->messager->libelleInviteAM($service, TARIF_RESERVATION);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuInviteAM_" . $service->code_service . "_{$delai}_{$choix}");

                    break;
                default:
                    $this->getMenuModeFacturation($service, $delai, $choix);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getChoixReservationAfficheDev()
    {
        $etal = $this->getEtatLecture();
        $code_service = $etal->page;
        $delai = $etal->id_consultation;

        $service = $this->retourneService($code_service);
        if ($service != null) {


            switch ($this->content) {
                case '0':
                    //    $this->getRedirectionMenuGroupe($service);
                    $this->flowMenuPrincipal($service->precedent, 1);
                    break;
                case '1':
                    $this->getMenuModeFacturation($service, 0);
                    break;
                case '2':
                    $this->menuFinParcours();
                    break;
                default:
                    $pourAfficher = $this->messager->libelleGetDelaiReservationAfficheDev($service, TARIF_RESERVATION);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuReservationAfficheDev" . "_{$service->code_service}");

                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getReserverElectricite()
    {
        $etal = $this->getEtatLecture();
        $page = $etal->page;
        $code_service = $etal->page;
        $specialite = $etal->id_consultation;


        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->flowMenuPrincipal($service->precedent, 1);
                    break;
                case '1':
                    $this->AfficheSpecialiteElectricite($service, "H24");
                    break;
                case '2':
                    $this->AfficheSpecialiteElectricite($service, "J3");
                    break;
                case '3':
                    $this->AfficheSpecialiteElectricite($service, "J7");
                    break;

                default:
                    $this->getRedirectionMenuGroupe($service);

                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function AfficheSpecialiteElectricite(Service $service, $delai)
    {
        if ($service->specialite == "OUI") {
            $tableauSpecialite = $this->tableauSpecialite[$service->keyword];
            $retMenu = $this->menuSpecialite->getMenu($tableauSpecialite);
            $pourAfficher = new EtatLecture(1, $service->libelle . "{CR}Faire un choix :", $retMenu);

            $this->setResponse($pourAfficher, $pourAfficher, "menuSpecialiteElect_" . $service->code_service . "_{$delai}");
        } else {
            $this->flowMenuPrincipal($service->precedent, 1);
        }
    }

    public function getReserverSpecialiteElectricite()
    {
        $etal = $this->getEtatLecture();
        $code_service = $etal->id_consultation;
        $delai = $etal->page;


        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->getRedirectionMenuGroupe($service);
                    break;
                default:
                    $content = strtolower($this->content);
                    $this->getRedirectionMenuSpecialiteElectricite($service, $content, $delai);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getRedirectionMenuSpecialiteElectricite(Service $service, $content, $delai)
    {
        $tableauSpecialite = $this->tableauSpecialite[$service->keyword];
        if ($choix = $this->menuSpecialite->getChoix($tableauSpecialite, $content)) {
            $choix = $choix["code_service"];
            $pourAfficher = $this->messager->libelleChoixForfaitReservationElectricite($service, TARIF_RESERVATION, $delai);
            $this->setResponse($pourAfficher, $pourAfficher, "menuConfirSpecialiteElect_" . $service->code_service . "_{$delai}_{$choix}");
        } else {
            $this->AfficheSpecialiteElectricite($service, $delai);
        }
    }

    public function getConfirSpecialiteElect()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $delai = $etal->id_consultation;
        $code_service = $etal->context;
        $choix = $etal->page;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->AfficheSpecialiteElectricite($service, $delai);
                    break;
                case '1':
                    $this->getMenuModeFacturation($service, $delai, $choix);
                    break;
                case '2':
                    $this->menuFinParcours();
                    break;
                default:

                    $tableauSpecialite = $this->tableauSpecialite[$service->keyword]["$choix"];
                    $choix = $tableauSpecialite["code_service"];
                    $pourAfficher = $this->messager->libelleChoixForfaitReservationElectricite($service, TARIF_RESERVATION, $delai);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuConfirSpecialiteElect_" . $service->code_service . "_{$delai}_{$choix}");
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getConfirmationPaiementAM()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $delai = $etal->id_consultation;
        $code_service = $etal->context;
        $choix = $etal->page;


        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->flowContinueMain();
                    break;
                default:

                    if (ctype_digit($this->content) && strlen($this->content) == LONGUEUR_CODE_PAIEMENT) {
                        // print "affichage de la facture" . PHP_EOL;
                        $this->getFacturationService($service, $delai, $choix);
                    } else {
                        $pourAfficher = $this->messager->libelleInviteAM($service, TARIF_RESERVATION);
                        $this->setResponse(__FUNCTION__, $pourAfficher, "menuInviteAM_" . $service->code_service . "_{$delai}_{$choix}");
                    }
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }


    public function getFacturationService(Service $service, $delai, $choix)
    {

        $service->montant = TARIF_RESERVATION;
        $codePin = trim($this->content);
        $autentification = $this->getAuthentification($codePin, $service);
        if ($autentification == true) {
            $ref_reservation = $this->generateReference($service);


            $delai = str_replace('J', '', $delai);
            $delai = str_replace('H', '', $delai);

            $heure = "09:00";

            if ($delai == "24") {
                $hh = 10;
                if ($hh <= 16) {
                    $date_rdv = date('Y-m-d');
                    $heure = date('H:i', strtotime("+1 hours"));
                } else {
                    $delai = "1";
                    $date_rdv = date('Y-m-d', strtotime("+$delai days"));
                }
            } else {
                $date_rdv = date('Y-m-d', strtotime("+$delai days"));
            }


            if ($service->specialite == "OUI") {
                $tableauSpecialite = $this->tableauSpecialite[$service->keyword]["$choix"];
                $keywordSpecialite = $tableauSpecialite["keyword"];
                $codeSpecialite = $tableauSpecialite["code_service"];
            } else {
                $keywordSpecialite = null;
                $codeSpecialite = null;
            }

            $reservation = new Reservation($this->link);
            $data = array(
                'reference' => $ref_reservation,
                'client_nom' => "client n." . $this->telephone,
                'telephone' => $this->telephone,
                'service_id' => $service->service_id,
                'service_libelle' => $service->libelle,
                'montant' => $service->montant,
                'delai' => $delai,
                'choix' => $choix,
                'paiement_statut' => 1,
                'statut' => 1,
                'date_rdv' => $date_rdv,
                'specialite' => $codeSpecialite,
                'description' => $keywordSpecialite,
                'code_pin' => $codePin
            );

            $reservation_id = $reservation->InsertReservation($data);

            $transaction_id = "1";
            $status_paie = '1';
            $raw_response = "SUCCESS";
            $operateur = "AIRTEL";
            $paiement_id = $reservation->insertPaiement($reservation_id, $transaction_id, $service->montant, $status_paie, $raw_response, $operateur);

            $pourAfficher = $this->messager->libelleReservationOK($service, $delai, $date_rdv, $heure);

            $this->setResponse(__FUNCTION__, $pourAfficher, "menu");
        } else {
            $pourAfficher = $this->messager->creditInsuffisant($service);
            $this->setResponse(__FUNCTION__, $pourAfficher, "menu", "FB");
        }
    }

    public function getAuthentification($codePin, Service $service)
    {
        return true;
    }

    public function getMenuInvitGenererFacture()
    {
        $pourAfficher = new EtatLecture(1, "Saisir le numero du client", "0.Retour");
        $this->setResponse(__FUNCTION__, $pourAfficher, "genererFacture");
    }
    public function getGenererFacture()
    {
        switch ($this->content) {
            case '0':
                $this->flowContinueMain();
                break;
            default:

                if (ctype_digit($this->content) && strlen($this->content) >= LONGUEUR_NUMERO) {

                    $numero = trim(substr($this->content, -LONGUEUR_NUMERO));
                    $pourAfficher = $this->menuUssd->menuGroupe(1, 1, null, "Saisir le service");
                    $this->setResponse(__FUNCTION__, $pourAfficher, "getGenererFactureService_" . $numero . "_1_1");
                } else {
                    $this->getMenuInvitGenererFacture();
                }
                break;
        }
    }
    public function getMenuGenererFacture()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();

        $page = $etal->page;
        $id_groupement = $etal->id_consultation;
        $numeroClient = $etal->context;
        $id_context = $numeroClient;
        $libelle = $etal->renew;
        $params = null;
        $params2 = null;

        switch ($this->content) {
            case '0':
                $this->getMenuInvitGenererFacture();
                break;

            default:
                $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page, $params, "Saisir le service");
                if (dbAccess::estEntier($this->content, 1)) {
                    $ideff = $this->retourneIdEffectifR($this->content, "select id_menu from menus_ussd where precedent=$id_groupement and is_active='1' $params order by position ASC");

                    if ($ideff == null)
                        $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                    else {

                        $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params, $params);
                        if ($afficho->present > 1) {

                            $this->setResponse($afficho, $afficho, $libelle . "_" . $id_context . "_{$ideff}_1");
                        } else {

                            $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params2);

                            if ($afficho->present > 1) {
                                $this->setResponse($afficho, $afficho, "debut_" . $id_context . "_{$ideff}_1");
                            } else {

                                $service = $this->retourneService($ideff);
                                if ($service != null) {

                                    $forfait = $this->retourneForfait($service->service_id, "id_service");

                                    $this->getMenuRedirectionGenerationFacture($service, $forfait, $ideff, $numeroClient);
                                    // if ($forfait != null) {

                                    //     if ($forfait->tarif == "devis") {
                                    //         $libelle = $service->libelle . "{CR}" . $forfait->affichage . "{CR}Merci de saisir le montant de la prestation";
                                    //         $pourAfficher = new EtatLecture(1, $libelle, "0.Retour");
                                    //         $this->setResponse(__FUNCTION__, $pourAfficher, "designationFacturePrestation_" . $numeroClient . "_{$ideff}_1");
                                    //     } else {
                                    //         $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                                    //         $pourAfficher = new EtatLecture(1, $libelle, "0.Retour");
                                    //         $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$ideff}_1");
                                    //     }
                                    // } else {
                                    //     $this->menuErreur();
                                    // }
                                } else {
                                    $this->menuErreur();
                                }
                            }
                        }
                    }
                } else {
                    $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                }
                break;
        }
    }



    public function getMenuRedirectionGenerationFacture(Service $service, $forfait, $ideff, $numeroClient)
    {
        if ($forfait != null) {

            if ($forfait->tarif == "devis") {
                $libelle = $service->libelle . "{CR}" . $forfait->affichage . "{CR}Merci de saisir le montant de la prestation";
                $pourAfficher = new EtatLecture(1, $libelle, "0.Retour");
                $this->setResponse(__FUNCTION__, $pourAfficher, "designationFacturePrestation_" . $numeroClient . "_{$ideff}_1");
            } else {
                $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                $pourAfficher = new EtatLecture(1, $libelle, "0.Retour");
                $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$ideff}_1");
            }
        } else {
            $this->menuErreur();
        }
    }

    public function getMenuDesignationPrestation()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();

        $page = $etal->page;
        $code_service = $etal->id_consultation;
        $numeroClient = $etal->context;
        $libelle = $etal->renew;

        $service = $this->retourneService($code_service);
        if ($service != null) {


            $forfait = $this->retourneForfait($service->service_id, "id_service");
            if ($forfait != null) {

                switch ($this->content) {
                    case '0':
                        $pourAfficher = $this->menuUssd->menuGroupe(1, 1, null, "Saisir le service");
                        $this->setResponse($pourAfficher, $pourAfficher, "getGenererFactureService_" . $numeroClient . "_1_1");
                        break;
                    default:

                        if (ctype_digit($this->content)) {

                            if ($service->keyword === "menage") {
                                $nombre = 1;
                                if (DESIGNATION_MENAGE > 0) {
                                    $nombre = max(1, ceil($this->content / DESIGNATION_MENAGE));
                                }
                            } else $nombre = $this->content;

                            $montant = $forfait->tarif * $nombre;
                            $service->montant = $montant;
                            $designation = trim($this->content);

                            // $libelle = "{$service->libelle}{CR}Merci de confirmer la prestation {CR}({$forfait->affichage}){CR}Details: {$designation} {$forfait->souscription_aff}{CR}Total : {$montant} FCFA";
                            // $pourAfficher = new EtatLecture(1, $libelle, "1. OUI{CR}2. NON {CR}0.Retour");

                            // $next = "getConfirmationDesignationPrestation_" . $numeroClient . "_{$service->service_id}_{$service->montant}_{$designation}_" . $nombre;
                            // $this->setResponse(__FUNCTION__, $pourAfficher, $next);

                            $this->getMenuConfirmationGenerationFacture($service, $forfait, $numeroClient, $montant);
                        } else {
                            $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                            $pourAfficher = new EtatLecture(1, $libelle, "0. Retour");
                            $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$service->service_id}_1");
                        }

                        break;
                }
            } else {
                $this->menuErreur();
            }
        } else {
            $this->menuErreur();
        }
    }

    public function getMenuConfirmationDesignationPrestation()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();

        //getConfirmationDesignationPrestation_5555555555_2_50000_50000_devis
        //getConfirmationDesignationPrestation_2222222222_4_15000_15000_5000

        $numeroClient = $etal->present;
        $designation = $etal->id_consultation;
        $montant = $etal->context;
        $code_service = $etal->renew;
        $nombre = $etal->page;

        $service = $this->retourneService($code_service);
        if ($service != null) {
            $forfait = $this->retourneForfait($service->service_id, "id_service");
            if ($forfait != null) {

                //$montant = $forfait->tarif * $nombre;
                $service->montant = $montant;
                switch ($this->content) {
                    case '0':
                        // $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                        // $pourAfficher = new EtatLecture(1, $libelle, "0. Retour");
                        // $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$service->service_id}_1");

                        $this->getMenuRedirectionGenerationFacture($service, $forfait, $code_service, $numeroClient);
                        break;
                    case '1':
                        $this->genererFacturePrestation($service, $forfait, $numeroClient, $designation, $nombre);
                        break;
                    case '2':
                        $this->menuFinParcours();
                        break;
                    default:

                        // $libelle = "{$service->libelle}{CR}Merci de confirmer la prestation {CR}({$forfait->affichage}){CR}Details: {$designation} {$forfait->souscription_aff}{CR}Total : {$montant} FCFA";
                        // $pourAfficher = new EtatLecture(1, $libelle, "1. OUI{CR}2. NON {CR}0.Retour");

                        // $next = "getConfirmationDesignationPrestation_" . $numeroClient . "_{$service->service_id}_{$service->montant}_{$designation}_" . $nombre;
                        // $this->setResponse(__FUNCTION__, $pourAfficher, $next);
                        $this->getMenuConfirmationGenerationFacture($service, $forfait, $numeroClient, $montant);
                        break;
                }
            } else {
                $this->menuErreur();
            }
        } else {
            $this->menuErreur();
        }
    }


    public function genererFacturePrestation(Service $service, $forfait, $numeroClient, $designation, $nombre)
    {
        //inserer facture et notifier le client

        if ($this->agent != null) {
            $agent_id = $this->agent->agent_id;
            $plus = " AND agent_id='" . $agent_id . "' ";
        } else {
            $agent_id = null;
            $plus = "";
        }

        $resultat = $this->retourneReservationService(" AND telephone='" . $numeroClient . "' AND service_id='" . $service->service_id . "' AND statut='1'  " . $plus);

        if ($resultat != null) {
            $reservation = $resultat[0];
            //print_r($reservation);

            $reservation_id = $reservation->reservation_id;
        } else {
            $reservation_id = null;
        }

        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $service->libelle), 0, 2));
        // $reference_facture = 'F' . $service->code_service . "-" . date('HisYmd') . rand(10, 99);
        $reference_facture = strtoupper(
            $prefix .
                substr(md5(
                    $service->libelle .
                        $service->code_service .
                        microtime()
                ), 0, 6)
        );
        // $specialite = $forfait->specialite;
        // $description = $forfait->description;
        // $agent_id = $forfait->agent_id;
        // $reservation_id = $forfait->reservation_id;

        $numeroAgent = $this->telephone;

        $sqlQuery = "INSERT INTO factures (numero_client, numero_agent, id_service, montant, designation, reservation_id,agent_id,reference,date_facture) VALUES (?,?,?,?,?,?,?,?,NOW())";
        $params = array($numeroClient, $this->telephone, $service->service_id, $service->montant, $designation, $reservation_id, $agent_id, $reference_facture);
        $status = $this->dbAcces->db_executeQuery($sqlQuery, $params);

        $pourAfficher = new EtatLecture(1, "Felicitaion ! La facture du client a bien ete genere. Vous receverer les details via SMS", "0. Retour");

        $montantFormate = number_format($service->montant, 0, ',', ' ');
        //$prixUnitaire = number_format($forfait->tarif, 0, ',', ' ');

        $notificationFactureClient =
            date('d/m/Y H:i') .
            "{CR}Facture N. : {$reference_facture}" .
            "{CR}Service : {$service->libelle}" .
            // "{CR}Prix unitaire : {$prixUnitaire} FCFA" .
            "{CR}Total : {$montantFormate} FCFA" .
            "{CR}Agent : {$numeroAgent}";

        $notificationFactureAgent =
            date('d/m/Y H:i') .
            "Facture N. : {$reference_facture}" .
            "{CR}Service : {$service->libelle}" .
            "{CR}Client : {$numeroClient}" .
            "{CR}Total : {$montantFormate} FCFA";



        $this->sms_envoi($numeroClient, $notificationFactureClient, "FACTURE", "FACTURE");
        $this->sms_envoi($this->telephone, $notificationFactureAgent, "FACTURE", "FACTURE");
        $this->setResponse(__FUNCTION__, $pourAfficher, "menu", "FB");
    }


    public function getMenuDesignationFacturePrestation()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $page = $etal->page;
        $code_service = $etal->id_consultation;
        $numeroClient = $etal->context;
        $libelle = $etal->renew;

        $service = $this->retourneService($code_service);
        if ($service != null) {
            $forfait = $this->retourneForfait($service->service_id, "id_service");
            if ($forfait != null) {
                switch ($this->content) {
                    case '0':
                        $pourAfficher = $this->menuUssd->menuGroupe(1, 1, null, "Saisir le service");
                        $this->setResponse(__FUNCTION__, $pourAfficher, "getGenererFactureService_" . $numeroClient . "_1_1");
                        break;
                    default:
                        if (isset($this->content) && !empty($this->content) && ctype_digit($this->content)) {

                            $montant =  trim($this->content);
                            $service->montant = $montant;
                            $this->getMenuConfirmationGenerationFacture($service, $forfait, $numeroClient, $montant);
                        } else {
                            $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                            $pourAfficher = new EtatLecture(1, $libelle, "0. Retour");
                            $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$service->service_id}_1");
                        }
                        break;
                }
            } else {
                $this->menuErreur();
            }
        } else {
            $this->menuErreur();
        }
    }

    function getMenuConfirmationGenerationFacture(Service $service, $forfait, $numeroClient, $montant)
    {

        $libelle = "{$service->libelle}{CR}Merci de confirmer la prestation {CR}Details: {$forfait->affichage}{CR}Total : {$montant} FCFA";
        $pourAfficher = new EtatLecture(1, $libelle, "1. OUI{CR}2. NON {CR}0.Retour");

        $next = "getConfirmationDesignationPrestation_" . $numeroClient . "_{$service->code_service}_{$service->montant}_{$montant}_" . $forfait->tarif;
        $this->setResponse(__FUNCTION__, $pourAfficher, $next);
    }

    public function getGroupement()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        // print_r($etal); exit;
        $page = $etal->page;
        $id_groupement = $etal->id_consultation;
        $id_context = $etal->context;
        $libelle = $etal->renew;
        $params = "";
        $params2 = "";

        switch ($this->content) {
            case '0':
                if ($page == 1 and $id_groupement != 0) {
                    $groupement = $this->menuUssd->retourneGroupement($id_groupement);
                    $pourAfficher = $this->menuUssd->menuGroupe($groupement->precedent, 1, $params);
                    while ($pourAfficher->present <= 1 and $groupement->precedent != 0) {
                        $groupement = $this->menuUssd->retourneGroupement($groupement->id_groupement);
                        $pourAfficher = $this->menuUssd->menuGroupe($groupement->precedent, 1, $params);
                    }
                    if ($pourAfficher->present > 1) {
                        $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$groupement->precedent}_" . $pourAfficher->page);
                    } else {
                        $this->flowContinueMain();
                    }
                } elseif ($page == 1 and $id_context == 0) {
                    $this->flowContinueMain();
                } else {
                    $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page - 1, $params);
                    $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                }
                break;
            case NOMBRE_SUIVANT:
                $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page + 1, $params);
                $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                break;
            default:
                $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page, $params);
                if (dbAccess::estEntier($this->content, 1)) {
                   
                    $ideff = $this->retourneIdEffectifR($this->content, "select id_menu from menus_ussd where precedent=$id_groupement and is_active='1' $params order by position ASC");

                    if ($ideff == null)
                        $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                    else {
                        $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params, $params);
                        if ($afficho->present > 1) {

                            $this->setResponse($afficho, $afficho, $libelle . "_" . $id_context . "_{$ideff}_1");
                        } else {

                            $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params2);

                            if ($afficho->present > 1) {
                                $this->setResponse($afficho, $afficho, "debut_" . $id_context . "_{$ideff}_1");
                            } else {

                                $this->flowContinueMain($ideff, $id_context);
                            }
                        }
                    }
                } else {
                    $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                }
                break;
        }
    }


    public function getMenuAfficherFacture($page = 1)
    {

        $pourAfficher = $this->listFacture($page);

        if ($pourAfficher->present > 0) {

            $this->setResponse(__function__, $pourAfficher, 'listFacture_' . $pourAfficher->page);
        } else {
            $message = $this->messager->auncunFacture();
            $this->setResponse(__function__, $message, 'menu');
        }

        // $pourAfficher = new EtatLecture(1, "Payer une Facture{CR}Entrer le numero de la facture : ", "0.Retour");
        // $this->setResponse(__FUNCTION__, $pourAfficher, "payerFacture");
    }

    public function getMenuListFacture()
    {

        // $etal = $this->getEtatLecture();
        $next =  $this->next;
        $page = $this->getEtatLecture($next)->page;

        switch (strtoupper($this->content)) {
            case NOMBRE_SUIVANT:
                $this->getMenuAfficherFacture($page + 1);
                break;
            case 0:
                if ($page == 1) {
                    $this->flowContinueMain();
                } else {
                    $this->getMenuAfficherFacture($page - 1);
                }
                break;

            default:
                if (ctype_digit(trim($this->content))) {
                    $stringRequete = "SELECT f.reference  AS libelle  FROM services s INNER JOIN factures f ON s.service_id = f.id_service 
                    WHERE f.numero_client = '" . $this->telephone . "' AND f.statut = 'non_regle' order by f.facture_id";

                    $id = $this->fonction->retourneIdEffectifR($this->content, $stringRequete);
                    if ($id != null) {

                        $facture = $this->retourneFacture(" and f.reference = '" . $id . "'");
                        if ($facture != null) {
                            $pourAfficher = new EtatLecture(1, "Facture Num: {$facture->reference}{CR}Service :{$facture->libelle}{CR}Total a payer : {$facture->montant} FCFA{CR}1. Proceder au paiement", "0.Retour");
                            $this->setResponse(__FUNCTION__, $pourAfficher, "confirmerFacture_{$facture->reference}");
                        } else {
                            $pourAfficher = new EtatLecture(1, "Desole, cette facture n'existe pas{CR}Entrer le numero de la facture : ", "0.Retour");
                            $this->setResponse(__FUNCTION__, $pourAfficher, "payerFacture");
                        }
                    } else {
                        $this->getMenuAfficherFacture($page);
                    }
                } else {
                    $this->getMenuAfficherFacture($page);
                }
                break;
        }
    }

    public function getMenuConfirmerFacture()
    {
        $etal = $this->getEtatLecture();
        $reference = $etal->page;
        $facture = $this->retourneFacture(" and f.reference = '" . $reference . "'");
        if ($facture != null) {

            switch ($this->content) {
                case 0:
                    $this->getMenuAfficherFacture(1);
                    break;
                case 1:

                    $montant = number_format($facture->montant, 0, '', '');
                    $pourAfficher = new EtatLecture(1, "Paiement de {$montant}F par AM, entrer le code PIN pour confirmer.", "0.Retour");
                    $this->setResponse(__FUNCTION__, $pourAfficher, "paiementFacture_{$facture->reference}");

                    break;
                default:
                    $pourAfficher = new EtatLecture(1, "Facture Num: {$facture->reference}{CR}Service :{$facture->libelle}{CR}Total a payer : {$facture->montant} FCFA{CR}1. Proceder au paiement", "0.Retour");
                    $this->setResponse(__FUNCTION__, $pourAfficher, "confirmerFacture_{$facture->reference}");
                    break;
            }
        } else {
            $this->menuErreur();
        }
    }


    public function getMenuPaiementFacture()
    {

        $etal = $this->getEtatLecture();
        $reference = $etal->page;
        $facture = $this->retourneFacture(" and f.reference = '" . $reference . "'");
        if ($facture != null) {

            switch ($this->content) {
                case 0:
                    $pourAfficher = new EtatLecture(1, "Facture Num: {$facture->reference}{CR}Service :{$facture->libelle}{CR}Total a payer : {$facture->montant} FCFA{CR}1. Proceder au paiement", "0.Retour");
                    $this->setResponse(__FUNCTION__, $pourAfficher, "confirmerFacture_{$facture->reference}");
                    break;

                default:
                    if (ctype_digit($this->content) && strlen($this->content) == LONGUEUR_CODE_PAIEMENT) {

                        print "facuration facture ";
                    } else {
                        $montant = number_format($facture->montant, 0, '', '');
                        $pourAfficher = new EtatLecture(1, "Paiement de {$montant}F par AM, entrer le code PIN pour confirmer.", "0.Retour");
                        $this->setResponse(__FUNCTION__, $pourAfficher, "paiementFacture_{$facture->reference}");
                    }

                    break;
            }
        } else {
            $this->menuErreur();
        }
    }


    public function getMenuInvitPayerFacture()
    {

        switch ($this->content) {
            case '0':
                $this->flowContinueMain();
                break;
            default:

                // print_r($this->content);
                $content = trim($this->content);
                $result =  $this->retourneFacture(" AND  reference='" . $content . "'  ");
                if ($result != null) {
                    // print_r($result);
                    // $this->setResponse($result, $result, "invit_payer_facture");
                } else {
                    $pourAfficher = new EtatLecture(1, "Desole, cette facture n'existe pas{CR}Entrer le numero de la facture : ", "0.Retour");
                    $this->setResponse(__FUNCTION__, $pourAfficher, "payerFacture");
                }
                break;
        }
    }

    public function getFactureEnAttente() {}
}
