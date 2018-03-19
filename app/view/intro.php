<form action="control.php" method="post" id="dateSelector">
    <fieldset>
        <div>Vyberte datum:</div>
        <input type="date" name="date" max="<?php echo date("Y-m-d", strtotime("-1 day")); ?>" />
        <input type="submit" value="Zobrazit" />
    </fieldset>
</form>

<table>
    <tr>
        <th>Detektor</th>
        <th>Datum a čas</th>
        <th>Intenzita</th>
        <th>Intenzita "N"</th>
        <th>Obsazenost</th>
        <th>Rychlost</th>
        <th>Stav</th>
        <th>Typ vozidla</th>
        <th>Trvání v setinách</th>
        <th>Rychlost (historie)</th>
        <th>Typ vozidla "10"</th>
    </tr>
    
    <?php
        for ($i = 0; $i < count($traffic); $i++) {
            echo "<tr>".$traffic[$i]->toString()."</tr>";
        }
    ?>
    
</table>

<table>
    <tr>
        <th>Název</th>
        <th>Město</th>
        <th>Ulice</th>
        <th>Zařízení</th>
        <th>Oblast</th>
    </tr>
    
    <?php
        for ($i = 0; $i < count($locations); $i++) {
            echo "<tr>".$locations[$i]->toString()."</tr>";
        }
    ?>
    
</table>