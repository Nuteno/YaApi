<?php
// require_once __DIR__ . '/vendor/autoload.php';
include_once 'php/user.php';




if (!isset($_SESSION['token'])) {

    if (isset($_GET['code'])) {
        $user->setToken($_GET['code']);
    } else {
        header('Location: login.php');
    }
}

$user = new User();
$user->connect();


if (isset($_GET['path'])) {
    $user->setPath($_GET['path']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Disk</title>
</head>


<body>
    <main class="main">
        <div class="files">
            <h1 class="path">Файлы/<?php echo $user->path ?></h1>
            <div class="files_ul">


                <?php
                $pathNumber =  count(explode("/", $user->path)) - 1;
                if ($pathNumber > 0) {
                    if ($pathNumber == 1)  echo  '<a class="item_link" href="index.php?path=">...</a>';
                    else {
                        $arrFromPath = explode("/", $user->path);
                        $prevPath = str_replace('/' . $arrFromPath[$pathNumber - 1], '', $user->path);
                        echo  '<a class="item_link dir" href="index.php?path=' . $prevPath . '">...</a>';
                    }
                }
                foreach ($user->getContent(isset($_GET['pag']) ? $_GET['pag'] : 0) as $item) {
                    if ($item->isDir()) {
                        $path = $item->getPath();
                        $path = mb_substr($path, 6);
                        echo '<a class="item_link dir" href="index.php?path=' . $path . '/"><span class="material-symbols-outlined">
                        folder
                        </span>' . $item->name . '</a>';
                    } else {
                        echo '<a href="#" class="file item_link">
                            <span class="material-symbols-outlined">
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
                    echo '<div class="pag_wrapper" data-pag="' . (isset($_GET['pag']) ? $_GET['pag'] : '0') . '">';
                    if (!isset($_GET['pag']) || $_GET['pag'] == 0) {
                        echo '<a class="pag_link"  href="index.php?' . explode("&", $_SERVER['QUERY_STRING'])[0] . '&pag=1"><span class="material-symbols-outlined">
                        arrow_forward_ios
                        </span></a>';
                    } elseif ($_GET['pag'] == $user->pagination()) {
                        echo '<a class="pag_link"  href="index.php?' . explode("&", $_SERVER['QUERY_STRING'])[0] . '&pag=' . ($_GET['pag'] - 1) . '"><span class="material-symbols-outlined">
                        arrow_back_ios
                        </span></a>';
                    } else {
                        echo '<a class="pag_link" href="index.php?' . explode("&", $_SERVER['QUERY_STRING'])[0] . '&pag=' . ($_GET['pag'] - 1) . '"><span class="material-symbols-outlined">
                        arrow_back_ios
                        </span></a>';
                        echo '<a class="pag_link"  href="index.php?' . explode("&", $_SERVER['QUERY_STRING'])[0] . '&pag=' . ($_GET['pag'] + 1) . '"><span class="material-symbols-outlined">
                        arrow_forward_ios
                        </span></a>';
                    }
                    echo '</div>';
                }

                ?>

            </div>

        </div>
        <div class="upload">
            <form enctype="multipart/form-data" method="post" class="form">
                <input type="file" name="file" id="input_file">
                <input type="submit" name="sub" id="input_sub" value="Загрузить">
            </form>
        </div>
    </main>
    <div class="modal_rename_wrapper">
        <div class="modal_rename">
            <form action="" class="rename_form">
                <h5>Введите новое имя</h5>
                <input type="text" name="rename" id="rename_input">
                <input type="submit" name="rename_sub" class="rename_sub" value="Переименовать">
            </form>
            <span class="material-symbols-outlined icon_close">
                close
            </span>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>

</html>