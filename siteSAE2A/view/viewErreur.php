<!DOCTYPE html>

<html lang ="fr">
<head>
    <meta charset="UTF-8">
    <title>Erreur</title>
</head>

<body>

<h1>ERREUR !!!!!</h1>
<?php
if (isset($dVueEreur)) {
    foreach ($dVueEreur as $value) {
        echo $value;
    }
}
?>


</body>
</html>