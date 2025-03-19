<?php 

include('../class.php');
$db = new global_class();

session_start();
$UserID = $_SESSION['UserID'];

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

// Fetch posts and return JSON response
echo json_encode($db->FetchUserPost($UserID, $offset, $limit));

?>
