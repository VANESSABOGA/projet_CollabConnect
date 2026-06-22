<?php
class Ussd
{
    public function handle($text, $phone, $specialite = false)
    {
        $levels = explode("*", $text);

        if ($specialite) {
            if ($text == "electricite") {
                

                return "CON Bienvenue CollabConnect\n
                1. Reserver
                2. Payer facture
                3. Mes reservations";
            }
        }

        if ($text == "") {

            return "CON Bienvenue CollabConnect\n
                1. Reserver
                2. Payer facture
                3. Mes reservations";
        }

        if ($text == "1") {

            return "CON Choisir service\n
                1. Repassage
                2. Menage
                3. Conception";

        }

        if ($levels[0] == 1 && isset($levels[1])) {

            return "CON Entrer date RDV\nFormat: 2026-05-30";

        }

        return "END Merci";
    }

    //      1-aujourd hui (Dans 1H)
// 2-Dans 3 jours
// 3-Dans 7 jour 


}