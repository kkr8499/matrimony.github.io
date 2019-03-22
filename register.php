<?php
error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");
use PHPMailer\PHPMailer\PHPMailer;
$msg = "";
include('config.php');

if(isset($_POST['submit'])) {
    $name = $db->real_escape_string( $_POST['name']);
    $email = $db->real_escape_string($_POST['email']);
    $password = $db->real_escape_string($_POST['password']);
    $cPassword = $db->real_escape_string($_POST['cPassword']);
    
    if($name == "" || $email == "" || $password != $cPassword) {
        $msg = "Please check your inputs!";
    } else {
        $sql = $db->query("SELECT id FROM users WHERE email='$email'" );
        if($sql->num_rows > 0) {
            $msg = "Email already exists in the database!";
        } else {
            $token = 'qwertzuiopasdfghjklyxcvbnmQWERTZUIOPASDFGHJKLYXCVBNM0123456789!$/()*';
            $token = str_shuffle($token);
            $token = substr($token, 0,10);

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            try {
                 $result = $db->query("INSERT INTO users(username,email,password,isemailconfirmed,token) VALUES('$name','$email','$hashedPassword',0,'$token')");
             
               if($result) {
                    include_once('PHPMailer/PHPMailer.php');
                     include_once('PHPMailer/Exception.php');
                     include_once('PHPMailer/SMTP.php');
            
                    $mail = new PHPMailer(true);
                    
                    //Server settings
                     $mail->SMTPDebug = 2;                                 // Enable verbose debug output
                      $mail->isSMTP();                                      // Set mailer to use SMTP
                      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                      $mail->SMTPAuth = true;                               // Enable SMTP authentication
                      $mail->Username = 'kirankumarr8499@gmail.com';                 // SMTP username
                     $mail->Password = '8499#Tanu';                           // SMTP password
                      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                     $mail->Port = 587;   
                    
                    $mail->setFrom('kirankumarr8499@gmail.com');
                    $mail->addAddress($email, $name);
                    $mail->Subject = "Please verify email";
                    $mail->isHTML(true);
                    $mail->Body = "
                    Please click on the link below<br><br>
                    <a href='http://localhost:8080/Matrimony/confirm.php?email=$email&token=$token'>Click here</a>
                    ";
                
                    if($mail->send()) {
                        $msg = "You have been registered!. Please verify your email";
                    } else {
                        $msg = "something wrong happend! Please try again";
                    }
               } else {
                   $msg = "DB error$result";
               }
            }
            catch (Exception $e) {
                $msg = "exception:"+$e->getMessage();
            }
          
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">


</head>
<body>
<div class="container" style="margin-top: 100px;">
    <div class="row justify-cotent-center">
        <div class="col-md-6 col-md-offset-3" align="center">
           <!--  <img src="img/logo.png"><br><br> -->
			<?php if ($msg != "") echo $msg ."<br><br>"?>
            <form method="post" action="register.php">
                <input class="form-control" name="name" placeholder="Name..."><br>
                <input class="form-control" name="email" type="email" placeholder="Email..."><br>
                <input class="form-control" name="password" type="password" placeholder="Password..."><br>
                <input class="form-control" name="cPassword" type="password" placeholder="Confirm Password..."><br>
                <input class="btn btn-primary" type="submit" name="submit" value="Register">
            </form>
        </div>
    </div>
</div>
</body>
</html>
