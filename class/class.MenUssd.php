<?php

class MenUssd
{
    var $db = null;
    var $table_menu = "menus_ussd";
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function retourneGroupement($id_groupement)
    {
        $id_groupement = (int) $id_groupement;

        $find = " SELECT * FROM " . $this->table_menu . " WHERE id_menu='" . $id_groupement . "' LIMIT 1";
        $result = $this->db->selectOBJ($find);
        if ($result != null) {
            $menu = $result[0];
            $menu->libelle = $this->nettoyerChemin($menu->libelle);
            return $menu;
        } else
            return null;
    }

    public function menuGroupe($id_groupe, $page = "1", $parametre = null, $title = null)
    {
        $groupement = $this->retourneGroupement($id_groupe);

        //$title = NULL;
        if ($title == null) {
            if ($id_groupe == 0 and $page == 1) {
                $principal = true;
                $title = TITLE_SERVICE;
            } else {
                if ($groupement)
                    $title = $groupement->libelle;
                if ($id_groupe != 0 and $page == 1)
                    $principal = true;
                else
                    $principal = false;
            }
        } else {
            $principal = true;
            //$title = $title;
        }

        $requete = "select libelle from " . $this->table_menu . " where precedent=$id_groupe and is_active='1' $parametre order by position ASC";
        $res = $this->db->retourneMenuDynamique($requete, $page, "libelle", $principal, 5);

        $res->title = $title;
        $res->context = $groupement;
        if ($id_groupe != 0) $res->contenu .= "{CR}0. Retour";
        // else{
        //     // print_r($res);
        // }
        $res->rechargement();
        return $res;
    }

    function nettoyerChemin($str)
    {
        return preg_replace('#/\s+#', '/', $str);
    }
}
