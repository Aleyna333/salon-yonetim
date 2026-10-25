<?php
session_start();
require "db.php";

$hata = "";

if (isset($_POST["kullanici_adi"])) {
    $kullanici_adi = $_POST["kullanici_adi"];
    $sifre = $_POST["sifre"];

    $stmt = mysqli_prepare($baglanti, "SELECT * FROM kullanicilar WHERE kullanici_adi = ?");
    mysqli_stmt_bind_param($stmt, "s", $kullanici_adi);
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);
    $kullanici = mysqli_fetch_assoc($sonuc);

    if ($kullanici && password_verify($sifre, $kullanici["sifre"])) {
        session_regenerate_id(true);
        $_SESSION["giris"] = true;
        header("Location: index.php");
        exit;
    } else {
        $hata = "Kullanıcı adı veya şifre hatalı";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card shadow-sm p-4" style="width:100%; max-width:360px;">
        <h4 class="mb-3 text-center">Salon Yönetim Sistemi</h4>
        <?php if ($hata != "") { ?>
            <div class="alert alert-danger"><?= $hata ?></div>
        <?php } ?>
        <form method="post">
            <input type="text" name="kullanici_adi" class="form-control mb-2" placeholder="Kullanıcı adı">
            <input type="password" name="sifre" class="form-control mb-3" placeholder="Şifre">
            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
        </form>
    </div>
</body>
</html>