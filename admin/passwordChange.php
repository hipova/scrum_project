<?php
session_start();
require_once '../Database.php';
$errors = [];
if (!isset($_SESSION['email'])) {
    header("Location:index.php");
}
//php for password change
//1. Check is Password Change button is clicked
if (isset($_POST['change'])) {

    $email = $_SESSION['email'];
    //2. Trim $_POST to remove accidental extra whitespace from inputs and put in variable
    $password = !empty($_POST['password']) ? trim($_POST['password']) : null;
    $passwordNew = !empty($_POST['newPassword']) ? trim($_POST['newPassword']) : null;
    $passwordConfirmNew = !empty($_POST['confirmNewPassword']) ? trim($_POST['confirmNewPassword']) : null;

    //3. Check if fields are empty
    if (empty($password)) {
        $errors[] = "Password is required!";
    }
    if (empty($passwordNew)) {
        $errors[] = "Password is required!";
    }
    if (empty($passwordConfirmNew)) {
        $errors[] = "Password is required!";
    }
    //4. Check if current password matches user email
    if (count($errors) == 0) {
        $db = new Database();
        $sql = "SELECT password FROM users WHERE email = :email";
        $db->executeWithParam($sql, array(array(':email', $email)));
        $resultSet = $db->single();
//        echo '<pre>';
//        print_r ($resultSet);
//        echo '</pre>';
//        print ($resultSet['password']);
//        die();
        //5. Check if current
        if (!password_verify($password, $resultSet['password'])) {
            $errors[] = "Password for " . $email . " is not correct";
        }

        if (count($errors) == 0) {

            //7. Check if passwords match
            if ($passwordNew !== $passwordConfirmNew) {
                $errors[] = "De 2 nieuwe wachtwoorden zijn niet gelijk";
            } else {
                $sql = 'UPDATE users SET password = :password WHERE email = :email';
                $db->executeWithParam($sql, array(array(':password', password_hash($passwordConfirmNew, PASSWORD_BCRYPT)), array(':email', $_SESSION['email'])));
                $db = null;
                require_once 'logout.php';
//                header("Location:loggedIn.php");
//                $_SESSION['password'] = true;
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Change password</title>
</head>
<body>
<?= $_SESSION['email']; ?>
<br><br>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-4 text-center">
            <form action="passwordChange.php" method="post">
                <div class="form-group">
                    <label for="exampleInputEmail1">Huidig wachtwoord</label>
                    <input type="password" class="form-control" id="exampleInputEmail1"
                           placeholder="Voer je wachtwoord in" name="password">
                    <small id="emailHelp" class="form-text text-muted">Voer je huidige wachtwoord in.</small>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Nieuw wachtwoord</label>
                    <input type="password" class="form-control" id="exampleInputPassword1"
                           placeholder="Je nieuwe wachtwoord"
                           name="newPassword">
                    <small id="emailHelp" class="form-text text-muted">Voer je nieuwe wachtwoord in.</small>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Nieuw wachtwoord bevestigen</label>
                    <input type="password" class="form-control" id="exampleInputPassword1"
                           placeholder="Herhaal je nieuwe wachtwoord"
                           name="confirmNewPassword">
                    <small id="emailHelp" class="form-text text-muted">Voer je nieuwe wachtwoord volledig in.</small>
                </div>
                <button type="submit" class="btn btn-primary" name="change">Wijzig wachtwoord</button>
            </form>
        </div>
        <div class="col"></div>
    </div>
</div>
<br/>
<!-- implode —> Join array elements with a string and use seperator <br><br> in this case (showing the different error messages under each other)-->
<p class="text-center"><?php echo implode("<br><br>", $errors); ?></p>
</body>
</html>