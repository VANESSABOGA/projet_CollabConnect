<?php

class Fonction extends Request
{

    public $Request;
    public $dbAcces;
    public $fonction;
    var $init;

    var $callerId = null;
    var $statut_factu = 'CHARGING_SUCCESS';

    public $canal;
    public $link;

    public function __construct($request, $canal, $link)
    {
        parent::__construct($request);

        $this->canal = $canal;
        $this->dbAcces = $link;
    }





    public function getLastNext()
    {
        $find = " Select * ,timestampdiff(second,date,'" . $this->maintenant . "') as diff,timestampdiff(second,date_sms,'" . $this->maintenant . "') as diff_sms FROM next_table WHERE numero = '" . $this->soa . "'";

        $result = $this->dbAcces->select($find);
        if ($result != null) {
            //$this->logger->handler(__class__.'.'.__function__, "erreur lors de la verification du statut du numero");
            return new Next($result[0]);
        } else {
            $array['id'] = "";
            $array['next'] = $this->next;
            $array['date'] = "";
            $array['numero'] = $this->soa;
            $array['diff'] = 0;
            return new Next($array);
        }
    }



    //function recuperant l'etat de lecture de l'information***********************
    public function getEtatLecture()
    {
        $next = $this->getLastNext();
        $tableau = explode("_", $next->next);
        $etat = new EtatLecture();
        $etat->page = $tableau[count($tableau) - 1];
        $etat->id_consultation = isset($tableau[count($tableau) - 2]) ? $tableau[count($tableau) - 2] : null;
        $etat->context = isset($tableau[count($tableau) - 3]) ? $tableau[count($tableau) - 3] : null;
        $etat->renew = isset($tableau[count($tableau) - 4]) ? $tableau[count($tableau) - 4] : null;
        $etat->present = isset($tableau[count($tableau) - 5]) ? $tableau[count($tableau) - 5] : null;
        return $etat;
    }


    public function retourneService($code_service, $champ = null, $table_service = "services")
    {
        $code_service = (int) $code_service;
        $find = "*";
        $champ = ($champ == null ? 'code_service' : 'keyword');
        $where = " $champ='$code_service'";
        $sqlQuery = "SELECT " . $find . " FROM " . $table_service . " WHERE " . $where;
        if ($ligne = $this->dbAcces->select($sqlQuery)) {
            return new Service($ligne[0]);
        } else
            return null;
    }




    public function retourneAgent($where = null)
    {
        if ($where == null) {
            $where = " AND telephone='" . $this->telephone . "'";
        }

        $sqlQuery = "SELECT * FROM agents WHERE 1=1 $where";
        $resultat = $this->dbAcces->selectOBJ($sqlQuery);
        if ($resultat != null) {
            return $resultat[0];
        } else
            return null;
    }

    public function retourneIdEffectifR($id, $requete)
    {
        //if ($requete == null) $requete = $stringRequete;
        if (dbAccess::estEntier(NOMBRE_SUIVANT, 1) and $id > NOMBRE_SUIVANT)
            $id--;

        $requete .= " limit 1 offset " . ($id - 1);
        $tab = $this->dbAcces->selectArray($requete);
        if ($tab != null)
            return $tab[0][0];
        return null;
    }


    public function retourneForfait($keyword = null, $champ = null)
    {
        $find = "*";
        if ($champ == null)
            $champ = 'souscription';
        //
        if ($keyword == null)
            $where = " WHERE is_active='1'  LIMIT 1 ";
        else
            $where = " WHERE $champ='$keyword'";
        $sqlQuery = "SELECT " . $find . " FROM forfait " . $where;
        if ($ligne = $this->dbAcces->selectOBJ($sqlQuery)) {
            return $ligne[0];
        } else
            return null;
    }

    public function retourneReservationService($params = null)
    {
        $sqlQuery = "  SELECT *  FROM reservations  WHERE 1=1 $params  ORDER BY reservation_id  LIMIT 1  ";

        if ($resultat = $this->dbAcces->selectOBJ($sqlQuery)) {
            return $resultat;
        } else
            return null;
    }


    public function retourneFacture($params = null)
    {

    //SELECT f.reference  AS libelle 
        //  FROM services s INNER JOIN factures f ON s.service_id = f.id_service 
        $sqlQuery = "  SELECT f.* , s.libelle , s.keyword  FROM factures f INNER JOIN services s ON f.id_service = s.service_id  WHERE 1=1 $params ORDER BY  f.date_facture LIMIT 1  ";
        if ($resultat = $this->dbAcces->selectOBJ($sqlQuery)) {
            return $resultat[0];
        } else
            return null;
    }

    public function listFacture($page = 1, $telephone = NULL)
    {

        if ($telephone == NULL) $telephone = $this->telephone;

        /*
        $stringRequete = "select libelle from " . Config::TBL_SERVICES . " where " . Config::TBL_SERVICES . ".keyword in (
        select subscription.level from subscription where telephone='" . $telephone . "' and subscription.active!='NO' group by level) and " . Config::TBL_SERVICES . ".statut='YES' group by " . Config::TBL_SERVICES . ".keyword order by id_service";
        */
        // echo $stringRequete = "SELECT s.libelle FROM services s INNER JOIN factures f ON s.id_service = f.id_service 
        //  WHERE f.numero_client = '" . $telephone . "'   AND f.statut = 'non_regle'   GROUP BY s.id_service ORDER BY s.id_service;";

        // $stringRequete = "SELECT CONCAT( s.libelle, ' | Montant : ', FORMAT(f.montant, 0), ' FCFA | Ref : ', f.reference ) AS libelle 
        //  FROM services s INNER JOIN factures f ON s.service_id = f.id_service 
        //  WHERE f.numero_client = '" . $telephone . "' AND f.statut = 'non_regle';";

        // $stringRequete = "SELECT CONCAT( s.libelle, '|', REPLACE(FORMAT(f.montant, 0), ',', ' '), ' FCFA| Ref : ', f.reference ) AS libelle 
        //  FROM services s INNER JOIN factures f ON s.service_id = f.id_service 
        //  WHERE f.numero_client = '" . $telephone . "' AND f.statut = 'non_regle'";

        $stringRequete = "SELECT f.reference  AS libelle 
         FROM services s INNER JOIN factures f ON s.service_id = f.id_service 
         WHERE f.numero_client = '" . $telephone . "' AND f.statut = 'non_regle' order by f.facture_id";

        $resultat =  $this->dbAcces->retourneMenuDynamique($stringRequete, $page, "0", 4);

        $resultat->title = "selectionnez la facture svp!";
        // if ($resultat->present > 1)  $resultat->contenu .= "{CR}99. Autres";
        // else 
        $resultat->contenu .= "{CR}{CR}0. Retour";
        $resultat->rechargement();
        return $resultat;
    }



    //function qui envoie les messages aux abonnes :::::::::::::::::::::
    public function sms_envoi($telephone, $message, $type = null, $sender = null, $type2 = "MT")
    {
        $message = str_replace('{CR}', PHP_EOL, $message);
        // $telephone = Config::$COUNTRY_CODE . substr($telephone, Config::$MSISDN_WITHOUT_COUNTRY_CODE_LENGTH);

        $affiche = "__________ debut SMS ________________" . PHP_EOL . "DA=$sender" . PHP_EOL . ".SMS envoye a : " . $telephone . PHP_EOL . "message : " . $message . PHP_EOL . "taille sms = " . strlen($message) . PHP_EOL . " __________ fin SMS __________________";
        print $affiche . PHP_EOL;
        //return true;

        // if ($this->canal != Config::PLATEFORM_AUTO)
        //     $smpp = $this->Request->setting->smscId;
        // else
        //     $smpp = $this->Request->smscid;

        // if ($sender == null)
        //     $sender = $this->Request->setting->shortcodeSMS;
        // #$sender = trim(utf8_decode("INFOS & DETENTE"));	

        // $message = trim(str_replace('{CR}', PHP_EOL, urlencode(utf8_decode($message))));
        // $url = "http://localhost:13013/cgi-bin/sendsms?username=gnt&password=globalnewtech&smsc=$smpp&to=" . $telephone . "&text=" . $message . "&from=" . urlencode($sender);
        // if ($type == Config::TYPE_FLASH)
        //     $url .= "&mclass=0&alt-dcs=1";
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $reply = trim(curl_exec($ch));
        // curl_close($ch);

        // $to_log = "date = " . @date('Y-m-d H:i:s') . " | telephone = " . $telephone . " | message = " . $message . " | sender = $sender | smsci = $smpp |  resultat  ==> " . $reply . " | url call =>" . $url;
        // //@file_put_contents("/var/www/html/MoovKiosqueService/SERVICES_STK/classes/logs/envoi_sms/log_" . @date('Y-m-d') . '.log', $to_log . PHP_EOL, FILE_APPEND);
        // #echo "message : $message\nmessage envoye à $telephone\n";
        // return $reply;
    }

    public function logSendMessage($message, $smpp, $type, $status, $contenuId = null, $plateforme = null, $telephone = null, $date = null)
    {
        if ($telephone == NULL)
            $telephone = $this->Request->soa;
        $plateforme = $this->Request->canal;

        if ($date == null)
            $date = @date("Y-m-d H:i:s");

        $sqlQuery = " INSERT INTO  sendmessage (`date`,`telephone`, `contenuId`, `message`, `smpp`, `plateforme`, `status`, `type`) VALUES (?,?,?,?,?,?,?,?)";
        $insertData2 = array($date, $telephone, $contenuId, $message, $smpp, $plateforme, $status, $type);
        $status = $this->dbAcces->db_executeQuery($sqlQuery, $insertData2);
        return $status;
    }


    public function retourneListeChaine($table, $champ)
    {
        $tableauTri = array();
        $find = $champ;
        $chaine = "";

        $i = 0;
        $sqlQuery = "SELECT " . $champ . " FROM " . $table . " ";
        if ($ligne = $this->dbAcces->selectArray($sqlQuery)) {

            
            while ($i < count($ligne) and count($ligne) > 0) {
                if ($ligne[$i][$champ] != '') {
                    $tri = str_replace(";", "|", $ligne[$i][$champ]);
                    $chaine = $chaine . $tri . "|";
                }
                $i++;
            }
            return substr($chaine, 0, strlen($chaine) - 1);
        } else
            return null;
    }


    public function getNext($type = 0, $next_w = NEXT_WRITING)
    {

        $tableauTri = array("where" => "numero='" . $this->telephone . "'", "limit" => "1", "order" => "id");
        $maintenant = @date("Y-m-d H:i:s");
        $find = "*,timestampdiff(second,date,'$maintenant') as diff,timestampdiff(second,date_sms,'$maintenant') as diff_sms";

        if ($type == 1) {
            //$infoBD=$this->findRecord($find,$tableauTri,"next_table");
            $infoBD = $this->dbAcces->querySelect($find, "next_table", $tableauTri);

            if ($infoBD != null) {
                return 1;
            }
            return 0;
        } elseif ($next_w) {
            //$infoBD=$this->findRecord($find,$tableauTri,"next_table");
            $infoBD = $this->dbAcces->querySelect($find, "next_table", $tableauTri);

            if ($infoBD != null) {
                $ret = new next_table($infoBD[0]);
                
                if (NEXT_WRITING)
                    $this->next = $ret->next;
                return $ret;
            } else {
                $array['id'] = "";
                $array['next'] = $this->next;
                $array['date'] = "";
                $array['numero'] = $this->telephone;
                $array['diff'] = 0;
                $array['next_sms'] = "menu";
                $array['diff_sms'] = 0;
                $n = new next_table($array);
                return $n;
            }
        } else {
            $array['id'] = "";
            $array['next'] = $this->next;
            $array['date'] = "";
            $array['numero'] = $this->telephone;
            $array['diff'] = 0;
            $array['next_sms'] = "menu";
            $array['diff_sms'] = 0;
            $n = new next_table($array);
            return $n;
        }
    }


    public function setNext($next = null, $next_ext = null, $url = null, $sessionId = null, $next_sms = null)
    {
        #$this->LOG(__FUNCTION__, "  Executing Main menu rule");

        $val = $this->getNext(1);

        if (NEXT_WRITING and $next !== null)
            $this->next = $next;
        if ($val != 0) {
            $maintenant = @date("Y-m-d H:i:s");
            $updateData = array();
            $element = "";
            if ($next !== null) {
                array_push($updateData, $next);
                $element .= " next = ? , date=NOW() , ";
            }
            if ($next_sms !== null) {
                array_push($updateData, $next_sms);
                $element .= " next_sms = ? , date_sms=NOW(), ";
            }
            if ($next_ext !== null) {
                array_push($updateData, $next_ext);
                $element .= " next_ext = ?, ";
            }
            if ($url !== null) {
                array_push($updateData, $url);
                $element .= " url = ?, ";
            }

            if ($sessionId !== null) {
                array_push($updateData, $sessionId);
                $element .= " sessionId = ?, ";
            } else {
                array_push($updateData, $this->sessionId);
                $element .= " sessionId = ?, ";
            }

            $element = substr($element, 0, length: -2);

            //debug("hhhhhhhhhhhhhh");
            $clauseWhere = "numero='" . $this->telephone . "' order by id";
            if (isset($updateData)) {
                $sqlQuery = "UPDATE next_table SET  $element WHERE $clauseWhere ";
                $this->dbAcces->db_executeQuery($sqlQuery, $updateData);
            }
            // return $req = $this->queryUpdate("next_table", $updateData, $clauseWhere);
            // return false;
        } else {
            // debug("iiiiiiiiiiiii");
            $maintenant = @date("Y-m-d H:i:s");
            $InsertData = array($this->telephone);
            $element = " numero, date , ";
            $option = " ? , NOW() , ";

            if ($next !== null) {
                array_push($InsertData, $next);
                $element .= " next, ";
                $option .= " ? , ";
            }
            if ($next_sms !== null) {
                array_push($InsertData, $next_sms);
                $element .= " next_sms, ";
                $option .= " ? , ";
            }
            if ($next_ext !== null) {
                array_push($InsertData, $next_ext);
                $element .= " next_ext, ";
                $option .= " ? , ";
            }

            if ($url !== null) {
                array_push($InsertData, $url);
                $element .= " url, ";
                $option .= " ? , ";
            }

            if ($sessionId !== null) {
                array_push($InsertData, $sessionId);
                $element .= " sessionId, ";
                $option .= " ? , ";
            } else {
                array_push($InsertData, $this->sessionId);
                $element .= " sessionId, ";
                $option .= " ? , ";
            }

            $element = substr($element, 0, -2);
            $option = substr($option, 0, -2);

            $sqlQuery = "INSERT INTO next_table ($element) VALUES ($option) ";
            $this->dbAcces->db_executeQuery($sqlQuery, $InsertData);
        }
    }


    public function execURLH($url, $tableau)
    {
        $url_final = Fonction::formatURL($url, $tableau);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_final);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $reply = trim(curl_exec($ch));
        curl_close($ch);
        return $reply;
    }

    public static function formatURL($url, array $tableau)
    {
        foreach ($tableau as $key => $value) {
            $url .= "$key=" . urlencode($value) . "&";
        }
        return substr($url, 0, strlen($url) - 1);
    }

  
    function parseKeyword($content, $listeKeywordService)
    {
        // Nettoyage
        $content = strtolower(trim($content));
        $content = preg_replace('/[^a-z0-9]/', '', $content);

        // Liste → tableau
        $keywords = explode('|', strtolower($listeKeywordService));
        $keywords = array_flip($keywords); // optimisation

        // 1. STOP GLOBAL
        if (in_array($content, ['stop', 'off', 'fin', 'desab', 'desabonne', 'desabonnement'], true)) {
            return [
                'type' => 'STOP_ALL'
            ];
        }

        // 2. STOP + KEYWORD (ex: stopbel)
        if (preg_match('/^stop([a-z]+)/', $content, $match)) {
            $keyword = $match[1];

            if (isset($keywords[$keyword])) {
                return [
                    'type' => 'STOP_ONE',
                    'keyword' => $keyword
                ];
            }
        }

        // 3. KEYWORD + DUREE (ex: bel30, bel15, bel7)
        if (preg_match('/^([a-z]+)(30|15|7)$/', $content, $match)) {
            $keyword = $match[1];
            $duree = $match[2];

            if (isset($keywords[$keyword])) {
                return [
                    'type' => 'SUB',
                    'keyword' => $keyword,
                    'duree' => $duree
                ];
            }
        }

        // 4. KEYWORD SIMPLE (ex: bel)
        if (isset($keywords[$content])) {
            return [
                'type' => 'SUB',
                'keyword' => $content,
                'duree' => null
            ];
        }

        // 5. Invalide
        return null;
    }


    public function ussdHttp($url, $next = null, $numero = null, $content = null, $sessionId = null, $code_service = 192, $type = "SIMPLE")
    {

        if ($numero == null)
            $numero = $this->soa;
        if ($next == null)
            $next = $this->next;
        if ($content == null)
            $content = $this->content;
        if ($sessionId == null)
            $sessionId = $this->sessionId;
        $tablo["SOA"] = INDICATIF . substr($numero, -SIGNIFICATIF);
        $tablo["Content"] = $content;
        $tablo["next"] = $next;
        $tablo["sessionId"] = $sessionId;
        $tablo["canal"] = "USSD";
        $tablo["code_service"] = $code_service;
        $tablo["type"] = $type;
        $chaine = $this->execURLH($url, $tablo);

        $tab = explode("\n", $chaine);
        $i = 0;
        foreach ($tab as $line) {
            $ls = trim(str_replace(array("\r", "\n"), array("", ""), $line));
            if (strlen($ls) <= 0)
                break;
            else
                $i++;
        }

        $n = $i + 1;
        $tabe = array();
        for ($i = $n; $i < count($tab); $i++) {
            $tabe[] = $tab[$i];
        }

        if (isset($tab[$n]))
            $contenu = ltrim(implode("\n", $tabe));
        else
            $contenu = "Probleme de connexion";
        $def = false;
        $fed = false;
        $free_flow = "FB";
        $next = "";
        foreach ($tab as $line) {
            if (preg_match("#FreeFlow#", $line)) {
                $tab2 = explode(":", $line);
                $n2 = count($tab2) - 1;
                if (isset($tab2[$n2]))
                    $free_flow = str_replace(" ", "", trim($tab2[$n2]));
                else
                    $free_flow = "FB";
                $def = true;
            }
            if (preg_match("#next#", $line)) {
                $tab2 = explode(":", $line);
                $n2 = count($tab2) - 1;
                if (isset($tab2[$n2]))
                    $next = str_replace(" ", "", trim($tab2[$n2]));
                else
                    $next = "";
                $fed = true;
            }
            if ($def and $fed)
                break;
        }
        $retour = new next_table();
        $retour->numero = $numero;
        $retour->next_ext = $next;
        $retour->contenu_ext = $contenu;
        $retour->freeFlow_ext = $free_flow;
        return $retour;
    }
}
