<?php
    require 'vendor/autoload.php';

    $processed = false;
    $storeFolder = 'uploads';

    if (!empty($_FILES))
    {
        $tempFile = $_FILES['file']['tmp_name'];
        $targetPath = dirname( __FILE__ ) . '/' . $storeFolder . '/';
        $targetFile =  $targetPath . $_FILES['file']['name'];
        move_uploaded_file($tempFile, $targetFile);

        $cropped_file = $storeFolder.'/cropped_'.$_FILES['file']['name'];
        $face_detect = new svay\FaceDetector('vendor/mauricesvay/php-facedetection/detection.dat');
        $face_detect->faceDetect($targetFile);
        $face_detect->cropFaceToJpeg($cropped_file);
        
        @unlink($targetFile);

        echo $cropped_file;
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>CropFace</title>
    <style type="text/css">
    * { padding: 0; margin: 0; box-sizing: border-box; }
    body {
        font-family: sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: black;
        background-image: url('/assets/photography.png');
        background-repeat: repeat;
    }
    .wrapper {
        margin: 20px auto;
        padding: 20px;
        width: 400px;
        height: auto;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .3);
    }
    h1 {
        margin-bottom: 10px;
    }
    #dropzone { height: 200px; width: 100%; margin: 20px auto 10px; border: 1px dashed #ccc; }
    #dropzone:hover { cursor: pointer;  }
    .dz-details, .dz-success-mark, .dz-error-mark { display: none; }
    .dz-image { text-align: center; }
    #results { margin-top: 10px; text-align: center; }
    #results img { display: inline-block; margin: 0 5px 5px 0; width: 80px; height: 80px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>CropFace</h1>
        <p>Déposez une image pour obtenir une version recadrée sur le visage.</p>
        <div id="dropzone"></div>
        <div id="results"></div>
    </div>
    <script src="/assets/dropzone.js"></script>
    <script type="text/javascript">
        var myDropzone = new Dropzone("div#dropzone", {
            url: '/index.php',
            method: 'POST',
            uploadMultiple: false,
            maxFiles: 1,
        });

        myDropzone.on("complete", function(file) {
            var $img = document.createElement("img"),
                $results = document.getElementById('results');
            
            $img.setAttribute('src', file.xhr.response);
            $img.setAttribute('width', 80);
            $img.setAttribute('height', 80);
            $results.insertBefore($img, $results.firstChild);
            myDropzone.removeFile(file);
        });
    </script>
</body>
</html>