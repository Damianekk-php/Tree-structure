<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPosition = isset($_POST['new_position']) ? intval($_POST['new_position']) : null;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if ($newPosition === null || $id === null || !in_array($type, ['category', 'product'])) {
        echo "Nieprawidłowe dane wejściowe.";
        exit;
    }

    try {
        if ($type === 'category') {
            $query = $db->prepare("UPDATE category SET position = :new_position WHERE id_category = :id");
        } elseif ($type === 'product') {
            $query = $db->prepare("UPDATE category_products SET position = :new_position WHERE id_product = :id");
        }

        $query->bindValue(':new_position', $newPosition, PDO::PARAM_INT);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount() > 0) {
            echo "Pozycja została pomyślnie zaktualizowana.";
        } else {
            echo "Nie znaleziono elementu lub brak zmian w pozycji.";
        }
    } catch (Exception $e) {
        echo "Błąd podczas aktualizacji pozycji: " . $e->getMessage();
    }
} else {
    echo "Nieprawidłowa metoda żądania.";
}
?>
