<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
if (!$UserID) {
    header('Location: login.php');
}

require_once __DIR__ . '/header.php';
?>
<nav class="navbar navbar-expand-lg navbar-light " style="background-color: #e3f2fd;">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand" href="index.php">Ana Sayfa</a>
        <a class="nav-link " href="user.php">Profil</a>
        <a class="nav-link " href="logout.php">Çıkış Yap</a>
    </div>
</nav>
<section class="container">
    <div class="row text-center my-5">
        <!-- <p id="kayit_tut" class="btn btn-primary p-5">Giris - Çıkış <br>Ekle</p> -->
        <div class="col-md-2 col-sm-4 col-6 m-auto p-4"> <img onclick="newAmount(1, '1TL')" src="img/1TL.png" alt="1TL"> </div>
        <div class="col-md-2 col-sm-4 col-6 m-auto p-4"> <img onclick="newAmount(2, '50kr')" src="img/50kr.png" alt="50kr"></div>
        <div class="col-md-2 col-sm-4 col-6 m-auto p-4"> <img onclick="newAmount(3, '25kr')" src="img/25kr.png" alt="25kr"></div>
        <div class="col-md-2 col-sm-4 col-6 m-auto p-4"> <img onclick="newAmount(4, '10kr')" src="img/10kr.png" alt="10kr"></div>
        <div class="col-md-2 col-sm-4 col-6 m-auto p-4"> <img onclick="newAmount(5, '5kr')" src="img/5kr.png" alt="5kr"> </div>
        <div class="col-md-2 col-sm-4 col-6 m-auto p-4"> <img onclick="newAmount(6, '1kr')" src="img/1kr.png" alt="1kr"> </div>
    </div>
</section>
<?php
$stmt = $db->prepare("SELECT *FROM target_box WHERE user_id=:id ORDER BY create_time DESC");
$stmt->execute([":id" => $UserID]);
if ($stmt->rowCount()) :
    $data = $stmt->fetchAll(PDO::FETCH_OBJ);
    $rowno = count($data);
    $total_amaount = 0;
    foreach ($data as $value) {
        $total_amaount += $amount[$value->amount];
    }
?>
<section class="container">
    <div class="alert alert-success m-5 text-center" style="font-size: 30px;"> 
        Toplam tutar : <b><?= number_format($total_amaount, 2, '.', '') ?></b> ₺ <br>
        Hedef : <b><?=number_format($hedef, 2, '.', '')?></b> ₺ <br>
        Kalan : <b><?= number_format($hedef-$total_amaount, 2, '.', '') ?></b> ₺
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <th>#</th>
                <th>Tarih</th>
                <th class="align-middle text-center">Tutar ( ₺ )</th>
                <th class="align-middle text-center">Kümülatif Tutar ( ₺ )</th>
                <th class=" text-center"></th>
            </thead>
            <tbody>
                <?php foreach ($data as $key => $value) : ?>
                <tr>
                    <th class="align-middle" style="width: 10%"><?= $rowno ?></th>
                    <td class="align-middle"><?= turkcetarih_formati('j F Y l', $value->create_time) ?></td>
                    <td class="align-middle text-center"><?= $amount[$value->amount] ?> ₺</td>
                    <td class="align-middle text-center"><?= number_format($total_amaount, 2, '.', '') ?> ₺</td>
                    <th class="align-middle"><img onclick="sil(<?= $value->id ?>, '<?=$amount_text[$value->amount]?>')" src="img/<?= $amount_text[$value->amount] ?>.png" alt="<?= $amount_text[$value->amount] ?>" style="height: 40px;"></th>
                </tr>
                <?php $rowno--;  $total_amaount -= $amount[$value->amount]; ?>
                <?php endforeach ?>
            </tbody>
        </table>
</section>
<?php else : ?>
<div class="alert alert-info container text-center"><b>Kayıt bulunmamaktadır!</b></div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>


<script>
function newAmount(id, value) {
    console.log(id);
    $.ajax({
        url: 'ajax.php?request_type=veri_ekle',
        type: 'POST',
        data: {
            amount_id: id
        },
        success: function(e) {

            if (e == "1") {
                swal(value,  'eklendi', "success");
            } else {
                swal(e);
            }
            setInterval(() => window.location.reload(false), 3500);
        }
    });
}


function sil(id, value) {
    if (confirm('Veri kaydını silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: 'ajax.php?request_type=veri_sil',
            type: 'POST',
            data: {
                id: id
            },
            success: function(e) {
                swal(e);
                if (e == "1") {
                    swal( value,  "çıkarıldı", "success");
                } else {
                    swal("Olmadı!", "Beklenmedik bir hata oluştu", "error");
                }
                setInterval(() => window.location.reload(false), 3500);
            }
        });
    }
}
</script>