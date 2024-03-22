<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Bootstrap JS dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSI 127B</title>
    
</head>

<html>
<head>
    <title>IMDB Movie Database</title></br>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>IMDB Movie Database</h1>

    <div style="display: flex;">
    <form action="tabsFinal.php" method="post">
        <input type="submit" name="v_tables" value="View All Tables">
        <input type="submit" name="v_actors" value="View All Actors">
    </form>
    <form action="allmoviesFinal.php" method="post">
        <input type="submit" name="v_movies" value="View All Movies">
    </form>
</div> 

<br>

<div>
    <br>
        <form action="findppl.php" method="post">
            <button type="submit" class="btn btn-primary">Find People</button>
        </form>
</div>

    <script>
        // Show textbox when a field is selected
        document.getElementById('selectPField').addEventListener('change', function() {
            var selectField = this.value;
            var textboxDiv = document.getElementById('textboxDiv');
            if (selectField !== "") {
                textboxDiv.style.display = 'block';
            } else {
                textboxDiv.style.display = 'none';
            }
        });
    </script>



        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["v_movies"])) {
                // Code to display all movies
                // MySQL database connection
                $servername = "localhost"; // Change if your MySQL server is hosted elsewhere
                $username = "root"; // Your MySQL username
                $password = ""; // Your MySQL password
                $dbname = "cosi127_pa1_3"; // Your MySQL database name,,, changed from cosi127_pa1_2

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch all movies
                $sql = "SELECT mp.name, mp.rating, mp.production, mp.budget, m.boxoffice_collection 
                        FROM MotionPicture mp
                        LEFT JOIN Movie m ON mp.id = m.mpid";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    echo "<h2>All Movies</h2>";
                    echo "<table>";
                    echo "<tr><th>Name</th><th>Rating</th><th>Production</th><th>Budget</th><th>Box Office Collection</th></tr>";
                    while($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["name"] . "</td><td>" . $row["rating"] . "</td><td>" . $row["production"] . "</td><td>" . $row["budget"] . "</td><td>" . $row["boxoffice_collection"] . "</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "0 results";
                }
                $conn->close();
            } elseif (isset($_POST["v_actors"])) {
                // Code to display all actors
                // MySQL database connection
                $servername = "localhost"; // Change if your MySQL server is hosted elsewhere
                $username = "root"; // Your MySQL username
                $password = ""; // Your MySQL password
                $dbname = "cosi127_pa1_3"; // Your MySQL database name,, changed from cosi127_pa1_2

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch all actors
                $sql = "SELECT name, nationality, dob, gender FROM People";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    echo "<h2>All Actors</h2>";
                    echo "<table>";
                    echo "<tr><th>Name</th><th>Nationality</th><th>Date of Birth</th><th>Gender</th></tr>";
                    while($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["name"] . "</td><td>" . $row["nationality"] . "</td><td>" . $row["dob"] . "</td><td>" . $row["gender"] . "</td></tr>";
                    }
                    echo "</table>";
                }    
                    else {
                    echo "0 results";
                    }
                    $conn->close();
                }
            }
?>  


</body>

<body>
    <div class="container">
        <br>
        <h1 style="text-align:center">COSI 127 - PA 1.3</h1><br>
        <h3 style="text-align:center">OUR IMDB</h3><br>
    </div>

    <div class="container">
    <form id="queryForm" method="post" action="php_scriptFinal.php">
        <div class="form-group">
            <label for="selectTable">Select Table:</label>
            <select class="form-control" id="selectTable" name="selectTable">
                <option value=""></option> <!-- Blank option -->
                <option value="MotionPicture">MotionPicture</option>
                <option value="User">User</option>
                <option value="Likes">Likes</option>
                <option value="Movie">Movies</option>
                <option value="Series">Series</option>
                <option value="People">People</option>
                <option value="Role">Role</option>
                <option value="Award">Award</option>
                <option value="Genre">Genre</option>
                <option value="Location">Location</option>
            </select>
        </div>
        <div class="form-group">
            <label for="selectField">Select Field:</label>
            <select class="form-control" id="selectField" name="selectField">
                <!-- Options will be dynamically populated based on the selected table -->
            </select>
        </div>
        <div class="form-group">
            <label for="searchTerm">Search Term:</label>
            <input type="text" class="form-control" id="searchTerm" name="searchTerm" placeholder="Enter search term">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script>
    document.getElementById("selectTable").addEventListener("change", function() {
        var selectTable = this.value;
        var selectField = document.getElementById("selectField");
        selectField.innerHTML = ""; // Clear existing options

        var options; // Define options array

        // Populate options array based on selectTable
        if (selectTable === "MotionPicture") {
            options = ["", "id", "name", "rating", "production", "budget", "series name", "shooting location country", "highest rating", "cast"];
        } else if (selectTable === "User") {
            options = ["", "email", "name", "age", "likes and ages"];
        } else if (selectTable === "Likes") {
            options = ["", "uemail", "mpid"];
        } else if (selectTable === "Movie") {
            options = ["","mpid", "boxoffice_collection"];
        } else if (selectTable === "Series") {
            options = ["", "mpid", "season_count"];
        } else if (selectTable === "People") {
            options = ["", "id", "name", "nationality", "dob", "gender"];
        } else if (selectTable === "Role") {
            options = ["", "mpid", "pid", "role_name"];
        } else if (selectTable === "Award") {
            options = ["", "mpid", "pid", "award_name", "award_year"];
        } else if (selectTable === "Genre") {
            options = ["", "mpid", "genre_name"];
        } else if (selectTable === "Location") {
            options = ["", "mpid", "zip", "city", "country"];
        }

        // Add options to selectField
        if (options) {
            options.forEach(function(option) {
                var opt = document.createElement("option");
                opt.value = option;
                opt.textContent = option;
                selectField.appendChild(opt);
            });
        }
    });
</script>

<script>
    document.getElementById("queryForm").addEventListener("submit", function(event) {
        // Retrieve the values of form input fields
        var selectTable = document.getElementById("selectTable").value;
        var selectField = document.getElementById("selectField").value;
        var searchTerm = document.getElementById("searchTerm").value;

        // Check if selectField is "genre_name"
        if (selectField === "genre_name") {
            // Change the form action to genre_query.php
            this.action = "genre_query.php";
        }

        if (selectField == "likes and ages"){
            this.action = "likes_users.php"
        }

        if (selectField == "highest rating"){
            this.action = "highest.php"
        }

        if (selectField == "cast"){
            this.action = "cast_members.php"
        }
        // else, the form action remains the same

        // Form submission continues as usual
    });
</script>


<br>
<div class="container">
        <h2>Like a Movie</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="motionPictureID">Motion Picture ID:</label>
                <input type="text" class="form-control" id="motionPictureID" name="motionPictureID">
            </div>
            <div class="form-group">
                <label for="userEmail">Your Email:</label>
                <input type="email" class="form-control" id="userEmail" name="userEmail">
            </div>
            <button type="submit" class="btn btn-primary" name="like">Like</button>
        </form>
    </div>
<br>
    <div class="container">
    <h2>See your likes</h2>
    <form action="tabsFinal.php" method="post">
        <div class="form-group">
            <label for="userEmailLikes">Your Email:</label>
            <input type="email" class="form-control" id="userEmaiLikes" name="userEmailLikes">
        </div>
        <button type="submit" class="btn btn-primary" name="see_likes">See Likes</button>
    </form>
    </div>

<br>

<?php
    // Check if the form has been submitted
    if(isset($_POST['like'])) {
        // Retrieve form data
        $motionPictureID = $_POST['motionPictureID'];
        $userEmail = $_POST['userEmail'];

        // Database connection details
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "cosi127_pa1_3";

        try {
            // Create connection
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement to insert like data into the Likes table
            $stmt = $conn->prepare("INSERT INTO Likes (mpid, uemail) VALUES (:mpid, :uemail)");
            // Bind parameters
            $stmt->bindParam(':mpid', $motionPictureID);
            $stmt->bindParam(':uemail', $userEmail);
            // Execute the query
            $stmt->execute();

            echo "Liked movie successfully!";
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        // Close the database connection
        $conn = null;
    }
?>

<br>

</body>
</html>
