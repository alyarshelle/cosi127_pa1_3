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



    
    <?php
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
    
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
    
            // Construct the SQL query based on selected criteria
            $query = "SELECT * FROM People";

            if (empty($selectPField) && empty($searchTerm)) {
                // Additional filtering options
                echo "<h2>Refine Your Search</h2>";
                echo "<form action='findppl2.php' method='post'>";
                
                // Include Role dropdown menu
                echo "<div>";
                echo "<label for='roleFilter'>Filter by Role:</label>";
                echo "<select name='roleFilter' id='roleFilter'>";
                echo "<option value=''>-- Select Role --</option>";

                // Retrieve all possible roles from the database and populate the dropdown
                $roleQuery = "SELECT DISTINCT role_name FROM Role";
                $roleResult = $conn->query($roleQuery);

                while ($row = $roleResult->fetch_assoc()) {
                    echo "<option value='" . $row['role_name'] . "'>" . $row['role_name'] . "</option>";
                }
                echo "</select>";
                echo "</div>";
                
                // Include Award dropdown menu
                echo "<div>";
                echo "<label for='awardFilter'>Filter by Award:</label>";
                echo "<select name='awardFilter' id='awardFilter'>";
                echo "<option value=''>-- Select Award --</option>";
                echo "<option value='all'>All Awards</option>"; // Option for selecting all awards

                // Retrieve all possible awards from the database and populate the dropdown
                $awardQuery = "SELECT DISTINCT award_name FROM Award";
                $awardResult = $conn->query($awardQuery);

                while ($row = $awardResult->fetch_assoc()) {
                    echo "<option value='" . $row['award_name'] . "'>" . $row['award_name'] . "</option>";
                }
                echo "</select>";
                echo "</div>";

                // Button to submit the form
                echo "<button type='submit' name='search'>Search</button>";
                echo "</form>";

                // Form for Motion Picture Ratings filter
                echo "<form action='findppl3.php' method='post'>";
                echo "<label for='ratingsFilter'>Minimum Rating:</label>";
                echo "<input type='number' name='ratingsFilter' id='ratingsFilter' min='0' step='1.0'>";
                echo "<button type='submit' name='search'>Apply Rating Filter</button>";
                echo "</form>";
            }
    
            else if (!empty($selectPField) && !empty($searchTerm)) {
                $query .= " WHERE $selectPField LIKE '%$searchTerm%'";
            }

    
            // Execute the query
            $result = $conn->query($query);
    
            // Display the results
            if ($result && $result->num_rows > 0) {
                echo "<h2>Query Results</h2>";
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
    
                
        }
    }


    ?>
    
    
</body>
</html>
