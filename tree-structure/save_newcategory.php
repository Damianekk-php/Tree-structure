<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCategory = isset($_POST['new_category']) ? intval($_POST['new_category']) : null;
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    if ($newCategory !== null && $id && $type) {
        try {
            if ($type === 'category' && $newCategory == $id) {
                echo "Nie można przenieść kategorii do samej siebie.";
                exit; 
            }

            if ($type === 'product' && $newCategory == 0) {
                echo "Nie można ustawić produktu jako kategorię główną.";
                exit; 
            }

            $currentCategoryQuery = $db->prepare('SELECT id_category FROM category_products WHERE id_product = :id');
            $currentCategoryQuery->bindValue(':id', $id, PDO::PARAM_INT);
            $currentCategoryQuery->execute();
            $currentCategory = $currentCategoryQuery->fetchColumn();

            if ($type === 'product' && $newCategory == $currentCategory) {
                echo "Nie można przenieść produktu do tej samej kategorii.";
                exit;
            }


            if ($type === 'category') {
                $query = $db->prepare('UPDATE category SET id_parent = :newCategory WHERE id_category = :id');
                $query->bindValue(':newCategory', $newCategory, PDO::PARAM_INT);
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();

                echo "Kategoria została pomyślnie przeniesiona.";
            } elseif ($type === 'product') {
                $query = $db->prepare('UPDATE category_products SET id_category = :newCategory WHERE id_product = :id');
                $query->bindValue(':newCategory', $newCategory, PDO::PARAM_INT);
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();

                echo "Produkt został pomyślnie przeniesiony.";
            } else {
                echo "Nieznany typ obiektu.";
            }
        } catch (PDOException $e) {
            echo "Błąd podczas przenoszenia: " . $e->getMessage();
        }
    } else {
        echo "Nieprawidłowe dane wejściowe.";
    }
} else {
    echo "Nieprawidłowe żądanie.";
}
