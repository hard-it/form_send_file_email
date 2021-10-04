<?php
// Файлы phpmailer
require '/home/n/nwtvya7z/beta.tvtambov.ru/public_html/inc/PHPMailer.php';
require '/home/n/nwtvya7z/beta.tvtambov.ru/public_html/inc/SMTP.php';
require '/home/n/nwtvya7z/beta.tvtambov.ru/public_html/inc/Exception.php';

// Переменные, которые отправляет пользователь
$name = $_POST['fio'];
$age = $_POST['age'];
$phone = $_POST['phone'];
$addr = $_POST['addr'];
$nomination = $_POST['nomination'];
$work = $_POST['work'];
$file = $_FILES['file'];

// Формирование самого письма
$title = "Заявка на онлайн-конкурс «Умельцы Тамбовщины»";
$body = "
<table style='border:1px solid #000; border-collapse:collapse'>
    <tr style='border:1px solid #000; border-collapse:collapse'>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'><b>Имя:</b></td>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'>$name</td>
    </tr>
    <tr style='border:1px solid #000; border-collapse:collapse'>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'><b>Возраст:</b></td>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'>$age</td>
    </tr>
    <tr style='border:1px solid #000; border-collapse:collapse'>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'><b>Телефон:</b></td>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'>$phone</td>
    </tr>
     <tr style='border:1px solid #000; border-collapse:collapse'>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'><b>Адрес проживания:</b></td>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'>$addr</td>
    </tr>
    <tr style='border:1px solid #000; border-collapse:collapse'>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'><b>Номинация:</b></td>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'>$nomination</td>
    </tr>
    <tr style='border:1px solid #000; border-collapse:collapse'>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'><b>Описание работ:</b></td>
        <td style='border:1px solid #000; border-collapse:collapse; padding:5px'>$work</td>
    </tr>
</table>
";
// Настройки PHPMailer
$mail = new PHPMailer\PHPMailer\PHPMailer();
try {
    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth   = true;
    //$mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

    // Настройки вашей почты
    $mail->Host       = 'smtp.yandex.ru'; // SMTP сервера вашей почты
    $mail->Username   = 'test'; // Логин на почте
    $mail->Password   = 'test'; // Пароль на почте
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->setFrom('test@test.com', 'Заявка на конкурс'); // Адрес самой почты и имя отправителя

    // Получатель письма
    $mail->addAddress('test@test.com');

    // Прикрипление файлов к письму
    if (!empty($file['name'][0])) {
        for ($ct = 0; $ct < count($file['tmp_name']); $ct++) {
            $uploadfile = tempnam(sys_get_temp_dir(), sha1($file['name'][$ct]));
            $filename = $file['name'][$ct];
            if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
                $mail->addAttachment($uploadfile, $filename);
                $rfile[] = "Файл $filename прикреплён";
            } else {
                $rfile[] = "Не удалось прикрепить файл $filename";
            }
        }
    }
// Отправка сообщения
    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body = $body;

// Проверяем отравленность сообщения
    if ($mail->send()) {$result = "success";}
    else {$result = "error";}

} catch (Exception $e) {
    $result = "error";
    $status = "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
}

// Отображение результата
echo $result;