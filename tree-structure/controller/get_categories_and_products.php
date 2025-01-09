<?php
require_once '../config/database.php';

try {
    $queryCategories = $db->prepare("SELECT id_category, name, id_parent, position 
                                     FROM category 
                                     ORDER BY position ASC");
    $queryCategories->execute();
    $categories = $queryCategories->fetchAll(PDO::FETCH_ASSOC);

    $queryProducts = $db->prepare("SELECT p.id_product, p.name, cp.id_category, cp.position 
                                    FROM products p 
                                    JOIN category_products cp ON p.id_product = cp.id_product 
                                    ORDER BY cp.id_category ASC, cp.position ASC");
    $queryProducts->execute();
    $products = $queryProducts->fetchAll(PDO::FETCH_ASSOC);

    $productGroup = [];
    foreach ($products as $product) {
        $productGroup[$product['id_category']][] = $product;
    }

    echo json_encode(['categories' => $categories, 'products' => $productGroup]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
