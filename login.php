<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        width: 100%;
        height: 100vh;
        overflow: hidden;
        background-color: rgb(188, 221, 235);
        ;
    }

    .login {
        text-decoration: none;
        color: black;
        border-color: transparent;
        border-radius: 10px;
        font-size: 17px;
        padding: 10px 35px;
        background-color: rgb(255, 238, 0);
        position: absolute;
        top: calc(50% - 19px);
        left: calc(50% - 35px - (17px * 2.5));
    }
</style>

<body>
    <?php
    require_once 'php/user.php';
    if (!isset($_SESSION['token'])) {
        echo
        '<a class="btn login" 
        href="https://oauth.yandex.ru/authorize?response_type=code&client_id=581cf6123ee2464b83790ff825c69879">
        Войти
    </a>';
    } else {
        header('Location: index.php');
    }
    // <a class="btn login" href="https://oauth.yandex.ru/authorize?response_type=token&client_id=581cf6123ee2464b83790ff825c69879">Страница запроса доступа</a>
    ?>
</body>

</html>