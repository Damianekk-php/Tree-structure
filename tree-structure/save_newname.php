<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = isset($_POST['new_name']) ? trim($_POST['new_name']) : null;
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    if ($newName && $id && $type) {
        if (!preg_match('/^[a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]+$/u', $newName)) {
            echo "Nazwa może zawierać tylko litery, cyfry oraz spacje.";
            exit;
        }

        try {
            if ($type === 'product') {
                $query = $db->prepare('UPDATE products SET name = :newName WHERE id_product = :id');
                $query->bindValue(':newName', $newName, PDO::PARAM_STR);
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();

                echo "Nazwa produktu została pomyślnie zaktualizowana.";
            } elseif ($type === 'category') {
                $query = $db->prepare('UPDATE category SET name = :newName WHERE id_category = :id');
                $query->bindValue(':newName', $newName, PDO::PARAM_STR);
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();

                echo "Nazwa kategorii została pomyślnie zaktualizowana.";
            } else {
                echo "Nieznany typ obiektu.";
            }
        } catch (PDOException $e) {
            echo "Błąd podczas aktualizacji: " . $e->getMessage();
        }
    } else {
        echo "Nieprawidłowe dane wejściowe.";
    }
} else {
    echo "Nieprawidłowe żądanie.";
}
?>
