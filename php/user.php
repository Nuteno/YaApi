<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();
class User
{
    public $client_id = '581cf6123ee2464b83790ff825c69879';
    public $client_secret = '757588d48eca4e8f81c44688ef65b3ed';
    public $disc;
    public $client;
    public $path;


    public function SetToken($code)
    {

        $query = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        );
        $query = http_build_query($query);


        $header = "Content-type: application/x-www-form-urlencoded";


        $opts = array(
            'http' =>
            array(
                'method'  => 'POST',
                'header'  => $header,
                'content' => $query
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents('https://oauth.yandex.ru/token', false, $context);
        $result = json_decode($result);


        $token = $result->access_token;
        $_SESSION = [
            'token' => $result->access_token
        ];
    }
    public function connect()
    {
        $this->client = new Arhitector\Yandex\Client\OAuth($_SESSION['token']);
        $this->disk = new Arhitector\Yandex\Disk($this->client);
    }
    public function getContent($pag = 0)
    {
        $resource = $this->disk->getResource('disk:/' . $this->path, 20, $pag * 20)['items'];
        return $resource->toArray();
    }
    public function setPath($pathToAdd)
    {
        $this->path = $this->path . $pathToAdd;
        $_SESSION['path'] = $this->path;
    }
    public function uploadFile($file)
    {
        $resource = $this->disk->getResource('disk:/' . $_SESSION['path'] . $file['name']);
        $has = $resource->has();
        if (!$has) {
            return $resource->upload($file['tmp_name']);
        }
    }
    public function getContentWithSession($pag = 0)
    {
        $resource = $this->disk->getResource('disk:/' . $_SESSION['path'], 20, $pag * 20)['items'];
        return $resource->toArray();
    }

    public function delete($pathToDelete)
    {
        $resource = $this->disk->getResource($pathToDelete);
        $resource->delete();
    }
    public function loadFromServer($file)
    {
        if (file_exists($file)) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
        return true;
    }

    public function downloadToServer($pathToFile, $name)
    {
        $resource = $this->disk->getResource($pathToFile);
        $nameToDownload = time() . '.' . explode('.', $name)[1];
        $resource->download("loads/" . $nameToDownload);
        return "loads/" . $nameToDownload;
    }
    public function rename($pathToFile, $newName)
    {
        $arrFromName = explode('/', $pathToFile);
        $type = explode(".", $pathToFile)[1];
        $newPath = str_replace(end($arrFromName), $newName . '.' . $type, $pathToFile);

        $resource = $this->disk->getResource($pathToFile);
        $resource->move($newPath);
    }
    public function pagination()
    {
        $resource = $this->disk->getResource('disk:/' . $_SESSION['path'], 10000000, 0);
        if ($resource->items->count() > 20) {
            return intdiv($resource->items->count(), 20);
        } else {
            return false;
        }
    }
}
