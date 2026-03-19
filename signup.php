<?php
session_start();

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // i will go in and change to match database this is placeholder
    $firstName = trim($_POST["first_name"] ?? "");
    $lastInitial = trim($_POST["last_initial"] ?? "");
    $dateOfBirth = trim($_POST["date_of_birth"] ?? "");
    $phoneNumber = trim($_POST["phone_number"] ?? "");
    $emailAddress = trim($_POST["email_address"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $criminalDate = trim($_POST["criminal_date"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $transportation = trim($_POST["transportation"] ?? "");
    $facility = trim($_POST["facility"] ?? "");
    $facilityDate = trim($_POST["facility_date"] ?? "");
    $probationStatus = trim($_POST["probation_status"] ?? "");

    // validation - also a placeholder, will change later
    if (
        empty($firstName) || empty($lastInitial) || empty($dateOfBirth) ||
        empty($phoneNumber) || empty($emailAddress) || empty($password) ||
        empty($criminalDate) || empty($gender) || empty($transportation) ||
        empty($facility) || empty($facilityDate) || empty($probationStatus)
    ) {
        $errorMessage = "All fields marked with * are required. Please complete the form.";
    } elseif (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
        // will also check if email already exists w database
    } elseif (strlen($password) < 8) {
        $errorMessage = "Password must be at least 8 characters long.";
        //will add more password validation 
    } else {
        // will insert database here
        $successMessage = "Account created successfully. You can now log in.";

        $_POST = array();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChronoSync - Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="signup-wrapper">
    <div class="signup-card">

        <div class="signup-topbar">
            <div class="brand">CHRONOSYNC</div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="#">Availability</a>
                <a href="#">Assignments</a>
                <a href="#">Profile</a>
            </div>
        </div>

        <div class="signup-body">
            <h1>Create Your Account</h1>

            <div class="signup-subtext">
                All fields marked with <span class="required">*</span> are required.
                Your information is kept confidential.
            </div>

            <?php if (!empty($errorMessage)) : ?>
                <div class="message-box">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)) : ?>
                <div class="success-box">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="signup.php">

                <div class="section-title">Personal Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name"
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="last_initial">Last Name / Initial <span class="required">*</span></label>
                        <input type="text" id="last_initial" name="last_initial"
                               value="<?php echo htmlspecialchars($_POST['last_initial'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number <span class="required">*</span></label>
                        <input type="text" id="phone_number" name="phone_number" placeholder="(555) 123-4567"
                               value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email_address">Email Address <span class="required">*</span></label>
                        <input type="email" id="email_address" name="email_address"
                               value="<?php echo htmlspecialchars($_POST['email_address'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password">
                        <div class="field-hint">
                            Minimum 8 characters; uppercase, lowercase, number, special character recommended.
                        </div>
                    </div>
                </div>

                <div class="section-title">Recovery Information</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="criminal_date">Criminal Date <span class="required">*</span></label>
                        <input type="date" id="criminal_date" name="criminal_date"
                               value="<?php echo htmlspecialchars($_POST['criminal_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender <span class="required">*</span></label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Female" <?php echo (($_POST['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Male" <?php echo (($_POST['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Non-Binary" <?php echo (($_POST['gender'] ?? '') == 'Non-Binary') ? 'selected' : ''; ?>>Non-Binary</option>
                            <option value="Prefer Not To Say" <?php echo (($_POST['gender'] ?? '') == 'Prefer Not To Say') ? 'selected' : ''; ?>>Prefer Not To Say</option>
                        </select>
                    </div>
                </div>

                <div class="section-title">Transportation &amp; Location</div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="transportation">Transportation <span class="required">*</span></label>
                        <select id="transportation" name="transportation">
                            <option value="">How will you transport with driver's license?</option>
                            <option value="Neighborhood" <?php echo (($_POST['transportation'] ?? '') == 'Neighborhood') ? 'selected' : ''; ?>>Neighborhood</option>
                            <option value="Bus Line" <?php echo (($_POST['transportation'] ?? '') == 'Bus Line') ? 'selected' : ''; ?>>Bus Line</option>
                            <option value="Own Vehicle" <?php echo (($_POST['transportation'] ?? '') == 'Own Vehicle') ? 'selected' : ''; ?>>Own Vehicle</option>
                            <option value="Ride From Others" <?php echo (($_POST['transportation'] ?? '') == 'Ride From Others') ? 'selected' : ''; ?>>Ride From Others</option>
                        </select>
                    </div>
                </div>

                <div class="section-title">Facility &amp; Legal History</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="facility">Visited Facility? <span class="required">*</span></label>
                        <select id="facility" name="facility">
                            <option value="">Select Facility</option>
                            <option value="Serenity Treatment Center" <?php echo (($_POST['facility'] ?? '') == 'Serenity Treatment Center') ? 'selected' : ''; ?>>Serenity Treatment Center</option>
                            <option value="Beacon House" <?php echo (($_POST['facility'] ?? '') == 'Beacon House') ? 'selected' : ''; ?>>Beacon House</option>
                            <option value="Hope Recovery" <?php echo (($_POST['facility'] ?? '') == 'Hope Recovery') ? 'selected' : ''; ?>>Hope Recovery</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="facility_date">Date Visited <span class="required">*</span></label>
                        <input type="date" id="facility_date" name="facility_date"
                               value="<?php echo htmlspecialchars($_POST['facility_date'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="probation_status">Probation Status <span class="required">*</span></label>
                        <select id="probation_status" name="probation_status">
                            <option value="">Select Status</option>
                            <option value="Not on Probation" <?php echo (($_POST['probation_status'] ?? '') == 'Not on Probation') ? 'selected' : ''; ?>>Not on Probation</option>
                            <option value="Currently on Probation" <?php echo (($_POST['probation_status'] ?? '') == 'Currently on Probation') ? 'selected' : ''; ?>>Currently on Probation</option>
                            <option value="Completed Probation" <?php echo (($_POST['probation_status'] ?? '') == 'Completed Probation') ? 'selected' : ''; ?>>Completed Probation</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="signup-btn">Create Account</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
