<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>

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
    <div id="term-agreement" class="term-agreement-container" hidden>
        <h1>Term and agreement</h1>
        <br>
        <div class="term-agreement-sub-container">
            <h1 id="welcome-heading">Welcome to Bithumbnn, the Future of Cryptocurrency Trading!</h1>

            <h2> Acceptance of Terms</h2>
            <p>By accessing Bithumbnn and using the services provided herein, you acknowledge that you have read,
                understood,
                and agreed to be bound by these Terms and Conditions. If you do not accept these terms, you must not
                register or
                continue to use our services.</p>

            <h2>Eligibility to Participate</h2>
            <p>You affirm that you are of legal age and have the legal capacity to enter into this agreement. Bithumbnn
                is not
                available to persons under the age of 18 or users previously suspended or removed from the platform.</p>

            <h2>Account Integrity</h2>
            <p>Upon creating an account with Bithumbnn, you agree to provide truthful and accurate information and to
                maintain
                the accuracy of such information. The security of your account credentials is your responsibility, and
                any
                activities under said account will be deemed as having been performed by you.</p>

            <h2>Services Offered</h2>
            <p>Bithumbnn provides a cutting-edge digital platform for trading a variety of cryptocurrencies. We reserve
                the
                right to modify, suspend, or discontinue any aspect of our services at any time, without notice.</p>

            <h2>Financial Terms</h2>
            <p>Fees for transactions and other services are subject to change and may vary based on market conditions.
                All
                financial transactions on Bithumbnn must comply with the financial regulations and standards set forth
                by
                relevant authorities.</p>

            <h2>Risk Acknowledgment</h2>
            <p>Trading cryptocurrencies involves significant risk. Prices can fluctuate on any given day. Due to such
                price
                fluctuations, you may increase or lose value in your assets at any given moment. You are solely
                responsible for
                your trading decisions and the risks associated with cryptocurrency trading.</p>

            <h2>Prohibited Conduct</h2>
            <p>You are prohibited from engaging in any form of illegal activity on Bithumbnn, including but not limited
                to
                fraud, money laundering, and other activities that could involve unlawful behavior.</p>

            <h2>Intellectual Property Rights</h2>
            <p>All content on Bithumbnn, including text, graphics, logos, and software, is the exclusive property of
                Bithumbnn
                and is protected by intellectual property laws. You may not use any content from our platform without
                express
                permission.</p>

            <h2>Privacy Assurance</h2>
            <p>Your privacy is important to us. Please review our Privacy Policy, which explains how we handle personal
                information. The Privacy Policy is part of these Terms and is incorporated by reference to provide you
                with full
                disclosure.</p>

            <h2>Account Termination</h2>
            <p>You may terminate your Bithumbnn account at any time. We also reserve the right to suspend or terminate
                your
                account if you violate any of the terms outlined here or engage in any activity deemed harmful to
                Bithumbnn or
                its users.</p>

            <h2>Dispute Resolution</h2>
            <p>In the event of a dispute, efforts should be made to resolve the issue amicably before resorting to
                formal
                arbitration or litigation.</p>

            <h2>Changes to the Terms</h2>
            <p>Bithumbnn reserves the right to modify these terms at any time. Your continued use of the platform after
                such
                modifications will constitute acknowledgment and acceptance of the modified terms.</p>

            <h2>Miscellaneous</h2>
            <p>These Terms are governed by the laws of [Jurisdiction]. Should any part of these Terms be held invalid or
                unenforceable, that portion shall be construed in a manner consistent with applicable law to reflect, as
                nearly
                as possible, the original intentions of the parties, and the remaining portions shall remain in full
                force and
                effect.</p>

            <h2>Contact Us</h2>
            <p>For any questions or concerns about these Terms and Conditions, please contact us at [Contact
                Information].</p>
            <br>
            <button id="cancel" class="btns" onclick="open_close()">Close</button>
        </div>
    </div>
    <main>
        <!-- <img src="./assets/images/Sign up-amico.svg" alt="Background"> -->
        <form action="php/login/register.php" method="post">
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
            <input type="checkbox" id="term_agreement" onchange="activated()" required><label for="term_agreement"> Accept our <a
                    href="#" onclick="open_close()">term and
                    agreement</a></label>
            <br>
            <br>

            <span class="btn-container"> <!-- For align the buttons -->
                <input type="submit" class="active-btn" name="register" value="Register">
                <a href="login.php">Have you already an account?</a>
            </span>
        </form>
    </main>

    <!-- Scripts -->
    <script>
        let signup = document.getElementById("signup-btn");
        let toConfirm = document.getElementById("confirm");
        let term_agreement = document.getElementById("term_agreement");

        function isClickAgree() {
            term_agreement.setAttribute("checked", "");
        }

        // Term and agreement Clicking btns
        function open_close() {
            let tag = document.querySelector(".term-agreement-container");

            let main = document.getElementById("main-container");

            if (tag.classList.contains("show")) {
                tag.classList.remove("show");
                main.style.filter = "blur(0px)";
            } else {
                tag.classList.add("show");
                main.style.filter = "blur(10px)";
            }
        }

        // If agree btns is active, checkbox will be clicked.
        function isClickAgree() {
            if (!toConfirm.classList.contains("checked")) {
                term_agreement.setAttribute("checked", "");
                toConfirm.classList.add("checked");
            }
        }

        // Function on Checkbox for activating on term and agreement.
        function activated() {
            if ($(term_agreement).is(":checked")) {
                toConfirm.classList.add("checked");
                term_agreement.setAttribute("checked", "");
            } else {
                toConfirm.classList.remove("checked");
                term_agreement.removeAttribute("checked", "");
            }
        }
    </script>
</body>

</html>