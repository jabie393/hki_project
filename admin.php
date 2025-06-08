<!-- Flow FE -->
<!-- ADMIN -->
<?php
include 'config/config.php';
session_start();

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login");
    exit();
}

// Ambil total pengajuan yang belum disetujui
$totalPendingQuery = "SELECT COUNT(*) as total FROM registrations WHERE status != 'Terdaftar'";
$totalPendingResult = $conn->query($totalPendingQuery);
$totalPending = $totalPendingResult->fetch_assoc()['total'];

// Ambil total hak cipta terdaftar
$totalRegisteredQuery = "SELECT COUNT(*) as total FROM registrations WHERE status = 'Terdaftar'";
$totalRegisteredResult = $conn->query($totalRegisteredQuery);
$totalRegistered = $totalRegisteredResult->fetch_assoc()['total'];

// Ambil data untuk grafik bar (jumlah hak cipta terdaftar per tahun)
$chartTahunQuery = "SELECT YEAR(created_at) as tahun, COUNT(*) as jumlah 
                    FROM registrations 
                    WHERE status = 'Terdaftar' 
                    GROUP BY YEAR(created_at)";
$chartTahunResult = $conn->query($chartTahunQuery);
$chartTahunLabels = [];
$chartTahunData = [];
while ($row = $chartTahunResult->fetch_assoc()) {
    $chartTahunLabels[] = $row['tahun'];
    $chartTahunData[] = $row['jumlah'];
}

// Ambil data untuk grafik pie (distribusi jenis hak cipta)
$chartJenisQuery = "SELECT jenis_hak_cipta, COUNT(*) as jumlah FROM registrations GROUP BY jenis_hak_cipta";
$chartJenisResult = $conn->query($chartJenisQuery);
$chartJenisLabels = [];
$chartJenisData = [];
while ($row = $chartJenisResult->fetch_assoc()) {
    $chartJenisLabels[] = $row['jenis_hak_cipta'];
    $chartJenisData[] = $row['jumlah'];
}
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>

<div id="admin-page">
    <div class="layout">
        <!-- Kiri -->
        <div class="left-column boxl">
            <!-- Card Total -->
            <div class="stat-card-left">
                <h1 id="registeredCount"><?php echo $totalRegistered; ?></h1>
                <p>Total Hak Cipta Terdaftar</p>
            </div>

            <!-- Bar Chart -->
            <div class="barbox">
                <h3>Hak Cipta Terdaftar per Tahun</h3>
                <canvas id="chartTahun"></canvas>
            </div>
        </div>

        <!-- Kanan -->
        <div class="right-column">
            <!-- Card Total -->
            <div class="box stat-card-right">
                <h1 id="pendingCount"><?php echo $totalPending; ?></h1>
                <p>Pengajuan Belum Disetujui</p>
            </div>

            <!-- Pie Chart -->
            <div class="box">
                <h3>Distribusi Jenis Hak Cipta</h3>
                <canvas id="chartJenis"></canvas>
            </div>
        </div>
    </div>

    <script>
        function initAdminPage() {
            // Data untuk grafik bar (Jumlah Hak Cipta per Tahun)
            const chartTahunLabels = <?php echo json_encode($chartTahunLabels); ?>;
            const chartTahunData = <?php echo json_encode($chartTahunData); ?>;

            new Chart(document.getElementById('chartTahun'), {
                type: 'bar',
                data: {
                    labels: chartTahunLabels,
                    datasets: [{
                        label: 'Jumlah',
                        data: chartTahunData,
                        backgroundColor: '#004080b7',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Data untuk grafik pie (Distribusi Jenis Hak Cipta)
            const chartJenisLabels = <?php echo json_encode($chartJenisLabels); ?>;
            const chartJenisData = <?php echo json_encode($chartJenisData); ?>;

            new Chart(document.getElementById('chartJenis'), {
                type: 'pie',
                data: {
                    labels: chartJenisLabels,
                    datasets: [{
                        data: chartJenisData,
                        backgroundColor: [
                            '#0074D9', '#FF4136', '#2ECC40', '#FF851B',
                            '#B10DC9', '#FFDC00', '#39CCCC', '#AAAAAA'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }
    </script>
</div>