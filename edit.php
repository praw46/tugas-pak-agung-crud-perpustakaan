<?php
include 'koneksi.php';

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM buku WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);

    if (mysqli_query($conn, "UPDATE buku SET judul='$judul', penulis='$penulis', tahun='$tahun' WHERE id='$id'")) {
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Perpustakaan Praw</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gradient-to-br from-dark-900 via-dark-800 to-dark-900 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 animate-slideUp">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-primary to-yellow-500 bg-clip-text text-transparent">
                Perpustakaan Praw
            </h1>
            <a href="index.php" class="btn-secondary">
                Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="card p-8 animate-slideUp" style="animation-delay: 0.1s;">
            <h2 class="text-3xl font-bold text-gray-100 mb-6">Edit Data Buku</h2>

            <form method="POST" action="" class="space-y-6">
                <!-- Judul Buku -->
                <div>
                    <label for="judul" class="block text-sm font-semibold text-gray-300 mb-2">
                        Judul Buku
                    </label>
                    <input 
                        type="text" 
                        id="judul"
                        name="judul" 
                        value="<?php echo htmlspecialchars($data['judul']); ?>" 
                        placeholder="Masukkan judul buku..." 
                        class="input-search w-full"
                        required
                        minlength="3"
                    >
                    <p class="text-xs text-gray-500 mt-1">Minimal 3 karakter</p>
                </div>

                <!-- Penulis -->
                <div>
                    <label for="penulis" class="block text-sm font-semibold text-gray-300 mb-2">
                        Nama Penulis
                    </label>
                    <input 
                        type="text" 
                        id="penulis"
                        name="penulis" 
                        value="<?php echo htmlspecialchars($data['penulis']); ?>" 
                        placeholder="Nama penulis atau penerbit..." 
                        class="input-search w-full"
                        required
                        minlength="2"
                    >
                    <p class="text-xs text-gray-500 mt-1">Siapa yang menulis buku ini?</p>
                </div>

                <!-- Tahun Terbit -->
                <div>
                    <label for="tahun" class="block text-sm font-semibold text-gray-300 mb-2">
                        Tahun Terbit
                    </label>
                    <input 
                        type="number" 
                        id="tahun"
                        name="tahun" 
                        value="<?php echo $data['tahun']; ?>" 
                        placeholder="Kapan buku ini terbit?" 
                        class="input-search w-full"
                        required
                        min="1900"
                        max="2099"
                    >
                    <p class="text-xs text-gray-500 mt-1">Tahun terbit buku</p>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6 border-t border-dark-700">
                    <button type="submit" name="update" class="btn-primary flex-1 py-3 transition-transform hover:scale-105 active:scale-95">
                        Update Buku
                    </button>
                    <a href="index.php" class="btn-secondary flex-1 py-3">
                        Batal
                    </a>
                </div>
            </form>

            <!-- Info Box -->
            <div class="mt-8 p-4 bg-dark-700 border border-dark-600 rounded-lg">
                <p class="text-sm text-gray-300">
                    <span class="text-primary font-semibold">Tips:</span> Perbarui data buku sesuai dengan informasi terkini.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto-capitalize input
        document.getElementById('judul').addEventListener('change', function() {
            this.value = this.value.trim();
        });
        document.getElementById('penulis').addEventListener('change', function() {
            this.value = this.value.trim();
        });
    </script>
</body>
</html>