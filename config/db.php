<?php
$conn = new PDO(
"mysql:host=localhost;dbname=todo_app;charset=utf8",
"root",
"",
[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);