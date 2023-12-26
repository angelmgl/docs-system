<?php

$sql = "SELECT 
        b.*,
        COALESCE(SUM(html_count), 0) + COALESCE(SUM(file_count), 0) + COALESCE(SUM(image_count), 0) AS total_docs_count
        FROM 
        businesses b
        LEFT JOIN 
        categories c ON b.id = c.business_id
        LEFT JOIN 
        (SELECT category_id, COUNT(*) AS html_count FROM html_docs GROUP BY category_id) hd ON c.id = hd.category_id
        LEFT JOIN 
        (SELECT category_id, COUNT(*) AS file_count FROM file_docs GROUP BY category_id) fd ON c.id = fd.category_id
        LEFT JOIN 
        (SELECT category_id, COUNT(*) AS image_count FROM image_docs GROUP BY category_id) imd ON c.id = imd.category_id
        GROUP BY 
        b.id
        ORDER BY 
        total_docs_count DESC
        LIMIT 5";

$stmt = $mydb->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$businesses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }
}

?>

<div class="panel">
    <h2>Empresas por cantidad de documentos</h2>

    <div>
        <?php foreach ($businesses as $business) { ?>
            <div class="my-business-card" style="margin-bottom: 10px;">
                <div class="logo" style="background-image: url(<?php echo get_logo($business) ?>)"></div>
                <div class="my-business-content">
                    <p class="my-business-name"><?php echo $business["name"] ?></p>
                    <p style="margin: 8px 0;" class="my-business-role">
                        <span style="margin-right: 10px; font-size: 14px;" class="status <?php echo $business['is_active'] === 1 ? 'active' : 'inactive' ?>">
                            <?php echo $business['is_active'] === 1 ? 'Activo' : 'Inactivo' ?>
                        </span>
                        <?php echo $business['total_docs_count'] ?> documentos totales
                    </p>
                    <a class="link" href="<?php echo BASE_URL . "/admin/contenido/?business_id=" . $business['id'] ?>">Ver documentos.</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>