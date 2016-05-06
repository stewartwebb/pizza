<?php

require_once('config.php');

function htmlPrepare($sString)
{	return htmlspecialchars($sString);								}

include_once('class.database.php');

$objDatabase = new Database();

$bGucci = 'derp';
$arrErrors = array();

if(array_key_exists('inSubmit', $_POST))
{
    if(!array_key_exists('inName', $_POST) && strlen($_POST['inName']) < 3)
        $arrErrors['inName'] = 'Include a name u scrub';

    if(!array_key_exists('inPizza', $_POST))
        $arrErrors['inPizza'] = 'Make a choice you scrub';

    if(!count($arrErrors))
    {
        $sSQL = 'SELECT pOrderID FROM tOrders WHERE sRemoteAddr = :sRemoteAddr AND dOrdered > CURRENT_DATE() - INTERVAL 12 HOURS AND dOrdered < CURRENT_DATE() - INTERVAL 13 HOURS';
        $sSQL = 'INSERT INTO tOrders SET sName = :sName, sChoice = :sChoice, dOrdered = NOW(),';
        $sSQL.= 'sRemoteAddr = :sRemoteAddr, sForwardAddr = :sForwardAddr';
        $objDatabase->query($sSQL);
        $objDatabase->bind(':sName', $_POST['inName']);
        $objDatabase->bind(':sChoice', $_POST['inPizza']);
        $objDatabase->bind(':sRemoteAddr', $_SERVER['REMOTE_ADDR']);
        $objDatabase->bind(':sForwardAddr', array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '');
        if($objDatabase->execute()) {
            $bGucci = 1;
        } else {
            $bGucci = 0;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pizza Ordererer</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<div class="page-container">
    <form method="POST">
        <? if($bGucci != 'derp') { if($bGucci){ echo '<p>SAVED ORDER</p>'; } else { echo '<p>FAILED TO SAVE</p>'; } } ?>
        <h1>Do you want pizza?</h1>
        <p style="color: red">This site <strong>will</strong> steal your IP address.</p>
        <p>You will receive one half of a pizza. You may not get the type of pizza you selected. Please don't hack me.</p>
        <div>
            <label for="inName">Name</label>
            <input type="text" name="inName" placeholder="Name">
        </div>
        <label>
            <div class="pizza">
                <img src="img/bbq.jpg" alt="BBQ Chicken Pizza">
                <p>BBQ Chicken</p>
                <input type="radio" value="BBQ Chicken Pizza" name="inPizza">
            </div>
        </label>
        <label>
            <div class="pizza">
                <img src="img/vegetable.jpg" alt="Vegetable Pizza">
                <p>Vegetable Feast</p>
                <input type="radio" value="Vegetable Pizza" name="inPizza">
            </div>
        </label>
        <label>
            <div class="pizza">
                <img src="img/hot.jpg" alt="Hot and Spicy">
                <p>Hot and Spicy</p>
                <input type="radio" value="Hot and Spicy" name="inPizza">
            </div>
        </label>
        <label>
            <div class="pizza">
                <img src="img/meatfeast.jpg" alt="Meat Feast Pizza">
                <p>Meat Feast</p>
                <input type="radio" value="Meat Feast Pizza" name="inPizza">
            </div>
        </label>
        <label>
            <div class="pizza">
                <img src="img/ham.jpg" alt="Ham and Pineapple Pizza">
                <p>Ham and Pineapple</p>
                <input type="radio" value="Ham and Pineapple Pizza" name="inPizza">
            </div>
        </label>
        <label>
            <div class="pizza">
                <img src="img/chickenpepper.jpg" alt="Chicken and Pepper Pizza">
                <p>Chicken and Pepper</p>
                <input type="radio" value="Chicken and Pepper Pizza" name="inPizza">
            </div>
        </label>
        <label>
            <div class="pizza">
                <img src="img/pepperoni.jpg" alt="Pepperoni Pizza">
                <p>Pepperono Pizza</p>
                <input type="radio" value="Pepperoni Pizza" name="inPizza">
            </div>
        </label>
        <label  >
            <div class="pizza">
                <img src="img/plain.jpg" alt="Plain Pizza">
                <p>Boring ol plain one...</p>
                <input type="radio" value="Plain Pizza" name="inPizza">
            </div>
        </label>
        <input type="submit" value="Order" name="inSubmit">
    </form>
    <h3>Today's orders</h3>
    <table>
        <thead>
            <tr><th>Name</th><th>IP</th><th>Pizza</th></tr>
        </thead>
        <tbody>
    <?php
        $sSQL = 'SELECT * FROM tOrders WHERE dOrdered >= CURRENT_DATE() - INTERVAL 13 HOUR ';
        $sSQL.= 'AND dOrdered <= CURRENT_DATE() + INTERVAL 16 HOUR';
        $objDatabase->query($sSQL);
        $arrResults = $objDatabase->resultSet();

        foreach($arrResults as $aResult)
        {
            echo '<tr><td>'.htmlPrepare($aResult['sName']).'</td><td>'.htmlPrepare($aResult['sRemoteAddr']).'</td><td>'.htmlPrepare($aResult['sChoice']).'</td></tr>';
        }
    ?>
        </tbody>
    </table>



    <div id="demo"></div>
    <script>
    var x = document.getElementById("demo");
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }
    function showPosition(position) {
        x.innerHTML = "Latitude: " + position.coords.latitude +
        "<br>Longitude: " + position.coords.longitude;
    }
    getLocation();
    </script>
</div>
</body>
</html>
