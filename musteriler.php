<?php
require "auth.php";
require "db.php";

function guvenli($veri) {
    return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function bosMu($veri) {
    return empty(trim($veri));
}

function telefonDuzelt($tel) {
    return preg_replace('/\D/', '', $tel);
}

function telefonGecerliMi($tel) {
    return strlen($tel) == 11 && substr($tel, 0, 2) == "05";
}

if(isset($_POST["edit_id"]) && $_POST["edit_id"] != ""){
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = telefonDuzelt($_POST["phone"]);
    $email = $_POST["email"];
    $edit_id = $_POST["edit_id"];

    if(bosMu($name) || bosMu($surname) || bosMu($phone) || bosMu($email)) {
        echo "Tüm alanlar doldurulmalı";
    } else if(!telefonGecerliMi($phone)) {
        echo "Telefon 05 ile başlayan 11 haneli olmalı (örn: 05321234567)";
    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Geçersiz mail";
    } else {
        $stmt = mysqli_prepare($baglanti, "UPDATE musteriler SET
        name = ?,
        surname = ?, 
        phone = ?, 
        email = ?
        WHERE id = ?");

        mysqli_stmt_bind_param($stmt, "ssssi", $name, $surname, $phone, $email, $edit_id);
        mysqli_stmt_execute($stmt);

        header("Location:musteriler.php");
        exit;
    }
} 
else if(isset($_POST["name"])) {
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = telefonDuzelt($_POST["phone"]);
    $email = $_POST["email"];

    if(
        bosMu($name) ||
        bosMu($surname) ||
        bosMu($phone) || 
        bosMu($email)
    ) {
        echo "Tüm alanlar doldurulmalı";
    } else if (!telefonGecerliMi($phone)) {
    echo "Telefon 05 ile başlayan 11 haneli olmalı (örn: 05321234567)";
    }  else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Geçersiz mail";
    }
    else {

    $stmt = mysqli_prepare($baglanti, "INSERT INTO musteriler (name, surname, phone, email) 
    VALUES (?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "ssss", $name, $surname, $phone, $email);
    mysqli_stmt_execute($stmt);

    header("Location:musteriler.php");
    exit;
  }
}


if(isset($_GET["sil_id"])){
    $sil_id = $_GET["sil_id"];

    $stmt = mysqli_prepare($baglanti, "DELETE from musteriler where id = ?");

    mysqli_stmt_bind_param($stmt, "i", $sil_id);
    mysqli_stmt_execute($stmt);

    header("Location:musteriler.php");
    exit;

}

if(isset($_GET["edit_id"])){
    $edit_id = $_GET["edit_id"];

    $stmt = mysqli_prepare($baglanti, "SELECT * from musteriler where id = ?");

    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);
    $editRow = mysqli_fetch_assoc($sonuc);
}


if(isset($_GET["arama"])){
    $arama = $_GET["arama"];

    $aramadegisken = "%" . "$arama" . "%";

   $stmt = mysqli_prepare($baglanti, "SELECT musteriler.*,
    (SELECT COUNT(*) FROM randevular WHERE randevular.musteri_id = musteriler.id AND randevular.durum = 'geldi') AS gelis_sayisi
    FROM musteriler WHERE name LIKE ? OR surname LIKE ?");
    mysqli_stmt_bind_param($stmt, "ss", $aramadegisken, $aramadegisken);
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);

}
 else 
{
    $arama = "";
    $stmt = mysqli_prepare($baglanti, "SELECT musteriler.*,
    (SELECT COUNT(*) FROM randevular WHERE randevular.musteri_id = musteriler.id AND randevular.durum = 'geldi') AS gelis_sayisi
    FROM musteriler");
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);

}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Müşteriler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
<div class="row">
<?php include "navbar.php"; ?>

<main class="col-12 col-md-10 p-3">
<h4 class="mb-3">Müşteriler</h4>
    
<form method="GET" class="d-flex mb-3">
    <input type="text" name="arama" placeholder="Ara..." class="form-control me-2" value="<?= guvenli($arama) ?>">
    <button type="submit" class="btn btn-primary">Ara</button>
</form>

<button id="yeniBtn" class="btn btn-success mb-3">+ Yeni Kişi</button>
<div id="modal" class="card card-body mb-3" style="max-width:400px; display: <?= isset($editRow) ? 'block' : 'none' ?>;">
<form method="post">
    <input type="hidden" name="edit_id" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["id"]);
    }  else {echo "";} ?>">
    <input type="text" class="form-control mb-2" name="name" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["name"]);
    }  else {echo "";} ?>" placeholder="Ad">
    <input type="text" class="form-control mb-2" name="surname" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["surname"]);
    }  else {echo "";} ?>" placeholder="Soyad">
    <input type="tel" class="form-control mb-2" name="phone" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["phone"]);
    }  else {echo "";} ?>" placeholder="Telefon">
    <input type="text" class="form-control mb-2" name="email" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["email"]);
    }  else {echo "";} ?>" placeholder="Mail">
    
    <button type="submit" class="btn btn-primary">Kaydet</button>
    <button type="button" class="btn btn-secondary" id="kapatBtn">Kapat</button>

</form>

</div>
 

<div class="table-responsive">
<table class="table table-hover align-middle bg-white border">
    <tr class="table-light">
        <th>Ad</th>
        <th>Soyad</th>
        <th>Telefon</th> 
        <th>Geliş</th>
        <th>Mail</th>
        <th>Düzenle</th>
        <th>Sil</th>
    </tr>


<?php  
 while($row = mysqli_fetch_assoc($sonuc)) {
    echo  "<tr>";
    echo  "<td>".guvenli($row["name"])."</td>";
    echo  "<td>".guvenli($row["surname"])."</td>";
    echo  "<td>".guvenli($row["phone"])."</td>";
    echo "<td>".guvenli($row["gelis_sayisi"])."</td>";
    echo  "<td>".guvenli($row["email"])."</td>";
    echo  "<td>";
    echo '<form method="GET">';
    echo '<input type="hidden" name="edit_id" value="' . guvenli($row['id']) . '">';
    echo '<button type="submit" class="btn btn-warning btn-sm">Düzenle</button>';
    echo '</form>';
    echo  "</td>";
    echo  "<td>";
    echo '<form method="GET">';
    echo '<input type="hidden" name="sil_id" value="' . guvenli($row['id']) . '">';
    echo '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Silmek istediğinize emin misiniz?\')">Sil</button>';
    echo '</form>';
    echo  "</td>";
    echo  "</tr>";
}
?>

</table>

</div>
</main>
</div>
</div>
<script src="script.js"></script>
</body>
