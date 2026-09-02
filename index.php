<?php
$page_title = "Election Results Portal";
require_once "classes/Binocom.php";
require_once "header.php";

$binocom = new Binocom();
$lgas = $binocom->getLgas(25);
$allPus = $binocom->getAllPollingUnits();
$parties = $binocom->getAllParties();
?>

<div class="row mb-4">
    <div class="col-12 text-center py-4 bg-white rounded-3 shadow-sm mb-4">
        <h1 class="fw-bold text-dark mb-2">Delta State 2011 Election Results Portal</h1>
        <p class="text-muted lead mb-0">Select an option below to view individual polling unit results, LGA summed vote totals, or record new election results.</p>
    </div>
</div>

<!-- Overview Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card p-3 stat-card shadow-sm border-0 border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">Local Governments</h6>
                    <h2 class="fw-bold mb-0 text-primary"><?php echo count($lgas); ?></h2>
                </div>
                <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                    <i class="bi bi-building fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card shadow-sm border-0 border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">Polling Units</h6>
                    <h2 class="fw-bold mb-0 text-success"><?php echo count($allPus); ?></h2>
                </div>
                <div class="bg-success-subtle p-3 rounded-circle text-success">
                    <i class="bi bi-geo-alt-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card shadow-sm border-0 border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">Registered Parties</h6>
                    <h2 class="fw-bold mb-0 text-warning"><?php echo count($parties); ?></h2>
                </div>
                <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                    <i class="bi bi-flag-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Task Cards -->
<div class="row g-4 mb-5">
    <!-- Question 1 Card -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3 text-primary">
                    <i class="bi bi-distribute-vertical fs-1"></i>
                </div>
                <h5 class="card-title fw-bold">Individual Polling Unit Results</h5>
                <span class="badge bg-primary-subtle text-primary mb-3 align-self-start">Question 1</span>
                <p class="card-text text-muted flex-grow-1">Select any polling unit in Delta State using chained combo boxes (LGA &rarr; Ward &rarr; Polling Unit) to view party score breakdowns.</p>
                <a href="question1.php" class="btn btn-outline-primary w-100 fw-semibold mt-3">
                    View PU Results <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Question 2 Card -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3 text-success">
                    <i class="bi bi-calculator-fill fs-1"></i>
                </div>
                <h5 class="card-title fw-bold">Summed LGA Results</h5>
                <span class="badge bg-success-subtle text-success mb-3 align-self-start">Question 2</span>
                <p class="card-text text-muted flex-grow-1">Select a Local Government Area (LGA) from a dropdown to calculate and view the aggregated total votes of all polling units under that LGA.</p>
                <a href="question2.php" class="btn btn-outline-success w-100 fw-semibold mt-3">
                    Calculate LGA Totals <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Question 3 Card -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3 text-warning">
                    <i class="bi bi-file-earmark-plus-fill fs-1"></i>
                </div>
                <h5 class="card-title fw-bold">Add New Polling Unit Result</h5>
                <span class="badge bg-warning-subtle text-warning mb-3 align-self-start">Question 3</span>
                <p class="card-text text-muted flex-grow-1">Store results for all political parties for a newly created polling unit using a user-friendly entry form.</p>
                <a href="question3.php" class="btn btn-outline-warning w-100 fw-semibold mt-3">
                    Store New Results <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once "footer.php"; ?>
