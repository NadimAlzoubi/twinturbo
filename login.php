<?php
session_start();
include_once('./inc/connect.php');
include_once('./constants.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $query = "SELECT * FROM users WHERE BINARY username = ? LIMIT 1";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION["sau_user_id"] = $row["id"];
            $_SESSION["sau_user_full_name"] = $row["full_name"];
            $_SESSION["sau_user_location"] = $row["location"];
            $_SESSION["sau_user_role"] = $row["role"];
            $_SESSION["sau_user_status"] = $row["status"];
            $_SESSION["sau_ver"] = $nadim->next_version;

            if($_SESSION["sau_user_status"] == 0){
                $loginError = "تم تعطيل هذا الحساب";
                $loginError .= "<br />";
                $loginError .= "This account has been disabled";
            }else{
                header("Location: index.php");
            }
        } else {
            $loginError = "اسم المستخدم أو كلمة المرور غير صحيحة";
            $loginError .= "<br />";
            $loginError .= "The username or password is incorrect";
        }
    }  else {
        $loginError = "اسم المستخدم أو كلمة المرور غير صحيحة";
        $loginError .= "<br />";
        $loginError .= "The username or password is incorrect";
    }
    $stmt->close();
    $connection->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/login.css">
    <title>Login</title>
</head>
<body class="gradient-background">
    <div class="wrapper">
        <form method="POST" class="form-signin">       
        <h2 class="form-signin-heading">LOGIN
        </h2>
        <input value="<?php echo isset($_POST['username']) ? trim($_POST['username']) : ''; ?>" type="text" class="form-control" name="username" placeholder="Username" required autofocus="" autocomplete="off"/>
        <input type="password" class="form-control" name="password" placeholder="Password" required autocomplete="off"/>      
        <?php if(isset($loginError)) { echo "<p>$loginError</p>"; } ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>   
        </form>
    </div>
    <center>
    <h3>Twin Turbo Express</h3>
    <br>
    <span style="line-height: 1.9">
        Contact The Developer: <?php echo $nadim->name; ?><br>
        WebSite: <a href="<?php echo $nadim->website_link; ?>" target="_blank"><?php echo $nadim->website; ?></a><br>
        Phone: <a href="tel:<?php echo $nadim->phone1_link; ?>"><?php echo $nadim->phone1; ?></a> | <a href="tel:<?php echo $nadim->phone2_link; ?>"><?php echo $nadim->phone2; ?></a><br>
        E-mail: <a href="mailto:<?php echo $nadim->email1; ?>"><?php echo $nadim->email1; ?></a> | <a href="mailto:<?php echo $nadim->email2; ?>"><?php echo $nadim->email2; ?></a>
    </span>
    </center>
</body>
</html>
