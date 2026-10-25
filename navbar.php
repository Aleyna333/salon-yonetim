<?php
$sayfa = basename($_SERVER["PHP_SELF"]);
$menu = [
    "index.php"      => "Anasayfa",
    "musteriler.php" => "Müşteriler",
    "calisanlar.php" => "Çalışanlar",
    "hizmetler.php"  => "Hizmetler",
    "randevular.php" => "Randevular",
];
?>

<!-- Telefon: açılır menü -->
<div class="col-12 d-md-none bg-white border-bottom p-2">
    <details>
        <summary class="btn btn-outline-secondary btn-sm">☰ Menü</summary>
        <div class="nav flex-column nav-pills gap-1 mt-2">
            <?php foreach ($menu as $dosya => $baslik): ?>
                <a href="<?= $dosya ?>" class="nav-link <?= $sayfa === $dosya ? 'active' : '' ?>"><?= $baslik ?></a>
            <?php endforeach; ?>
             <a href="cikis.php" class="nav-link text-danger">Çıkış</a>
        </div>
    </details>
</div>

<!-- Masaüstü: sidebar -->
<div class="col-md-2 d-none d-md-block sidebar p-2">
    <div class="nav flex-column nav-pills gap-1">
        <?php foreach ($menu as $dosya => $baslik): ?>
            <a href="<?= $dosya ?>" class="nav-link <?= $sayfa === $dosya ? 'active' : '' ?>"><?= $baslik ?></a>
        <?php endforeach; ?>
        <a href="cikis.php" class="nav-link text-danger">Çıkış</a>
    </div>
</div>