<html>

<head>
    <title>XSS 1</title>
</head>

<body>
    <p>
        <?php
            echo $_GET['input'];
        ?>
    </p>
</body>

</html>