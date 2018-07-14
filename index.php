<?php
/**
* Created by Jan-Hendrik Caspers
* Date: 14.07.2018
* Time: 09:41
* Home Control (HC) Time input for setting heating, shutter or lighting times on a 24 hours base
* This is a demonstration file how to use it
*/

require('./classes/hctimeinput.php');

$shutter = new \jhhctimeinput\hctimeinput(1);
$heating = new \jhhctimeinput\hctimeinput(2);

?>
<!doctype html>
<html lang="us">
<head>
	<meta charset="utf-8">
	<title>SmartHome TimeProgramm Input</title>
	<link href="./jui/jquery-ui.css" rel="stylesheet">
	<style>
		body{
			font-family: "Trebuchet MS", sans-serif;
			margin: 50px;
		}
		.hcmousedown {
			border: 1px dashed red;
		}
        .clear{
            clear: both;
        }
        .timebar{
            font-size: 9px;
        }
        .timevalue{
            float:left;
            width: 28px;
        }
	</style>
</head>
<body>


<h3>1st Input</h3>
<div class="timebar">
    <?php for ($i=0;$i<24;$i++): ?>
        <div class="timevalue"><?php printf("%02d", $i);?>:00</div>
    <?php endfor; ?>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<div class="ShutterControl" id="sh1"></div><br />


<br />&nbsp;
<br />&nbsp;


<h3>2nd Input with different Colors</h3>
<div class="timebar">
    <?php for ($i=0;$i<24;$i++): ?>
        <div class="timevalue"><?php printf("%02d", $i);?>:00</div>
    <?php endfor; ?>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<div class="HeatingControl" id="htg1"></div><br />




<script src="js/jquery.js"></script>
<script src="jui/jquery-ui.js"></script>
<script src="js/hctime.js"></script>
<script>

    $(function() {
        // Initialize the TimeInput widget, this will render the SVG and handle the mouse input
        // language=JQuery-CSS
        $('.ShutterControl').hctimeprogram({
            selstop: function( event, data ) {
                let hcdata = $(this).hctimeprogram("getarray");
                let id = <?php echo $shutter->getId();?>;
                $.ajax({
                    method: "POST",
                    url: "ajax.php",
                    data: { id: id, data: hcdata }
                })
                    .done(function( msg ) {
                        // Do something after saving the Data
                        // or check if the return value confirms a successfull save
                        //alert( "Data Saved: " + msg );
                    });
            }, times:[<?php  echo $shutter->GetJSDataArray(); ?>]
        });
        // language=JQuery-CSS
        $(".HeatingControl").hctimeprogram({
            selstop: function( event, data ) {
                let hcdata = $(this).hctimeprogram("getarray");
                let id = <?php echo $heating->getId();?>;
                $.ajax({
                    method: "POST",
                    url: "ajax.php",
                    data: { id: id, data: hcdata }
                })
                    .done(function( msg ) {
                        // Do something after saving the Data
                        // or check if the return value confirms a successfull save
                        //alert( "Data Saved: " + msg );
                    });
            }, activecolor1: '#123456', activecolor2: '#654321',times:[<?php  echo $heating->GetJSDataArray(); ?>]
        });
    });
</script>
</body>
</html>
