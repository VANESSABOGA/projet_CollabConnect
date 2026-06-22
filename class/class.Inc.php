<?php

/**
 * Created by PhpStorm.
 * User: bogav
 * Date: 22/03/24
 * Time: 20:55
 */

class Inc
{



    static  function inChaine($chaine, $separateur = "|", $inf = " , ")
    {
        $chaine = explode($separateur, $chaine);
        $res = '';
        for ($i = 0; $i < count($chaine); $i++) {
            $res .= "" . $chaine[$i] . $inf;
        }
        return substr($res, 0, strlen($res) - 3);
    }

    static  function getformatTablo($separateur, $a_separer)
    {
        $tablo = array();
        $i = 0;
        $tablo1 = explode($separateur, $a_separer);
        $taille = (count($tablo1) - 1);
        while ($i <= $taille) {
            $tablo[] = $tablo1[$i];
            $i++;
        }
        return $tablo;
    }

    static  function addKeyArray($a_separer, $tablo, $separateur = "|")
    {
        $i = 0;
        $tablo1 = explode($separateur, $a_separer);
        $taille = (count($tablo1) - 1);
        while ($i <= $taille) {
            $tablo[] = $tablo1[$i];
            $i++;
        }
        return $tablo;
    }

    static function  multiStReplace($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        return  $ready;
    }

    static function replaceInKey($key, $content)
    {
        $tablo = array("");
        $mot_plus = Inc::addKeyArray($key, $tablo);
        //print_r($mot_plus);
        $content = Inc::multiStReplace($mot_plus, strtoupper($content));
        return $content;
    }

    static  function getRetourneOneKeyword($chaine = NULL, $separateur = "|")
    {
        if ($chaine == NULL) return null;
        $chaine = explode($separateur = "|", $chaine);
        $res = '';
        return $chaine[0];
        //return substr($res,0,strlen($res)-3);
    }


    static function dateDiff($date1, $date2)
    {
        $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi eviter d'avoir une difference negative
        $retour = array();

        $tmp = $diff;
        $retour['second'] = $tmp % 60;

        $tmp = floor(($tmp - $retour['second']) / 60);
        $retour['minute'] = $tmp % 60;

        $tmp = floor(($tmp - $retour['minute']) / 60);
        $retour['heure'] = $tmp % 24;

        $tmp = floor(($tmp - $retour['heure'])  / 24);
        $retour['jour'] = $tmp;

        return $retour;
    }


    static function diff_date($dateFin, $dateDebut = null)
    {
        //$maintenant =  date('Y-m-d H:i:s');
        if ($dateDebut == null) $dateDebut = date("Y-m-d H:i:s");

        print $dateDebut . "//" . $dateFin . PHP_EOL;
        $date1 = strtotime($dateDebut);
        $date2 = strtotime($dateFin);

        $date_diff = Inc::dateDiff($date1, $date2);
        return $date_diff;
    }


    function listerLesJourEntre2DatesSTK($dateDebut, $dateFin)
    {
        if ($dateDebut == null) $dateDebut = date("Y-m-d H:i:s");

        $signe = '-';
        if (strtotime($dateFin) >= strtotime($dateDebut)) {
            $signe = '+';
        }

        list($date_debut, $heure_debut) = explode(' ', $dateDebut, 2);
        list($date_fin, $heure_fin) = explode(' ', $dateFin, 2);

        list($debut_annee, $debut_mois, $debut_jour) = explode('-', $date_debut, 3);
        list($fin_annee, $fin_mois, $fin_jour) = explode('-', $date_fin, 3);

        $tablo = [];
        $tablo_jour = array();

        $debut_date = mktime(0, 0, 0, $debut_mois, $debut_jour, $debut_annee);
        $fin_date = mktime(0, 0, 0, $fin_mois, $fin_jour, $fin_annee);

        for ($i = $debut_date; $i <= $fin_date; $i += 86400) {
            #echo date("l, F d Y.",$i).PHP_EOL ;
            $jour = date("Y-m-d", $i);
            array_push($tablo_jour, $jour);
        }
        $tablo["total"] = $i;
        $tablo["signe"] = $signe;
        $tablo["jour"] = $tablo_jour;
        return $tablo;
    }

    function listerLesJourEntre2Dates($dateDebut, $dateFin)
    {
        list($date_debut, $heure_debut) = explode(' ', $dateDebut, 2);
        list($date_fin, $heure_fin) = explode(' ', $dateFin, 2);

        $signe = '-';
        if (strtotime($dateFin) >= strtotime($dateDebut)) {
            $signe = '+';
        }

        list($debut_annee, $debut_mois, $debut_jour) = explode('-', $date_debut, 3);
        list($fin_annee, $fin_mois, $fin_jour) = explode('-', $date_fin, 3);

        $tablo = [];
        $tablo_jour = array();

        $debut_date = mktime(0, 0, 0, $debut_mois, $debut_jour, $debut_annee);
        $fin_date = mktime(0, 0, 0, $fin_mois, $fin_jour, $fin_annee);


        for ($i = $debut_date; $i <= $fin_date; $i += 86400) {
            #echo date("l, F d Y.",$i).PHP_EOL ;
            $jour = date("Y-m-d", $i);
            array_push($tablo_jour, $jour);
        }
        $tablo["total"] = $i;
        $tablo["signe"] = $signe;
        $tablo["jour"] = $tablo_jour;
        return $tablo;
    }


    function listerLesSemaines($dateDebut, $dateFin)
    {
        $date_debut = date('Y-m-d H:i:s', strtotime($dateDebut));
        $date_fin = date('Y-m-d H:i:s', strtotime($dateFin));

        $dates = Inc::getDatesBetween($date_debut, $date_fin);
        $compteurSemaine = 0;

        $tablo_lundi = array();
        $tablo_dim = array();

        for ($t = 0; $t <= count($dates) - 1; $t++) {

            $jour =  $dates[$t];
            if ($compteurSemaine ==  0) {
                $firstday = $jour;
                #print "1 er jour de semaine : ".$firstday." 00:00:00".PHP_EOL;
                array_push($tablo_lundi, $firstday);
                $compteurSemaine++;
            } elseif ($compteurSemaine ==  6) {
                $lastday = $jour;
                $compteurSemaine = 0;

                array_push($tablo_dim, $lastday);
                #print "dernier jour de semaine : ".$lastday." 23:59:59".PHP_EOL;
            } else {
                $compteurSemaine++;
            }
        }
        return array("debut" => $tablo_lundi, "fin" => $tablo_dim, "jours" => $dates);
    }

    function getDatesBetween($start, $end)
    {
        if ($start > $end) {
            return false;
        }

        //$sdate    = strtotime("$start +1 day");
        $sdate    = strtotime($start);
        $edate    = strtotime("$end +1 day");

        $dates = array();
        $s = 0;
        for ($i = $sdate; $i < $edate; $i += strtotime('+1 day', 0)) {
            #$dates[] = date('Y-m-d', $i);
            array_push($dates, date('Y-m-d', $i));
            /*
            if($s==0) {
                print "1 er jour de semaine : ".date('Y-m-d', $i).PHP_EOL;
            }
            if($s==6) {
                $s=0;
                print "dernier jour de semaine : ".date('Y-m-d', $i).PHP_EOL;
            }
            */
            $s++;
        }

        return $dates;
    }


    function dateFormat($date, $add, $tyepe = "m", $fff = NULL)
    {
        $date = strtotime($date);
        if ($tyepe == "m") $date = strtotime("+$add day", $date);
        else $date = (strtotime(date($date)) + $add);
        $dateF = date('Y-m-d H:i:s', strtotime($date));
        if ($fff != NULL) $date = date("Y-m-d H:i:s", (strtotime(date($dateF)) - 1));
        return $date;
    }
}
