<?php
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="csrf-app.zip"');
readfile('csrf-app.zip');
?>