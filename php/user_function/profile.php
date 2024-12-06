

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>

    <!-- Style link -->
    <link rel="stylesheet" href="./style/profile.css">


    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <h1 class="w-[80%] m-auto text-4xl">Account Settings</h1>
    <br><br>
    <!-- main container -->
    <div class="main-container">
        <!-- Side Bar Navigation -->
        <nav>
            <h2>My Profile</h2>
            <a href="#">Security</a>
            <a href="#">Teams</a>
            <a href="#">Team Member</a>
            <a href="#">Notification</a>
            <a href="#">Billing</a>
            <a href="#">Data Expart</a>
            <br>
            <a href="#" id="delete-acc">Delete Account</a>
        </nav>

        <!-- Info Container -->
        <div class="info-container">
            <h2 class="text-2xl">My Profile</h2>
            <br>
            <!-- Name Div -->
            <div class="name-container">
                <div class="name-sub-container">
                    <img src="./assets/images/profile.png" alt="Profile">
                    <!-- Details -->
                    <span>
                        <h3 class="text-2xl font-bold">Rafiqur Rahman</h3> <!-- Name -->
                        <h5 class="">Team Manager</h5> <!-- Position -->
                        <p>Leads, United Kingom</p> <!-- Description -->
                    </span>
                </div>
                <!-- Edit Btn -->
                <button class="edit-btns">Edit <i class="fa-solid fa-pen-to-square"></i></button>
            </div>
            <br>
            <!-- Personal Information -->
            <div class="personal-info-container">
                <!-- Heading -->
                <div class="personal-info-heading-container">
                    <h4 class="text-2xl">Personal Information</h4>
                    <button class="edit-btns">Edit <i class="fa-solid fa-pen-to-square"></i></button>
                </div>
                <br>
                <!-- Details Container -->
                <div class="personal-info-detail-container">

                    <!-- First Name -->
                    <div>
                        <h5 class="text-2xl font-bold">First Name</h5>
                        <p class="text-[18px]">Rafiqur</p>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <h5 class="text-2xl font-bold">Last Name</h5>
                        <p class="text-[18px]">Rahman</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <h5 class="text-2xl font-bold">Eamil address</h5>
                        <p class="text-[18px]">refiqurrahman51@gmail.com</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <h5 class="text-2xl font-bold">Phone</h5>
                        <p class="text-[18px]">+09 345 678 901</p>
                    </div>

                    <!-- Bio -->
                    <div>
                        <h5 class="text-2xl font-bold">Bio</h5>
                        <p class="text-[18px]">Team Manager</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>