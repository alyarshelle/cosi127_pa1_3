<html>
<head>
    <a href="indexFinal.php" class="btn btn-primary">Home</a>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>IMDB Movie Database</title>
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




    <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["v_tables"])) {
                    // MySQL database connection
                    $servername = "localhost"; // Change if your MySQL server is hosted elsewhere
                    $username = "root"; // Your MySQL username
                    $password = ""; // Your MySQL password
                    $dbname = "cosi127_pa1_3"; // Your MySQL database name, changed from cosi127_pa1_2
            
                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);
            
                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
            
                    // Query to fetch all table names
                    $sql_tables = "SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname'";
                    $result_tables = $conn->query($sql_tables);
            
                    if ($result_tables->num_rows > 0) {
                        // Output data of each table
                        while ($row_table = $result_tables->fetch_assoc()) {
                            $table_name = $row_table["table_name"];
                            echo "<h2>$table_name</h2>";
            
                            // Query to fetch all rows from the current table
                            $sql_rows = "SELECT * FROM $table_name";
                            $result_rows = $conn->query($sql_rows);
            
                            if ($result_rows->num_rows > 0) {
                                // Output data of each row
                                echo "<table class='table table-bordered'>";
                                echo "<thead class='thead-dark'>";
                                $header_printed = false;
                                while ($row = $result_rows->fetch_assoc()) {
                                    if (!$header_printed) {
                                        echo "<tr>";
                                        foreach ($row as $key => $value) {
                                            echo "<th>$key</th>";
                                        }
                                        echo "</tr>";
                                        $header_printed = true;
                                    }
                                    echo "<tr>";
                                    foreach ($row as $value) {
                                        echo "<td>$value</td>";
                                    }
                                    echo "</tr>";
                                }
                                echo "</thead>";
                                echo "</table>";
                            } else {
                                echo "0 results";
                            }
                        }
                    } else {
                        echo "0 tables";
                    }
                    $conn->close();
                }





                elseif (isset($_POST["v_movies"])) {
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
                $sql = "SELECT DISTINCT People.name, People.nationality, People.dob, People.gender 
                        FROM People 
                        JOIN Role ON People.id = Role.pid 
                        WHERE Role.role_name = 'Actor'";
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





    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['see_likes'])) {
        // Retrieve form data
        $userEmailLikes = $_POST['userEmailLikes'];

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

            // Prepare SQL statement to fetch liked movies for the given email
            $stmt = $conn->prepare("SELECT mp.name, mp.rating, mp.production, mp.budget 
                                    FROM Likes l
                                    INNER JOIN MotionPicture mp ON l.mpid = mp.id
                                    WHERE l.uemail = :userEmail AND mp.id >= 200");
            // Bind parameters
            $stmt->bindParam(':userEmail', $userEmailLikes);
            // Execute the query
            $stmt->execute();
            $likedMovies = $stmt->fetchAll();

            if (count($likedMovies) > 0) {
                // Output the table of liked movies
                echo "<h2>Movies Liked by $userEmailLikes</h2>";
                echo "<table class='table table-bordered'>";
                echo "<tr><th>Name</th><th>Rating</th><th>Production</th><th>Budget</th></tr>";
                foreach ($likedMovies as $movie) {
                    echo "<tr><td>{$movie['name']}</td><td>{$movie['rating']}</td><td>{$movie['production']}</td><td>{$movie['budget']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No liked movies found for $userEmailLikes";
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        // Close the database connection
        $conn = null;
    }
    ?>
</body>


<body>
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
</body>






</html>
