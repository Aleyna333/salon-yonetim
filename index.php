<?php
require "db.php";

function guvenli($veri) {
    return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function bosMu($veri) {
    return empty(trim($veri));
}

if(isset($_POST["edit_id"]) && $_POST["edit_id"] != ""){
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $edit_id = $_POST["edit_id"];

    $stmt = mysqli_prepare($baglanti, "UPDATE kişiler SET
    name = ?,
    surname = ?, 
    phone = ?, 
    email = ?
    WHERE id = ?");

    mysqli_stmt_bind_param($stmt, "ssssi", $name, $surname, $phone, $email, $edit_id);
    mysqli_stmt_execute($stmt);

   header("Location:index.php");
exit;
}
else if(isset($_POST["name"])) {
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    if(
        bosMu($name) ||
        bosMu($surname) ||
        bosMu($phone) || 
        bosMu($email)
    ) {
        echo "Tüm alanlar doldurulmalı";
    } else if (!is_numeric($phone) || strlen($phone) < 10 || strlen($phone) > 11 ) {
        echo "Telefon sadece rakam içermeli ve 10-11 haneli olmalı";
    }  else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Geçersiz mail";
    }
    else {

    $stmt = mysqli_prepare($baglanti, "INSERT INTO kişiler (name, surname, phone, email) 
    VALUES (?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "ssss", $name, $surname, $phone, $email);
    mysqli_stmt_execute($stmt);

    header("Location:index.php");
    exit;
  }
}


if(isset($_GET["sil_id"])){
    $sil_id = $_GET["sil_id"];

    $stmt = mysqli_prepare($baglanti, "DELETE from kişiler where id = ?");

    mysqli_stmt_bind_param($stmt, "i", $sil_id);
    mysqli_stmt_execute($stmt);

    header("Location:index.php");
    exit;

}

if(isset($_GET["edit_id"])){
    $edit_id = $_GET["edit_id"];

    $stmt = mysqli_prepare($baglanti, "SELECT * from kişiler where id = ?");

    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);
    $editRow = mysqli_fetch_assoc($sonuc);
}


if(isset($_GET["arama"])){
    $arama = $_GET["arama"];

    $aramadegisken = "%" . "$arama" . "%";

    $stmt = mysqli_prepare($baglanti, "SELECT * FROM kişiler WHERE name LIKE ? OR surname LIKE ?");

    mysqli_stmt_bind_param($stmt, "ss", $aramadegisken, $aramadegisken);
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);

}
 else 
{
    $arama = "";
    $stmt = mysqli_prepare($baglanti, "SELECT * FROM kişiler ");
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);

}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mini Adres Defteri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<form method="GET" class="d-flex mb-3">
    <input type="text" name="arama" placeholder="Ara..." class="form-control me-2">
    <button type="submit" class="btn btn-primary">Ara</button>
</form>

<button id="yeniBtn" class="btn btn-success mb-3">+ Yeni Kişi</button>
<div id="modal" style="display: <?php if(isset($editRow)) {echo 'block'; } else {echo 'none'; } ?>; border:1px solid black; padding:20px; width:300px; background:white;">

<form method="post">
    <input type="hidden" name="edit_id" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["id"]);
    }  else {echo "";} ?>">
    <input type="text" name="name" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["name"]);
    }  else {echo "";} ?>" placeholder="Ad">
    <input type="text" name="surname" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["surname"]);
    }  else {echo "";} ?>" placeholder="Soyad">
    <input type="text" name="phone" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["phone"]);
    }  else {echo "";} ?>" placeholder="Telefon">
    <input type="text" name="email" value="<?php if(isset( $editRow)) {
    echo guvenli($editRow["email"]);
    }  else {echo "";} ?>" placeholder="Mail">
    
    <button type="submit">Kaydet</button>
    <button type="button" id="kapatBtn">Kapat</button>

</form>

</div>
 

<table class="table table-bordered table-hover">
    <tr>
        <th>Ad</th>
        <th>Soyad</th>
        <th>Telefon</th> 
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
    echo  "<td>".guvenli($row["email"])."</td>";
    echo  "<td>";
    echo '<form method="GET">';
    echo '<input type="hidden" name="edit_id" value="' . guvenli($row['id']) . '">';
    echo '<button type="submit" class = "btn btn-warning btn-sm">Düzenle</button>';
    echo '</form>';
    echo  "</td>";
    echo  "<td>";
    echo '<form method="GET">';
    echo '<input type="hidden" name="sil_id" value="' . guvenli($row['id']) . '">';
    echo '<button type="submit" class = "btn btn-danger btn-sm" onclick="return confirm(\'Silmek istediğinize emin misiniz?\')">Sil</button>';
    echo '</form>';
    echo  "</td>";
    echo  "</tr>";
}
?>

</table>

<script src="script.js"></script>
<div class="container mt-4">
</div>
</body>
</html>
