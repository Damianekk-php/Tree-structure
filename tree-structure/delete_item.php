<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    if ($id && $type) {
        try {
            if ($type === 'category') {
                $parentQuery = $db->prepare('SELECT id_parent FROM category WHERE id_category = :id');
                $parentQuery->bindValue(':id', $id, PDO::PARAM_INT);
                $parentQuery->execute();
                $parentCategory = $parentQuery->fetch(PDO::FETCH_ASSOC);

                $checkQuery = $db->prepare('SELECT COUNT(*) FROM category_products WHERE id_category = :id');
                $checkQuery->bindValue(':id', $id, PDO::PARAM_INT);
                $checkQuery->execute();
                $productCount = $checkQuery->fetchColumn();

                if (($parentCategory['id_parent'] == 0) && $productCount > 0) {
                    echo "Nie można usunąć kategorii, ponieważ są do niej przypisane produkty i nie ma nadrzędnej kategorii.";
                    return;
                }

                $parentCategoryId = $parentCategory ? $parentCategory['id_parent'] : null;

                if ($parentCategoryId !== 0) {
                    $updateProductsQuery = $db->prepare('UPDATE category_products SET id_category = :newParentId WHERE id_category = :oldCategoryId');
                    $updateProductsQuery->bindValue(':newParentId', $parentCategoryId, PDO::PARAM_INT);
                    $updateProductsQuery->bindValue(':oldCategoryId', $id, PDO::PARAM_INT);
                    $updateProductsQuery->execute();
                }

                if ($parentCategory) {
                    $updateSubcategoriesQuery = $db->prepare('UPDATE category SET id_parent = :newParentId WHERE id_parent = :oldParentId');
                    $updateSubcategoriesQuery->bindValue(':newParentId', $parentCategoryId === null ? 0 : $parentCategoryId, PDO::PARAM_INT);
                    $updateSubcategoriesQuery->bindValue(':oldParentId', $id, PDO::PARAM_INT);
                    $updateSubcategoriesQuery->execute();
                }

                $deleteQuery = $db->prepare('DELETE FROM category WHERE id_category = :id');
                $deleteQuery->bindValue(':id', $id, PDO::PARAM_INT);
                $deleteQuery->execute();

                echo "Kategoria została pomyślnie usunięta.";
            } elseif ($type === 'product') {
                $deleteQuery = $db->prepare('DELETE FROM products WHERE id_product = :id');
                $deleteQuery->bindValue(':id', $id, PDO::PARAM_INT);
                $deleteQuery->execute();

                echo "Produkt został pomyślnie usunięty.";
            } else {
                echo "Nieznany typ obiektu.";
            }
        } catch (PDOException $e) {
            echo "Błąd podczas usuwania: " . $e->getMessage();
        }
    } else {
        echo "Nieprawidłowe dane wejściowe.";
    }
} else {
    echo "Nieprawidłowe żądanie.";
}
?>
