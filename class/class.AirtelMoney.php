<?php

class AirtelMoney
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $country;
    private $currency;
    private $db;


    public function __construct($db, $sandbox = true)
    {
        $this->db = $db;
        $this->clientId = "903e50a2-b93e-4c1e-94fe-e37d66922cfa";
        $this->clientSecret = "64d165bc-ce70-49ca-97f7-a494fe6db90a";
        $this->country = "CG";
        $this->currency = "XAF";

        $this->baseUrl = $sandbox
            ? "https://openapiuat.airtel.africa"
            : "https://openapi.airtel.africa";
    }

    /**
     * Recuperation du token OAuth2
     */

    public function getAccessToken()
    {
        $url = $this->baseUrl . "/auth/oauth2/token";

        $payload = [
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret,
            "grant_type" => "client_credentials"
        ];

        $response = $this->request(
            "POST",
            $url,
            [
                "Content-Type: application/json",
                "Accept: */*"
            ],
            json_encode($payload)
        );

        return json_decode($response, true);
    }

    /**
     * Paiement USSD Push
     */
    public function paymentPush(
        $telephone,
        $montant,
        $reference,
        $transactionId
    ) {
        $token = $this->getAccessToken();

        if (!isset($token['access_token'])) {
            return $token;
        }

        $url = $this->baseUrl . "/merchant/v1/payments/";

        $payload = [
            "reference" => $reference,
            "subscriber" => [
                "country" => $this->country,
                "currency" => $this->currency,
                "msisdn" => $telephone
            ],
            "transaction" => [
                "amount" => $montant,
                "country" => $this->country,
                "currency" => $this->currency,
                "id" => $transactionId
            ]
        ];

        $headers = [
            "Authorization: Bearer " . $token['access_token'],
            "Content-Type: application/json",
            "Accept: */*",
            "X-Country: " . $this->country,
            "X-Currency: " . $this->currency
        ];

        $response = $this->request(
            "POST",
            $url,
            $headers,
            json_encode($payload)
        );

        return json_decode($response, true);
    }

    /**
     * Verification du statut d'une transaction
     */
    public function checkTransaction($transactionId)
    {
        $token = $this->getAccessToken();

        if (!isset($token['access_token'])) {
            return $token;
        }

        $url = $this->baseUrl .
            "/standard/v1/payments/" .
            $transactionId;

        $headers = [
            "Authorization: Bearer " . $token['access_token'],
            "Accept: */*",
            "X-Country: " . $this->country,
            "X-Currency: " . $this->currency
        ];

        $response = $this->request(
            "GET",
            $url,
            $headers
        );

        return json_decode($response, true);
    }

    /**
     * Fonction CURL generique
     */
    private function request(
        $method,
        $url,
        $headers = [],
        $body = null
    ) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        if ($body !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            return json_encode([
                "error" => curl_error($curl)
            ]);
        }

        curl_close($curl);

        return $response;
    }

    private function request2(
        $method,
        $url,
        $headers = [],
        $body = null
    ) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,

            // 🔥 FIX CRITIQUE AIRTEL
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            // 🔐 SSL SAFE MODE (NE PAS DeSACTIVER)
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,

            CURLOPT_TIMEOUT => 30,
        ]);

        if ($body !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        curl_close($curl);

        // ❌ CURL ERROR
        if ($response === false) {
            return [
                "success" => false,
                "error" => $error
            ];
        }

        return [
            "success" => ($httpCode >= 200 && $httpCode < 300),
            "http_code" => $httpCode,
            "response" => json_decode($response, true),
            "raw" => $response
        ];
    }
    
    function getTransactionStatusLabel($status)
    {
        switch ($status) {
            case 'TS':
                return 'Paiement effectue avec succès';

            case 'TF':
                return 'Paiement echoue';

            case 'TIP':
                return 'Paiement en cours';

            case 'TA':
                return 'Paiement ambigu - verification requise';

            case 'TE':
                return 'Paiement expire';

            default:
                return 'Statut inconnu';
        }
    }

    function airtelPayment($reference, $msisdn, $amount, $transactionId)
    {
        $token = $this->getAccessToken();

        if (!isset($token['access_token'])) {
            return [
                "success" => false,
                "error" => "Unable to get access token",
                "response" => $token
            ];
        }

        $url = "https://openapiuat.airtel.africa/merchant/v1/payments/";

        $payload = [
            "reference" => $reference,
            "subscriber" => [
                "country" => "CG",
                "currency" => "XAF",
                "msisdn" => $msisdn
            ],
            "transaction" => [
                "amount" => $amount,
                "country" => "CG",
                "currency" => "XAF",
                "id" => $transactionId
            ]
        ];

        $headers = [
            "Accept: */*",
            "Content-Type: application/json",
            "X-Country: CG",
            "X-Currency: XAF",
            "Authorization: Bearer " . $token['access_token'],
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // 🔥 FIX HTTP/2 ERROR
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        // 🚀 EXECUTE REQUEST
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        // ❌ CURL ERROR CHECK
        if ($response === false) {
            return [
                "success" => false,
                "error" => $error
            ];
        }

        return [
            "success" => ($httpCode == 200),
            "http_code" => $httpCode,
            "response" => json_decode($response, true),
            "raw_response" => $response
        ];
    }
    public function saveTransaction(
        $reference_facture,
        $transaction_id,
        $telephone,
        $montant,
        $request
    ) {

        $sqlQuery = "INSERT INTO transactions(
                reference_facture,
                transaction_id,
                telephone,
                montant,
                raw_request
            )
            VALUES(?,?,?,?,?)";

        $insertData = array($reference_facture, $transaction_id, $telephone, $montant, json_encode($request));
        $resultat = $this->db->db_executeQuery($sqlQuery, $insertData);

        return array([
            'reference_facture' => $reference_facture,
            'transaction_id'    => $transaction_id,
            'telephone'         => $telephone,
            'montant'           => $montant,
            'raw_request'       => json_encode($request),
            'resultat'            => $resultat
        ]);
    }

    public function updateStatus(
        $transaction_id,
        array $response
    ) {

        $transaction = $response['data']['transaction'] ?? [];
        $statusInfo  = $response['status'] ?? [];

        $sqlQuery = "UPDATE transactions SET
                status = ?,
                airtel_money_id = ?,
                response_code = ?,
                result_code = ?,
                message = ?,
                raw_response = ?,
                nb_verifications = nb_verifications + 1,

                date_validation =
                    CASE
                        WHEN status = 'TS'
                        THEN NOW()
                        ELSE date_validation
                    END

            WHERE transaction_id = ?";

        $status          = $transaction['status'] ?? null;
        $airtel_money_id = $transaction['airtel_money_id'] ?? null;
        $response_code   = $statusInfo['response_code'] ?? null;
        $result_code     = $statusInfo['result_code'] ?? null;
        $message         = $transaction['message'] ?? null;
        $raw_response    = json_encode($response);
        $transaction_id  = $transaction_id;


        $insertData = array($status, $airtel_money_id, $response_code, $result_code, $message, $raw_response, $transaction_id);
        $resultat = $this->db->db_executeQuery($sqlQuery, $insertData);

        return array([
            'status'          => $status,
            'airtel_money_id' => $airtel_money_id,
            'response_code'   => $response_code,
            'result_code'     => $result_code,
            'message'         => $message,
            'raw_response'    => json_encode($response),
            'transaction_id'  => $transaction_id,
            'resultat'          => $resultat
        ]);
    }

    public function getPendingTransactions()
    {
        $sqlQuery = "SELECT *   FROM transactions  WHERE status IN ('TIP','TA')  AND nb_verifications < 10";
        $insertData = array();
        return $this->db->selectOBJ($sqlQuery, $insertData);
    }

    public function facturePayee($reference)
    {
        $sqlQuery = "SELECT COUNT(*) total
            FROM transactions
            WHERE reference_facture = :reference
            AND status = ?";

        $insertData = array('TF');
        return $this->db->selectOBJ($sqlQuery, $insertData);
    }

    public function getHistoriqueClient($telephone)
    {
        $sqlQuery = "SELECT * FROM transactions   WHERE telephone = ?   ORDER BY id DESC";
        $insertData = array($telephone);
        return $this->db->selectOBJ($sqlQuery, $insertData);
    }

    public function getTransaction($transaction_id)
    {
        $sqlQuery = "SELECT * FROM transactions   WHERE transaction_id = ?";
        $insertData = array($transaction_id);
        return $this->db->selectOBJ($sqlQuery, $insertData);
    }
}
