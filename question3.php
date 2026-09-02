<?php
$page_title = "Store Results for New Polling Unit";
require_once "classes/Binocom.php";
require_once "header.php";

$binocom = new Binocom();
$lgas = $binocom->getLgas(25);
$parties = $binocom->getAllParties();

$success_message = "";
$error_message = "";
$new_pu_id = 0;

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lga_id = isset($_POST['lga_id']) ? (int)$_POST['lga_id'] : 0;
    $ward_id = isset($_POST['ward_id']) ? (int)$_POST['ward_id'] : 0;
    $polling_unit_name = trim($_POST['polling_unit_name'] ?? '');
    $polling_unit_number = trim($_POST['polling_unit_number'] ?? '');
    $polling_unit_description = trim($_POST['polling_unit_description'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $long = trim($_POST['long'] ?? '');
    $entered_by_user = trim($_POST['entered_by_user'] ?? 'Election Officer');
    $user_ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // Collect party scores
    $party_scores = $_POST['party_scores'] ?? [];

    // Validation
    if ($lga_id <= 0 || $ward_id <= 0 || empty($polling_unit_name)) {
        $error_message = "Please select an LGA, Ward, and enter a Polling Unit Name.";
    } else {
        // Auto-generate polling_unit_id integer if needed
        $pu_data = [
            'polling_unit_id' => rand(100, 999),
            'ward_id' => $ward_id,
            'lga_id' => $lga_id,
            'uniquewardid' => $ward_id,
            'polling_unit_number' => $polling_unit_number ?: ('PU-' . rand(1000, 9999)),
            'polling_unit_name' => $polling_unit_name,
            'polling_unit_description' => $polling_unit_description,
            'lat' => $lat,
            'long' => $long,
            'entered_by_user' => $entered_by_user,
            'user_ip_address' => $user_ip_address
        ];

        $inserted_pu_id = $binocom->addPollingUnitWithResults($pu_data, $party_scores);

        if ($inserted_pu_id) {
            $new_pu_id = $inserted_pu_id;
            $success_message = "Successfully created new Polling Unit '{$polling_unit_name}' and recorded results for all " . count($party_scores) . " political parties!";
        } else {
            $error_message = "An error occurred while saving the polling unit and party results to the database.";
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Question 3: Add Polling Unit Results</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm">
            <div>
                <h2 class="fw-bold text-dark mb-1"><i class="bi bi-plus-circle text-warning me-2"></i>Store New Polling Unit Results</h2>
                <p class="text-muted mb-0">Create a new polling unit and record election results for ALL political parties.</p>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show p-4 shadow-sm mb-4" role="alert">
        <h4 class="alert-heading fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Success!</h4>
        <p class="mb-3"><?php echo htmlspecialchars($success_message); ?></p>
        <hr>
        <div class="d-flex gap-2">
            <a href="question1.php?pu_id=<?php echo $new_pu_id; ?>" class="btn btn-success fw-semibold">
                <i class="bi bi-eye me-1"></i> View Recorded Results (Question 1)
            </a>
            <a href="question3.php" class="btn btn-outline-success fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Add Another Polling Unit
            </a>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="POST" action="question3.php" id="addPuForm">
    <!-- Section 1: Polling Unit Location & Details Card -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-warning"><i class="bi bi-geo-alt-fill me-2"></i>1. Polling Unit Location & Info</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- LGA Select (Chained Combo) -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Local Government Area (LGA) <span class="text-danger">*</span></label>
                    <select class="form-select" id="selectLgaQ3" name="lga_id" required>
                        <option value="">-- Select LGA --</option>
                        <?php foreach ($lgas as $lga): ?>
                            <option value="<?php echo $lga['lga_id']; ?>">
                                <?php echo htmlspecialchars($lga['lga_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ward Select (Chained Combo) -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ward <span class="text-danger">*</span></label>
                    <select class="form-select" id="selectWardQ3" name="ward_id" required>
                        <option value="">-- Select LGA First --</option>
                    </select>
                </div>

                <!-- Polling Unit Name -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Polling Unit Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="polling_unit_name" placeholder="e.g. Asaba Central Primary School PU 1" required>
                </div>

                <!-- Polling Unit Number -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Polling Unit Number</label>
                    <input type="text" class="form-control" name="polling_unit_number" placeholder="e.g. DT0102005">
                </div>

                <!-- Entered By User -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Entered By (Officer Name)</label>
                    <input type="text" class="form-control" name="entered_by_user" value="INEC Presiding Officer">
                </div>

                <!-- Latitude & Longitude -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Latitude</label>
                    <input type="text" class="form-control" name="lat" placeholder="6.2059">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Longitude</label>
                    <input type="text" class="form-control" name="long" placeholder="6.6959">
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Polling Unit Description / Remarks</label>
                    <textarea class="form-control" name="polling_unit_description" rows="2" placeholder="Optional description of polling unit location..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Party Results Entry Card -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-warning"><i class="bi bi-box-seam me-2"></i>2. Enter Results for ALL Political Parties</h5>
            <span class="badge bg-warning-subtle text-dark fw-bold"><?php echo count($parties); ?> Parties Registered</span>
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-4">Enter the recorded votes/scores for each political party below. Default score is 0.</p>
            
            <div class="row g-3">
                <?php foreach ($parties as $party): ?>
                    <?php 
                        $party_code = htmlspecialchars($party['partyname'] ?: $party['partyid']);
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <span class="badge bg-dark px-3 py-2 fs-6 fw-bold"><?php echo $party_code; ?></span>
                            </div>
                            <div style="max-width: 140px;">
                                <input type="number" 
                                       min="0" 
                                       class="form-control form-control-lg fw-bold text-end party-score-input" 
                                       name="party_scores[<?php echo $party_code; ?>]" 
                                       value="0" 
                                       required>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card-footer bg-white p-4 text-end">
            <button type="reset" class="btn btn-light me-2">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
            </button>
            <button type="submit" class="btn btn-warning btn-lg px-5 fw-bold shadow-sm">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save Polling Unit Results
            </button>
        </div>
    </div>
</form>

<!-- Script for Chained Combo Box (LGA -> Ward) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lgaSelect = document.getElementById('selectLgaQ3');
    const wardSelect = document.getElementById('selectWardQ3');

    lgaSelect.addEventListener('change', function() {
        const lgaId = this.value;
        wardSelect.innerHTML = '<option value="">-- Loading Wards... --</option>';

        if (!lgaId) {
            wardSelect.innerHTML = '<option value="">-- Select LGA First --</option>';
            return;
        }

        fetch(`api.php?action=get_wards&lga_id=${lgaId}`)
            .then(res => res.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">-- Select Ward --</option>';
                if (data.status === 'success') {
                    data.data.forEach(ward => {
                        const opt = document.createElement('option');
                        opt.value = ward.ward_id;
                        opt.textContent = ward.ward_name;
                        wardSelect.appendChild(opt);
                    });
                }
            });
    });
});
</script>

<?php require_once "footer.php"; ?>
