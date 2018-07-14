<?php
if (isset($_POST['id']) && isset($_POST['data'])) {
    require('./classes/hctimeinput.php');

    $id = (int)$_POST['id'];
    $data = $_POST['data'];
    $hcobject = new \jhhctimeinput\hctimeinput($id);

    $i=0;
    foreach($data as $row) {
        $hcobject->UpdateInterval($i,($row==1?1:0));
        $i++;
    }
}


?>