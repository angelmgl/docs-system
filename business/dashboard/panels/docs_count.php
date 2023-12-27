<?php

$sql = "SELECT 
        c.*,
        COALESCE(SUM(html_count), 0) + COALESCE(SUM(image_count), 0) + COALESCE(SUM(file_count), 0) AS total_docs_count
        FROM 
        categories c
        LEFT JOIN (
        SELECT category_id, COUNT(*) AS html_count FROM html_docs GROUP BY category_id
        ) hd ON c.id = hd.category_id
        LEFT JOIN (
        SELECT category_id, COUNT(*) AS image_count FROM image_docs GROUP BY category_id
        ) imd ON c.id = imd.category_id
        LEFT JOIN (
        SELECT category_id, COUNT(*) AS file_count FROM file_docs GROUP BY category_id
        ) fd ON c.id = fd.category_id
        WHERE 
        c.business_id = $my_business
        GROUP BY 
        c.id
        ORDER BY 
        total_docs_count DESC";

$stmt = $mydb->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

?>

<div class="panel">
    <h2>Documentos por categoría</h2>

    <div>
        <?php foreach ($categories as $category) { ?>
            <div class="my-business-card" style="margin-bottom: 10px;">
                <div class="my-business-content">
                    <p class="my-business-name"><?php echo $category["name"] ?></p>
                    <p style="margin: 8px 0;" class="my-business-role">
                        <?php echo $category['total_docs_count'] ?> documentos totales
                    </p>
                    <a class="link" href="<?php echo BASE_URL . "/business/contenido/categorias/?category_id=" . $category['id'] ?>">Ver documentos</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>