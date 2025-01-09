<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);
    if (isset($_POST['id']) && isset($_POST['position']) && isset($_POST['parent'])) {
        $categoryId = $_POST['id'];
        $newPosition = $_POST['position'];
        $parentId = $_POST['parent'];
        require_once '../config/database.php';

        try {
            $query = $db->prepare('UPDATE category SET position = :position, id_parent = :id_parent WHERE id_category = :id_category');
            $query->bindValue(':position', $newPosition, PDO::PARAM_INT);
            $query->bindValue(':id_parent', $parentId, PDO::PARAM_INT);
            $query->bindValue(':id_category', $categoryId, PDO::PARAM_INT);
            $query->execute();

            echo "Pozycja kategorii została zaktualizowana pomyślnie.";
        } catch (Exception $e) {
            echo "Błąd podczas aktualizacji pozycji kategorii: " . $e->getMessage();
        }
    } else {
        echo "Brak wymaganych danych.";
    }
} else {
    echo "Nieprawidłowa metoda zapytania.";
}


?>
