<!DOCTYPE html>
<html>
<head>
    <title>Retrieve Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> 
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style type="text/css">
        .title {
            text-align: center;
            margin-top: 20px;
        }
        .input-group {
            margin-top: 20px;
        }
        .btn-refresh {
            margin-left: 10px;
            height: 34px; /* Match the height of the search button */
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="msg">
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
        ?>
    </div>
    <?php endif ?>
    <div class="container">
        <h1 class="title">PABLO WAR RECORDS</h1>
        <a href="create.php"><button type="button" class="btn btn-labeled btn-success">
            <span class="btn-label"> <i class="fa fa-plus"></i></span>Add New Record</button></a>
        <!-- Search Form -->
        <form method="post" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search a Record" value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                </span>
                <span class="input-group-btn">
                    <a href="index.php" class="btn btn-default btn-refresh"><i class="fa fa-refresh"></i> Refresh</a>
                </span>
            </div>
        </form>
        <div class="row">
            <div class="col-12">
                <?php
                    // Include config file
                    include 'config.php';

                    // Check if the 'date' column exists
                    $checkColumnSql = "SHOW COLUMNS FROM `users` LIKE 'date'";
                    $result = mysqli_query($conn, $checkColumnSql);
                    if (mysqli_num_rows($result) == 0) {
                        // Column doesn't exist, so add it
                        $alterTableSql = "ALTER TABLE `users` ADD `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `phone`";
                        if (mysqli_query($conn, $alterTableSql) === FALSE) {
                            echo "Error updating table: " . mysqli_error($conn);
                        }
                    }

                    // Initialize the search term
                    $search = '';
                    if (isset($_POST['search'])) {
                        $search = $_POST['search'];
                    }

                    // Attempt select query execution
                    if (!empty($search)) {
                        $searchTerms = explode(' ', $search);
                        $conditions = [];

                        foreach ($searchTerms as $term) {
                            $term = mysqli_real_escape_string($conn, $term);
                            $conditions[] = "first_name LIKE '%$term%' OR last_name LIKE '%$term%' OR email LIKE '%$term%' OR phone LIKE '%$term%' OR DATE(date) LIKE '%$term%' OR TIME(date) LIKE '%$term%'";
                        }

                        $sql = "SELECT * FROM users WHERE " . implode(' OR ', $conditions);
                    } else {
                        $sql = "SELECT * FROM users";
                    }

                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-striped table-sm">';
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th>ID</th>";
                                    echo "<th>First Name</th>";
                                    echo "<th>Last Name</th>";
                                    echo "<th>Email</th>";
                                    echo "<th>Phone</th>";
                                    echo "<th>Date & Time</th>";
                                    echo "<th>Action</th>";
                                echo "</tr>";
                            echo "</thead>";

                            echo "<tbody>";
                            while($row = mysqli_fetch_array($result)){
                                echo "<tr>";
                                    echo "<td>". $row['id'] ."</td>";
                                    echo "<td>". $row['first_name'] ."</td>";
                                    echo "<td>". $row['last_name'] ."</td>";
                                    echo "<td>". $row['email'] ."</td>";
                                    echo "<td>". $row['phone'] ."</td>";
                                    echo "<td>". $row['date'] ."</td>";
                                    echo "<td>";
                                        echo '<a href="show.php?id='. $row['id'] .'" class="mr-3" title="View Record" data-toggle="tooltip"><span class="btn btn-primary fa fa-eye"></span></a>';
                                        echo " ";
                                        echo '<a href="edit.php?id='. $row['id'] .'" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="btn btn-warning fas fa-edit"></span></a>';
                                        echo " ";
                                        echo '<a href="delete.php?id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><span class="btn btn-danger fa fa-trash"></span></a>';
                                    echo "</td>"; 
                                echo "</tr>"; 
                            }
                            echo "</tbody>"; 
                            echo "</table>"; 
                            mysqli_free_result($result);
                        } else{
                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close connection
                    mysqli_close($conn);
                ?>
            </div>
        </div>
    </div>
</body>
</html>
