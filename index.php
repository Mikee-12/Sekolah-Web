<?php
include 'koneksi.php';

// Query untuk mengambil total siswa
$sql_total_siswa = "SELECT COUNT(*) as total_siswa FROM siswa";
$result_siswa = $conn->query($sql_total_siswa);
$total_siswa = 0;

if ($result_siswa->num_rows > 0) {
    $row_siswa = $result_siswa->fetch_assoc();
    $total_siswa = $row_siswa['total_siswa'];
}

// Query untuk data chart - siswa per tahun masuk (10 tahun terakhir)
$current_year = date('Y');
$sql_chart = "SELECT tahun_masuk, COUNT(*) as jumlah 
              FROM siswa 
              WHERE tahun_masuk >= ($current_year - 9) 
              GROUP BY tahun_masuk 
              ORDER BY tahun_masuk";
$result_chart = $conn->query($sql_chart);

$chart_data = [];
$chart_labels = [];

// Buat array untuk 10 tahun terakhir dengan nilai 0 sebagai default
for ($i = 9; $i >= 0; $i--) {
    $year = $current_year - $i;
    $chart_labels[] = $year;
    $chart_data[$year] = 0;
}

// Isi data dari database
if ($result_chart->num_rows > 0) {
    while ($row = $result_chart->fetch_assoc()) {
        $chart_data[$row['tahun_masuk']] = (int)$row['jumlah'];
    }
}

// Convert ke array berurutan untuk JavaScript
$final_chart_data = [];
foreach ($chart_labels as $year) {
    $final_chart_data[] = $chart_data[$year];
}

// Query untuk data pie chart - distribusi jurusan
$sql_jurusan = "SELECT j.nama_jurusan, COUNT(s.id) as jumlah 
                FROM jurusan j 
                LEFT JOIN siswa s ON j.id_jurusan = s.id_jurusan 
                GROUP BY j.id_jurusan, j.nama_jurusan 
                ORDER BY jumlah DESC";
$result_jurusan = $conn->query($sql_jurusan);

$jurusan_labels = [];
$jurusan_data = [];

if ($result_jurusan->num_rows > 0) {
    while ($row = $result_jurusan->fetch_assoc()) {
        $jurusan_labels[] = $row['nama_jurusan'];
        $jurusan_data[] = (int)$row['jumlah'];
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Data SMKN 6</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Dashboard" />
    <meta name="author" content="ColorlibHQ" />

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
      
      .chart-container {
        position: relative;
        width: 100%;
        height: 400px;
        margin-top: 30px;
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
      
      /* Enhanced Small Box Styles */
      .small-box {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
      }
      
      .small-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      }
      
      .small-box .inner {
        padding: 20px;
        position: relative;
        z-index: 2;
      }
      
      .small-box h3 {
        font-weight: 700;
        margin-bottom: 5px;
      }
      
      .small-box p {
        font-size: 1rem;
        opacity: 0.9;
      }
      
      /* Add icon to small boxes */
      .small-box .icon {
        position: absolute;
        right: 20px;
        top: 20px;
        font-size: 70px;
        opacity: 0.2;
        transition: all 0.3s ease;
      }
      
      .small-box:hover .icon {
        opacity: 0.4;
        transform: scale(1.1);
      }
      
      /* Chart enhancements */
      #myBarChart {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      }
      
      /* Sidebar enhancements */
      .app-sidebar {
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
      }
      
      .nav-sidebar .nav-item > .nav-link.active {
        background-color: rgba(255,255,255,0.1);
        border-left: 3px solid var(--primary-blue);
      }
      
      /* Content header */
      .app-content-header {
        background: white;
        padding: 15px 0;
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      }
      
      /* Footer */
      .app-footer {
        background: #f8f9fa;
        padding: 15px;
        border-top: 1px solid #eee;
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
        .small-box .inner h3 {
          font-size: 1.5rem;
        }
      }
      
      @media (max-width: 768px) {
        .small-box .inner h3 {
          font-size: 1.3rem;
        }
        .small-box .inner p {
          font-size: 0.9rem;
        }
        .chart-container {
          height: 300px;
        }
      }
      
      @media (max-width: 576px) {
        .small-box .inner h3 {
          font-size: 1.1rem;
        }
        .app-content-header h3 {
          font-size: 1.2rem;
        }
        .breadcrumb {
          font-size: 0.8rem;
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
            <img src="dist/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">Sistem Data Siswa</span>
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
                <h3 class="mb-0">Dashboard</h3>
                <p class="text-muted">Overview Sistem Data Sekolah</p>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-3">
          <div class="row">
            <div class="col-lg-3 col-md-6 col-6">
              <div class="small-box" style="background-color: var(--primary-blue)">
                <div class="inner">
                  <h3 id="current-year-students"><?php echo $total_siswa; ?></h3>
                  <p>Total Siswa</p>
                </div>
                <div class="icon">
                  <i class="bi bi-people-fill"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
              <div class="small-box" style="background-color: var(--primary-green)">
                <div class="inner">
                  <h3>24</h3>
                  <p>Kelas</p>
                </div>
                <div class="icon">
                  <i class="bi bi-door-open"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
              <div class="small-box" style="background-color: var(--primary-red)">
                <div class="inner">
                  <h3><?php echo count($jurusan_labels); ?></h3>
                  <p>Jurusan</p>
                </div>
                <div class="icon">
                  <i class="bi bi-book-half"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
              <div class="small-box" style="background-color: var(--primary-yellow)">
                <div class="inner">
                  <h3>45</h3>
                  <p>Guru & Staff</p>
                </div>
                <div class="icon">
                  <i class="bi bi-person-badge"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-lg-8">
              <div class="card shadow-sm">
                <div class="card-header">
                  <h3 class="card-title">Siswa per Tahun Masuk (10 Tahun Terakhir)</h3>
                </div>
                <div class="card-body">
                  <div class="chart-container">
                    <canvas id="myBarChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-lg-4">
              <div class="card shadow-sm">
                <div class="card-header">
                  <h3 class="card-title">Distribusi Jurusan</h3>
                </div>
                <div class="card-body">
                  <div class="chart-container">
                    <canvas id="myPieChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      <!-- Footer -->
      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">SMKN 6 - Sistem Informasi Sekolah</div>
        <strong>Copyright &copy; <span id="current-year"></span></strong>
      </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      // Set current year in footer
      document.getElementById('current-year').textContent = new Date().getFullYear();

      // Data dari PHP untuk chart
      const chartLabels = <?php echo json_encode($chart_labels); ?>;
      const chartData = <?php echo json_encode($final_chart_data); ?>;

      // Initialize bar chart dengan data dari database
      const ctx = document.getElementById('myBarChart').getContext('2d');
      const myBarChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: chartLabels,
          datasets: [{
            label: 'Jumlah Siswa per Tahun Masuk',
            data: chartData,
            borderColor: '#4e79a7',
            backgroundColor: 'rgba(78, 121, 167, 0.3)',
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointBackgroundColor: '#4e79a7',
            pointRadius: 4,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                boxWidth: 12,
                padding: 20
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return `Siswa: ${context.raw}`;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0,0,0,0.05)'
              },
              title: {
                display: true,
                text: 'Jumlah Siswa'
              }
            },
            x: {
              grid: {
                display: false
              },
              title: {
                display: true,
                text: 'Tahun Masuk'
              }
            }
          }
        }
      });

      // Data untuk pie chart dari PHP
      const jurusanLabels = <?php echo json_encode($jurusan_labels); ?>;
      const jurusanData = <?php echo json_encode($jurusan_data); ?>;

      // Pie Chart untuk Jurusan Distribution
      const pieCtx = document.getElementById('myPieChart').getContext('2d');
      const myPieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
          labels: jurusanLabels,
          datasets: [{
            data: jurusanData,
            backgroundColor: [
              '#4ED7F1',
              '#A8DF8E',
              '#FA7070',
              '#FFD66B',
              '#9D76C1',
              '#FF9F43',
              '#6EC1E4',
              '#FF6B6B',
              '#4ECDC4',
              '#45B7D1'  // Tambahan warna jika diperlukan
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });

      // Sidebar toggle functionality
      document.addEventListener("DOMContentLoaded", function () {
        const sidebarToggleBtn = document.querySelector('[data-lte-toggle="sidebar"]');
        const navbar = document.querySelector('.app-header.navbar');

        sidebarToggleBtn.addEventListener('click', function () {
          const body = document.body;
          if (body.classList.contains('sidebar-collapse')) {
            navbar.classList.add('sidebar-open');
          } else {
            navbar.classList.remove('sidebar-open');
          }
        });
      });
    </script>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="dist/js/adminlte.js"></script>
  </body>
</html>

<?php $conn->close(); ?>