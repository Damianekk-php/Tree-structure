<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['product']) && isset($_POST['category'])) {
        $product = trim($_POST['product']);
        $categoryId = $_POST['category'];

        if (!preg_match('/^[a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/u', $product)) {
            echo "Nazwa produktu może zawierać tylko litery, cyfry oraz spacje.";
            exit;
        }

        if (empty($product) || empty($categoryId)) {
            echo "Proszę podać nazwę produktu oraz wybrać kategorię.";
            exit;
        }

        require_once '../config/database.php';
        $date = date("Y-m-d H:i:s");

        try {
            $query = $db->prepare('INSERT INTO products (name, date_add) VALUES(:name, :date_add)');
            $query->bindValue(':name', $product, PDO::PARAM_STR);
            $query->bindValue(':date_add', $date, PDO::PARAM_STR);
            $query->execute();

            $productId = $db->lastInsertId();
            if (!$productId) {
                throw new Exception("Nie udało się dodać produktu.");
            }

            $query = $db->prepare('SELECT COUNT(*) AS count FROM category_products WHERE id_category = :id_category');
            $query->bindValue(':id_category', $categoryId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $position = $result['count'] + 1;

            $query = $db->prepare('INSERT INTO category_products (id_category, id_product, position) VALUES(:id_category, :id_product, :position)');
            $query->bindValue(':id_category', $categoryId, PDO::PARAM_INT);
            $query->bindValue(':id_product', $productId, PDO::PARAM_INT);
            $query->bindValue(':position', $position, PDO::PARAM_INT);
            $query->execute();

            echo "Produkt został dodany pomyślnie.";
        } catch (Exception $e) {
            echo "Błąd podczas dodawania produktu: " . $e->getMessage();
        }
    } else {
        echo "Nie podano produktu lub kategorii.";
    }
} else {
    echo "Nieprawidłowa metoda zapytania.";
}
