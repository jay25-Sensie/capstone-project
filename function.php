<?php

function check_login($con)
{
    if(isset($_SESSION['userID']))
    {
        $id = $_SESSION['userID'];
        $query ="select * from users where userID = '$id' limit 1";

        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0)
        {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }
    //redirect to login
    header("location: Admin_Staff_login.php");
    die;
}