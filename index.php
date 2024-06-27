<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
</head>
<body>
    <h1>DATA BARANG</h1>
    <form action="" method="post">
        <input type="text" name="nama" id="nama" placeholder="Nama Barang" required>
        <input type="number" name="harga" id="harga" placeholder="Harga" required>
        <input type="number" name="jumlah" id="jumlah" placeholder="Jumlah Barang" required>
        <button type="submit" name="Kirim">Tambah</button>
    </form>

    <?php
    session_start();
    $message = "";

    if(!isset($_SESSION['kasir'])){
        $_SESSION['kasir'] = array();
    }

    if(isset($_POST['Kirim'])){
        if(!empty($_POST['nama']) && !empty($_POST['harga']) && !empty($_POST['jumlah'])){
            $nama = $_POST['nama'];
            $harga = $_POST['harga'];
            $jumlah = $_POST['jumlah'];

            $found = false;
            foreach ($_SESSION['kasir'] as &$item) {
                if ($item['nama'] == $nama) {
                    $item['jumlah'] += $jumlah;
                    $found = true;
                    $message = "Barang sudah ada, jumlah barang ditambahkan.";
                    break;
                }
            }

            if (!$found) {
                $data = [
                    'nama' => $nama,
                    'harga' => $harga,
                    'jumlah' => $jumlah
                ];
                array_push($_SESSION['kasir'], $data);
                $message = "Barang berhasil ditambahkan.";
            }
        }
    }

    if(isset($_GET['hapus'])){
        $key = $_GET['hapus'];
        unset($_SESSION['kasir'][$key]);
        header("Location: index.php");
    }

    if(isset($_POST['checkout'])){
        $total = 0;
        foreach ($_SESSION['kasir'] as $item) {
            $total += $item['harga'] * $item['jumlah'];
        }

        $bayar = $_POST['bayar'];
        if ($bayar >= $total) {
            $kembalian = $bayar - $total;
            $message = "Pembayaran berhasil. Kembalian: $kembalian. Struk:\n";
            foreach ($_SESSION['kasir'] as $item) {
                $message .= "Nama Barang: " . $item['nama'] . ", Jumlah: " . $item['jumlah'] . ", Harga: " . $item['harga'] . ", Total: " . ($item['harga'] * $item['jumlah']) . "\n";
            }
            $message .= "Total Belanja: $total\n";
            $message .= "Uang Dibayar: $bayar\n";
            $message .= "Kembalian: $kembalian\n";

            // Reset data
            $_SESSION['kasir'] = array();
        } else {
            $message = "Uang yang dibayarkan kurang.";
        }
    }
    ?>

    <?php
    $totalHarga = 0;
    if (!empty($_SESSION['kasir'])) {
        foreach ($_SESSION['kasir'] as $key => $value) {
            echo "<p><strong>Nama Barang:</strong> " . htmlspecialchars($value['nama']) . "</p>";
            echo "<p><strong>Harga Barang:</strong> " . htmlspecialchars($value['harga']) . "</p>";
            echo "<p><strong>Jumlah Barang:</strong> " . htmlspecialchars($value['jumlah']) . "</p>";
            echo "<p><strong>Total Harga Barang:</strong> " . htmlspecialchars($value['harga'] * $value['jumlah']) . "</p>";
            echo '<a href="?hapus='. $key .'">Hapus</a>';
            echo '<hr>';
            $totalHarga += $value['harga'] * $value['jumlah'];
        }
        echo "<h2>Total Keseluruhan: $totalHarga</h2>";
    }
    ?>

    <?php if ($message): ?>
        <p><?= nl2br(htmlspecialchars($message)) ?></p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['kasir'])): ?>
        <form action="" method="post">
            <h2>Checkout</h2>
            <label for="bayar">Uang Dibayar:</label>
            <input type="number" name="bayar" id="bayar" required>
            <button type="submit" name="checkout">Checkout</button>
        </form>
    <?php endif; ?>
</body>
</html>
