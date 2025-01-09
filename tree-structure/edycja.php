<?php
$type = isset($_GET['type']) ? $_GET['type'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($type && $name && $id) {
    require_once 'config/database.php';

    $currentCategoryId = intval($id);

    $query = $db->prepare("
        WITH RECURSIVE subcategories AS (
            SELECT id_category
            FROM category
            WHERE id_category = :current_id
            UNION ALL
            SELECT c.id_category
            FROM category c
            INNER JOIN subcategories sc ON c.id_parent = sc.id_category
        )
        SELECT id_category, name
        FROM category
        WHERE id_category NOT IN (SELECT id_category FROM subcategories)
    ");

    $query->bindValue(':current_id', $currentCategoryId, PDO::PARAM_INT);
    $query->execute();
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <link rel="stylesheet" href="view/css/style.css">
    <h2>Edytuj <?php echo ($type === 'category') ? 'kategorię' : 'produkt'; ?></h2>

    <form id="edit-name-form">
        <label for="new_name">Nowa nazwa:</label>
        <input type="text" id="new_name" name="new_name" value="<?php echo htmlspecialchars($name); ?>" required>

        <input type="hidden" id="type" name="type" value="<?php echo htmlspecialchars($type); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <button type="button" id="submit-name-btn">Zapisz zmiany nazwy</button>
    </form>
    <div id="name-response"></div>

    <form id="move-category-form">
        <label for="new_category">Przenieś do kategorii:</label>
        <select id="new_category" name="new_category" required>
            <option value="">-- Wybierz kategorię --</option>
            <option value="0">Ustaw jako kategorię główną</option>
            <?php foreach ($categories as $category) { ?>
                <option value="<?php echo $category['id_category']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php } ?>
        </select>

        <button type="button" id="submit-move-btn">Przenieś</button>
    </form>
    <div id="move-response"></div>

    <form id="edit-position-form">
        <label for="new_position">Nowa pozycja:</label>
        <input type="number" id="new_position" name="new_position" value="1" required>

        <input type="hidden" id="type" name="type" value="<?php echo htmlspecialchars($type); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <button type="button" id="submit-position-btn">Zapisz zmianę pozycji</button>
    </form>
    <div id="position-response"></div>

    <form id="delete-form">
        <p>Usuń: <?php echo ($type === 'category') ? 'kategorię' : 'produkt'; ?>.</p>
        <button type="button" id="delete-btn">Usuń <?php echo ($type === 'category') ? 'kategorię' : 'produkt'; ?></button>
    </form>
    <div id="delete-response"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#submit-name-btn').on('click', function() {
            var newName = $('#new_name').val();
            var type = $('#type').val();
            var id = $('#id').val();

            $.ajax({
                type: "POST",
                url: "save_newname.php",
                data: { new_name: newName, type: type, id: id },
                success: function(response) {
                    $('#name-response').html(response);
                    if (response.includes("pomyślnie")) {
                        window.opener.loadTreeView();
                        window.close();
                    }
                },
                error: function(xhr, status, error) {
                    $('#name-response').html('Wystąpił błąd przy zapisie.');
                }
            });
        });

        $('#submit-move-btn').on('click', function() {
            var newCategory = $('#new_category').val();
            var type = $('#type').val();
            var id = $('#id').val();

            $.ajax({
                type: "POST",
                url: "save_newcategory.php",
                data: { new_category: newCategory, type: type, id: id },
                success: function(response) {
                    $('#move-response').html(response);
                    if (response.includes("pomyślnie")) {
                        window.opener.loadTreeView();
                        window.close();
                    }
                },
                error: function(xhr, status, error) {
                    $('#move-response').html('Wystąpił błąd przy przenoszeniu.');
                }
            });
        });

        $('#submit-position-btn').on('click', function() {
            var newPosition = $('#new_position').val();
            var type = $('#type').val();
            var id = $('#id').val();

            $.ajax({
                type: "POST",
                url: "save_newposition.php",
                data: { new_position: newPosition, type: type, id: id },
                success: function(response) {
                    $('#position-response').html(response);
                    if (response.includes("pomyślnie")) {
                        window.opener.loadTreeView();
                        window.close();
                    }
                },
                error: function(xhr, status, error) {
                    $('#position-response').html('Wystąpił błąd przy zapisie pozycji.');
                }
            });
        });

        $('#delete-btn').on('click', function() {
            var type = $('#type').val();
            var id = $('#id').val();

            if (confirm("Czy na pewno chcesz usunąć ten " + (type === 'category' ? 'kategorię' : 'produkt') + "?")) {
                $.ajax({
                    type: "POST",
                    url: "delete_item.php",
                    data: { type: type, id: id },
                    success: function(response) {
                        $('#delete-response').html(response);
                        if (response.includes("pomyślnie")) {
                            window.opener.loadTreeView();
                            window.close();
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#delete-response').html('Wystąpił błąd przy usuwaniu.');
                    }
                });
            }
        });
    </script>
    <?php
} else {
    echo "Nieprawidłowe dane wejściowe.";
}
?>
