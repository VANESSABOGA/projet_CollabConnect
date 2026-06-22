<?php
class MENU extends Fonction
{


    var $messager = null;

    function __construct($request, $canal, $link)
    {

        parent::__construct($request, $canal, $link);
        // $this->fonction = new Fonction($request, $init, $canal);
        // $this->MO = trim(strtoupper($this->fonction->Request->content));
        // $this->callerId = $this->fonction->cdr();
        //  $this->callers = $this->fonction->retourneCallers($this->callerId);
        #$this->mes_abonnements = $this->fonction->getAbonnementTelephone();
        $this->messager = new Message($this);
    }

    /*public function setResponse($result, $text, $next = 'menu', $freeFlow = '', $facturation = "YES")
    {
        if ($this->canal == "USSD") {
            $this->setNext($next);
            $text = $text->pourAfficher;

            if ($freeFlow === "") {
                $freeFlow = "FB";
            } else {
                $freeFlow = "FC";
            }

            header("next: " . $next);
            header('FreeFlow: ' . $freeFlow);
            header('tailleMessage: ' . strlen($text));
            header('Content-Type: text/plain');

            //$text = str_replace('{CR}', PHP_EOL, $text);
            //print "taille message ==>" . strlen($text) . PHP_EOL;
            print 'next: ' . $next . PHP_EOL;
            print 'ussdString: ' . $text;
        } else {
            $a_afficher = $text->title_sans_caractere;
            //$this->cdr($a_afficher);
            //$this->sms_envoi($this->telephone, $a_afficher, __FUNCTION__);
        }
    }*/

    public function setResponse($result, $text, $next = 'menu', $freeFlow = '', $facturation = "YES")
    {
        if ($this->canal == "USSD") {

            $this->setNext($next);

            if (is_object($text) && isset($text->pourAfficher)) {
                $text = $text->pourAfficher;
            }

            if (!is_string($text)) {
                $text = (string)$text;
            }

            $freeFlow = ($freeFlow === "") ? "FB" : "FC";

            header("freeflow: " . $freeFlow);
            header("next: " . $next);
            header("Content-Type: text/plain; charset=ISO-8859-1");

            echo "freeflow: " . $freeFlow . "\n";
            echo "next: " . $next . "\n";
            echo "ussdstring: " . trim($text);
        } else {
            $a_afficher = $text->title_sans_caractere;
            // $this->cdr($a_afficher);
            $this->sms_envoi($this->telephone, $a_afficher, __FUNCTION__);
        }
    }

    public function menuErreur(Service $service = null)
    {

        $message = $this->messager->echecOperation($service);
        $this->setResponse(__FUNCTION__, $message, 'menu');
    }

    public function generateReference(Service $service)
    {
        return 'RSV-s' . $service->code_service . "-" . date('YmdHis') . rand(10, 99);
    }
}
