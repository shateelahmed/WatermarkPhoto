<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <form action="upload_image.php" method="post" enctype="multipart/form-data" id="imageUploadForm">
            <label class="btn btn-primary btn-lg btn-file" id="selectImageButton">
                Click Here to Select an Image <input type="file" name="fileToUpload" id="fileToUpload" class="hidden">
            </label>
            <div id="image-holder"></div>
            <input type="submit" class="btn btn-success btn-lg" value="Download Image" name="submit" id="submitButton" style="display: none;">
        </form>
        <script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script>
            $("#fileToUpload").on('change', function () {
                var imgPath = $(this)[0].value;

                var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

                // alert(extn);
                if (imgPath != "" && extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                    if (typeof (FileReader) != "undefined") {
                            $("#image-holder").empty();

                            var reader = new FileReader();
                            reader.onload = function (e) {
                                $("#image-holder").css('background-image', 'url(' + e.target.result + ')');
                                $("<img />", {
                                    "src": "overlay.png",
                                    "class": "thumb-image",
                                    "height": "400",
                                    "width": "400"
                                }).appendTo($("#image-holder"));
                            }
                            // image_holder.show();
                            reader.readAsDataURL($(this)[0].files[0]);
                            // document.getElementById('submitButton').disabled = false;
                            $("#submitButton").show();
                    } else {
                        $("#submitButton").hide();
                        alert("This browser does not support FileReader.");
                    }
                } else {
                    $("#submitButton").hide();
                    // document.getElementById('submitButton').disabled = true;
                    alert("You have to select an image!");
                    // $('#imageUploadForm')[0].reset();
                    location.reload();
                }
            });
        </script>
    </body>
</html>