<?php
require "auth.php";
require "db.php";

function guvenli($veri) {
    return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function whatsappLink($tel, $mesaj) {
    $tel = "90" . substr($tel, 1);
    return "https://wa.me/" . $tel . "?text=" . urlencode($mesaj);
}

$stmt = mysqli_prepare($baglanti, "SELECT COUNT(*) as toplam_musteri from musteriler ");
     mysqli_stmt_execute($stmt); 
     $sonuc = mysqli_stmt_get_result($stmt);
     $toplam_musteri = mysqli_fetch_assoc($sonuc);

     $stmt = mysqli_prepare($baglanti, "SELECT COUNT(*) as toplam_calisan from calisanlar");
     mysqli_stmt_execute($stmt);
     $sonuc = mysqli_stmt_get_result($stmt);
     $toplam_calisan = mysqli_fetch_assoc($sonuc);

    $stmt = mysqli_prepare($baglanti, "SELECT COUNT(*) as toplam_hizmet from hizmetler");
     mysqli_stmt_execute($stmt);
     $sonuc = mysqli_stmt_get_result($stmt);
     $toplam_hizmet = mysqli_fetch_assoc($sonuc);

     
    $stmt = mysqli_prepare($baglanti, "SELECT COUNT(*) as bugunku_randevu from randevular WHERE DATE(tarih) = DATE(NOW()) AND durum != 'iptal'");
     mysqli_stmt_execute($stmt);
     $sonuc = mysqli_stmt_get_result($stmt);
     $bugunku_randevu = mysqli_fetch_assoc($sonuc);

     $stmt = mysqli_prepare($baglanti, "SELECT musteriler.name as musteri_adi, hizmetler.name as hizmet_adi, randevular.tarih as randevu_tarihi
    FROM randevular
    JOIN musteriler ON randevular.musteri_id = musteriler.id
    JOIN hizmetler ON randevular.hizmet_id = hizmetler.id
    WHERE DATE(randevular.tarih) = CURDATE() AND randevular.durum != 'iptal'
    ORDER BY randevular.tarih");
    mysqli_stmt_execute($stmt);
    $bugun_sonuc = mysqli_stmt_get_result($stmt);

     
  $stmt = mysqli_prepare($baglanti, "SELECT musteriler.name as musteri_adi, musteriler.phone as musteri_tel, hizmetler.name as hizmet_adi, randevular.tarih as randevu_tarihi
FROM randevular
JOIN musteriler ON randevular.musteri_id = musteriler.id
JOIN hizmetler ON randevular.hizmet_id = hizmetler.id
WHERE DATE(randevular.tarih) = CURDATE() + INTERVAL 1 DAY AND randevular.durum = 'bekliyor'
ORDER BY randevular.tarih");
mysqli_stmt_execute($stmt);
$yarin_sonuc = mysqli_stmt_get_result($stmt); 
     
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salon Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
<div class="row">

<?php include "navbar.php"; ?>

<main class="col-12 col-md-10 p-3">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Bugün</h4>
    <a href="randevular.php" class="btn btn-primary">+ Randevu Ekle</a>
</div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm text-center border-primary"><div class="card-body">
                <h6>Toplam Müşteri</h6>
                <p class="fs-3 fw-bold text-primary mb-0"><?= guvenli($toplam_musteri["toplam_musteri"]) ?></p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm text-center border-success"><div class="card-body">
                <h6>Toplam Çalışan</h6>
                <p class="fs-3 fw-bold text-success mb-0"><?= guvenli($toplam_calisan["toplam_calisan"]) ?></p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm text-center border-warning"><div class="card-body">
                <h6>Toplam Hizmet</h6>
                <p class="fs-3 fw-bold text-warning mb-0"><?= guvenli($toplam_hizmet["toplam_hizmet"]) ?></p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm text-center border-info"><div class="card-body">
                <h6>Bugünkü Randevu</h6>
                <p class="fs-3 fw-bold text-info mb-0"><?= guvenli($bugunku_randevu["bugunku_randevu"]) ?></p>
            </div></div>
        </div>
    </div>

    <h5>Bugünkü Randevular</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle bg-white border">
            <thead class="table-light">
                <tr><th>Saat</th><th>Müşteri</th><th>Hizmet</th></tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($bugun_sonuc) == 0){ ?>
                <tr><td colspan="3">Bugün için randevu yok</td></tr>
            <?php } else { while($row = mysqli_fetch_assoc($bugun_sonuc)) { ?>
                <tr>
                    <td><?= date("H:i", strtotime($row["randevu_tarihi"])) ?></td>
                    <td><?= guvenli($row["musteri_adi"]) ?></td>
                    <td><?= guvenli($row["hizmet_adi"]) ?></td>
                </tr>
            <?php } } ?>
            </tbody>
        </table>
    </div>


    <h5 class="mt-4">Yarının Randevuları</h5>
<div class="table-responsive">
    <table class="table table-hover align-middle bg-white border">
        <thead class="table-light">
            <tr><th>Saat</th><th>Müşteri</th><th>Hizmet</th><th>Hatırlat</th></tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($yarin_sonuc) == 0){ ?>
            <tr><td colspan="4">Yarın için randevu yok</td></tr>
        <?php } else { while($row = mysqli_fetch_assoc($yarin_sonuc)) {
            $mesaj = "Merhaba " . $row["musteri_adi"] . ", yarın saat " . date("H:i", strtotime($row["randevu_tarihi"])) . " için " . $row["hizmet_adi"] . " randevunuz var. Gelemeyecekseniz lütfen bu numaraya yazın. Bekleriz."; ?>
            <tr>
                <td><?= date("H:i", strtotime($row["randevu_tarihi"])) ?></td>
                <td><?= guvenli($row["musteri_adi"]) ?></td>
                <td><?= guvenli($row["hizmet_adi"]) ?></td>
                <td><a href="<?= guvenli(whatsappLink($row["musteri_tel"], $mesaj)) ?>" target="_blank" class="btn btn-success btn-sm">WhatsApp</a></td>
            </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>
</main>
</div>
</div>
</body>
</html>