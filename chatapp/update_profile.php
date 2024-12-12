<?php 
    include 'php/config.php';
    session_start();
    $user_id = $_SESSION['user_id'];

    if(!isset($user_id)){
        header('location: login.php');
    }

    $select = mysqli_query($conn, "SELECT * FROM user_form WHERE user_id = '$user_id' ");
    if(mysqli_num_rows($select) > 0){
        $row = mysqli_fetch_assoc($select);
    }

    if(isset($_POST['update_profile'])){
        $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
        $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

    $update_nm = mysqli_query($conn, "UPDATE user_form SET fname = '$update_name'
                                            WHERE user_id = '$user_id' ");
                            if($update_nm){
                                $alert[] = "Name Update Succesful!" ;
                            }
                    if(filter_var($update_email, FILTER_VALIDATE_EMAIL)){
                        $update_email = mysqli_query($conn, "UPDATE user_form SET email = '$update_email'
                                            WHERE user_id = '$user_id' ");
                    }else{
                        $alert[] = "$update_email is not a valid email!" ;
                    }

    $image = $_FILES['update_image']['name'];
    $image_size = $_FILES['update_image']['size'];
    $image_tmp_name = $_FILES['update_image']['tmp_name'];
    $image_rename = $image;
    $image_folder = 'uploaded_img/'.$image_rename;

    if(!empty($image)){
        if($image_size > 2000000){
            $alert[] = "Image size is too big!" ;
        }else{
            $update_img = mysqli_query($conn, "UPDATE user_form SET img = '$image'
                                            WHERE user_id = '$user_id' ");
            move_uploaded_file($image_tmp_name, $image_folder);
            header('location: update_profile.php');
        }
    }

    $main_pass = $row['password'];
    $old_pass = mysqli_real_escape_string($conn, md5($_POST['old_pass']));
    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

    if(!empty($old_pass) || !empty($new_pass) || !empty($confirm_pass)){
        if($old_pass != $main_pass){
            $alert[] = "Old password not matched!";
        }elseif($new_pass != $confirm_pass){
            $alert[] = "Confirm password not matched!";
        }else{
            $update_pass = mysqli_query($conn, "UPDATE user_form SET password = '$confirm_pass'
            WHERE user_id = '$user_id' ");
            $alert[] = "Password update succesfull!";
        }
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Update profile</title>
</head>
<body>
    <div class="update-profile">
        <form action="" method="post" enctype="multipart/form-data">
        <img src="uploaded_img/<?php echo $row['img'] ?>" alt="">
        <?php 
                if(isset($alert)){
                    foreach($alert as $alert){
                        echo '<div class="alert">'.$alert.'</div>';
                    }
                }
            ?>
            <div class="flex">
                <div class="inputBox">
                    <span>Username:</span>
                    <input type="text" name="update_name" value="<?php echo $row['fname'] ?>" class="box">
                    <span>Email:</span>
                    <input type="email" name="update_email" value="<?php echo $row['email'] ?>" class="box">
                    <span>Update your profile image</span>
                    <input type="file" name="update_image" accept="image/*" class="box">
                </div>
                <div class="inputBox">
                    <span>Old password:</span>
                    <input type="password" name="old_pass" class="box">
                    <span>New password:</span>
                    <input type="password" name="new_pass" class="box">
                    <span>Confirm password</span>
                    <input type="password" name="confirm_pass" class="box">
                </div>
            </div>
            <div class="flex btns">
                <input type="submit" value="update profile" name="update_profile" class="btn">
                <a href="home.php" class="delete-btn">Back</a>
            </div>
        </form>
    </div>
</body>
</html>