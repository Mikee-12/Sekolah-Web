<?php
include 'koneksi.php';

// Ambil parameter filter dan search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_tahun = isset($_GET['tahun_masuk']) ? $_GET['tahun_masuk'] : '';
$filter_jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';
$filter_agama = isset($_GET['agama']) ? $_GET['agama'] : '';

// Query untuk mendapatkan data filter
$tahun_query = "SELECT DISTINCT tahun_masuk FROM siswa ORDER BY tahun_masuk DESC";
$tahun_result = $conn->query($tahun_query);

$jurusan_query = "SELECT * FROM jurusan ORDER BY nama_jurusan";
$jurusan_result = $conn->query($jurusan_query);

$agama_query = "SELECT * FROM agama ORDER BY nama_agama";
$agama_result = $conn->query($agama_query);

// Build query dengan filter dan search
$sql = "SELECT s.*, j.nama_jurusan, a.nama_agama 
        FROM siswa s 
        LEFT JOIN jurusan j ON s.id_jurusan = j.id_jurusan 
        LEFT JOIN agama a ON s.id_agama = a.id_agama
        WHERE 1=1";

// Tambahkan kondisi search
if (!empty($search)) {
    $search_escaped = $conn->real_escape_string($search);
    $sql .= " AND (s.nama LIKE '%$search_escaped%' 
              OR s.nisn LIKE '%$search_escaped%' 
              OR s.alamat LIKE '%$search_escaped%' 
              OR s.no_hp LIKE '%$search_escaped%')";
}

// Tambahkan filter tahun masuk
if (!empty($filter_tahun)) {
    $sql .= " AND s.tahun_masuk = '$filter_tahun'";
}

// Tambahkan filter jurusan
if (!empty($filter_jurusan)) {
    $sql .= " AND s.id_jurusan = '$filter_jurusan'";
}

// Tambahkan filter agama
if (!empty($filter_agama)) {
    $sql .= " AND s.id_agama = '$filter_agama'";
}

$sql .= " ORDER BY s.id";
$result = $conn->query($sql);

?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Data Siswa - SMKN 6</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Data Siswa" />
    <meta name="author" content="SMKN 6" />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="dist/css/adminlte.css" />

    <style>
        :root {
            --primary-blue: #4ED7F1;
            --primary-green: #A8DF8E;
            --primary-red: #FA7070;
            --primary-yellow: #FFD66B;
            --primary-purple: #9D76C1;
        }
        
        .app-header.navbar {
            width: 100%;
            left: 0;
            position: fixed;
            top: 0;
            transition: width 0.3s ease, left 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1030;
        }
        
        .app-header.navbar.sidebar-open {
            width: calc(100% - 250px);
            left: 250px;
        }
        
        .app-sidebar {
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .nav-sidebar .nav-item > .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            border-left: 3px solid var(--primary-blue);
        }
        
        .app-content-header {
            background: white;
            padding: 15px 0;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .app-footer {
            background: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #eee;
        }
        
        /* Table styling */
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .table-header {
            background: var(--primary-blue);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        
        .table-responsive {
            border-radius: 0 0 10px 10px;
            overflow: hidden;
        }
        
        table {
            margin-bottom: 0;
        }
        
        th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn-primary:hover {
            background-color: #3bc5e0;
            border-color: #3bc5e0;
        }
        
        .action-links a {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.875rem;
            margin: 0 2px;
            transition: all 0.2s;
        }
        
        .edit-link {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .edit-link:hover {
            background-color: #28a745;
            color: white;
        }
        
        .delete-link {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .delete-link:hover {
            background-color: #dc3545;
            color: white;
        }
        
        .badge {
            font-size: 0.8rem;
        }
        
        .total-info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-top: 20px;
            text-align: center;
            color: #666;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .app-header.navbar.sidebar-open {
                width: 100%;
                left: 0;
            }
            .app-main {
                margin-left: 0 !important;
            }
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            .action-links a {
                display: block;
                margin: 2px 0;
                text-align: center;
            }
        }

        .filter-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0;
}

.filter-section .form-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
}

.filter-section .form-control,
.filter-section .form-select {
    border: 1px solid #ced4da;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.filter-section .form-control:focus,
.filter-section .form-select:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 0.2rem rgba(78, 215, 241, 0.25);
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Responsive untuk mobile */
@media (max-width: 768px) {
    .filter-section .col-md-2,
    .filter-section .col-md-4 {
        margin-bottom: 1rem;
    }
    
    .filter-section .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .filter-section .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Navbar -->
        <nav class="app-header navbar navbar-expand bg-body fixed-top sidebar-open">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">Home</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                            <i class="bi bi-search"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image" />
                            <span class="d-none d-md-inline">Administrator</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image" />
                                <p>Administrator <small>SMKN 6</small></p>
                            </li>
                            <li class="user-footer">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                                <a href="#" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="index.php" class="brand-link">
                    <img src="dist/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
                    <span class="brand-text fw-light">Sistem Data Siswa</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item menu-open">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-table"></i>
                                <p>Data<i class="nav-arrow bi bi-chevron-right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="data_siswa.php" class="nav-link active">
                                        <i class="nav-icon bi bi-people-fill"></i>
                                        <p>Data Siswa</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="data_agama.php" class="nav-link">
                                        <i class="nav-icon bi bi-journal-bookmark-fill"></i>
                                        <p>Agama</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="data_jurusan.php" class="nav-link">
                                        <i class="nav-icon bi bi-book-half"></i>
                                        <p>Jurusan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-file-earmark-text"></i>
                                <p>Laporan</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main pt-4" style="margin-top: 30px;">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Data Siswa</h3>
                            <p class="text-muted">Kelola data siswa SMKN 6</p>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="#">Data</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Data Siswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="table-container">
                            <div class="table-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-1"><i class="bi bi-people-fill me-2"></i>Daftar Siswa</h4>
                                        <p class="mb-0 opacity-75">Informasi lengkap data siswa SMKN 6</p>
                                    </div>
                                    <a href="tambah_siswa.php" class="btn btn-light">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Siswa
                                    </a>
                                </div>
                            </div>

                            <div class="filter-section" style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
    <form method="GET" action="" class="row g-3">
        <!-- Search Box -->
        <div class="col-md-4">
            <label for="search" class="form-label">
                <i class="bi bi-search"></i> Pencarian
            </label>
            <input type="text" 
                   class="form-control" 
                   id="search" 
                   name="search" 
                   placeholder="Cari nama, NISN, alamat, atau no HP..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <!-- Filter Tahun Masuk -->
        <div class="col-md-2">
            <label for="tahun_masuk" class="form-label">
                <i class="bi bi-calendar"></i> Tahun Masuk
            </label>
            <select class="form-select" id="tahun_masuk" name="tahun_masuk">
                <option value="">Semua Tahun</option>
                <?php
                if ($tahun_result->num_rows > 0) {
                    while ($tahun = $tahun_result->fetch_assoc()) {
                        $selected = ($filter_tahun == $tahun['tahun_masuk']) ? 'selected' : '';
                        echo "<option value='" . $tahun['tahun_masuk'] . "' $selected>" . $tahun['tahun_masuk'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        
        <!-- Filter Jurusan -->
        <div class="col-md-2">
            <label for="jurusan" class="form-label">
                <i class="bi bi-book"></i> Jurusan
            </label>
            <select class="form-select" id="jurusan" name="jurusan">
                <option value="">Semua Jurusan</option>
                <?php
                if ($jurusan_result->num_rows > 0) {
                    while ($jurusan = $jurusan_result->fetch_assoc()) {
                        $selected = ($filter_jurusan == $jurusan['id_jurusan']) ? 'selected' : '';
                        echo "<option value='" . $jurusan['id_jurusan'] . "' $selected>" . $jurusan['nama_jurusan'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        
        <!-- Filter Agama -->
        <div class="col-md-2">
            <label for="agama" class="form-label">
                <i class="bi bi-journal-bookmark"></i> Agama
            </label>
            <select class="form-select" id="agama" name="agama">
                <option value="">Semua Agama</option>
                <?php
                if ($agama_result->num_rows > 0) {
                    while ($agama = $agama_result->fetch_assoc()) {
                        $selected = ($filter_agama == $agama['id_agama']) ? 'selected' : '';
                        echo "<option value='" . $agama['id_agama'] . "' $selected>" . $agama['nama_agama'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        
        <!-- Tombol Filter -->
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="?" class="btn btn-outline-secondary" title="Reset Filter">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>
    
    <!-- Info Filter Aktif -->
    <?php if (!empty($search) || !empty($filter_tahun) || !empty($filter_jurusan) || !empty($filter_agama)): ?>
    <div class="mt-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted">Filter aktif:</span>
            <?php if (!empty($search)): ?>
                <span class="badge bg-info">Pencarian: "<?php echo htmlspecialchars($search); ?>"</span>
            <?php endif; ?>
            <?php if (!empty($filter_tahun)): ?>
                <span class="badge bg-primary">Tahun: <?php echo $filter_tahun; ?></span>
            <?php endif; ?>
            <?php if (!empty($filter_jurusan)): ?>
                <?php
                // Cari nama jurusan
                $jurusan_result->data_seek(0); 
                while ($jur = $jurusan_result->fetch_assoc()) {
                    if ($jur['id_jurusan'] == $filter_jurusan) {
                        echo "<span class='badge bg-success'>Jurusan: " . $jur['nama_jurusan'] . "</span>";
                        break;
                    }
                }
                ?>
            <?php endif; ?>
            <?php if (!empty($filter_agama)): ?>
                <?php
                // Cari nama agama
                $agama_result->data_seek(0);
                while ($ag = $agama_result->fetch_assoc()) {
                    if ($ag['id_agama'] == $filter_agama) {
                        echo "<span class='badge bg-warning'>Agama: " . $ag['nama_agama'] . "</span>";
                        break;
                    }
                }
                ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 10%;">NISN</th>
                                            <th style="width: 15%;">Nama</th>
                                            <th style="width: 10%;">Tgl Lahir</th>
                                            <th style="width: 8%;">JK</th>
                                            <th style="width: 10%;">Agama</th>
                                            <th style="width: 12%;">Jurusan</th>
                                            <th style="width: 8%;">Angkatan</th>
                                            <th style="width: 15%;">Alamat</th>
                                            <th style="width: 10%;">No HP</th>
                                            <th style="width: 12%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            $no = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                // Konversi jenis kelamin
                                                $jenis_kelamin = ($row["jenis_kelamin"] == "L") ? "L" : "P";
                                                $jk_badge = ($row["jenis_kelamin"] == "L");
                                                
                                                // Format tanggal lahir
                                                $tgl_lahir = date('d/m/Y', strtotime($row["tgl_lahir"]));
                                                
                                                // Gunakan nama dari JOIN atau fallback jika NULL
                                                $nama_agama = $row["nama_agama"] ? $row["nama_agama"] : "N/A";
                                                $nama_jurusan = $row["nama_jurusan"] ? $row["nama_jurusan"] : "N/A";
                                                
                                                // Potong alamat jika terlalu panjang
                                                $alamat = $row["alamat"] ? 
                                                    (strlen($row["alamat"]) > 30 ? 
                                                        substr($row["alamat"], 0, 30) . "..." : 
                                                        $row["alamat"]) : "-";
                                                
                                                echo "<tr>
                                                    <td class='fw-bold text-muted'>$no</td>
                                                    <td><span class='badge bg-secondary'>".$row["nisn"]."</span></td>
                                                    <td class='fw-semibold'>".$row["nama"]."</td>
                                                    <td><small>".$tgl_lahir."</small></td>
                                                    <td><span class='$jk_badge'>".$jenis_kelamin."</span></td>
                                                    <td><small>".$nama_agama."</small></td>
                                                    <td><strong>".$nama_jurusan."</strong></td>
                                                    <td class='text-center'>".$row["tahun_masuk"]."</td>
                                                    <td><small title='".$row["alamat"]."'>".$alamat."</small></td>
                                                    <td><small>".($row["no_hp"] ? $row["no_hp"] : "-")."</small></td>
                                                    <td class='action-links'>
                                                        <a href='edit_siswa.php?id=".$row["id"]."' class='edit-link' title='Edit Data'>
                                                            <i class='bi bi-pencil-square'></i>
                                                        </a>
                                                        <a href='#' class='delete-link' title='Hapus Data' onclick='deleteStudent(".$row["id"].", \"".$row["nama"]."\")'>
                                                            <i class='bi bi-trash'></i>
                                                        </a>
                                                    </td>
                                                </tr>";
                                                $no++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='11' class='text-center text-muted py-4'>
                                                <i class='bi bi-inbox fs-1'></i><br>
                                                Tidak ada data siswa yang ditemukan
                                            </td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="total-info">
                            <i class="bi bi-people-fill me-2"></i>
                            <strong>Total Data: <?php echo $result->num_rows; ?> siswa</strong>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">SMKN 6 - Sistem Informasi Sekolah</div>
            <strong>Copyright &copy; <?php echo date('Y'); ?></strong>
        </footer>
    </div>

    <!-- Scripts -->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="dist/js/adminlte.js"></script>
    
    <script>
        // Sidebar toggle functionality
        document.addEventListener("DOMContentLoaded", function () {
            const sidebarToggleBtn = document.querySelector('[data-lte-toggle="sidebar"]');
            const navbar = document.querySelector('.app-header.navbar');

            if (sidebarToggleBtn && navbar) {
                sidebarToggleBtn.addEventListener('click', function () {
                    const body = document.body;
                    if (body.classList.contains('sidebar-collapse')) {
                        navbar.classList.add('sidebar-open');
                    } else {
                        navbar.classList.remove('sidebar-open');
                    }
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
    const autoSubmitSelects = document.querySelectorAll('#tahun_masuk, #jurusan, #agama');
    
    autoSubmitSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            // Uncomment baris di bawah jika ingin auto submit
            // this.form.submit();
        });
    });
    
    // Enter key untuk search
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});
// Fungsi untuk delete siswa dengan AJAX
function deleteStudent(id, nama) {
    if (confirm('Yakin ingin menghapus data siswa "' + nama + '"?')) {
        // Tampilkan loading
        const loadingHtml = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><br><small>Menghapus data...</small></div>';
        
        // Buat AJAX request
        fetch('delete_siswa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan pesan sukses
                showAlert('success', data.message);
                
                // Reload halaman setelah 1.5 detik
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                // Tampilkan pesan error
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat menghapus data!');
        });
    }
}

// Fungsi untuk menampilkan alert
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${iconClass} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Cari container untuk alert atau buat baru
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '70px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        alertContainer.style.maxWidth = '400px';
        document.body.appendChild(alertContainer);
    }
    
    alertContainer.innerHTML = alertHtml;
    
    // Auto hide setelah 3 detik
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 150);
        }
    }, 3000);
}
    </script>
</body>
</html>

<?php $conn->close(); ?>