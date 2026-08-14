<?php


class Message
{
    const CONTENU_INDISPONIBLE = "Desole! Le contenu demande est momentanement indisponible. Veuillez reessayer plus tard. La radio Omega FM vous remercie!{CR}0: Retour{CR}00: Accueil";
    public $NISSA;
    public $rapport;
    public $projet;

    var $central = null;
    var $setting = null;

    var $shortcode = null;
    var $shortcodeSMS = null;
    var $projetName = null;


    public function __construct(Central $central)
    {
        $this->central = $central;
        // $this->setting = $this->central->fonction->Request->setting;
        $this->setting = "";
        $this->shortcode = "";
        $this->shortcodeSMS = "";
        $this->projetName = "";
        // $this->shortcode = $this->setting->shortcode;
        // $this->shortcodeSMS = $this->setting->shortcodeSMS;
        // $this->projetName = $this->setting->service_name;
    }

    public static function regeneration(EtatLecture $etat)
    {
        return new EtatLecture($etat->page, $etat->title, $etat->contenu, $etat->suivant, $etat->present);
    }


    public function libelleGetReservation($libelle = "")
    {
        return new EtatLecture(1, "{$libelle}", "1. Reserver{CR}0. Retour{CR}00. Accueil");
    }

    public function libelleGetDelaiReservation(Service $service)
    {

        return new EtatLecture(
            1,
            "{$service->libelle}{CR}Dans combien de temps souhaitez -vous que l'agent passe pour executer la tache ?",
            "1. Dans 2 jours{CR}2. Dans 3 jours{CR}3. Dans 7 jours{CR}0. Retour{CR}00. Accueil"
        );
    }

    public function libelleGetDelaiReservationElectricite(Service $service)
    {
        return new EtatLecture(
            1,
            "{$service->libelle}{CR}Dans combien de temps souhaitez -vous que l'agent passe pour executer la tache ?",
            "1. Aujourd hui (Dans 1H){CR}2. Dans 3 jours{CR}3. Dans 7 jours{CR}0. Retour{CR}00. Accueil"
        );
    }


    public function libelleGetDelaiReservationAfficheDev(Service $service, $montant)
    {
        $lib = "";

        if ($service->keyword == "affiche") {
            $lib = "conception de visuel";
        } elseif ($service->keyword == "informatique") {
            $lib = "developpement";
        }
        return new EtatLecture(
            1,
            "{$service->libelle}{CR}Vous recevrez un lien par SMS pour remplir le formulaire de la demande de $lib. Cout de reservation : {$montant}F.",
            "1. OUI{CR}2. NON{CR}0. Retour{CR}00. Accueil"
        );
    }


    public function libelleChoixForfaitReservationElectricite(Service $service, $montant, $delai)
    {

        $title = "{$service->libelle}{CR}Un agent vous contactera bientot pour la prise en charge.{CR}Cout de reservation : {$montant}F. ";

        // $title = "{$libelle}{CR}Cout de reservation : {$montant}F, un agent passera dans {$delai}jrs a 9h pour la tache.";
        $option = "1. OUI{CR}2. NON{CR}0.Retour";
        return new EtatLecture(1, $title, $option);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function libelleChoixForfaitReservation(Service $service, $montant, $delai)
    {
        if ($service->keyword == "menage") {
            //$libelle = $service->infos;
            $libelle = "Cout du service Menage 5000frs/piece de 9mettre carre.";
        } else
            $libelle = $service->libelle;

        if ($delai == "24")
            $delai = "1";

        $delai = str_replace('J', '', $delai);
        $title = "{$libelle}{CR}Cout de reservation : {$montant}F, un agent passera dans {$delai}jrs a 9h pour excecuter la tache.";
        $option = "1. OUI{CR}2. NON{CR}0.Retour";
        return new EtatLecture(1, $title, $option);
    }





    public function libelleModeFacturation(Service $service, $montant, $delai)
    {
        $title = "{$service->libelle}{CR}Choisir le mode paiement de la reservation";
        $option = "1. Airtel Money{CR}0.Retour";
        return new EtatLecture(1, $title, $option);
    }



    public function libelleInviteAM(Service $service, $montant)
    {
        $title = "{$service->libelle}{CR}Paiement de {$montant}F. par AM, entrer le code PIN pour confirmer.";
        $option = "0.Retour";
        return new EtatLecture(1, $title, $option);
    }
    public function libelleReservationOK(Service $service, $delai, $dateRDV, $heure = null)
    {

        //remplacer J dans $delai
        $delai = str_replace('J', '', $delai);
        //date = delai + date actuelle ouvre
        // $delais = [2, 3, 7];

        // foreach ($delais as $delai) {

        //     $dateRDV = date('Y-m-d', strtotime("+$delai days"));

        //     echo formatDateRDV($dateRDV) . "<br>";
        // }
        // $dateRDV = date('Y-m-d', strtotime("+$delai days"));


        $dateResevation = $this->formatDateRDV($dateRDV, $heure);
        return new EtatLecture(1, $service->description . "Felicitation, votre rendez-vous de {$service->libelle} est pris en compte pour {$dateResevation}, un agent vous contactera bientôt. Merci pour la confiance!", "00. Accueil");
    }

    public function echecOperation(Service $service = NULL)
    {
        if ($service != NULL)
            $libelle = $service->libelle;
        else
            $libelle = $this->projetName;
        return new EtatLecture(1, "ton service {$libelle} est momentanement indisponible. Veuillez ressayer plus tard.", "");
    }

    public static function creditInsuffisant(Service $service)
    {
        return new EtatLecture(1, "Desole ! Votre credit est insuffisant pour effectuer cette operation. Vous devez disposer d'au moins {$service->montant}F", "00. Accueil");
    }

    public static function echecFacturation(Service $service)
    {
        return new EtatLecture(1, "Desole ! une erreur s'est produite durant la facturation. Merci de ressayer ou de contacter le service client", "00. Accueil");
    }

    public function auncunFacture(Service $service = NULL)
    {
        if ($service != NULL) $libelle = $service->libelle;
        else $libelle = "";
        return new EtatLecture(1, "Desole! tu n'as aucune facture au service {$libelle}.", "00. Accueil");
    }

    public static function auncunAbonnementRubrique(Service $service)
    {
        return new EtatLecture(1, "Desole! tu n'as aucune souscription active au service " . $service->description, "00. Accueil");
    }

   
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    public function resultatName($nom1, $nom2)
    {
        $lovename = strtolower(preg_replace("/ /", "", strip_tags(trim($nom1 . $nom2))));

        $alp = count_chars($lovename);

        for ($i = 0; $i <= 255; $i++) {
            if ($alp[$i] != false) {
                $anz = strlen($alp[$i]);

                if ($anz < 2) {
                    $calc[] = $alp[$i];
                } else {
                    for ($a = 0; $a < $anz; $a++) {
                        $calc[] = substr($alp[$i], $a, 1);
                    }
                }
            }
        }
        if (!isset($calc) or count($calc) < 2) {
            $calc = array('4', '3');
        }

        while (($anzletter = count($calc)) > 2) {
            $lettermitte = ceil($anzletter / 2);

            for ($i = 0; $i < $lettermitte; $i++) {
                $sum = array_shift($calc) + array_shift($calc);
                $anz = strlen($sum);

                if ($anz < 2) {
                    $calcmore[] = $sum;
                } else {
                    for ($a = 0; $a < $anz; $a++) {
                        $calcmore[] = substr($sum, $a, 1);
                    }
                }
            }

            $anzc = count($calcmore);

            for ($b = 0; $b < $anzc; $b++) {
                $calc[] = $calcmore[$b];
            }

            array_splice($calcmore, 0);
        }
        if (!isset($calc) or !isset($calc[0]) or !isset($calc[1])) {
            $calc[0] = 5;
            $calc[1] = 3;
        }
        $this->rapport = $calc[0] . $calc[1] . " %";
        return "Votre rapport de compatibilite romantique est de " . $calc[0] . $calc[1] . " % pour $nom1 et $nom2";
    }
    public function resultatAstro($nom1, $nom2)
    {
        return $this->resultatName($nom1, $nom2);
    }




    public static function abonnementProlongement()
    {
        return new EtatLecture(1, "Veuillez selectionner l'offre a ajouter a ton forfait actuel.", "1. Offre Semaine{CR}2. Offre Quinzaine{CR}3. Offre Mois{CR}0. Retour{CR}00. Accueil");
    }


    public function smsWrongMt()
    {
        return "Desole ,  tu as saisi un mot-cle incorrect. Pour vous abonner a TOGOCOM INFOS , compose {$this->shortcode}";
    }

    public function formatDateRDV($date, $heure = null)
    {
        if (empty($date)) {
            return "";
        }

        $jours = [
            "Sunday" => "Dimanche",
            "Monday" => "Lundi",
            "Tuesday" => "Mardi",
            "Wednesday" => "Mercredi",
            "Thursday" => "Jeudi",
            "Friday" => "Vendredi",
            "Saturday" => "Samedi"
        ];

        $mois = [
            "01" => "Jan",
            "02" => "Fev",
            "03" => "Mars",
            "04" => "Avr",
            "05" => "Mai",
            "06" => "Juin",
            "07" => "Juil",
            "08" => "Août",
            "09" => "Sept",
            "10" => "Oct",
            "11" => "Nov",
            "12" => "Dec"
        ];

        $timestamp = strtotime($date);

        $jourNom = $jours[date("l", $timestamp)];
        $jour = date("d", $timestamp);
        $moisNom = $mois[date("m", $timestamp)];
        $annee = date("Y", $timestamp);

        $dateFormatee = "$jourNom $jour $moisNom $annee";

        // Ajouter heure si fournie
        if (!empty($heure)) {

            $heureFormat = date("G\\h", strtotime($heure));

            $dateFormatee .= " a $heureFormat";
        }

        return $dateFormatee;
    }
}
