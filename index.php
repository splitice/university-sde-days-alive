<?php
define ('DAY_DIVISOR', 60*60*24);
function days_alive($dob){
    $now = time();
    $date_diff = $now - strtotime($dob);
    echo floor($date_diff/DAY_DIVISOR);
}

include(__DIR__.'/include/db.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>How many days alive?</title>

    <!-- Twitter Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="container">
    <h1>How many days alive?</h1>
    <form role="form" method="post" action="">
        <div class="form-group clearfix">
            <label for="inputName" class="control-label col-sm-2">Full Name</label>
            <div class="col-sm-3">
                <input name="name" type="text" class="form-control" id="inputName" placeholder="John Citizen">
            </div>
        </div>
        <div class="form-group clearfix">
            <label for="inputDob" class="control-label col-sm-2">Date of Birth (DD-MM-YYYY)</label>
            <div class="col-sm-3">
                <input name="dob" type="date" class="form-control" id="inputDob" placeholder="DD-MM-YYYY">
            </div>
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>

    <?php
    if($_POST){
        ?>
    <div class="alert alert-success">
        <strong>Well Done! </strong> You have been alive for <strong><?=days_alive($_POST['dob']);?></strong> days.
    </div>
    <?php
        $ts = strtotime($_POST['dob']);
        $sql = 'INSERT INTO latest_entries (le_timestamp, le_name, le_birthdate) VALUES(NOW(),\''.pg_escape_string($pgcon, $_POST['name']).'\', \''.date( 'Y-m-d H:i:s', $ts ).'\')';
        pg_query($pgcon,$sql);
    }
    ?>
    <hr />
    <h2>Latest 10 Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <td>Time</td>
                <td>Name</td>
                <td>Birth Date</td>
                <td>Days Alive</td>
            </tr>
        </thead>
        <?php
        $res = pg_query($pgcon,'SELECT * FROM latest_entries ORDER BY le_timestamp DESC LIMIT 10');
        if($res){
            //Query completed successfully
            while($row = pg_fetch_assoc($res)){
                echo '<tr>';
                echo '<td>', $row['le_timestamp'],'</td>';
                echo '<td>', $row['le_name'],'</td>';
                echo '<td>', $row['le_birthdate'],'</td>';
                echo '<td>', days_alive($row['le_birthdate']),'</td>';
                echo '</tr>';
            }
        }else{
            echo '<tr>';
            echo '<td colspan="4">Sorry! Unable to retrieve data at this time</td>';
            echo '</tr>';
        }
        ?>
    </table>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</body>
</html>
<?php
pg_close($pgcon);