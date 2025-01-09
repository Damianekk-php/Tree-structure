<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);
    if (isset($_POST['product_id']) && isset($_POST['new_position']) && isset($_POST['new_category_id'])) {
        $productId = $_POST['product_id'];
        $newPosition = $_POST['new_position'];
        $categoryId = $_POST['new_category_id'];
        require_once '../config/database.php';

        try {
            $query = $db->prepare('UPDATE category_products SET position = :position, id_category = :id_category WHERE id_product = :id_product');
            $query->bindValue(':position', $newPosition, PDO::PARAM_INT);
            $query->bindValue(':id_category', $categoryId, PDO::PARAM_INT);
            $query->bindValue(':id_product', $productId, PDO::PARAM_INT);
            $query->execute();

            echo "Pozycja produktu została zaktualizowana pomyślnie.";
        } catch (Exception $e) {
            echo "Błąd podczas aktualizacji pozycji produktu: " . $e->getMessage();
        }
    } else {
        echo "Brak wymaganych danych.";
    }
} else {
    echo "Nieprawidłowa metoda zapytania.";
}
?>
