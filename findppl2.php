<!DOCTYPE html>
<html lang="en">
<head>
    <a href="indexFinal.php" class="btn btn-primary">Home</a>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Search</title>
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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve filtering parameters
        $roleFilter = $_POST["roleFilter"];
        $awardFilter = $_POST["awardFilter"];
        

        // Construct SQL query
        $query = "SELECT * FROM People WHERE 1";

        if (!empty($roleFilter)) {
            $query .= " AND id IN (SELECT pid FROM Role WHERE role_name = '$roleFilter')";
            
            // Additional filtering options based on selected role
            if ($roleFilter == "Director") {
                // Include additional filtering options for director

                echo "<div>";
                echo "<h4>Additional Filtering for Directors:</h4>";

                echo "<form action='findppl3.php' method='post'>"; // Changed action to findppl3.php
                // Filter by location
                echo "<label for='locationFilter'>Filter by Location:</label>";
                echo "<select name='locationFilter' id='locationFilter'>";
                echo "<option value=''>-- Select Location --</option>"; // Add a default option

                // Fetch distinct locations of motion pictures directed by the person
                $locationQuery = "SELECT DISTINCT Location.zip 
                                    FROM Location 
                                    INNER JOIN MotionPicture ON Location.mpid = MotionPicture.id 
                                    INNER JOIN Role ON MotionPicture.id = Role.mpid 
                                    WHERE Role.role_name = 'director' 
                                    AND Role.pid IN (SELECT id FROM People WHERE People.id IN 
                                        (SELECT pid FROM Role WHERE role_name = 'director' AND Role.mpid IN 
                                            (SELECT id FROM MotionPicture)))";

                $locationResult = $conn->query($locationQuery);

                if ($locationResult && $locationResult->num_rows > 0) {
                    while ($row = $locationResult->fetch_assoc()) {
                        echo "<option value='" . $row['zip'] . "'>" . $row['zip'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No Locations Found</option>"; // Provide a message if no locations found
                }
                echo "</select>";
                echo "</div>";


                // Filter by type of motion picture (TV series or Movie)
                echo "<div>";
                echo "<label for='motionTypeFilter'>Filter by Type:</label>";
                echo "<select name='motionTypeFilter' id='motionTypeFilter'>";
                echo "<option value=''>-- Select Type --</option>"; // Empty initial selection
                echo "<option value='TV series'>TV series</option>"; // Option for TV series
                echo "<option value='Movie'>Movie</option>"; // Option for Movie
                echo "</select>";
                echo "</div>";

                echo "<input type='hidden' name='roleFilter' value='$roleFilter'>";
                echo "<input type='submit' value='Apply Director Filters'>";
                echo "</form>";
                

            }

            if ($roleFilter == "Actor") {
                echo "<form action='findppl3.php' method='post'>";
                echo "<h4>Additional Filtering for Actors:</h4>";
                
                // Additional textbox for searching by birthday
                echo "<label for='searchByBirthday'>Search Actor By Birthday:</label>";
                echo "<input type='date' id='searchByBirthday' name='searchByBirthday'>";
                
                $sql = "SELECT DISTINCT production FROM MotionPicture";
                $result = $conn->query($sql);
                
                // Check if there are any productions
                if ($result && $result->num_rows > 0) {
                    // Generate the checklist
                    echo "<br><label>Select Productions:</label><br>";
                    while ($row = $result->fetch_assoc()) {
                        $production = $row['production'];
                        echo "<input type='checkbox' name='productions[]' value='$production'>$production<br>";
                    }
                } else {
                    echo "No productions found.";
                }
                
                // Submit button for both filters
                echo "<button type='submit' name='spb'>Search</button>";
                echo "</form>";
            }
            
            

            if ($roleFilter == "Producer") {
                echo "<form action='findppl3.php' method='post'>";
                echo "<h4>Additional Filtering for Producers:</h4>";
                
                // Filter by Nationality (drop-down menu)
                $sql = "SELECT DISTINCT nationality FROM People";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    echo "<label for='nationality'>Nationality:</label>";
                    echo "<select name='nationality' id='nationality'>";
                    echo "<option value=''>Select Nationality</option>";
                    while ($row = $result->fetch_assoc()) {
                        $nationality = $row['nationality'];
                        echo "<option value='$nationality'>$nationality</option>";
                    }
                    echo "</select><br>";
                } else {
                    echo "No nationalities found.<br>";
                }
                
                // Filter by Box Office Collection (minimum) (text box)
                echo "<label for='box_office_min'>Box Office Collection (min.):</label>";
                echo "<input type='number' name='box_office_min' id='box_office_min' step='1.0'><br>";

                // Filter by Budget (maximum) (text box)
                echo "<label for='budget_max'>Budget (max.):</label>";
                echo "<input type='number' name='budget_max' id='budget_max' step='1.0'><br>";

                // Button to submit the form
                echo "<button type='submit' name='search'>Search</button>";

                echo "</form>";
            }
            


            

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        }


        // Check if $awardFilter is not empty and is not 'all'
        if (!empty($awardFilter) && $awardFilter !== 'all') {
            // If an individual award is selected
            $query .= " AND People.id IN (SELECT pid FROM Award WHERE award_name = '$awardFilter')";
            // Display the Age Sorting dropdown menu and search button
    
            echo "<form action='findppl3.php' method='post'>"; // Form sends data to findppl3.php
            echo "<div>";
            echo "<h4>Additional Filtering for Awards:</h4>";
            echo "<label for='ageSort'>Sort by Age:</label>";
            echo "<select name='ageSort' id='ageSort'>";
            echo "<option value='oldest'>Oldest to Youngest</option>";
            echo "<option value='youngest'>Youngest to Oldest</option>";
            echo "</select>";
            echo "<button type='submit' name='search'>Search by Age</button>";
            echo "</div>";
            echo "<div>";
            echo "<label for='minAwards'>Minimum Awards:</label>";
            echo "<input type='number' name='minAwards' id='minAwards' min='0'>";
            echo "<button type='submit' name='search2'>Search by Min. Awards</button>";
            echo "</div>";
            echo "</form>"; // Close the form
        } elseif ($awardFilter === 'all') {
            // If "All Awards" is selected
            // Join the Award table to fetch the awards
            $query .= " AND People.id IN (SELECT pid FROM Award)";
            // Display the Age Sorting dropdown menu and search button
            echo "<form action='findppl3.php' method='post'>"; // Form sends data to findppl3.php
            echo "<div>";
            echo "<h4>Additional Filtering for Awards:</h4>";
            echo "<label for='ageSort'>Sort by Age:</label>";
            echo "<select name='ageSort' id='ageSort'>";
            echo "<option value='oldest'>Oldest to Youngest</option>";
            echo "<option value='youngest'>Youngest to Oldest</option>";
            echo "</select>";
            echo "<button type='submit' name='search'>Search by Age</button>";
            echo "</div>";
            echo "<div>";
            echo "<label for='minAwards'>Minimum Awards:</label>";
            echo "<input type='number' name='minAwards' id='minAwards' min='0'>";
            echo "<button type='submit' name='search2'>Search by Min. Awards</button>";
            echo "</div>";
            echo "</form>"; // Close the form
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
        } else {
            echo "No results foundeee.";
        }
    }

    // Close the database connection
    $conn->close();





    ?>
</body>
</html>
