<?php
include '../middleware.php';
auth();
include '../config/db.php';

$isAdmin = $_SESSION['user']['role'] === 'admin';

if(isset($_POST['title'])){
$userId = $isAdmin && isset($_POST['user_id']) ? $_POST['user_id'] : $_SESSION['user']['id'];
if($_POST['id']){
$stmt=$conn->prepare("UPDATE tasks SET title=?,description=? WHERE id=? AND user_id=?");
$stmt->execute([$_POST['title'],$_POST['description'],$_POST['id'],$_SESSION['user']['id']]);
}else{
$stmt=$conn->prepare("INSERT INTO tasks(user_id,title,description) VALUES (?,?,?)");
$stmt->execute([$userId,$_POST['title'],$_POST['description']]);}}


if(isset($_GET['done'])){
$conn->prepare("UPDATE tasks SET status='completed' WHERE id=? AND user_id=?")
->execute([$_GET['done'],$_SESSION['user']['id']]);}


if(isset($_GET['delete'])){
$conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?")
->execute([$_GET['delete'],$_SESSION['user']['id']]);}


header('Location: ' . ($isAdmin ? '../admin/dashboard.php' : '../index.php'));
exit;
