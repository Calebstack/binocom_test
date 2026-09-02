<?php
if (!isset($page_title)) {
    $page_title = "Delta State 2011 Election Results";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Bincom Elections</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --dark-color: #1e293b;
            --bg-light: #f8fafc;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
            font-weight: 600;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        }
        .party-badge {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.4em 0.8em;
            border-radius: 0.5rem;
        }
        .stat-card {
            border-left: 4px solid var(--primary-color);
        }
        .main-content {
            flex: 1;
        }
        footer {
            background-color: #1e293b;
            color: #94a3b8;
            padding: 1.5rem 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <i class="bi bi-box-seam-fill text-primary fs-4"></i>
            <span>BINCOM <span class="text-primary">ELECTIONS</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house-door me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'question1.php') ? 'active' : ''; ?>" href="question1.php">
                        <i class="bi bi-geo-alt me-1"></i> Polling Unit Results (Q1)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'question2.php') ? 'active' : ''; ?>" href="question2.php">
                        <i class="bi bi-pie-chart me-1"></i> LGA Summed Results (Q2)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'question3.php') ? 'active' : ''; ?>" href="question3.php">
                        <i class="bi bi-plus-circle me-1"></i> Store PU Results (Q3)
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container main-content">
