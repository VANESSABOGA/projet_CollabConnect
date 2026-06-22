<?php

header('Content-Type: application/json');

include('../config/config.php');
include('../classes/Database.php');
include('../classes/Reservation.php');

$db = new Database();
$conn = $db->connect();

$reservation = new Reservation($conn);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['telephone'])) {

    echo json_encode(array(
        'status' => 'ERROR',
        'message' => 'telephone obligatoire'
    ));

    exit;
}

$payload = array(
    'client_nom' => isset($data['client_nom']) ? $data['client_nom'] : 'CLIENT',
    'telephone' => $data['telephone'],
    'service_id' => $data['service_id'],
    'date_rdv' => $data['date_rdv'],
    'montant' => $data['montant']
);

$reference = $reservation->create($payload);

echo json_encode(array(
    'status' => 'SUCCESS',
    'reference' => $reference,
    'message' => 'Reservation enregistree'
));