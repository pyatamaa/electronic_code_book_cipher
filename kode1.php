<?php

function hexToBin($hex)
{
    $bin = '';
    $length = strlen($hex);
    for ($i = 0; $i < $length; $i++) {
        $bin .= str_pad(decbin(hexdec($hex[$i])), 4, '0', STR_PAD_LEFT);
    }
    return $bin;
}

function binToHex($bin)
{
    $hex = '';
    $length = strlen($bin);
    for ($i = 0; $i < $length; $i += 4) {
        $hex .= dechex(bindec(substr($bin, $i, 4)));
    }
    return $hex;
}

function encrypt($plaintextHex, $keyBinary)
{
    // Merubah plaintext hexadesimal ke dalam bentuk biner
    $plaintextBinary = hexToBin($plaintextHex);

    // Panjang blok 4 bit
    $blockSize = 4;

    // Membagi plaintext biner per blok
    $blocks = str_split($plaintextBinary, $blockSize);

    // Inisialisasi ciphertext
    $ciphertext = '';

    // Inisialisasi hasil XOR yang belum diubah ke hexadesimal
    $xorResults = [];

    // Menampilkan hasil biner plaintext per blok
    // echo "Plaintext (Biner per Blok 4 Bit): ";
    // foreach ($blocks as $block) {
    //     echo $block . ' ';
    // }
    // echo "\n";

    // Menampilkan key
    // echo "Key (Biner): $keyBinary\n";

    // Proses enkripsi per blok
    foreach ($blocks as $block) {
        // XOR blok dengan kunci
        $blockXOR = sprintf('%04b', bindec($block) ^ bindec($keyBinary));

        // Geser setiap blok 1 bit ke kiri
        $blockShifted = substr($blockXOR, 1) . substr($blockXOR, 0, 1);

        // Tambahkan hasil XOR ke array
        $xorResults[] = $blockShifted;

        // Konversi hasil XOR yang sudah digeser ke dalam bentuk hexadesimal
        $blockHex = binToHex($blockShifted);

        // Tambahkan hasil ke ciphertext
        $ciphertext .= $blockHex;
    }

    // Menampilkan hasil XOR (belum diubah ke dalam hexadesimal)
    // echo "Hasil XOR (Biner per Blok 4 Bit): ";
    // foreach ($xorResults as $result) {
    //     echo $result . ' ';
    // }
    // echo "\n";

    return $ciphertext;
}

function decrypt($ciphertextHex, $keyBinary)
{
    // Merubah ciphertext hexadesimal ke dalam bentuk biner
    $ciphertextBinary = hexToBin($ciphertextHex);

    // Panjang blok 4 bit
    $blockSize = 4;

    // Membagi ciphertext biner per blok
    $blocks = str_split($ciphertextBinary, $blockSize);

    // Inisialisasi plaintext
    $plaintext = '';

    // Inisialisasi hasil XOR yang belum diubah ke hexadesimal
    $xorResults = [];

    // Menampilkan hasil biner ciphertext per blok
    // echo "Ciphertext (Biner per Blok 4 Bit): ";
    // foreach ($blocks as $block) {
    //     echo $block . ' ';
    // }
    // echo "\n";

    // Menampilkan key
    echo "Key (Biner): $keyBinary\n";

    // Proses dekripsi per blok
    foreach ($blocks as $block) {
        // Geser setiap blok 1 bit ke kanan
        $blockShifted = substr($block, -1) . substr($block, 0, -1);

        // XOR blok dengan kunci
        $blockXOR = sprintf('%04b', bindec($blockShifted) ^ bindec($keyBinary));

        // Tambahkan hasil XOR ke array
        $xorResults[] = $blockXOR;

        // Konversi hasil XOR ke dalam bentuk hexadesimal
        $blockHex = binToHex($blockXOR);

        // Tambahkan hasil ke plaintext
        $plaintext .= $blockHex;
    }

    // Menampilkan hasil XOR (belum diubah ke dalam hexadesimal)
    // echo "Hasil XOR (Biner per Blok 4 Bit): ";
    // foreach ($xorResults as $result) {
    //     echo $result . ' ';
    // }
    // echo "\n";

    return $plaintext;
}

// Input dari formulir jika sudah disubmit
$plaintextHex = isset($_POST["plaintext"]) ? $_POST["plaintext"] : "";
$keyBinary = isset($_POST["key"]) ? $_POST["key"] : "";
$ciphertextHex = isset($_POST["ciphertext"]) ? $_POST["ciphertext"] : "";
$mode = isset($_POST["mode"]) ? $_POST["mode"] : "encrypt";

$result = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($mode == "encrypt") {
        $result = encrypt($plaintextHex, $keyBinary);
    } elseif ($mode == "decrypt") {
        $result = decrypt($ciphertextHex, $keyBinary);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enkripsi/Dekripsi</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
     integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<div class="card text-bg-primary w-75 mb-3 text-center" style="max-width: 18rem;">
  <div class="card-body">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <p class="h2">Enkripsi/Dekripsi</p>
        <label for="mode">Mode:</label>
        <select class="form-select" aria-label="Disabled select" name="mode" id="mode" disabled>
            <option value="encrypt" <?php echo ($mode == "encrypt") ? "selected" : ""; ?>>Enkripsi</option>
            <option value="decrypt" <?php echo ($mode == "decrypt") ? "selected" : ""; ?>>Dekripsi</option>
        </select>
        <div class="mb-3">
        <?php if ($mode == "encrypt"): ?>
            <label for="plaintext" class="form-label">Plaintext:</label>
            <input type="text" class="form-control" name="plaintext" id="plaintext" value="<?php echo $plaintextHex; ?>" required>
        </div>
        <div class="mb-3">
        <?php elseif ($mode == "decrypt"): ?>
            <label for="decrypt" class="form-label">Ciphertext:</label>
            <input type="text" class="form-control" name="ciphertext" id="ciphertext" value="<?php echo $ciphertextHex; ?>" required>
        </div>
        <?php endif; ?>
        <div class="mb-3">
            <label for="key" class="form-label">Kunci (biner):</label>
            <input type="text" class="form-control" name="key" id="key" value="<?php echo $keyBinary; ?>" required>
        <div class="col-auto">
            <p></p>
            <button type="submit" class="btn btn-success" value="<?php echo ($mode == "encrypt") ? "Enkripsi" : "Dekripsi"; ?>">Submit</button>
        </div>
    </form>
  </div>
</div>
</body>
  <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <h3>Hasil <?php echo ($mode == "encrypt") ? "Enkripsi" : "Dekripsi"; ?></h3>
        <p><?php echo ($mode == "encrypt") ? "Plaintext" : "Ciphertext"; ?>: <?php echo ($mode == "encrypt") ? $plaintextHex : $ciphertextHex; ?></p>
        <p>Kunci: <?php echo $keyBinary; ?></p>
        <p><?php echo ($mode == "encrypt") ? "Ciphertext" : "Plaintext"; ?>: <?php echo $result; ?></p>
    <?php endif; ?>

<!-- <div class="card text-center">
  <div class="card-body"> -->
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</html>
