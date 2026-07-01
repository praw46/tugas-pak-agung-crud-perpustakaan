<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);

    if (mysqli_query($conn, "INSERT INTO buku (judul, penulis, tahun) VALUES ('$judul', '$penulis', '$tahun')")) {
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
    <title>Tambah Buku - Perpustakaan Praw</title>
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
            <h2 class="text-3xl font-bold text-gray-100 mb-6">Tambah Buku Baru</h2>

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
                        placeholder="Masukkan judul buku yang menarik..." 
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
                        placeholder="Kapan buku ini terbit?" 
                        class="input-search w-full"
                        required
                        min="1900"
                        max="2099"
                        value="<?php echo date('Y'); ?>"
                    >
                    <p class="text-xs text-gray-500 mt-1">Tahun terbit buku</p>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6 border-t border-dark-700">
                    <button type="submit" name="simpan" class="btn-primary flex-1 py-3 transition-transform hover:scale-105 active:scale-95">
                        Simpan Buku
                    </button>
                    <a href="index.php" class="btn-secondary flex-1 py-3">
                        Batal
                    </a>
                </div>
            </form>

            <!-- Info Box -->
            <div class="mt-8 p-4 bg-dark-700 border border-dark-600 rounded-lg">
                <p class="text-sm text-gray-300">
                    <span class="text-primary font-semibold">Tips:</span> Pastikan data buku sudah benar sebelum disimpan. Anda bisa mengeditnya nanti.
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