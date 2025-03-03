<?php
session_start();
require __DIR__ . '/model/db.php';

// Fetch stored content for the admin tools section
$stmt = $db->prepare("SELECT id, content FROM site_content WHERE section = 'admin_tools'");
$stmt->execute();
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debt Buster Tools</title>
    <link rel="stylesheet" href="style.css">

    <!-- TinyMCE (Only Load for Admins) -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
    <script src="https://cdn.tiny.cloud/1/btynv80wdibaaigf2qfyqy5hj0bwp5cryjqsdr6sfpmd2azh/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <?php endif; ?>
</head>
<body>
<?php include 'nav.php'; ?>
<section class="banner">
    <h2 class="banner-text">Snowball Your Success, Avalanche Your Debt - Financial Tools for You</h2>
</section>

<main class="debt-buster-container">
    <section class="tool-section">
        <div class="tool-box">
            <div class="admin-content">
                <?php if (!empty($resources)): ?>
                    <?php foreach ($resources as $resource): ?>
                        <div class="resource-box">
                            <?= htmlspecialchars_decode($resource['content']); ?>

                        <!--edit button-->
                        <?php if (isset($_SESSION['role'])&& $_SESSION['role'] === 'admin'): ?>
                            <button class="edit-btn" onclick="editResource(<?= $resource['id']; ?>, `<?= htmlspecialchars($resource['content'], ENT_QUOTES); ?>`)">Edit

                            </button>

                        <?php endif; ?>
                        </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <p>No admin content available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Show TinyMCE Editor Only for Admins -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <section class="admin-tools">
            <h2>Admin Tools Editor</h2>
            <form id="admin-editor-form" action="save_editor_content.php" method="post">
                <textarea id="admin-editor" name="editor_content"></textarea>
                <button type="submit" class="submit-btn">Save Content</button>
            </form>
        </section>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
        tinymce.init({
        selector: "#admin-editor",
        width: 650,
        plugins: [ 'table powerpaste', 'lists media', 'paste' ],
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify'
    });
});

function editResource(id, content) {
    console.log("Editing resource:", id, content); // Debugging

    // Ensure TinyMCE is initialized
    if (tinymce.get("admin-editor")) {
        tinymce.get("admin-editor").setContent(content);
    } else {
        document.getElementById("admin-editor").value = content;
    }

    // Show the editor (if it's hidden)
    document.getElementById("admin-editor-form").style.display = "block";

    // Get form element
    let editorForm = document.getElementById("admin-editor-form");

    // Ensure only one hidden input exists
    let resourceInput = document.getElementById("resource-id");
    if (!resourceInput) {
        resourceInput = document.createElement("input");
        resourceInput.type = "hidden";
        resourceInput.name = "resource_id";
        resourceInput.id = "resource-id";
        editorForm.appendChild(resourceInput);
    }
    resourceInput.value = id;

    // Set form action to update the specific resource
    editorForm.action = "update-resource.php";
}
</script>

    <?php endif; ?>
</main>

<footer class="footer">
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
</footer>

</body>
</html>
