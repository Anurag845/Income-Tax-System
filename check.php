<?php
include 'db_connect.php';

$conn = OpenCon();

echo "Connected Successfully";

CloseCon($conn);

?>
