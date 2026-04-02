<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChronoSync - My Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="profile-wrapper">
    <div class="profile-card">

        <div class="profile-topbar">
            <div class="brand">CHRONOSYNC</div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="#">Availability</a>
                <a href="#">Assignments</a>
                <a href="profile.php" class="active-link">Profile</a>
            </div>
        </div>

        <div class="profile-body">
            <h1>My Profile</h1>

            <div class="profile-summary">
                <div class="profile-avatar">
                    <?php echo htmlspecialchars($profile["initials"]); ?>
                </div>

                <div class="profile-summary-text">
                    <h2><?php echo htmlspecialchars($profile["full_name"]); ?></h2>
                    <p><?php echo htmlspecialchars($profile["email"]); ?></p>

                    <div class="profile-badges">
                        <span class="badge badge-green">
                            <?php echo htmlspecialchars($profile["active_volunteer"]); ?>
                        </span>
                        <span class="badge badge-blue">
                            <?php echo htmlspecialchars($profile["years_clean"]); ?>
                        </span>
                    </div>
                </div>
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

            <form method="post" action="profile.php">

                <div class="profile-section">
                    <div class="profile-section-title">Personal Information</div>

                    <div class="profile-form-row">
                        <div class="profile-form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name"
                                   value="<?php echo htmlspecialchars($profile["first_name"]); ?>">
                        </div>

                        <div class="profile-form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name"
                                   value="<?php echo htmlspecialchars($profile["last_name"]); ?>">
                        </div>
                    </div>

                    <div class="profile-form-row">
                        <div class="profile-form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth"
                                   value="<?php echo htmlspecialchars($profile["date_of_birth"]); ?>">
                        </div>

                        <div class="profile-form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($profile["phone"]); ?>">
                        </div>
                    </div>

                    <div class="profile-form-row">
                        <div class="profile-form-group">
                            <label for="clean_date">Clean Date</label>
                            <input type="date" id="clean_date" name="clean_date"
                                   value="<?php echo htmlspecialchars($profile["clean_date"]); ?>">
                        </div>

                        <div class="profile-form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender">
                                <option value="Female" <?php echo ($profile["gender"] === "Female") ? "selected" : ""; ?>>Female</option>
                                <option value="Male" <?php echo ($profile["gender"] === "Male") ? "selected" : ""; ?>>Male</option>
                                <option value="Non-Binary" <?php echo ($profile["gender"] === "Non-Binary") ? "selected" : ""; ?>>Non-Binary</option>
                                <option value="Prefer Not To Say" <?php echo ($profile["gender"] === "Prefer Not To Say") ? "selected" : ""; ?>>Prefer Not To Say</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <div class="profile-section-title">Transportation &amp; Location</div>

                    <div class="profile-form-row">
                        <div class="profile-form-group full-width">
                            <label for="transportation">Transportation</label>
                            <select id="transportation" name="transportation">
                                <option value="Have your own transport with driver's license" <?php echo ($profile["transportation"] === "Have your own transport with driver's license") ? "selected" : ""; ?>>
                                    Have your own transport with driver's license
                                </option>
                                <option value="Neighborhood" <?php echo ($profile["transportation"] === "Neighborhood") ? "selected" : ""; ?>>
                                    Neighborhood
                                </option>
                                <option value="Bus Line" <?php echo ($profile["transportation"] === "Bus Line") ? "selected" : ""; ?>>
                                    Bus Line
                                </option>
                                <option value="Ride From Others" <?php echo ($profile["transportation"] === "Ride From Others") ? "selected" : ""; ?>>
                                    Ride From Others
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="profile-form-row">
                        <div class="profile-form-group">
                            <label for="neighborhood">Neighborhood</label>
                            <input type="text" id="neighborhood" name="neighborhood"
                                   value="<?php echo htmlspecialchars($profile["neighborhood"]); ?>">
                        </div>

                        <div class="profile-form-group">
                            <label for="bus_line">Bus Line #</label>
                            <input type="text" id="bus_line" name="bus_line"
                                   value="<?php echo htmlspecialchars($profile["bus_line"]); ?>">
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <div class="profile-section-title">Facility &amp; Probation</div>

                    <div class="profile-form-row">
                        <div class="profile-form-group full-width">
                            <label for="treatment_last_2_years">Treatment in last 2 yrs?</label>
                            <select id="treatment_last_2_years" name="treatment_last_2_years">
                                <option value="Yes" <?php echo ($profile["treatment_last_2_years"] === "Yes") ? "selected" : ""; ?>>Yes</option>
                                <option value="No" <?php echo ($profile["treatment_last_2_years"] === "No") ? "selected" : ""; ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="profile-form-row">
                        <div class="profile-form-group">
                            <label for="facility">Facility</label>
                            <input type="text" id="facility" name="facility"
                                   value="<?php echo htmlspecialchars($profile["facility"]); ?>">
                        </div>

                        <div class="profile-form-group">
                            <label for="discharge_date">Discharge</label>
                            <input type="date" id="discharge_date" name="discharge_date"
                                   value="<?php echo htmlspecialchars($profile["discharge_date"]); ?>">
                        </div>
                    </div>

                    <div class="profile-form-row">
                        <div class="profile-form-group full-width">
                            <label for="probation_status">Probation Status</label>
                            <select id="probation_status" name="probation_status">
                                <option value="Not on Probation" <?php echo ($profile["probation_status"] === "Not on Probation") ? "selected" : ""; ?>>Not on Probation</option>
                                <option value="Currently on Probation" <?php echo ($profile["probation_status"] === "Currently on Probation") ? "selected" : ""; ?>>Currently on Probation</option>
                                <option value="Completed Probation" <?php echo ($profile["probation_status"] === "Completed Probation") ? "selected" : ""; ?>>Completed Probation</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="profile-btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>