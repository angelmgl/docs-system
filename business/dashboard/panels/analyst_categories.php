<?php

$sql = "SELECT * FROM businesses WHERE id = $my_business";

$stmt = $mydb->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];

$stmt = $mydb->prepare("
        SELECT c.* 
        FROM categories c
        JOIN user_categories uc ON c.id = uc.category_id
        WHERE c.business_id = ? AND uc.user_id = ?
    ");
$stmt->bind_param("ii", $my_business, $my_id);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$stmt->close();

?>

<br>
<div class="panel" style="grid-column: 1 / span 2;">
    <h2>Categorías asignadas</h2>

    <?php if (empty($categories)) { ?>
        <p>Esta empresa no tiene categorías creadas aún...</p>
        <?php } else {
        foreach ($categories as $category) { ?>
            <div class="category-item">
                <div class="category-header">
                    <h2><?php echo $category["name"] ?></h2>
                    <p><?php echo $category["description"] ?></p>
                </div>
                <div class="category-content">
                    <a href="<?php echo BASE_URL ?>/business/contenido/categorias?category_id=<?php echo $category["id"]; ?>" class="btn btn-primary">Ver documentos</a>
                </div>
            </div>
    <?php }
    } ?>
</div>