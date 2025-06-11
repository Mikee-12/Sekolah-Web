<?php
include 'koneksi.php';

// Query untuk mengambil data jurusan
$query = "SELECT * FROM jurusan ORDER BY id_jurusan ASC";
$result = $conn->query($query);
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Data Jurusan - SMKN 6</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Data Jurusan" />
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
        
        .app-footer {
            background: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #eee;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-green));
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(78, 215, 241, 0.1);
        }

        .btn-custom {
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 500;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
            border: none;
            color: white;
        }

        .btn-add:hover {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-green));
            color: white;
        }

        .badge-custom {
            background: linear-gradient(135deg, var(--primary-purple), var(--primary-blue));
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
        }
        
        .app-content-header {
            background: white;
            padding: 15px 0;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .table-header {
            background: var(--primary-purple);
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

        .action-links a {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.875rem;
            margin: 0 2px;
            transition: all 0.2s;
        }

        .delete-link {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }

        .delete-link:hover {
            background-color: #dc3545;
            color: white;
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
              <a href="#" class="nav-link">Home</a>
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
                <span class="d-none d-md-inline">Alexander Pierce</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <li class="user-header text-bg-primary">
                  <img src="dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image" />
                  <p>Alexander Pierce <small>Administrator</small></p>
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

        <main class="app-main pt-4" style="margin-top: 30px;">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Data Jurusan</h3>
                            <p class="text-muted">Kelola data jurusan SMKN 6</p>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="#">Data</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Data Jurusan</li>
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
                                        <h4 class="mb-1"><i class="bi bi-book-half me-2"></i>Daftar Jurusan</h4>
                                        <p class="mb-0 opacity-75">Informasi lengkap data jurusan SMKN 6</p>
                                    </div>
                                    <a href="tambah_jurusan.php" class="btn btn-light">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Jurusan
                                    </a>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">No</th>
                                            <th style="width: 15%;">ID Jurusan</th>
                                            <th style="width: 50%;">Nama Jurusan</th>
                                            <th style="width: 25%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result && $result->num_rows > 0) {
                                            $no = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td class='fw-bold text-muted'>$no</td>
                                                    <td><span class='badge bg-secondary'>".$row["id_jurusan"]."</span></td>
                                                    <td class='fw-semibold'>".$row["nama_jurusan"]."</td>
                                                    <td class='action-links'>
                                                        <a href='#' class='delete-link' title='Hapus Data' onclick='deleteJurusan(".$row["id_jurusan"].", \"".$row["nama_jurusan"]."\")'>
                                                            <i class='bi bi-trash'></i>
                                                        </a>
                                                    </td>
                                                </tr>";
                                                $no++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center text-muted py-4'>
                                                <i class='bi bi-inbox fs-1'></i><br>
                                                Tidak ada data jurusan yang ditemukan
                                            </td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="total-info">
                            <i class="bi bi-book-half me-2"></i>
                            <strong>Total Data: <?php echo $result ? $result->num_rows : 0; ?> jurusan</strong>
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

        // Konfirmasi hapus data
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-danger')) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    // Tambahkan logika hapus data di sini
                    console.log('Data akan dihapus');
                }
            }
        });

        // Notifikasi untuk tombol yang belum memiliki fungsi
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-add') || e.target.closest('.btn-info') || e.target.closest('.btn-warning')) {
                alert('Fitur ini sedang dalam pengembangan');
            }
        });

        function deleteJurusan(id, nama) {
            if (confirm('Yakin ingin menghapus data jurusan "' + nama + '"?')) {
                // Buat AJAX request atau redirect ke delete_jurusan.php
                fetch('delete_jurusan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
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

<?php 
// Tutup koneksi database
if (isset($result)) {
    $result->free();
}
$conn->close(); 
?>