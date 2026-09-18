<?php
require "db.php";

function guvenli($veri){
   return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function bosMu($veri){
    return empty(trim($veri));
}

if(isset($_POST["edit_id"]) && ($_POST["edit_id"]) != "") {
$name = $_POST["name"];
$prıce = $_POST["prıce"];
$edit_id = $_POST["edit_id"];

if( 
    bosMu($name)||
    bosMu($prıce)
){
    echo"Bu alanlar dolu olmalı!";
} else{
$stmt = mysqli_prepare($baglanti, "UPDATE hizmetler SET
name = ?,
prıce = ?
WHERE id = ?
");
mysqli_stmt_bind_param($stmt, "sdi", $name, $prıce, $edit_id);
mysqli_stmt_execute($stmt);
header("location:hizmetler.php");
exit;
}
}

 elseif(isset($_POST["name"])){
$name = $_POST["name"];
$prıce = $_POST["prıce"];

if( 
    bosMu($name)||
    bosMu($prıce)
){
    echo"Bu alanlar dolu olmalı!";
} else {
$stmt = mysqli_prepare($baglanti, "INSERT INTO hizmetler(name, prıce) VALUES(?, ?)");
mysqli_stmt_bind_param($stmt, "sd", $name, $prıce);
mysqli_stmt_execute($stmt);
header("location:hizmetler.php");
exit;
}
}


if(isset($_GET["edit_id"])){
$edit_id = $_GET["edit_id"];
$stmt = mysqli_prepare($baglanti, "SELECT * from hizmetler WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $edit_id);
mysqli_stmt_execute($stmt);
$sonuc = mysqli_stmt_get_result($stmt);
$editRow = mysqli_fetch_assoc($sonuc);
}


if(isset($_POST["sil_id"])){
$sil_id = $_POST["sil_id"];
$stmt = mysqli_prepare($baglanti, "DELETE from hizmetler WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $sil_id);
mysqli_stmt_execute($stmt);
header("location:hizmetler.php");
exit;
}

$stmt = mysqli_prepare($baglanti, "SELECT * from hizmetler");
mysqli_stmt_execute($stmt);
$sonuc = mysqli_stmt_get_result($stmt);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hizmetler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<div class="row">

<?php include "navbar.php"; ?>

<div class="col-10 p-3">
<form method="post">
    <input type="hidden" name="edit_id" value="<?php if(isset($editRow)) {
    echo guvenli($editRow["id"]);
    } else{echo"";} ?>">

    <input type="text" class="form-control mb-2" name="name" placeholder="Hizmet" value="<?php if(isset($editRow)) {
    echo guvenli($editRow["name"]);
    } else{echo"";} ?>">

    <input type="text" class="form-control mb-2" name="prıce" placeholder="Ücret" value="<?php if(isset($editRow)) {
    echo guvenli($editRow["prıce"]);
    } else{echo"";} ?>">

    <button type="submit" class="btn btn-primary">Kaydet</button>
    <?php if(isset($editRow)) { ?>
        <a href="hizmetler.php" class="btn btn-secondary">İptal</a>
        <?php } ?>
</form>

<table class="table table-bordered table-hover">
    <tr>
        <th>name</th>
        <th>prıce</th>
        <th>Düzenle</th>
        <th>Sil</th>
    </tr>

    <?php
    while($row = mysqli_fetch_assoc($sonuc)) {
    ?>

    <tr>
        <td><?= guvenli($row["name"]) ?></td>
        <td><?= guvenli($row["prıce"]) ?></td>
        <td> 
            <form method="get"> 
            <input type="hidden" name="edit_id" value="<?= guvenli($row["id"]) ?>">
            <button type="submit" class="btn btn-warning btn-sm">Düzenle</button>
        </form>
        </td>
         <td> 
            <form method="post"> 
            <input type="hidden" name="sil_id" value="<?= guvenli($row["id"]) ?>">
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misniz?')">Sil</button>
        </form>
        </td>
    </tr>

    <?php 
    }
    ?>
</table>
</div>
</div>
     
</body>
</html>