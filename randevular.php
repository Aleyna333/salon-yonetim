<?php
require "auth.php";
require  "db.php";

function guvenli($veri) {
    return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function bosMU($veri){
    return empty(trim($veri));
}

$gecerli_durumlar = ["bekliyor", "geldi", "gelmedi", "iptal"];
$renkler = ["bekliyor" => "warning", "geldi" => "success", "gelmedi" => "danger", "iptal" => "secondary"];

function cakismaVarMi($baglanti, $calisan_id, $tarih, $haric_id) {
    $stmt = mysqli_prepare($baglanti, "SELECT id FROM randevular WHERE calisan_id = ? AND tarih = ? AND id != ? AND durum != 'iptal'");
    mysqli_stmt_bind_param($stmt, "isi", $calisan_id, $tarih, $haric_id);
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($sonuc) > 0;
}

function whatsappLink($tel, $mesaj) {
    $tel = "90" . substr($tel, 1);
    return "https://wa.me/" . $tel . "?text=" . urlencode($mesaj);
}

$stmt = mysqli_prepare($baglanti, "SELECT * from musteriler");
mysqli_stmt_execute($stmt);
$musteri_sonuc = mysqli_stmt_get_result($stmt);


$stmt = mysqli_prepare($baglanti, "SELECT * from hizmetler");
mysqli_stmt_execute($stmt);
$hizmet_sonuc = mysqli_stmt_get_result($stmt);

$stmt = mysqli_prepare($baglanti, "SELECT * from calisanlar");
mysqli_stmt_execute($stmt);
$calisan_sonuc = mysqli_stmt_get_result($stmt);


if(isset($_POST["edit_id"]) && $_POST["edit_id"] != "") {
    $musteri_id = $_POST["musteri_id"];
    $hizmet_id = $_POST["hizmet_id"];
    $calisan_id = $_POST["calisan_id"];
    $tarih = $_POST["tarih"];
    $edit_id = $_POST["edit_id"];
    $durum = $_POST["durum"] ?? "bekliyor";
if(!in_array($durum, $gecerli_durumlar)) { $durum = "bekliyor"; }

    if( 
    bosMU($musteri_id)||
    bosMU($hizmet_id)||
    bosMU($calisan_id) ||
    bosMU($tarih)
    ){
        echo"Bu alanlar dolu olmalı!";
    }else if(cakismaVarMi($baglanti, $calisan_id, $tarih, $edit_id)){
        echo "Bu personelin bu saatte başka bir randevusu var!";
    }else{
   $stmt = mysqli_prepare($baglanti, "UPDATE randevular SET 
    musteri_id = ?, 
    hizmet_id = ?,
    calisan_id = ?,
    tarih = ?,
    durum = ?
    WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "iiissi", $musteri_id, $hizmet_id, $calisan_id, $tarih, $durum, $edit_id);
    mysqli_stmt_execute($stmt);
    header("location:randevular.php");
    exit;
    }

}else if(isset($_POST["musteri_id"])){
    $musteri_id = $_POST["musteri_id"];
    $hizmet_id = $_POST["hizmet_id"];
    $calisan_id = $_POST["calisan_id"];
    $tarih = $_POST["tarih"];
    $durum = $_POST["durum"] ?? "bekliyor";
if(!in_array($durum, $gecerli_durumlar)) { $durum = "bekliyor"; }
   

    if(
    bosMU($musteri_id)||
    bosMU($hizmet_id)||
    bosMU($calisan_id) ||
    bosMU($tarih)
    ){
        echo"Bu alanlar dolu olmalı!";
       }else if(cakismaVarMi($baglanti, $calisan_id, $tarih, 0)){
        echo "Bu personelin bu saatte başka bir randevusu var!";
    }else{
    $stmt = mysqli_prepare($baglanti, "INSERT INTO randevular(musteri_id, hizmet_id, calisan_id, tarih, durum) VALUES(?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiiss", $musteri_id, $hizmet_id, $calisan_id, $tarih, $durum);
    mysqli_stmt_execute($stmt);
    header("location:randevular.php");
    exit;
    }
}

$stmt = mysqli_prepare($baglanti, "SELECT musteriler.name as musteri_adi, musteriler.surname as musteri_soyadi, musteriler.phone as musteri_tel, hizmetler.name as hizmet_adi, calisanlar.name as calisan_adi, randevular.tarih as randevu_tarihi, randevular.id as randevu_id, randevular.durum as durum
FROM randevular
JOIN musteriler ON randevular.musteri_id = musteriler.id
JOIN hizmetler ON randevular.hizmet_id = hizmetler.id
LEFT JOIN calisanlar ON randevular.calisan_id = calisanlar.id
ORDER BY randevular.tarih");

mysqli_stmt_execute($stmt);
$sonuc = mysqli_stmt_get_result($stmt);

    if(isset($_GET["edit_id"])){
        $edit_id = $_GET["edit_id"];
        $stmt =  mysqli_prepare($baglanti, "SELECT * from randevular WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $edit_sonuc = mysqli_stmt_get_result($stmt);
        $editRow = mysqli_fetch_assoc($edit_sonuc);
    }

    if(isset($_POST["sil_id"])) {
    $sil_id = $_POST["sil_id"];
    $stmt = mysqli_prepare($baglanti, "DELETE from randevular WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $sil_id);
    mysqli_stmt_execute($stmt);
    header("location:randevular.php");
    exit;
    }

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Randevular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
<div class="row">
    <?php include "navbar.php"; ?>

    <main class="col-12 col-md-10 p-3">
        <form method="post">
        <input type="hidden" name="edit_id" value="<?php if(isset($editRow)) {
    echo guvenli($editRow["id"]);
    } else{echo"";} ?>">

 <select name="musteri_id" class="form-control mb-2">

        <?php while($row = mysqli_fetch_assoc($musteri_sonuc)){ ?>
        <option value="<?= guvenli($row["id"]) ?>" <?php if(isset($editRow) && $editRow["musteri_id"] == $row["id"])
            echo "selected"; ?>><?= guvenli($row["name"]) . " " . guvenli($row["surname"])?></option>

        <?php } ?>
    </select>

        <select name="hizmet_id" class="form-control mb-2">
        <?php while($row = mysqli_fetch_assoc($hizmet_sonuc)){ ?>
        <option value="<?= guvenli($row["id"]) ?>" <?php if(isset($editRow) && $editRow["hizmet_id"] == $row["id"])         
            echo "selected"; ?>><?= guvenli($row["name"]) ?></option>

        <?php } ?>
    </select>

    <select name="calisan_id" class="form-control mb-2">
    <?php while($row = mysqli_fetch_assoc($calisan_sonuc)){ ?>
    <option value="<?= guvenli($row["id"]) ?>" <?php if(isset($editRow) && $editRow["calisan_id"] == $row["id"]) echo "selected"; ?>>
        <?= guvenli($row["name"]) . " " . guvenli($row["surname"]) ?>
    </option>
    <?php } ?>
</select>

<input type="datetime-local" class="form-control mb-2" name="tarih"
       value="<?= isset($editRow) ? date("Y-m-d\TH:i", strtotime($editRow["tarih"])) : "" ?>">

       <select name="durum" class="form-control mb-2">
    <?php foreach($gecerli_durumlar as $d) { ?>
    <option value="<?= $d ?>" <?php if(isset($editRow) && $editRow["durum"] == $d) echo "selected"; ?>><?= ucfirst($d) ?></option>
    <?php } ?>
</select>

    <button type="submit" class="btn btn-primary">Kaydet</button>
    <?php if(isset($editRow)) { ?>
        <a href="randevular.php" class="btn btn-secondary">İptal</a>
        <?php } ?>

    </form>   
        <table class="table table-bordered table-hover">
        <tr>
            <th>Müşteri</th>
            <th>Hizmet</th>
            <th>Çalışan</th>
            <th>Randevu Tarihi</th>
            <th>Durum</th>
            <th>Hatırlat</th>
            <th>Düzenle</th>
            <th>Sil</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($sonuc)){ ?>
        <tr>
            <td><?php echo guvenli($row["musteri_adi"] . " " . $row["musteri_soyadi"]);?></td> 
            <td><?php echo guvenli($row["hizmet_adi"]);?></td>
            <td><?= guvenli($row["calisan_adi"] ?? "-") ?></td>
            <td><?= date("d.m.Y H:i", strtotime($row["randevu_tarihi"])) ?></td>
            <td><span class="badge text-bg-<?= $renkler[$row["durum"]] ?? "secondary" ?>"><?= guvenli($row["durum"]) ?></span></td>
            <td>
            <?php if($row["durum"] == "bekliyor") {
                $mesaj = "Merhaba " . $row["musteri_adi"] . ", " . date("d.m.Y H:i", strtotime($row["randevu_tarihi"])) . " tarihinde " . $row["hizmet_adi"] . " randevunuz var. Gelemeyecekseniz lütfen bu numaraya yazın. Bekleriz.";
            ?>
                <a href="<?= guvenli(whatsappLink($row["musteri_tel"], $mesaj)) ?>" target="_blank" class="btn btn-success btn-sm">WhatsApp</a>
            <?php } ?>
            </td>
            <td> 
                <form method="get">
                    <input type="hidden" name="edit_id" value="<?= guvenli($row["randevu_id"])?>">
                    <button type="submit" class="btn btn-warning btn-sm">Düzenle</button>
                </form>
          </td>
            <td>
                <form method="post">
                    <input type="hidden" name="sil_id" value="<?= guvenli($row["randevu_id"] )?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
                </form>
            </td>
        </tr>

        <?php
        }
        ?>

    </table>
        </main>

</div>
</div>
</body>
</html>