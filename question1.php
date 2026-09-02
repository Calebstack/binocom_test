<?php
$page_title = "Individual Polling Unit Results";
require_once "classes/Binocom.php";
require_once "header.php";

$binocom = new Binocom();
$lgas = $binocom->getLgas(25);

// Check if a specific PU was requested via GET param
$selected_pu_id = isset($_GET['pu_id']) ? (int)$_GET['pu_id'] : 0;
$pu_details = null;
$pu_results = [];

if ($selected_pu_id > 0) {
    $pu_details = $binocom->getPollingUnitDetails($selected_pu_id);
    if ($pu_details) {
        $pu_results = $binocom->getPollingUnitResults($selected_pu_id);
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Question 1: Polling Unit Results</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm">
            <div>
                <h2 class="fw-bold text-dark mb-1"><i class="bi bi-geo-alt text-primary me-2"></i>Individual Polling Unit Results</h2>
                <p class="text-muted mb-0">Use the chained combo box below to select State, Local Government, Ward, and Polling Unit.</p>
            </div>
        </div>
    </div>
</div>

<!-- Selection Filter Card (Chained Combo Box) -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-funnel me-2"></i>Select Polling Unit (Chained Filter)</h5>
    </div>
    <div class="card-body p-4">
        <form id="puSelectionForm" method="GET" action="question1.php">
            <div class="row g-3">
                <!-- State Combo -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">1. State</label>
                    <select class="form-select" id="selectState" name="state_id">
                        <option value="25" selected>Delta State (State ID: 25)</option>
                    </select>
                </div>

                <!-- LGA Combo -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">2. Local Government (LGA)</label>
                    <select class="form-select" id="selectLga" name="lga_id">
                        <option value="">-- Select LGA --</option>
                        <?php foreach ($lgas as $lga): ?>
                            <option value="<?php echo $lga['lga_id']; ?>" <?php echo (isset($pu_details['lga_id']) && $pu_details['lga_id'] == $lga['lga_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lga['lga_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ward Combo -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">3. Ward</label>
                    <select class="form-select" id="selectWard" name="ward_id">
                        <option value="">-- Select LGA First --</option>
                    </select>
                </div>

                <!-- Polling Unit Combo -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">4. Polling Unit</label>
                    <select class="form-select" id="selectPu" name="pu_id" required>
                        <option value="">-- Select Polling Unit --</option>
                    </select>
                </div>
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary px-4 fw-semibold" id="btnFetchResults">
                    <i class="bi bi-search me-1"></i> Display Results
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Results Display Section -->
<?php if ($selected_pu_id > 0): ?>
    <?php if ($pu_details): ?>
        <?php
            $total_votes = array_sum(array_column($pu_results, 'party_score'));
        ?>
        <div class="row g-4 mb-4" id="resultsSection">
            <!-- Polling Unit Metadata Card -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-info-circle text-primary me-2"></i>Polling Unit Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="text-muted d-block small text-uppercase fw-bold">Name</span>
                            <span class="fs-5 fw-bold text-dark"><?php echo htmlspecialchars($pu_details['polling_unit_name'] ?: 'N/A'); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted d-block small text-uppercase fw-bold">Unit Number</span>
                            <span class="badge bg-secondary fs-6"><?php echo htmlspecialchars($pu_details['polling_unit_number'] ?: 'N/A'); ?></span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <span class="text-muted d-block small text-uppercase fw-bold">Local Govt (LGA)</span>
                                <span class="fw-semibold text-dark"><?php echo htmlspecialchars($pu_details['lga_name'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block small text-uppercase fw-bold">Ward</span>
                                <span class="fw-semibold text-dark"><?php echo htmlspecialchars($pu_details['ward_name'] ?: 'N/A'); ?></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <span class="text-muted d-block small text-uppercase fw-bold">Latitude</span>
                                <span class="small text-dark"><?php echo htmlspecialchars($pu_details['lat'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block small text-uppercase fw-bold">Longitude</span>
                                <span class="small text-dark"><?php echo htmlspecialchars($pu_details['long'] ?: 'N/A'); ?></span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-uppercase text-muted small">Total Votes Cast</span>
                            <span class="fs-4 fw-bold text-primary"><?php echo number_format($total_votes); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Election Results Breakdown Table & Progress -->
            <div class="col-lg-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-trophy text-warning me-2"></i>Party Score Breakdown</h5>
                        <span class="badge bg-primary-subtle text-primary"><?php echo count($pu_results); ?> Parties Recorded</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($pu_results)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">S/N</th>
                                            <th>Party Abbreviation</th>
                                            <th>Votes Received</th>
                                            <th style="width: 35%;">Vote Share</th>
                                            <th>Entered By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $count = 1;
                                        foreach ($pu_results as $res): 
                                            $score = (int)$res['party_score'];
                                            $percent = ($total_votes > 0) ? round(($score / $total_votes) * 100, 1) : 0;
                                        ?>
                                            <tr>
                                                <td class="ps-4 fw-semibold text-muted"><?php echo $count++; ?></td>
                                                <td>
                                                    <span class="badge bg-primary px-3 py-2 fs-6 fw-bold"><?php echo htmlspecialchars($res['party_abbreviation']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fs-5 fw-bold text-dark"><?php echo number_format($score); ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height: 10px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percent; ?>%;"></div>
                                                        </div>
                                                        <span class="fw-semibold small text-muted"><?php echo $percent; ?>%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="small text-muted d-block"><?php echo htmlspecialchars($res['entered_by_user'] ?: 'System'); ?></span>
                                                    <span class="small text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($res['date_entered'] ?: ''); ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-exclamation-circle fs-1 d-block mb-2 text-warning"></i>
                                <h5>No announced results found for this polling unit.</h5>
                                <p class="mb-0">You can use <a href="question3.php">Question 3 Page</a> to enter scores for this polling unit.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger shadow-sm my-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Polling unit not found. Please select a valid polling unit.
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Chained Combo Box JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lgaSelect = document.getElementById('selectLga');
    const wardSelect = document.getElementById('selectWard');
    const puSelect = document.getElementById('selectPu');

    const initialLgaId = "<?php echo isset($pu_details['lga_id']) ? $pu_details['lga_id'] : ''; ?>";
    const initialWardId = "<?php echo isset($pu_details['ward_id']) ? $pu_details['ward_id'] : ''; ?>";
    const initialPuId = "<?php echo $selected_pu_id > 0 ? $selected_pu_id : ''; ?>";

    // Function to load polling units
    function loadPollingUnits(lgaId, wardId, targetPuId = null) {
        puSelect.innerHTML = '<option value="">-- Loading Polling Units... --</option>';
        let url = `api.php?action=get_polling_units`;
        if (lgaId) url += `&lga_id=${lgaId}`;
        if (wardId) url += `&ward_id=${wardId}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                puSelect.innerHTML = '<option value="">-- Select Polling Unit --</option>';
                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(pu => {
                        const opt = document.createElement('option');
                        opt.value = pu.uniqueid;
                        const puName = pu.polling_unit_name || 'Unnamed Unit';
                        const puNum = pu.polling_unit_number ? ` (${pu.polling_unit_number})` : ` (PU ID: ${pu.uniqueid})`;
                        opt.textContent = `${puName}${puNum}`;
                        if (targetPuId && pu.uniqueid == targetPuId) {
                            opt.selected = true;
                        }
                        puSelect.appendChild(opt);
                    });
                } else {
                    puSelect.innerHTML = '<option value="">-- No Polling Units Found --</option>';
                }
            })
            .catch(err => {
                puSelect.innerHTML = '<option value="">-- Error Loading Polling Units --</option>';
            });
    }

    // 1. When LGA changes -> Load Wards & Polling Units
    lgaSelect.addEventListener('change', function() {
        const lgaId = this.value;
        wardSelect.innerHTML = '<option value="">-- Select Ward --</option>';

        if (!lgaId) {
            wardSelect.innerHTML = '<option value="">-- Select LGA First --</option>';
            loadPollingUnits(null, null, initialPuId);
            return;
        }

        fetch(`api.php?action=get_wards&lga_id=${lgaId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    data.data.forEach(ward => {
                        const opt = document.createElement('option');
                        opt.value = ward.ward_id;
                        opt.textContent = ward.ward_name;
                        if (initialWardId && ward.ward_id == initialWardId) {
                            opt.selected = true;
                        }
                        wardSelect.appendChild(opt);
                    });

                    if (wardSelect.value) {
                        wardSelect.dispatchEvent(new Event('change'));
                    } else {
                        loadPollingUnits(lgaId, null, initialPuId);
                    }
                }
            });
    });

    // 2. When Ward changes -> Load Polling Units for that Ward
    wardSelect.addEventListener('change', function() {
        const lgaId = lgaSelect.value;
        const wardId = this.value;
        loadPollingUnits(lgaId, wardId, initialPuId);
    });

    // Initial load: If an LGA is selected, filter by LGA; otherwise, load all polling units by default
    if (lgaSelect.value) {
        lgaSelect.dispatchEvent(new Event('change'));
    } else {
        loadPollingUnits(null, null, initialPuId);
    }
});
</script>

<?php require_once "footer.php"; ?>
