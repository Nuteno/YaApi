<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once 'php/user.php';
$user = new User();
$user->connect();


function loadContent($user)
{
    $pathNumber =  count(explode("/", $_SESSION['path'])) - 1;
    if ($pathNumber > 0) {
        if ($pathNumber == 1)  echo  '<a class="item_link dir" href="index.php?path=">...</a>';
        else {
            $arrFromPath = explode("/", $_SESSION['path']);
            $prevPath = str_replace('/' . $arrFromPath[$pathNumber - 1], '', $_SESSION['path']);
            echo  '<a class="item_link dir" href="index.php?path=' . $prevPath . '">...</a>';
        }
    }
    foreach ($user->getContentWithSession(isset($_POST['pag']) ? $_POST['pag'] : 0) as $item) {
        if ($item->isDir()) {
            $path = $item->getPath();
            $path = mb_substr($path, 6);
            echo '<a class="item_link dir" href="index.php?path=' . $path . '/"><span class="material-symbols-outlined">
                folder
                </span>' . $item->name . '</a>';
        } else {
            echo '<a href="#" class="file item_link"><span class="material-symbols-outlined">
            description
            </span>'
                . $item->name .
                '<div class="icons_wrapper" data-path="' . $item->getPath() . '">
                    <span class="material-symbols-outlined edit_icon">
                    edit
                    </span>
                    <span class="material-symbols-outlined download_icon">
                    file_download
                    </span>
                    <span class="material-symbols-outlined delete_icon" >
                    delete
                    </span>
                    
            </div>
            </a>';
        }
    }
    if ($user->pagination()) {
        echo '<div class="pag_wrapper" data-pag="' . (isset($_POST['pag']) ? $_POST['pag'] : '0') . '">';
        if (!isset($_POST['pag']) || $_POST['pag'] == 0) {
            echo '<a class="pag_link"  href="index.php?path=' . $_SESSION['path'] . '&pag=1"><span class="material-symbols-outlined">
            arrow_forward_ios
            </span></a>';
        } elseif ($_POST['pag'] == $user->pagination()) {
            echo '<a class="pag_link"  href="index.php?path=' . $_SESSION['path'] . '&pag=' . ($_POST['pag'] - 1) . '"><span class="material-symbols-outlined">
            arrow_back_ios
            </span></a>';
        } else {
            echo '<a class="pag_link" href="index.php?path=' . $_SESSION['path'] . '&pag=' . ($_POST['pag'] - 1) . '"><span class="material-symbols-outlined">
            arrow_back_ios
            </span></a>';
            echo '<a class="pag_link"  href="index.php?path=' . $_SESSION['path'] . '&pag=' . ($_POST['pag'] + 1) . '"><span class="material-symbols-outlined">
            arrow_forward_ios
            </span></a>';
        }
        echo '</div>';
    }
}








if (isset($_FILES['file']['name'])) {


    //Отделяем расширение файла от имени
    $type = explode('.', $_FILES['file']['name'])[1];
    //Проверка размера и расширения файла
    if (($_FILES['file']['size'] < 10 * 1024 * 1024) && ($type != 'exe') && ($type != 'sh')) {
        $user->uploadFile($_FILES['file']); //Загржаем файл на диск
        $_SESSION['message'] = "Файл загружен";
    } else {
        $_SESSION['message'] = "В загрузке отказано";
    }
    loadContent($user); //Возвращаем разметку
}
if (isset($_POST['delete'])) { //Этот параметр приходит из JS 
    $user->delete($_POST['delete']);
    loadContent($user); //Возвращаем разметку
}
if (isset($_GET['download'])) { //пользователь перешел на страницу с эти параметром
    $arrFromName = explode('/', $_GET['download']);
    $fileOnServer = $user->downloadToServer($_GET['download'], end($arrFromName)); //Скачеваем файл на сервер и получаем его расположение на сервере
    $user->loadFromServer($fileOnServer); //Скачиваем файл с сервера
    header("Location: index.php"); //Редиректим пользователья, а не возвращяем разметку, т.к. мы изменяли заголовки при скачивании файла
}
if (isset($_POST['rename'])) { //Этот параметр приходит из JS 
    $user->rename($_POST['rename'], $_POST['newname']);
    loadContent($user); //Возвращаем разметку
}
