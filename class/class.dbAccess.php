<?php


class dbAccess
{

    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    public $conn;
    public $connexion;
    public $errorCode = 0;
    public $errorMessage = null;
    public $to_log = null;

    function __construct()
    {
        $this->connexion = $this->db_connexion();

        // $this->host = $this->databaseName;
    }

    public function _close() {}

    public function to_log() {}

    function db_connexion()
    {
        // try {
        //     $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
        //     return $db;
        // } catch (PDOException $e) {

        //     die('Erreur : ' . $e->getMessage());
        // }

        try {

            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES utf8");
        } catch (PDOException $e) {

            die($e->getMessage());
        }

        // debug("connecte");
        return $this->conn;
    }

    private function _setErrors($error = null)
    {
        if (null !== $error) {
            $this->errorCode = $error->getCode();
            $this->errorMessage = $error->getMessage();
            $this->to_log(__CLASS__ . '.' . __FUNCTION__, 'failed to perform the operation: code=' . $error->getCode() . ', message=' . $error->getMessage());
            return;
        }
        $this->errorCode = 0;
        $this->errorMessage = null;
    }


    public function selectboundParameters($sqlQuery, array $boundParameters = NULL)
    {
        #echo $sqlQuery;
        $this->to_log(__CLASS__ . '.' . __FUNCTION__, 'executing query[' . $sqlQuery . '], boundParameters[' . json_encode($boundParameters) . ']');
        $this->_setErrors();
        try {
            $recipesStatement = $this->connexion->prepare($sqlQuery);
            $recipesStatement->execute($boundParameters);
            return $recipesStatement->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $this->_setErrors($e);
            return null;
        }
    }

    function select($sqlQuery)
    {
        // echo $sqlQuery.PHP_EOL;
        try {
            $recipesStatement = $this->connexion->prepare($sqlQuery);
            $recipesStatement->execute();
            return $recipesStatement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->_setErrors($e);
            return null;
        }
    }

    function selectOBJ($sqlQuery)
    {
        //   echo $sqlQuery.PHP_EOL;
        try {
            $recipesStatement = $this->connexion->prepare($sqlQuery);
            $recipesStatement->execute();
            return $recipesStatement->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $this->_setErrors($e);
            return null;
        }
    }

    public function parse_params($params)
    {
        $return = '';
        if (array_key_exists('where', $params))
            $return .= ' WHERE ' . $params['where'];
        if (array_key_exists('order', $params))
            $return .= ' ORDER BY ' . $params['order'];
        if (array_key_exists('limit', $params))
            $return .= ' LIMIT ' . $params['limit'];
        //$this->db_debug($return);
        return $return;
    }
    public function querySelect($find, $findTable, $findParams = array(), $libelle = FALSE, $index = FALSE, $message = FALSE, $read = NULL, $plus = FALSE, $affich = TRUE)
    {
        if ($findTable == null) {
            $sqlQuery = "SELECT $find";
        } else {
            $sqlQuery = "SELECT $find FROM $findTable";
            $sqlQuery .= $this->parse_params($findParams);
        }
        #echo PHP_EOL . $sqlQuery . PHP_EOL;
        $result = $this->select($sqlQuery);
        return $result;
    }

    function selectArray($sqlQuery)
    {
        // echo $sqlQuery;
        try {
            $recipesStatement = $this->connexion->prepare($sqlQuery);
            $recipesStatement->execute();
            return $recipesStatement->fetchAll();
        } catch (PDOException $e) {
            $this->_setErrors($e);
            return null;
        }
    }

    function db_executeQuery($sqlQuery, $params)
    {
        // print $sqlQuery; print_r($params);
        $recipesStatement = $this->connexion->prepare($sqlQuery);
        if (!$recipesStatement->execute($params)) {
            // print_r($recipesStatement->errorInfo());
            if ($recipesStatement->errorInfo()[0] == 23000)
                return "100-23000";
            else
                return false;
        } else {
            $lastId = $this->connexion->lastInsertId();
            return $lastId;
        }
    }


    public static function formatURL($url, array $tableau)
    {
        foreach ($tableau as $key => $value) {
            $url .= "$key=" . urlencode($value) . "&";
        }
        return substr($url, 0, strlen($url) - 1);
    }

    public function execURLH($url_final)
    {
        //if($url_final==NULL) $url_final=fonction::formatURL($url,$tableau);
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



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //retourne un tableau tableau[0]la page,tableau[2] le contenu de la page ,tableau[3] comptient si suivant
    #public function retourneMenuDynamique($requete,$page,$principal=false,$taillePage=Config::$TAILLE_RUBRIQUES_MENU,$tout=false, $nombreSuivant=Config::$NOMBRE_SUIVANT){
    public function retourneMenuDynamique($requete, $page, $labell = '1', $principal = false, $taillePage = TAILLE_RUBRIQUES_MENU, $tout = false, $nombreSuivant = NOMBRE_SUIVANT)
    {
        $suivant = false;
        $message = "";
        $pageEffective = 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $taillePage;
        $limit = $taillePage + 1;
        $requete2 = $requete . " limit $limit offset $offset"; //

        
        $tab = $this->selectArray($requete2);
        //print_r($tab);
        //si on a rien on diminue la page jusqua la bonne*****************
        while (count($tab) < 1 and $page > 1) {
            $page--;
            $offset = ($page - 1) * $taillePage;
            $requete2 = $requete . " limit $limit offset $offset";
            $tab = $this->selectArray($requete2);
        }
        if (count($tab) == $taillePage + 1)
            $suivant = true;

        $pageEffective = $page;
        $n = 0;
        for ($i = 0; $i < count($tab); $i++) {


            if ($i < $taillePage) {
                if (dbAccess::estEntier($nombreSuivant, 1) and ($offset + $i + 1) >= $nombreSuivant) {
                    $n = $offset + $i + 2;
                    if ($i != 0)
                        $message .= "{CR}" . ($offset + $i + 2) . ". " . trim(ucfirst(($tab[$i][$labell])));
                    else
                        $message .= ($offset + $i + 2) . ". " . trim(ucfirst($tab[$i][$labell]));
                } else {
                    $n = $offset + $i + 1;
                    if ($i != 0)
                        $message .= "{CR}" . ($offset + $i + 1) . ". " . trim(ucfirst($tab[$i][$labell]));
                    else
                        $message .= ($offset + $i + 1) . ". " . trim(ucfirst($tab[$i][$labell]));
                }
            }
        }
        $n++;

        if (!$principal or $pageEffective != 1)
            $message .= "{CR}0. Retour";
        elseif ($page != 1)
            $message .= "{CR}0. Retour";
        if ($suivant)
            $message .= "{CR}$nombreSuivant. Suivant";
        #if(!$suivant) $message.="{CR}00. Accueil";

        return new EtatLecture($pageEffective, '', $message, $suivant, count($tab));
    }

    public static function estEntier($valeur, $type = 0)
    {
        // option regex stricte si demande
        if ($type == 1) {
            return is_string($valeur) && preg_match('/^[0-9]+$/', $valeur);
        }

        // dejà int natif
        if (is_int($valeur)) {
            return true;
        }

        // securise type
        if (!is_string($valeur) && !is_numeric($valeur)) {
            return false;
        }

        // supprime espaces
        $valeur = trim((string)$valeur);

        // vide => false
        if ($valeur === '') {
            return false;
        }

        // entier valide (le plus fiable)
        return ctype_digit($valeur);
    }
}
