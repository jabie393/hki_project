<?php
include '../config/config.php';
date_default_timezone_set('Asia/Jakarta');

// Biarkan script bisa dipanggil dari browser atau CLI
if (php_sapi_name() === 'cli') {
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

// Proteksi akses kalau via browser
if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['key']) || $_GET['key'] !== 'hapus7hari') {
        http_response_code(403);
        exit("[ERROR] Unauthorized.\n");
    }
    header('Content-Type: text/plain');
}

// Fungsi hapus folder
function deleteFolder($folder_path)
{
    if (!is_dir($folder_path)) return;

    $files = array_diff(scandir($folder_path), ['.', '..']);
    foreach ($files as $file) {
        $file_path = $folder_path . DIRECTORY_SEPARATOR . $file;
        is_dir($file_path) ? deleteFolder($file_path) : unlink($file_path);
    }
    rmdir($folder_path);
}

// Hitung batas waktu: 7 hari lalu
$limit = date('Y-m-d H:i:s', strtotime('-7 days'));
echo "[LOG] Batas waktu: $limit\n";

// Ambil data pengajuan ditolak
$sql = "SELECT id, user_id FROM registrations WHERE status = 'Ditolak' AND rejected_at <= ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("[ERROR] Prepare gagal: " . $conn->error . "\n");
}

$stmt->bind_param("s", $limit);
$stmt->execute();
$result = $stmt->get_result();
$total_deleted = 0;

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $user_id = $row['user_id'];
    echo "[LOG] Menghapus ID $id milik user $user_id...\n";

    // Hapus dokumen
    $doc_stmt = $conn->prepare("SELECT file_path FROM documents WHERE registration_id = ?");
    if ($doc_stmt) {
        $doc_stmt->bind_param("i", $id);
        $doc_stmt->execute();
        $doc_result = $doc_stmt->get_result();
        while ($doc = $doc_result->fetch_assoc()) {
            if ($doc['file_path'] && file_exists($doc['file_path'])) {
                unlink($doc['file_path']);
                echo "[LOG] File dihapus: " . $doc['file_path'] . "\n";
            }
        }
        $doc_stmt->close();
    }

    $del_doc_stmt = $conn->prepare("DELETE FROM documents WHERE registration_id = ?");
    if ($del_doc_stmt) {
        $del_doc_stmt->bind_param("i", $id);
        $del_doc_stmt->execute();
        $del_doc_stmt->close();
    }

    // Hapus folder upload
    $folder_path = "../uploads/users/$user_id/files/$id/";
    deleteFolder($folder_path);
    echo "[LOG] Folder dihapus: $folder_path\n";

    // Hapus data registrasi
    $del_reg_stmt = $conn->prepare("DELETE FROM registrations WHERE id = ?");
    if ($del_reg_stmt) {
        $del_reg_stmt->bind_param("i", $id);
        $del_reg_stmt->execute();
        $del_reg_stmt->close();
        echo "[LOG] Registrasi ID $id dihapus.\n";
    }

    $total_deleted++;
}

echo "[SELESAI] Total pengajuan dihapus: $total_deleted\n";

// Logging opsional ke file
file_put_contents('/home/fahk3771/cron_log.txt', "[" . date("Y-m-d H:i:s") . "] Dihapus: $total_deleted\n", FILE_APPEND);

$stmt->close();
$conn->close();


// == Jika via url : wget -qO- "https://domain.com/services/cron_delete.php?key=hapus7hari" == //
