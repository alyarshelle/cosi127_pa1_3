<head>
    <a href="indexFinal.php" class="btn btn-primary">Home</a>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>IMDB Movie Database</title></br>
    <h1>IMDB Movie Database</h1>
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<form action="tabsFinal.php" method="post">
        <input type="submit" name="v_tables" value="View All Tables">
        <input type="submit" name="v_actors" value="View All Actors">
        <input type="submit" name="v_movies" value="View All Movies"><br>

    </form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <label for="sort_by">Sort By:</label>
    <select name="sort_by" id="sort_by">
        <option value=""></option>
        <option value="Rating">Rating</option>
        <option value="Budget">Budget</option>
        <option value="Boxoffice_Collection">Box Office Collection</option>
    </select>
    <button type="submit" name="sort_movies">Sort</button>
</form>

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
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["name"] . "</td><td>" . $row["rating"] . "</td><td>" . $row["production"] . "</td><td>" . $row["budget"] . "</td><td>" . $row["boxoffice_collection"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }
        $conn->close();
    } elseif (isset($_POST["sort_movies"])) {
        $sort_by = $_POST['sort_by'];
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

        if($sort_by == ""){
            // Query to fetch and sort movies based on the selected option
            $sql = "SELECT mp.name, mp.rating, mp.production, mp.budget, m.boxoffice_collection 
            FROM MotionPicture mp
            LEFT JOIN Movie m ON mp.id = m.mpid
            ORDER BY Rating ASC"; // Assuming ascending order
        }

        else{
        // Query to fetch and sort movies based on the selected option
        $sql = "SELECT mp.name, mp.rating, mp.production, mp.budget, m.boxoffice_collection 
                FROM MotionPicture mp
                LEFT JOIN Movie m ON mp.id = m.mpid
                ORDER BY $sort_by ASC"; // Assuming ascending order
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            if($sort_by == ""){
                echo "<h2>Movies Sorted By: Rating</h2>";
            }
            else{
                echo "<h2>Movies Sorted By: $sort_by</h2>";
            }
            echo "<table>";
            echo "<tr><th>Name</th><th>Rating</th><th>Production</th><th>Budget</th><th>Box Office Collection</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["name"] . "</td><td>" . $row["rating"] . "</td><td>" . $row["production"] . "</td><td>" . $row["budget"] . "</td><td>" . $row["boxoffice_collection"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }
        $conn->close();
    }
}
?>
