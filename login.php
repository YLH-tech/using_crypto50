<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Page</title>

    <!-- Style link -->
    <link rel="stylesheet" href="./style/login-signup.css">
    <script src="js/eye.js" defer></script>

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
        <form action="./php/login/login.php" method="post">
            <h1>Welcome to CoinEX!</h1>
            <p>Ready to trade easily!</p>
            <h4> <?php if (isset($_GET['error'])) { ?>
                    <p class="error"> <?php echo $_GET['error']; ?></p>
                <?php } ?>
            </h4>
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


            <div class="btn-container"> <!-- For align the buttons -->
                <!-- Forgot & Create Btns   -->
                <span class="forgot-create">
                    <a href="php/login/forgot.php">
                        <h5 id="forgot-btn">Forgot password?</h5>
                    </a>
                    <a href="signup.php">Create an account?</a>
                </span>
                <br>
                <!-- Under Buttons -->
                <span class="btn-gp">
                    <button type="submit" class="active-btn" name="login">Login</button>
                    <a href="./index.php" class="active-btn guest-btn">As a Guest</a>
                </span>
            </div>
        </form>
    </main>
</body>

</html>