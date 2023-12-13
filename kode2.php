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
    // echo "Key (Biner): $keyBinary\n";

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
</head>
<body>
    <h2>Enkripsi/Dekripsi</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="mode">Mode:</label>
        <select name="mode" id="mode">
            <option value="encrypt" <?php echo ($mode == "encrypt") ? "selected" : ""; ?>>Enkripsi</option>
            <option value="decrypt" <?php echo ($mode == "decrypt") ? "selected" : ""; ?>>Dekripsi</option>
        </select>
        <br>

        <?php if ($mode == "encrypt"): ?>
            <label for="plaintext">Plaintext (hex):</label>
            <input type="text" name="plaintext" id="plaintext" value="<?php echo $plaintextHex; ?>" required>
        <?php elseif ($mode == "decrypt"): ?>
            <label for="ciphertext">Ciphertext (hex):</label>
            <input type="text" name="ciphertext" id="ciphertext" value="<?php echo $ciphertextHex; ?>" required>
        <?php endif; ?>

        <br>

        <label for="key">Kunci (biner):</label>
        <input type="text" name="key" id="key" value="<?php echo $keyBinary; ?>" required>
        <br>

        <input type="submit" value="<?php echo ($mode == "encrypt") ? "Enkripsi" : "Dekripsi"; ?>">
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <h3>Hasil <?php echo ($mode == "encrypt") ? "Enkripsi" : "Dekripsi"; ?></h3>
        <p><?php echo ($mode == "encrypt") ? "Plaintext" : "Ciphertext"; ?>: <?php echo ($mode == "encrypt") ? $plaintextHex : $ciphertextHex; ?></p>
        <p>Kunci: <?php echo $keyBinary; ?></p>
        <p><?php echo ($mode == "encrypt") ? "Ciphertext" : "Plaintext"; ?>: <?php echo $result; ?></p>
    <?php endif; ?>
</body>
</html>
