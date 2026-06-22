<?php
class Service
{
    var $service_id = null;
    var $libelle = null;
    var $keyword = null;
    var $description = null;
    var $montant = 0;
    var $duree_estimee = 0;
    var $code_service = null;
    var $specialite = null;
    var $infos = null;
    var $actif = 1;

    // Champs calcules / compatibles ancien système
    var $level = null;
    var $url = null;
    var $node = null;
    var $service = null;
    var $projet = null;
    var $id_projet = null;
    var $external=null;
    var $url_central=null;
    var $precedent = null;

    /**
     * Constructeur
     */
    public function __construct(array $infos = null)
    {
        if ($infos != null) {

            foreach ($infos as $key => $value) {

                if (isset($value)) {

                    $this->{$key} = $value;
                }
            }

            // Nettoyage
            $this->libelle = $this->nettoyerTexte($this->libelle);
            $this->description = $this->nettoyerTexte($this->description);

            // Compatibilite anciens systèmes
            $this->service = $this->libelle;
            $this->projet = $this->libelle;
            $this->id_projet = $this->code_service;

            // Gestion keyword multiple
            if (!empty($this->keyword)) {

                $chaine = explode(";", $this->keyword);

                $this->level = trim($chaine[0]);
            }
        }
    }

    /**
     * Nettoyage texte
     */
    public function nettoyerTexte($texte)
    {
        if ($texte == null) {
            return '';
        }

        $texte = trim($texte);

        // Supprime espaces multiples
        $texte = preg_replace('/\s+/', ' ', $texte);

        return $texte;
    }

    /**
     * Retour keywords SQL
     *
     * Exemple :
     * 'stk','service','moov'
     */
    public function inChaine()
    {
        if (empty($this->keyword)) {
            return "''";
        }

        $chaine = explode(";", $this->keyword);

        $res = '';

        foreach ($chaine as $valeur) {

            $res .= "'" . trim($valeur) . "',";
        }

        return rtrim($res, ',');
    }

    /**
     * Conversion objet -> Service
     */
    public static function castService($obj)
    {
        if (!is_object($obj)) {
            return null;
        }

        $array = array(
            "service_id",
            "libelle",
            "keyword",
            "description",
            "montant",
            "duree_estimee",
            "code_service",
            "specialite",
            "actif"
        );

        $service = new Service(array());

        foreach ($array as $val) {

            if (isset($obj->{$val})) {

                $service->{$val} = $obj->{$val};
            }
        }

        return $service;
    }

    /**
     * Retourne tableau keywords
     */
    public function getKeywords()
    {
        if (empty($this->keyword)) {
            return array();
        }

        return explode(';', $this->keyword);
    }

    /**
     * Verifie si service actif
     */
    public function isActif()
    {
        return ($this->actif == 1);
    }

    /**
     * Format montant
     */
    public function getMontantFormate()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Duree lisible
     */
    public function getDuree()
    {
        if ($this->duree_estimee <= 0) {
            return 'Non definie';
        }

        return $this->duree_estimee . ' min';
    }

    /**
     * Retour tableau complet
     */
    public function toArray()
    {
        return array(
            'service_id'      => $this->service_id,
            'libelle'         => $this->libelle,
            'keyword'         => $this->keyword,
            'description'     => $this->description,
            'montant'         => $this->montant,
            'duree_estimee'   => $this->duree_estimee,
            'code_service'    => $this->code_service,
            'specialite'      => $this->specialite,
            'actif'           => $this->actif
        );
    }

    /**
     * JSON
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Debug rapide
     */
    public function afficher()
    {
        echo '<pre>';
        print_r($this->toArray());
        echo '</pre>';
    }
}

?>

