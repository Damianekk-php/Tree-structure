<?php

require_once '../config/database.php';

$query = $db->prepare('SELECT id_category, name FROM category');
$query->execute();

$categories = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categories);

?>