<?php

class MenuServiceSpecialite
{
    /**
     * Retour menu formate
     */
    public function menuDebut($menu)
    {
        $tableau = $this->menu2($menu);

        return $this->getMenu($tableau);
    }

   
    
    /**
     * Retourne un menu ou une entree precise
     */
    public function menu2($menu,$tableauSpecialite, $plus = false)
    {
        $menus = $tableauSpecialite;

        // Menu inexistant
        if (!isset($menus[$menu])) {
            return array();
        }

        // Retour menu complet
        if ($plus === false) {
            return $menus[$menu];
        }

        // Retour entree specifique
        if (isset($menus[$menu][$plus])) {
            return $menus[$menu][$plus];
        }

        return array();
    }

    /**
     * Generation texte USSD
     */
    public function getMenu($tableau, $page = 1, $taillePage = 4)
    {
        if (empty($tableau)) {
            return "Aucun menu disponible";
        }

        $menu = '';

        // Pagination
        $debut = ($page - 1) * $taillePage;

        $tableauPage = array_slice(
            $tableau,
            $debut,
            $taillePage,
            true
        );

        foreach ($tableauPage as $cle => $valeur) {

            $menu .= $cle . ". " . $valeur['libelle'] . "{CR}";
        }

        // Pagination suivante
        $total = count($tableau);

        if (($debut + $taillePage) < $total) {

            $menu .= "0. Suivant{CR}";
        }

        return trim($menu);
    }

    /**
     * Retour code service selon choix utilisateur
     */
    public function getChoix($tableau, $content)
    {
        // $tableau = $this->menu2($menu);

        if (isset($tableau[$content])) {

            // return trim($tableau[$content]['code_service']);
             return $tableau[$content];
        }

        return null;
    }

    /**
     * Retour URL du service choisi
     */
    public function getUrl($menu, $content)
    {
        $tableau = $this->menu2($menu);

        if (isset($tableau[$content])) {

            return trim($tableau[$content]['url']);
        }

        return null;
    }

    /**
     * Retour keyword
     */
    public function getKeyword($menu, $content)
    {
        $tableau = $this->menu2($menu);

        if (isset($tableau[$content])) {

            return trim($tableau[$content]['keyword']);
        }

        return null;
    }
}