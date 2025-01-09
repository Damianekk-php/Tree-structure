<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['category'])) {
        $categoryName = trim($_POST['category']);
        $parentCategoryId = $_POST['parent_category'];

        if (!preg_match('/^[a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/u', $categoryName)) {
            echo "Nazwa kategorii może zawierać tylko litery, cyfry oraz spacje.";
            exit;
        }

        if ($parentCategoryId === 'null') {
            $parentCategoryId = 0;
        }

        require_once '../config/database.php';
        
        try {
            $query = $db->prepare('SELECT COUNT(*) AS count FROM category WHERE id_parent = :id_parent');
            $query->bindValue(':id_parent', $parentCategoryId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $position = $result['count'] + 1;

            $query = $db->prepare('INSERT INTO category (id_parent, name, date_add, position) VALUES(:id_parent, :name, NOW(), :position)');
            $query->bindValue(':id_parent', $parentCategoryId, PDO::PARAM_INT);
            $query->bindValue(':name', $categoryName, PDO::PARAM_STR);
            $query->bindValue(':position', $position, PDO::PARAM_INT);
            $query->execute();

            echo "Kategoria została dodana pomyślnie.";
        } catch (Exception $e) {
            echo "Błąd podczas dodawania kategorii: " . $e->getMessage();
        }
    } else {
        echo "Nie podano nazwy kategorii.";
    }
} else {
    echo "Nieprawidłowa metoda zapytania.";
}
