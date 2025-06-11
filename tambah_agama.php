<?php
include 'koneksi.php';

// Proses form jika ada data yang dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_agama = $_POST['id_agama'];
    $nama_agama = $_POST['nama_agama'];
    
    // Validasi ID Agama unique
    $check_id = "SELECT id_agama FROM agama WHERE id_agama = ?";
    $stmt_check = $conn->prepare($check_id);
    $stmt_check->bind_param("i", $id_agama);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        $error_message = "ID Agama sudah terdaftar! Silakan gunakan ID yang berbeda.";
    } else {
        // Insert data ke database
        $sql = "INSERT INTO agama (id_agama, nama_agama) VALUES (?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_agama, $nama_agama);
        
        if ($stmt->execute()) {
            $success_message = "Data agama berhasil ditambahkan!";
            // Redirect setelah 2 detik
            header("refresh:2;url=data_agama.php");
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $stmt_check->close();
}

// Ambil ID agama terakhir untuk suggestion
$sql_last_id = "SELECT MAX(id_agama) as last_id FROM agama";
$result_last_id = $conn->query($sql_last_id);
$last_id = 0;
if ($result_last_id && $result_last_id->num_rows > 0) {
    $row_last_id = $result_last_id->fetch_assoc();
    $last_id = $row_last_id['last_id'] ? $row_last_id['last_id'] : 0;
}
$suggested_id = $last_id + 1;
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Tambah Agama - SMKN 6</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Tambah Agama" />
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
        
        /* Form styling */
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .form-header {
            background: var(--primary-red);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        
        .form-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(78, 215, 241, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background-color: #3bc5e0;
            border-color: #3bc5e0;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: rgba(168, 223, 142, 0.1);
            border-color: var(--primary-green);
            color: #155724;
        }
        
        .alert-danger {
            background-color: rgba(250, 112, 112, 0.1);
            border-color: var(--primary-red);
            color: #721c24;
        }
        
        .required {
            color: var(--primary-red);
        }
        
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
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
            .form-body {
                padding: 20px;
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
            <span class="brand-text fw-light">Data Sekolah</span>
          </a>
        </div>
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                <a href="index.php" class="nav-link active">
                  <i class="nav-icon bi bi-speedometer2"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-table"></i>
                  <p>Data<i class="nav-arrow bi bi-chevron-right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link">
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
                  <i class="nav-icon bi bi-plus"></i>
                  <p>Tambah Data<i class="nav-arrow bi bi-chevron-right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="tambah_siswa.php" class="nav-link">
                      <i class="nav-icon bi bi-people-fill"></i>
                      <p>Tambah Data Siswa</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="tambah_agama.php" class="nav-link">
                      <i class="nav-icon bi bi-journal-bookmark-fill"></i>
                      <p>Tambah Agama</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="tambah_jurusan.php" class="nav-link">
                      <i class="nav-icon bi bi-book-half"></i>
                      <p>Tambah Jurusan</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
      </aside>

        <!-- Main Content -->
        <main class="app-main pt-4" style="margin-top: 56px;">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Tambah Agama</h3>
                            <p class="text-muted">Tambah data agama baru ke sistem</p>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="data_agama.php">Data Agama</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Tambah Agama</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Alert Messages -->
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="form-container">
                            <div class="form-header">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-journal-bookmark-fill me-3 fs-4"></i>
                                    <div>
                                        <h4 class="mb-1">Form Tambah Agama</h4>
                                        <p class="mb-0 opacity-75">Lengkapi data agama dengan benar</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-body">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_agama" class="form-label">ID Agama <span class="required">*</span></label>
                                                <input type="number" class="form-control" id="id_agama" name="id_agama" min="1" max="99" value="<?php echo $suggested_id; ?>" required>
                                                <div class="form-text">ID unik untuk agama (1-99). Suggestion: <?php echo $suggested_id; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nama_agama" class="form-label">Nama Agama <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="nama_agama" name="nama_agama" maxlength="20" placeholder="Contoh: Islam, Kristen, dll" required>
                                                <div class="form-text">Maksimal 20 karakter</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-2"></i>Simpan Data
                                        </button>
                                        <a href="data_agama.php" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>Kembali
                                        </a>
                                    </div>
                                </form>
                            </div>
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

            // Form validation
            const form = document.querySelector('form');
            const idAgamaInput = document.getElementById('id_agama');
            const namaAgamaInput = document.getElementById('nama_agama');

            // ID Agama validation (hanya angka)
            idAgamaInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value > 99) this.value = 99;
                if (this.value < 1 && this.value !== '') this.value = 1;
            });

            // Nama Agama validation (kapitalkan huruf pertama)
            namaAgamaInput.addEventListener('blur', function() {
                if (this.value) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
                }
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                const idAgama = parseInt(idAgamaInput.value);
                const namaAgama = namaAgamaInput.value.trim();

                if (idAgama < 1 || idAgama > 99) {
                    e.preventDefault();
                    alert('ID Agama harus antara 1-99');
                    idAgamaInput.focus();
                    return;
                }

                if (namaAgama.length < 3) {
                    e.preventDefault();
                    alert('Nama Agama minimal 3 karakter');
                    namaAgamaInput.focus();
                    return;
                }
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>