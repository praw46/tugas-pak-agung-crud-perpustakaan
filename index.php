<?php
include 'koneksi.php';

// Fitur Hapus Data (Delete)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM buku WHERE id='$id'");
    header("Location: index.php");
    exit;
}

// Get filter dan sort parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_desc';

// Build query
$query_base = "SELECT * FROM buku";
if (!empty($search)) {
    $query_base .= " WHERE judul LIKE '%$search%' OR penulis LIKE '%$search%'";
}

// Apply sorting
switch ($sort) {
    case 'judul_asc':
        $query_base .= " ORDER BY judul ASC";
        break;
    case 'judul_desc':
        $query_base .= " ORDER BY judul DESC";
        break;
    case 'penulis_asc':
        $query_base .= " ORDER BY penulis ASC";
        break;
    case 'tahun_asc':
        $query_base .= " ORDER BY tahun ASC";
        break;
    case 'tahun_desc':
        $query_base .= " ORDER BY tahun DESC";
        break;
    default:
        $query_base .= " ORDER BY id DESC";
}

$query = mysqli_query($conn, $query_base);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Perpustakaan - Praw Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <script>
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold animate-slideUp ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function confirmDelete(id, judul) {
            if (confirm(`Yakin hapus "${judul}"?`)) {
                window.location.href = `index.php?hapus=${id}`;
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-dark-900 via-dark-800 to-dark-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8 animate-slideUp">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-primary to-yellow-500 bg-clip-text text-transparent">
                        Perpustakaan Praw
                    </h1>
                    <p class="text-gray-400 ml-15">Kelola koleksi buku Anda dengan mudah dan modern</p>
                </div>
                <a href="tambah.php" class="btn-primary whitespace-nowrap">
                    Tambah Buku
                </a>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <?php
                $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM buku"))['count'];
                $tahun_terakhir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM buku WHERE tahun >= YEAR(NOW())"))['count'];
                $authors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT penulis) as count FROM buku"))['count'];
                ?>
                <div class="stat-card">
                    <h3 class="text-2xl font-bold text-primary"><?php echo $total; ?></h3>
                    <p class="text-gray-400 text-sm">Total Buku</p>
                </div>
                <div class="stat-card">
                    <h3 class="text-2xl font-bold text-primary"><?php echo $authors; ?></h3>
                    <p class="text-gray-400 text-sm">Total Penulis</p>
                </div>
                <div class="stat-card">
                    <h3 class="text-2xl font-bold text-primary"><?php echo $tahun_terakhir; ?></h3>
                    <p class="text-gray-400 text-sm">Tahun Ini</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="card p-6 mb-8 animate-slideUp" style="animation-delay: 0.1s;">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Cari Buku atau Penulis</label>
                    <div class="relative group">
                        <input 
                            type="text" 
                            id="searchInput" 
                            placeholder="Ketik untuk mencari... (Ctrl+K)" 
                            class="input-search w-full pr-10"
                            onkeyup="liveSearch()"
                        >
                        <button 
                            onclick="clearSearch()" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-all duration-200 opacity-0 hover:scale-110 cursor-pointer"
                            id="clearBtn"
                            type="button"
                            title="Hapus pencarian (Esc)"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Tekan Ctrl+K untuk fokus, Esc untuk hapus</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Urutkan</label>
                    <select id="sortInput" class="w-full input-search" onchange="updateSort()">
                        <option value="id_desc">Terbaru Ditambahkan</option>
                        <option value="judul_asc">Judul (A-Z)</option>
                        <option value="judul_desc">Judul (Z-A)</option>
                        <option value="penulis_asc">Penulis (A-Z)</option>
                        <option value="tahun_desc">Tahun (Terbaru)</option>
                        <option value="tahun_asc">Tahun (Terlama)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Books Display Section -->
        <div class="animate-slideUp" style="animation-delay: 0.2s;">
            <?php
            $has_data = mysqli_num_rows($query) > 0;
            
            if ($has_data) {
                ?>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="booksContainer">
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <div class="card p-6 hover:shadow-2xl transition-all duration-300 hover:border-primary/50 group" 
                             data-animate 
                             style="opacity: 0; transform: translateY(10px); transition: all 0.4s ease-out;">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-lg font-semibold text-gray-400 opacity-60">#<?php echo $no++; ?></span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-100 group-hover:text-primary transition-colors duration-200 line-clamp-2">
                                        <?php echo htmlspecialchars($row['judul']); ?>
                                    </h3>
                                </div>

                            </div>

                            <div class="space-y-2 mb-6">
                                <div class="flex items-center gap-2 text-gray-300">
                                    <span class="text-sm"><?php echo htmlspecialchars($row['penulis']); ?></span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-400">
                                    <span class="text-sm font-semibold text-primary"><?php echo $row['tahun']; ?></span>
                                </div>
                            </div>

                            <div class="flex gap-3 pt-4 border-t border-dark-700">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit flex-1 text-center transition-transform hover:scale-105">
                                    Edit
                                </a>
                                <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['judul'])); ?>')" class="btn-delete flex-1 transition-transform hover:scale-105">
                                    Hapus
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="mt-6 text-center text-gray-400 text-sm animate-fadeIn">
                    Menampilkan <span class="text-primary font-semibold"><?php echo mysqli_num_rows($query); ?></span> buku
                </div>
                <?php
            } else {
                ?>
                <div class="card p-12 text-center animate-slideUp" style="animation-delay: 0.3s;">

                    <h3 class="text-2xl font-bold text-gray-300 mb-2">Tidak ada data buku</h3>
                    <p class="text-gray-400 mb-6">
                        <?php echo !empty($search) ? 'Coba cari dengan kata kunci lain' : 'Mulai dengan menambahkan buku pertama'; ?>
                    </p>
                    <a href="tambah.php" class="btn-primary">
                        Tambah Buku Pertama
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <script>
        let searchTimeout;
        let currentSort = '<?php echo $sort; ?>';

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold animate-slideUp ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function confirmDelete(id, judul) {
            if (confirm(`Yakin hapus "${judul}"?`)) {
                window.location.href = `index.php?hapus=${id}`;
            }
        }

        // Update clear button visibility
        function updateClearButton() {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearBtn');
            if (searchInput.value.trim()) {
                clearBtn.style.opacity = '1';
                clearBtn.style.pointerEvents = 'auto';
            } else {
                clearBtn.style.opacity = '0';
                clearBtn.style.pointerEvents = 'none';
            }
        }

        // Live Search dengan Debounce
        function liveSearch() {
            const searchInput = document.getElementById('searchInput');
            const searchValue = searchInput.value.trim();
            
            updateClearButton();
            
            // Hapus timeout sebelumnya
            clearTimeout(searchTimeout);
            
            // Pindahkan ke halaman dengan parameter search setelah delay
            searchTimeout = setTimeout(() => {
                const sort = document.getElementById('sortInput').value;
                
                const params = new URLSearchParams({
                    search: searchValue,
                    sort: sort
                });
                window.location.href = `index.php?${params.toString()}`;
            }, 400);
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('sortInput').value = 'id_desc';
            updateClearButton();
            window.location.href = 'index.php';
        }

        function updateSort() {
            const sort = document.getElementById('sortInput').value;
            const search = document.getElementById('searchInput').value;
            const params = new URLSearchParams({
                search: search,
                sort: sort
            });
            window.location.href = `index.php?${params.toString()}`;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            const sortInput = document.getElementById('sortInput');
            const searchInput = document.getElementById('searchInput');
            
            if (sortInput) sortInput.value = currentSort;
            if (searchInput) {
                searchInput.value = '<?php echo htmlspecialchars($search); ?>';
                updateClearButton();
            }
            
            // Trigger staggered animation on cards
            document.querySelectorAll('[data-animate]').forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('searchInput').focus();
            }
            // Escape to clear
            if (e.key === 'Escape') {
                const searchInput = document.getElementById('searchInput');
                if (searchInput.value) {
                    clearSearch();
                }
            }
        });
    </script>
</body>
</html>