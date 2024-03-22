<html>
<head>
<a href="indexFinal.php" class="btn btn-primary">Home</a>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

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


<?php
$query = ""; // Define $query variable

$request_method_post = ($_SERVER["REQUEST_METHOD"] == "POST") ? "true" : "false";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Retrieve form data
    if (
        isset($_POST["selectTable"], $_POST["selectField"], $_POST["searchTerm"]) &&
        !empty($_POST["selectTable"]) && !empty($_POST["selectField"]) && !empty($_POST["searchTerm"])
    ) {

        $selectTable = $_POST["selectTable"];
        $selectField = $_POST["selectField"];
        $searchTerm = $_POST["searchTerm"];

        // Construct the SQL query
        $query = "SELECT * FROM $selectTable WHERE $selectField LIKE '%$searchTerm%'";

        // Assuming you have a database connection stored in $conn variable
        // Execute the query (assuming you have a database connection already established)
        // For example:
        $servername = "localhost"; // Change if your MySQL server is hosted elsewhere
        $username = "root"; // Your MySQL username
        $password = ""; // Your MySQL password
        $dbname = "cosi127_pa1_3"; // Your MySQL database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        //DIFFERENT CASES

        //Motion Picture
        //MOVIES
        if($selectField == "name"){
            $query = "SELECT MotionPicture.name, MotionPicture.rating, MotionPicture.production, MotionPicture.budget
                        FROM MotionPicture
                        JOIN Movie ON id = mpid
                            WHERE name LIKE '%$searchTerm%' AND id >= 200";
        }

        //SERIES
        if($selectField == "series name"){
            $query = "SELECT MotionPicture.*, Series.* 
                        FROM MotionPicture
                        JOIN Series ON id = mpid
                            WHERE name LIKE '%$searchTerm%' AND id >= 100";
        }

        //SHOOTING LOCATION COUNTRY
        if($selectField == "shooting location country"){

            $sT = $searchTerm;
            $searchTerm = '%' . $searchTerm . '%';

            $query = "SELECT DISTINCT name
              FROM MotionPicture
              JOIN Location ON id = mpid 
              WHERE country LIKE '$searchTerm'";

            echo "<p><strong>Filter Criteria:</strong> $sT</p>";
        

            // Execute query
            $result = $conn->query($query);

            // Display query results
            if ($result && $result->num_rows > 0) {
                echo "<h2>Query Results</h2>";
                echo "<table border='1'>";
                // Output table headers
                echo "<table class='table table-bordered'>";
                echo "<thead class='thead-dark'>";
                $header_printed = false;
                echo "<tr>";
                while ($fieldInfo = $result->fetch_field()) {
                    echo "<th>" . $fieldInfo->name . "</th>";
                }
                echo "</tr>";

                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . $value . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }

        else{
            // Execute query
            $result = $conn->query($query);

            // Display query results
            if ($result && $result->num_rows > 0) {
                echo "<h2>Query Results</h2>";
                echo "<table border='1'>";
                // Output table headers
                echo "<table class='table table-bordered'>";
                echo "<thead class='thead-dark'>";
                $header_printed = false;
                echo "<tr>";
                while ($fieldInfo = $result->fetch_field()) {
                    echo "<th>" . $fieldInfo->name . "</th>";
                }
                echo "</tr>";

                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . $value . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    }

    else if(!empty($_POST["selectTable"]) && empty($_POST["selectField"]) && empty($_POST["searchTerm"])){
       
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

        $table_name = $_POST["selectTable"];

        echo "<h2>$table_name</h2>";

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
        $conn->close();
    }

    else if(!empty($_POST["selectTable"]) && !empty($_POST["selectField"]) && empty($_POST["searchTerm"])){
    
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

        $table_name = $_POST["selectTable"];
        $selectField = $_POST["selectField"];

        if($selectField == "movie name"){
            $query = "SELECT MotionPicture.*, Movie.* 
                        FROM MotionPicture
                        JOIN Movie ON id = mpid
                            WHERE id >= 200";
        }

        if($selectField == "series name"){
            $query = "SELECT MotionPicture.*, Series.* 
                        FROM MotionPicture
                        JOIN Series ON id = mpid
                            WHERE id >= 100";
        }
        
        if($selectField == "shooting location country"){
            $query = "SELECT MotionPicture.name, Location.*
                        FROM MotionPicture
                        JOIN Location ON id = mpid ";
        }

        if($selectField == "genre_name"){
            $query = "SELECT Genre.*, MotionPicture.name, MotionPicture.rating, Location.city, Location.zip
                        From MotionPicture
                        JOIN Location ON MotionPicture.id = Location.mpid
                        JOIN Genre ON MotionPicture.id = Genre.mpid";

        }


        else{
            $query = "SELECT * FROM $table_name ORDER BY $selectField";
        }

        echo "<h2>$table_name</h2>";

        $result_rows = $conn->query($query);

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
        $conn->close();
    }

    else if(empty($_POST["selectTable"]) && empty($_POST["selectField"]) && empty($_POST["searchTerm"])){
        // echo "GGG";
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


}

?>

</body>
</html>
