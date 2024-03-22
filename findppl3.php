<!DOCTYPE html>
<html lang="en">
<head>
    <a href="indexFinal.php" class="btn btn-primary">Home</a>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            margin-bottom: 10px;
        }
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
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>


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

    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    //var_dump($_POST);
    // Connect to your database
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

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["roleFilter"]) && isset($_POST["locationFilter"]) && isset($_POST["motionTypeFilter"])) {
        // Retrieve filtering parameters
        $roleFilter = $_POST["roleFilter"];
        $locationFilter = $_POST["locationFilter"];
        $motionTypeFilter = $_POST["motionTypeFilter"];

        // Construct SQL query
        // Initialize the base query
        $query = "SELECT People.name AS Director, MotionPicture.name AS 'Motion Picture Name' FROM People";

        // Construct JOIN clauses based on filter conditions
        if (!empty($roleFilter) || !empty($locationFilter) || !empty($motionTypeFilter)) {
            $query .= " INNER JOIN Role ON People.id = Role.pid";
        }

        if (!empty($locationFilter) || !empty($motionTypeFilter)) {
            $query .= " INNER JOIN MotionPicture ON Role.mpid = MotionPicture.id";
        }

        if (!empty($locationFilter)) {
            $query .= " INNER JOIN Location ON MotionPicture.id = Location.mpid";
        }

        if (!empty($motionTypeFilter)) {
            if ($motionTypeFilter === 'Movie') {
                $query .= " INNER JOIN Movie ON MotionPicture.id = Movie.mpid";
            } elseif ($motionTypeFilter === 'TV series') {
                $query .= " INNER JOIN Series ON MotionPicture.id = Series.mpid";
            }
        }

        // Add WHERE clause with all filter conditions
        $conditions = [];

        if (!empty($roleFilter)) {
            $conditions[] = "Role.role_name = '$roleFilter'";
        }

        if (!empty($locationFilter)) {
            $conditions[] = "Location.zip = '$locationFilter'";
        }

        if (!empty($motionTypeFilter)) {
            if ($motionTypeFilter === 'Movie') {
                $conditions[] = "Movie.mpid IS NOT NULL";
            } elseif ($motionTypeFilter === 'TV series') {
                $conditions[] = "Series.mpid IS NOT NULL";
            }
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Execute the query
        $result = $conn->query($query);

        // Display the results
        if ($result && $result->num_rows > 0) {
            echo "<h4>Query Results</h4>";
            echo "<table border='1'>";
            // Output table headers
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
        } else {
            echo "No results found!!!.";
        }
    }


    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["spb"])) {
        // Retrieve selected productions from the form
        $productions = isset($_POST["productions"]) ? $_POST["productions"] : array();
        $searchByBirthday = isset($_POST['searchByBirthday']) ? $_POST['searchByBirthday'] : null;

        // Construct SQL query
        $query = "SELECT People.name AS actor_name, MotionPicture.name AS production_name
                    FROM People
                    JOIN Role ON People.id = Role.pid
                    JOIN MotionPicture ON Role.mpid = MotionPicture.id
                    WHERE Role.role_name = 'Actor'";

        // Add search by birthday condition
        if (!empty($searchByBirthday)) {
            $query .= " AND People.dob = '$searchByBirthday'";
        }

        // Add search by production condition
        if (!empty($productions)) {
            $productionValues = array_map(function($production) use ($conn) {
                return "'" . $conn->real_escape_string($production) . "'";
            }, $productions);

            $subQuery = "(SELECT COUNT(DISTINCT MotionPicture.production)
                            FROM MotionPicture
                            INNER JOIN Role ON MotionPicture.id = Role.mpid
                            WHERE Role.pid = People.id AND MotionPicture.production IN (" . implode(",", $productionValues) . "))";

            $query .= "AND ($subQuery) = " . count($productions);
        }

        // If productions array is empty but searchByBirthday is not empty, include birthday condition only
        if (empty($productions) && !empty($searchByBirthday)) {
            $query .= " GROUP BY People.name";
        }



        // Execute the query
        $result = $conn->query($query);

        // Display the results
        if ($result && $result->num_rows > 0) {
            echo "<h2>Actors in Selected Productions</h2>";
            echo "<table border='1'>";
            echo "<tr><th>Actor Name</th><th>Motion Picture Name</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["actor_name"] . "</td><td>" . $row["production_name"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No results found.";
        }
    }

    // Check if form is submitted and all required parameters are set
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nationality"], $_POST["box_office_min"], $_POST["budget_max"])) {
        // Retrieve filtering parameters
        $nationality = $_POST["nationality"];
        $box_office_min = floatval($_POST["box_office_min"]);
        $budget_max = floatval($_POST["budget_max"]);

        // Construct the SQL query

        $query = "SELECT People.name AS producer_name, MotionPicture.name AS movie_name, Movie.boxoffice_collection, MotionPicture.budget
            FROM People
            INNER JOIN Role ON People.id = Role.pid
            INNER JOIN Movie ON Role.mpid = Movie.mpid
            INNER JOIN MotionPicture ON Movie.mpid = MotionPicture.id
            WHERE Role.role_name = 'Producer'";

        // Add additional filtering conditions based on the provided parameters
        if (!empty($nationality)) {
            $query .= " AND People.nationality = '$nationality'";
        }

        // Add filtering conditions for box_office_min and budget_max
        if (!empty($box_office_min)) {
            $query .= " AND Movie.boxoffice_collection >= $box_office_min";
        }

        if (!empty($budget_max)) {
            $query .= " AND MotionPicture.budget <= $budget_max";
        }

        // Execute the query
        $result = $conn->query($query);

        // Display the results
        if ($result && $result->num_rows > 0) {
            echo "<h2>Producers Satisfying Criteria</h2>";
            echo "<table border='1'>";
            echo "<tr><th>Producer Name</th><th>Movie Name</th><th>Box Office Collection</th><th>Budget</th></tr>";
            
            // Loop through each row in the result set
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["producer_name"] . "</td>";
                echo "<td>" . $row["movie_name"] . "</td>";
                echo "<td>" . $row["boxoffice_collection"] . "</td>"; // Adjust column name if necessary
                echo "<td>" . $row["budget"] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "No producers found satisfying the criteria.";
        }

    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ratingsFilter"])) {
        // Retrieve the ratings filter value
        $minRating = $_POST["ratingsFilter"];
        
        // Construct the SQL query to find movies with rating higher than the minimum
        $movieQuery = "SELECT id, name FROM MotionPicture WHERE rating > ?";
        $stmt = $conn->prepare($movieQuery);
        $stmt->bind_param("i", $minRating);
        $stmt->execute();
        $movieResult = $stmt->get_result();

        if ($movieResult->num_rows > 0) {
            echo "<h4>People Holding Multiple Roles in Motion Picture with a Rating Higher Than or Equal to: $minRating</h4>";
            echo "<table border='1'>";
            echo "<tr><th>Person Name</th><th>Motion Picture Name</th><th>Role Count</th></tr>";

            while ($movieRow = $movieResult->fetch_assoc()) {
                $movieId = $movieRow['id'];
                $movieName = $movieRow['name'];

                // Construct the SQL query to find people who played multiple roles in the movie
                $query = "SELECT People.name AS person_name, COUNT(Role.pid) AS role_count
                            FROM People
                            INNER JOIN Role ON People.id = Role.pid
                            WHERE Role.mpid = ?
                            GROUP BY People.name
                            HAVING COUNT(Role.pid) > 1";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $movieId);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["person_name"] . "</td>";
                    echo "<td>" . $movieName . "</td>";
                    echo "<td>" . $row["role_count"] . "</td>";
                    echo "</tr>";
                }
            }

            echo "</table>";
            } else {
                echo "No results found.";
            }
            
        }

    // Check if form is submitted and all required parameters are set
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"]) && isset($_POST["ageSort"])) {

        $ageSort = $_POST["ageSort"];
        $minAwards = isset($_POST["minAwards"]) ? intval($_POST["minAwards"]) : 0; // Default to 0 if not provided

        $query = "SELECT DISTINCT People.name AS Actor,
                    TIMESTAMPDIFF(YEAR, STR_TO_DATE(People.dob, '%Y-%m-%d'), AwardYear.award_year) AS 'Age at Award'
                    FROM People
                    JOIN Award ON People.id = Award.pid
                    JOIN (SELECT pid, MIN(STR_TO_DATE(CONCAT(award_year, '-01-01'), '%Y-%m-%d')) AS award_year
                    FROM Award
                    GROUP BY pid) AS AwardYear
                    ON People.id = AwardYear.pid";

            if ($ageSort === 'youngest') {
            $query .= " HAVING `Age at Award` = (SELECT MAX(`Age at Award`) FROM ($query) AS T)
            OR `Age at Award` = (SELECT MIN(`Age at Award`) FROM ($query) AS T)";
            }

            if ($ageSort === 'oldest') {
                $query .= " HAVING `Age at Award` = (SELECT MIN(`Age at Award`) FROM ($query) AS T)
                            OR `Age at Award` = (SELECT MAX(`Age at Award`) FROM ($query) AS T)";
                $query .= " ORDER BY `Age at Award` DESC";
            }
            

        // Execute the query
        $result = $conn->query($query);

        // Display the results
        if ($result && $result->num_rows > 0) {
            echo "<h4>Query Results</h4>";
            echo "<table border='1'>";
            // Output table headers
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
        } else {
            echo "No results found.";
        }
    }


    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search2"])) {
        // Retrieve the input values
        $minAwards = isset($_POST["minAwards"]) ? intval($_POST["minAwards"]) : 0; // Default to 0 if not provided

        // Construct the SQL query with parameterized values
        $query = "SELECT People.name AS Person, MotionPicture.name AS 'Motion Picture', Award.award_year AS 'Award Year', COUNT(*) AS 'Award Count'
                    FROM People
                    INNER JOIN Award ON People.id = Award.pid
                    INNER JOIN MotionPicture ON Award.mpid = MotionPicture.id
                    GROUP BY People.name, MotionPicture.name, Award.award_year
                    HAVING COUNT(*) >= ?";

        // Prepare the statement
        $stmt = $conn->prepare($query);

        // Bind the parameter
        $stmt->bind_param("i", $minAwards); // Assuming $minAwards is an integer, change "i" to "s" if it's a string

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();
        


        // Display the results
        if ($result && $result->num_rows > 0) {
            echo "<h4>Query Results</h4>";
            echo "<table border='1'>";
            // Output table headers
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
        } else {
            echo "No results found.";
        }
    }


    


        // Close the database connection
    $conn->close();

    








    ?>
</body>
</html>
