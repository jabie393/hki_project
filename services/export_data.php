<?php
include '../config/config.php';
include '../helpers/base_url_helper.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login");
    exit();
}

$tahun = isset($_GET['tahun_pengajuan']) ? intval($_GET['tahun_pengajuan']) : '';
$filter = '';
if ($tahun) {
    $filter = "AND YEAR(registrations.created_at) = $tahun";
    $filename = $tahun . " rekapitulasi hak cipta UNIRA MALANG.csv";
} else {
    $filename = "Semua Data rekapitulasi hak cipta UNIRA MALANG.csv";
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, [
    'Nomor Pengajuan',
    'Nomor Sertifikat',
    'Username',
    'Tanggal Pengajuan',
    'Judul',
    'Jenis',
    'Sub Jenis',
    'Pencipta',
    'Status',
    'Sertifikat'
]);

$query = "SELECT
            registrations.nomor_pengajuan,
            registrations.nomor_sertifikat,
            users.username,
            registrations.created_at,
            registrations.judul_hak_cipta,
            registrations.jenis_hak_cipta,
            registrations.sub_jenis_hak_cipta,
            registrations.id,
            registrations.status,
            registrations.certificate_path,
            registrations.user_id
        FROM registrations
        JOIN users ON registrations.user_id = users.id
        WHERE registrations.status = 'Terdaftar' $filter
        ORDER BY registrations.created_at DESC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    // Ambil nama pencipta dari tabel creators berdasarkan registration_id
    $pencipta = '';
    $creators = $conn->query("SELECT nama FROM creators WHERE registration_id = '{$row['id']}'");
    $namaArr = [];
    while ($creator = $creators->fetch_assoc()) {
        $namaArr[] = $creator['nama'];
    }
    $pencipta = implode(', ', $namaArr);

    // Sertifikat: tambahkan BASE_URL jika ada certificate_path
    $sertifikat = '';
    if (!empty($row['certificate_path'])) {
        $sertifikat = BASE_URL . '/' . ltrim($row['certificate_path'], '/');
    }

    fputcsv($output, [
        $row['nomor_pengajuan'],
        $row['nomor_sertifikat'],
        $row['username'],
        $row['created_at'],
        $row['judul_hak_cipta'],
        $row['jenis_hak_cipta'],
        $row['sub_jenis_hak_cipta'],
        $pencipta,
        $row['status'],
        $sertifikat
    ]);
}
fclose($output);
exit;