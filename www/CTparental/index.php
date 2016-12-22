<?php
include 'locale.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo gettext("Access has been Denied!"); ?></title>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="http://127.0.0.1/CTparental/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="http://127.0.0.1/CTparental/css/main.css" type="text/css">
        
        <script src="http://127.0.0.1/CTparental/js/jquery-1.12.3.min.js"></script>
        <script src="http://127.0.0.1/CTparental/js/bootstrap.min.js"></script>
    </head>

    <body>
        <div class="container">
            <div class="header clearfix">
                <h3 class="text-muted"><?php echo gettext("Access has been Denied!"); ?></h3>
            </div>
            
            <div class="jumbotron">
                <img src="http://127.0.0.1/CTparental/images/2518388623_1.png" />

                <h1><?php echo gettext("Access to the page:");?></h1>
                <h2><?php echo $_SERVER["HTTP_HOST"]; ?></h2>
                <hr />
                <h3><?php echo gettext("... has been denied.");?></h3>
            </div>
            
            <p class="text-justify text-warning">
                <?php
                    echo gettext("You are seeing this error because what you attempted to access appears to contain,")."&nbsp;".gettext("or is labeled as containing, material that has been deemed inapproriate.")."&nbsp;".gettext("If you have any queries contact your ICT Co-ordinator or Network Manager.");
                ?>
            </p>

            <footer class="footer">
                <span><?php echo gettext("Filtered by ");?> <a href="http://www.thekelleys.org.uk/dnsmasq/doc.html" target="_blank">Dnsmasq</a></span>
                <span class="pull-right">CTparental</span>
            </footer>
        </div>
    </body>
</html>
