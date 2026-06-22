<?php

/**
 * Created by PhpStorm.
 * User: YGC
 * Date: 23/01/15
 * Time: 13:55
 */

class EtatLecture
{
    var $id_consultation = 1;
    var $page = 1;
    //contenu de depart
    var $contenu = "";
    var $suivant = false;
    var $present = 0;
    var $context = null;
    var $renew = null;
    //titre de depart
    var $title = null;
    //titre sans caractere blanc
    var $title_white = null;
    //contenu sans caractere blanc
    var $contenu_white = null;
    //titre sans accent
    var $title_ascii = null;
    //contenu sans accent
    var $contenu_ascii = null;
    //titre d'affichage de sortie
    var $title_affiche = null;
    //contenu d'affichage de sortie
    var $contenu_affiche = null;

    var $pourAfficher = null;
    public function __construct($page = 1, $title = "", $contenu = "", $suivant = false, $present = 0, $context = null)
    {
        $this->page = $page;
        $this->contenu = $contenu;
        $this->suivant = $suivant;
        $this->present = $present;
        $this->title = $title;
        $this->context = $context;
        $this->title_white = str_replace("\r", "", $this->title);
        $this->title_white = str_replace("\n", "{CR}", $this->title);
        $this->contenu_white = str_replace("\r", "", $this->contenu);
        $this->contenu_white = str_replace("\n", "", $this->contenu);
        $this->title_ascii = str_replace(array('e', 'à', 'è', 'Ê', 'ê', 'ç'), array('e', 'a', 'e', 'E', 'e', 'c'), $this->title_white);
        $this->contenu_ascii = str_replace(array('e', 'à', 'è', 'Ê', 'ê', 'ç'), array('e', 'a', 'e', 'E', 'e', 'c'), $this->contenu_white);

        if (ENV_ACCENT) {
            $this->contenu_affiche = $this->contenu_white;
            $this->title_affiche = $this->title_white;
        } else {
            $this->contenu_affiche = $this->contenu_ascii;
            $this->title_affiche = $this->title_ascii;
        }

        $title = isset($this->title_affiche) ? trim($this->title_affiche) : '';
        $content = isset($this->contenu_affiche) ? trim($this->contenu_affiche) : '';

        if ($title != '' && $content != '') {
            $this->pourAfficher = $title . "{CR}" . $content;
        } elseif ($title != '') {
            $this->pourAfficher = $title;
        } elseif ($content != '') {
            $this->pourAfficher = $content;
        } else {
            $this->pourAfficher = "Information indisponible";
        }
    }
    public function contenuTout()
    {
        return $this->title . "{CR}" . $this->contenu;
    }
    public function replaceWhite()
    {
        if ($this->contenu_white != '' and $this->title_white == '')
            return $this->contenu_white;
        elseif ($this->title_white != '' and $this->contenu_white == '')
            return $this->title_white;
        return $this->title_white . "{CR}" . $this->contenu_white;
    }
    public function contenuAscii()
    {
        if ($this->contenu_ascii != '' and $this->title_ascii == '')
            return $this->contenu_ascii;
        elseif ($this->title_ascii != '' and $this->contenu_ascii == '')
            return $this->title_ascii;
        return $this->title_ascii . "{CR}" . $this->contenu_ascii;
    }
    public function affichage()
    {
        if (ENV_ACCENT) {
            return $this->replaceWhite();
        } else {
            return $this->contenuAscii();
        }
    }
    public function rechargement()
    {
        if ($this->title != '')
            $this->title_white = str_replace("\r", "", $this->title);
        if ($this->title != '')
            $this->title_white = str_replace("\n", "{CR}", $this->title);
        if ($this->contenu != '')
            $this->contenu_white = str_replace("\r", "", $this->contenu);
        if ($this->contenu != '')
            $this->contenu_white = str_replace("\n", "", $this->contenu);
        $this->title_ascii = str_replace(array('e', 'à', 'è', 'Ê', 'ê', 'ç'), array('e', 'a', 'e', 'E', 'e', 'c'), $this->title_white);
        $this->contenu_ascii = str_replace(array('e', 'à', 'è', 'Ê', 'ê', 'ç'), array('e', 'a', 'e', 'E', 'e', 'c'), $this->contenu_white);
        if (ENV_ACCENT) {
            $this->contenu_affiche = $this->contenu_white;
            $this->title_affiche = $this->title_white;
        } else {
            $this->contenu_affiche = $this->contenu_ascii;
            $this->title_affiche = $this->title_ascii;
        }

        $title = isset($this->title_affiche) ? trim($this->title_affiche) : '';
        $content = isset($this->contenu_affiche) ? trim($this->contenu_affiche) : '';

        if ($title != '' && $content != '') {
            $this->pourAfficher = $title . "{CR}" . $content;
        } elseif ($title != '') {
            $this->pourAfficher = $title;
        } elseif ($content != '') {
            $this->pourAfficher = $content;
        } else {
            $this->pourAfficher = "Information indisponible";
        }
    }
}
