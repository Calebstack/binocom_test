<?php
$page_title = "Summed LGA Election Results";
require_once "classes/Binocom.php";
require_once "header.php";

$binocom = new Binocom();
$lgas = $binocom->getLgas(25);

$selected_lga_id = isset($_GET['lga_id']) ? (int)$_GET['lga_id'] : 0;
$lga_details = null;
$summed_results = [];
$announced_lga_results = [];
$polling_units = [];
$total_lga_votes = 0;

if ($selected_lga_id > 0) {
    $lga_details = $binocom->getLgaDetails($selected_lga_id);
    if ($lga_details) {
        // Core Question 2 Requirement: Calculate summed total from polling unit results
        $summed_results = $binocom->getLgaSummedResults($selected_lga_id);
        $total_lga_votes = array_sum(array_column($summed_results, 'total_score'));

        // Fetch polling units under this LGA
        $polling_units = $binocom->getPollingUnitsByLga($selected_lga_id);

        // Fetch official announced LGA results for comparison check
        if (isset($lga_details['lga_name'])) {
            $announced_lga_results = $binocom->getAnnouncedLgaResults($lga_details['lga_name']);
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Question 2: LGA Summed Results</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm">
            <div>
                <h2 class="fw-bold text-dark mb-1"><i class="bi bi-pie-chart text-success me-2"></i>Summed Total Result for LGA</h2>
                <p class="text-muted mb-0">Select any Local Government Area (LGA) to calculate and aggregate all polling unit results under it.</p>
            </div>
        </div>
    </div>
</div>

<!-- LGA Select Box Form Card -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-success"><i class="bi bi-building me-2"></i>Select Local Government Area</h5>
    </div>
    <div class="card-body p-4">
        <form method="GET" action="question2.php" class="row align-items-end g-3">
            <div class="col-md-9">
                <label class="form-label fw-semibold">Choose Local Government (LGA):</label>
                <select name="lga_id" class="form-select form-select-lg" required>
                    <option value="">-- Select Local Government Area --</option>
                    <?php foreach ($lgas as $lga): ?>
                        <option value="<?php echo $lga['lga_id']; ?>" <?php echo ($selected_lga_id == $lga['lga_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lga['lga_name']); ?> (LGA ID: <?php echo $lga['lga_id']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold">
                    <i class="bi bi-calculator me-1"></i> Calculate Totals
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($selected_lga_id > 0): ?>
    <?php if ($lga_details): ?>
        
        <!-- Summary Stat Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card p-3 shadow-sm border-0 bg-white">
                    <span class="text-muted text-uppercase small fw-bold">Selected LGA</span>
                    <h3 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($lga_details['lga_name']); ?></h3>
                    <small class="text-muted">Delta State (State ID: <?php echo $lga_details['state_id']; ?>)</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow-sm border-0 bg-white">
                    <span class="text-muted text-uppercase small fw-bold">Total Votes Across Polling Units</span>
                    <h3 class="fw-bold text-success mb-0"><?php echo number_format($total_lga_votes); ?></h3>
                    <small class="text-muted">Calculated by summing `announced_pu_results`</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow-sm border-0 bg-white">
                    <span class="text-muted text-uppercase small fw-bold">Polling Units in LGA</span>
                    <h3 class="fw-bold text-primary mb-0"><?php echo count($polling_units); ?></h3>
                    <small class="text-muted">Registered polling units in database</small>
                </div>
            </div>
        </div>

        <!-- Calculated Summed Results Table -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-bar-chart-fill text-success me-2"></i>Summed Results by Political Party</h5>
                    <small class="text-muted">Directly aggregated from individual polling unit entries in <code>announced_pu_results</code></small>
                </div>
                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2">Question 2 Solution</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($summed_results)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Rank</th>
                                    <th>Party Abbreviation</th>
                                    <th>Summed Total Votes</th>
                                    <th style="width: 40%;">Percentage Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                foreach ($summed_results as $res): 
                                    $score = (int)$res['total_score'];
                                    $percent = ($total_lga_votes > 0) ? round(($score / $total_lga_votes) * 100, 1) : 0;
                                    $badge_class = ($rank == 1) ? 'bg-success' : (($rank == 2) ? 'bg-primary' : 'bg-secondary');
                                ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge <?php echo $badge_class; ?> rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                <?php echo $rank++; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold fs-5 text-dark"><?php echo htmlspecialchars($res['party_abbreviation']); ?></span>
                                        </td>
                                        <td>
                                            <span class="fs-5 fw-bold text-success"><?php echo number_format($score); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 12px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percent; ?>%;"></div>
                                                </div>
                                                <span class="fw-bold small text-muted" style="min-width: 45px;"><?php echo $percent; ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-info-circle fs-1 d-block mb-2 text-muted"></i>
                        <h5>No polling unit results found for <?php echo htmlspecialchars($lga_details['lga_name']); ?> in `announced_pu_results`.</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comparison Section with Announced LGA Results Table -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-arrow-left-right text-warning me-2"></i>Audit & Cross-Check: Announced LGA Results Comparison</h5>
                <small class="text-muted">Comparing our summed total against records in <code>announced_lga_results</code> table</small>
            </div>
            <div class="card-body">
                <?php if (!empty($announced_lga_results)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Party</th>
                                    <th>Summed PU Votes (Our Calc)</th>
                                    <th>Announced LGA Votes (Official)</th>
                                    <th>Variance / Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Map announced results
                                $announced_map = [];
                                foreach ($announced_lga_results as $alr) {
                                    $announced_map[trim($alr['party_abbreviation'])] = (int)$alr['party_score'];
                                }

                                // Map summed results
                                $summed_map = [];
                                foreach ($summed_results as $sr) {
                                    $summed_map[trim($sr['party_abbreviation'])] = (int)$sr['total_score'];
                                }

                                $all_parties = array_unique(array_merge(array_keys($announced_map), array_keys($summed_map)));
                                sort($all_parties);

                                foreach ($all_parties as $party_abbr):
                                    $calc = $summed_map[$party_abbr] ?? 0;
                                    $off = $announced_map[$party_abbr] ?? 'N/A';
                                    $match = ($off !== 'N/A' && $calc === $off);
                                ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($party_abbr); ?></td>
                                        <td class="fw-semibold text-success"><?php echo number_format($calc); ?></td>
                                        <td class="fw-semibold text-primary"><?php echo ($off !== 'N/A') ? number_format($off) : 'N/A'; ?></td>
                                        <td>
                                            <?php if ($off === 'N/A'): ?>
                                                <span class="badge bg-secondary">No Official Entry</span>
                                            <?php elseif ($match): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Exact Match</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i> Discrepancy (Diff: <?php echo number_format($calc - $off); ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border mb-0 text-muted">
                        <i class="bi bi-info-circle me-1"></i> No corresponding entry found in <code>announced_lga_results</code> for "<?php echo htmlspecialchars($lga_details['lga_name']); ?>".
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-danger shadow-sm my-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Invalid LGA selected.
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>
