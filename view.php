<?php

require_once __DIR__ . '/config.php';
try {
    $db = new PDO('mysql:host='.$db_host.';dbname='.$db_name , $db_user, $db_password);
} catch (PDOException $e) {
    die("Database bağlantı hatası");
}
function turkcetarih_formati($format, $datetime = 'now')
{
    $z = date("$format", strtotime($datetime));
    $gun_dizi = array(
        'Monday'    => 'Pazartesi',
        'Tuesday'   => 'Salı',
        'Wednesday' => 'Çarşamba',
        'Thursday'  => 'Perşembe',
        'Friday'    => 'Cuma',
        'Saturday'  => 'Cumartesi',
        'Sunday'    => 'Pazar',
        'January'   => 'Ocak',
        'February'  => 'Şubat',
        'March'     => 'Mart',
        'April'     => 'Nisan',
        'May'       => 'Mayıs',
        'June'      => 'Haziran',
        'July'      => 'Temmuz',
        'August'    => 'Ağustos',
        'September' => 'Eylül',
        'October'   => 'Ekim',
        'November'  => 'Kasım',
        'December'  => 'Aralık',
        'Mon'       => 'Pts',
        'Tue'       => 'Sal',
        'Wed'       => 'Çar',
        'Thu'       => 'Per',
        'Fri'       => 'Cum',
        'Sat'       => 'Cts',
        'Sun'       => 'Paz',
        'Jan'       => 'Oca',
        'Feb'       => 'Şub',
        'Mar'       => 'Mar',
        'Apr'       => 'Nis',
        'Jun'       => 'Haz',
        'Jul'       => 'Tem',
        'Aug'       => 'Ağu',
        'Sep'       => 'Eyl',
        'Oct'       => 'Eki',
        'Nov'       => 'Kas',
        'Dec'       => 'Ara',
    );
    foreach ($gun_dizi as $en => $tr) {
        $z = str_replace($en, $tr, $z);
    }
    if (strpos($z, 'Mayıs') !== false && strpos($format, 'F') === false) $z = str_replace('Mayıs', 'May', $z);
    return $z;
}
$UserID = 1;


?>
<!doctype html>
<html lang="tr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <title><?= $site_title?></title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">

</head>

<body>
    <div class="text-center col-md-4 m-auto">
        <img src="img/bg.webp" alt="logo" class="my-5" style="width: 70%;">
    </div>

<?php

$stmt = $db->prepare("SELECT * FROM target_box WHERE user_id=:id ORDER BY create_time DESC");
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
                    <th class="align-middle"><img  src="img/<?= $amount_text[$value->amount] ?>.png" alt="<?= $amount_text[$value->amount] ?>" style="height: 40px;"></th>
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
