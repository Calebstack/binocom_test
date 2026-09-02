<?php
header('Content-Type: application/json');
require_once "classes/Binocom.php";

$binocom = new Binocom();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch($action) {
    case 'get_wards':
        $lga_id = isset($_GET['lga_id']) ? (int)$_GET['lga_id'] : 0;
        $wards = $binocom->getWardsByLga($lga_id);
        echo json_encode(['status' => 'success', 'data' => $wards]);
        break;

    case 'get_polling_units':
        $lga_id = isset($_GET['lga_id']) ? (int)$_GET['lga_id'] : 0;
        $ward_id = isset($_GET['ward_id']) && $_GET['ward_id'] !== '' ? (int)$_GET['ward_id'] : null;

        if ($ward_id !== null && $ward_id > 0) {
            $pus = $binocom->getPollingUnitsByWard($lga_id, $ward_id);
        } else if ($lga_id > 0) {
            $pus = $binocom->getPollingUnitsByLga($lga_id);
        } else {
            $pus = $binocom->getAllPollingUnits();
        }
        echo json_encode(['status' => 'success', 'data' => $pus]);
        break;

    case 'get_pu_results':
        $pu_id = isset($_GET['pu_id']) ? (int)$_GET['pu_id'] : 0;
        $details = $binocom->getPollingUnitDetails($pu_id);
        $results = $binocom->getPollingUnitResults($pu_id);
        echo json_encode([
            'status' => 'success',
            'details' => $details,
            'results' => $results
        ]);
        break;

    case 'get_lga_results':
        $lga_id = isset($_GET['lga_id']) ? (int)$_GET['lga_id'] : 0;
        $lgaDetails = $binocom->getLgaDetails($lga_id);
        $summedResults = $binocom->getLgaSummedResults($lga_id);
        $announcedResults = [];
        if ($lgaDetails && isset($lgaDetails['lga_name'])) {
            $announcedResults = $binocom->getAnnouncedLgaResults($lgaDetails['lga_name']);
        }
        echo json_encode([
            'status' => 'success',
            'lga' => $lgaDetails,
            'summed_results' => $summedResults,
            'announced_results' => $announcedResults
        ]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
