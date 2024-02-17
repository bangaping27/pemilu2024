<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Pemilihan Umum</title>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>

<h1>Informasi Hasil PEMILU PRESIDEN & WAKIL PRESIDEN RI 2024</h1>
<div style="margin-top: 20px;">
    <h3>Keterangan Warna:</h3>
    <ul>
        <li><span style="color: red;">Merah</span>: Wilayah dimenangkan oleh Pasangan Calon 01</li>
        <li><span style="color: blue;">Biru</span>: Wilayah dimenangkan oleh Pasangan Calon 02</li>
        <li><span style="color: green;">Hijau</span>: Wilayah dimenangkan oleh Pasangan Calon 03</li>
    </ul>
    <p>Data diproses secara otomatis</p>
    <p>Sumber data: <a href="https://sirekap-obj-data.kpu.go.id">sirekap-obj-data.kpu.go.id</a></p>
    <p>Data Pemilu: <a href="https://sirekap-obj-data.kpu.go.id/pemilu/hhcw/ppwp.json">https://sirekap-obj-data.kpu.go.id/pemilu/hhcw/ppwp.json</a></p>
    <p>Data Wilayah: <a href="https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/0.json">https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/0.json</a></p>
</div>

</body>
</html>

<?php

// Inisialisasi array untuk menyimpan informasi pasangan calon yang unggul di setiap wilayah
$unggul_per_wilayah = array(
    '01' => array('total_suara' => 0, 'wilayah' => array(), 'total_wilayah' => 0),
    '02' => array('total_suara' => 0, 'wilayah' => array(), 'total_wilayah' => 0),
    '03' => array('total_suara' => 0, 'wilayah' => array(), 'total_wilayah' => 0)
);

// Mendapatkan data JSON dari URL dengan kode wilayah ACEH ("11")
$url = 'https://sirekap-obj-data.kpu.go.id/pemilu/hhcw/ppwp.json';
$json_data = file_get_contents($url);

if ($json_data !== false) {
    // Decode JSON
    $data = json_decode($json_data, true);

    // Batas nilai curang
    $batasCurang = 300;

    // Iterasi melalui data dan menampilkan baris tabel
    foreach ($data['table'] as $wilayah => $detail) {
        // Menghitung total suara untuk semua pasangan calon
        $total_suara = 0;
        foreach ($detail as $key => $value) {
            if (strpos($key, '100') === 0) {
                $total_suara += $value;
            }
        }

        // Menambahkan total suara ke masing-masing pasangan calon
        $unggul_per_wilayah['01']['total_suara'] += $detail['100025'] ?? 0;
        $unggul_per_wilayah['02']['total_suara'] += $detail['100026'] ?? 0;
        $unggul_per_wilayah['03']['total_suara'] += $detail['100027'] ?? 0;

        // Jika total suara untuk semua paslon adalah 0, maka skip baris ini
        if ($total_suara === 0) {
            continue;
        }

        // Memeriksa apakah kunci ada sebelum mengakses nilainya
        $suara01 = $detail['100025'] ?? 0;
        $suara02 = $detail['100026'] ?? 0;
        $suara03 = $detail['100027'] ?? 0;

        // Menentukan suara terbanyak di antara ketiga pasangan calon
        $terbanyak = max($suara01, $suara02, $suara03);

        // Menentukan pasangan calon yang unggul
        $unggul = '';
        if ($terbanyak === $suara01) {
            $unggul = '01';
        } elseif ($terbanyak === $suara02) {
            $unggul = '02';
        } elseif ($terbanyak === $suara03) {
            $unggul = '03';
        }

        // Menyimpan informasi pasangan calon yang unggul di wilayah ini
        $unggul_per_wilayah[$unggul]['wilayah'][] = $wilayah;
        $unggul_per_wilayah[$unggul]['total_wilayah']++;
    }

    // Menampilkan hasil informasi pasangan calon yang unggul di setiap wilayah
    echo '<h2>Informasi Pasangan Calon yang Unggul di Setiap Wilayah:</h2>';
    foreach ($unggul_per_wilayah as $pasangan => $info) {
        echo '<p>Pasangan calon ' . $pasangan . ' unggul dengan total suara ' . $info['total_suara'] . ' di ' . $info['total_wilayah'] . ' wilayah: ' . implode(', ', $info['wilayah']) . '</p>';
    }
} else {
    echo '<p>Gagal mengambil data dari URL yang diberikan.</p>';
}

// Mendapatkan data JSON dari URL
$url = 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/0.json';
$json_data = file_get_contents($url);

if ($json_data !== false) {
    // Decode JSON
    $data = json_decode($json_data, true);

    // Cek apakah data berhasil di-decode
    if ($data !== null) {
        echo '<h2>Data Wilayah:</h2>';
        echo '<table>';
        echo '<tr><th>Nama</th><th>ID</th><th>Kode</th><th>Tingkat</th></tr>';
        
        // Iterasi melalui data untuk menampilkan setiap entri dalam tabel
        foreach ($data as $entry) {
            echo '<tr style="color:';
            // Ubah warna teks berdasarkan pasangan calon yang unggul
            if (in_array($entry['kode'], $unggul_per_wilayah['01']['wilayah'])) {
                echo 'red';
            } elseif (in_array($entry['kode'], $unggul_per_wilayah['02']['wilayah'])) {
                echo 'blue';
            } elseif (in_array($entry['kode'], $unggul_per_wilayah['03']['wilayah'])) {
                echo 'green';
            }
            echo '">';
            echo '<td>' . $entry['nama'] . '</td>';
            echo '<td>' . $entry['id'] . '</td>';
            echo '<td>' . $entry['kode'] . '</td>';
            echo '<td>' . $entry['tingkat'] . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    } else {
        echo '<p>Gagal mengurai data JSON.</p>';
    }
} else {
    echo '<p>Gagal mengambil data dari URL yang diberikan.</p>';
}
?>

