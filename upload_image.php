<?php
$target_dir = "uploaded_images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$filename = basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
list($width_original, $height_original) = getimagesize($_FILES["fileToUpload"]["tmp_name"]);


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

if($width_original != $height_original) {
    echo "Please upload a square image!";
    $uploadOk = 0;
    exit();
        
} else {
    $uploadOk = 1;
}

if($width_original < 500) {
    echo "Please upload an image larger than 500px!";
    $uploadOk = 0;
    exit();
        
} else {
    $uploadOk = 1;
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 50000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        
        
        list($width_original, $height_original) = getimagesize($target_file);
        $onlyname = basename($target_file, ".".$imageFileType);
        /**
         * Coverting user image to jpg
         */
        if($imageFileType != "jpg" && $imageFileType != "jpeg") {
            $temp_img = "";
            if ($imageFileType == "gif") {
                $temp_img = imagecreatefromgif($target_file);
            } else if ($imageFileType == "png") {
                $temp_img = imagecreatefrompng($target_file);
            }
            $tci = imagecreatetruecolor($width_original, $height_original);
            imagecopyresampled($tci, $temp_img, 0, 0, 0, 0, $width_original, $height_original, $width_original, $height_original);  

            imagejpeg($tci, "converted_images/".$onlyname.".jpg", 100);
            $target_file = "converted_images/".$onlyname.".jpg";
        }

        /**
         * Resinzing the user image if width and height larger than 500px
         */
        if ($width_original < 500) {
            $temp_img2 = "";
            $img = imagecreatefromjpeg($target_file);
            $tci = imagecreatetruecolor(500, 500);
            imagecopyresampled($tci, $img, 0, 0, 0, 0, 500, 500, $width_original, $height_original);
            imagejpeg($tci, "resized_images/".$onlyname.".jpg", 100);
            $target_file = "resized_images/".$onlyname.".jpg";
        }

        /**
         * Watermarking the user image
         * @var [type]
         */
        $watermark = imagecreatefrompng('overlay.png');
        imagealphablending($watermark, false);
        imagesavealpha($watermark, true);
        $img = imagecreatefromjpeg($target_file);
        $img_width = imagesx($img);
        $img_height = imagesy($img);
        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);
        $dst_x = ($img_width/2) - ($watermark_width/2);
        $dst_y = ($img_height/2) - ($watermark_height/2);
        imagecopy($img, $watermark, $dst_x, $dst_y, 0, 0, $watermark_width, $watermark_height);
        imagejpeg($img, "generated_images/".basename( $_FILES["fileToUpload"]["name"]), 100);
        imagedestroy($img);
        imagedestroy($watermark);

        

        $url = 'generated_images/'.basename( $_FILES["fileToUpload"]["name"] );
        // $url = 'http://localhost/bal/generated_images/'.basename( $_FILES["fileToUpload"]["name"] );
        // copy('http://localhost/bal/generated_images/'.basename( $_FILES["fileToUpload"]["name"]), 'flower.jpg');

        $filename = $url;
        // Validate the filename (You so don't want people to be able to download
        // EVERYTHING from your site...)

        if (!file_exists($filename))
        {
            header('HTTP/1.0 404 Not Found');
            die();
        }
        // A check of filemtime and IMS/304 management would be good here

        // Be sure to disable buffer management if needed
        while(ob_get_level()) {
           ob_end_clean();
        }

        // Do not send out full path.
        $basename = basename($filename);

        Header('Content-Type: application/download');
        Header("Content-Disposition: attachment; filename=\"$basename\"");
        header('Content-Transfer-Encoding: binary'); // Not really needed
        Header('Content-Length: ' . filesize($filename));
        Header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

        readfile($filename);

        header('Location: http://localhost/bal?message=Success');
    } else {
        echo "Sorry, there was an error creating your file.";
    }
}
?>