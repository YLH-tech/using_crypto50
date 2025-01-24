<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up Page</title>

    <!-- Style link -->
    <link rel="stylesheet" href="../style/login-signup.css">
    <script src="../js/eye.js" defer></script>


    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .error {
            background: #F2DEDE;
            color: #e02f2c;
            padding: 10px;
            width: 95%;
            border-radius: 5px;
            margin: 20px auto;
        }
    </style>
</head>


<body>
    <main>
        <!-- <img src="./assets/images/Sign up-amico.svg" alt="Background"> -->
        <form action="../php/login/adminRegister.php" method="post">
            <h1>Welcome to CoinEX!</h1>
            <p>Glad to see you!</p>
            <h4> <?php if (isset($_GET['error'])) { ?>
                    <p class="error"> <?php echo $_GET['error']; ?></p>
                <?php } ?>
            </h4>
            <!-- Name input -->
            <fieldset>
                <legend>Name</legend>
                <span>
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" placeholder="Enter name" required />
                </span>
            </fieldset>

            <!-- Email input -->
            <fieldset>
                <legend>Email</legend>
                <span>
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter email" required />
                </span>
            </fieldset>

            <!-- Password input -->
            <fieldset>
                <legend>Password</legend>
                <span>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Enter password" required />

                    <i class="fas fa-eye toggle-password" data-toggle="#password"></i>

                </span>
            </fieldset>

            <!-- Retype-password input -->
            <fieldset>
                <legend>Retype Password</legend>
                <span>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="retype_password" id="confirmPassword" placeholder="Retype password" required />
                    <i class="fas fa-eye toggle-password" data-toggle="#confirmPassword"></i>
                </span>
            </fieldset>

            <span class="btn-container"> <!-- For align the buttons -->
                <input type="submit" class="active-btn" name="register" value="register">
                <a href="admin_login.php">Have you already an account?</a>
            </span>
        </form>
    </main>


</body>

</html>