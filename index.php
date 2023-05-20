<?php
    include_once 'lib/config.php';
    include_once 'lib/Database.php';

    $db = new Database();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Image Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">

                <?php
                    if ($_SERVER['REQUEST_METHOD'] == "POST"){
                        $Permitted = array('jpg','jpeg','png','gif','pdf');
                        $file_name = $_FILES['image']['name'];
                        $file_size = $_FILES['image']['size'];
                        $file_temp = $_FILES['image']['tmp_name'];
                        $div_img = explode('.',$file_name);
                        $img_ext = strtolower(end($div_img));
                        $image_unique = substr(md5(time()),0,10).'.'.$img_ext;
                        $uploaded_image = "uploads/".$image_unique;

                        if (empty($file_name)){
                            echo '<span>Please Select Image</span>';
                        }elseif ($file_size > 524288){
                            echo '<span>Image Size Should be less then 512KB</span>';
                        }elseif (in_array($img_ext, $Permitted) === false){
                            echo '<span>Can you Upload only:-'.implode(',',$Permitted).'</span>';
                        }else{
                            move_uploaded_file($file_temp,$uploaded_image);
                            $query = "INSERT INTO `image`(`image`) VALUES ('$uploaded_image')";
                            $inserted_row = $db->insert($query);
                            if ($inserted_row){
                                echo '<span>Image Save Success!</span>';
                            }else{
                                echo '<span>Image Not Save!</span>';
                            }
                        }

                    }
                ?>

                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Upload Image</label><br>
                        <input type="file" class="form-control-file" name="image" id="image">
                    </div>
                    <br>
                    <div>
                        <input type="submit" name="submit" value="Save">
                    </div>

                </form>



                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">User Image</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                    if (isset($_GET['del'])){
                                        $id = $_GET['del'];
                                        //Select Upload Picture
                                        $imgQuery = "SELECT * FROM `image` WHERE `id` = '$id'";
                                        $getUploadImage = $db->select($imgQuery);
                                        if ($getUploadImage){
                                            while ($selectImage = $getUploadImage->fetch_assoc()){
                                                $delUplodImg = $selectImage['image'];
                                                unlink($delUplodImg);
                                            }
                                        }
                                        //Delete Picture from Database
                                        $querydel = "DELETE FROM `image` WHERE `id` = '$id'";
                                        $delImage = $db->delete($querydel);
                                        if ($delImage){
                                            echo '<span>Image Delete Success!</span>';
                                        }else{
                                            echo '<span>Image Not Delete Success!</span>';
                                        }
                                    }
                                ?>

                                <?php
                                //Select Picture from Database
                                $query = "SELECT * FROM `image` ORDER BY `id` DESC";
                                $getImage = $db->select($query);
                                if ($getImage){
                                $i = 0;
                                while ($result = $getImage->fetch_assoc()){
                                $i++;
                                ?>
                                <tr>
                                    <th scope="row"><?=$i;?></th>
                                    <td><img src="<?=$result['image'];?>" alt="" width="120px" height="140px"></td>
                                    <td><a href="?del=<?=$result['id'];?>">Delete</a></td>
                                </tr>
                                    <?php
                                }
                                }
                                ?>
                                </tbody>
                            </table>





            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</body>
</html>