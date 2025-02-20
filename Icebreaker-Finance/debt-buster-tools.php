<?php
session_start();
require __DIR__ . '/model/db.php';
// Fetch stored content for the admin tools section
$stmt = $db->prepare("SELECT content FROM site_content WHERE section = 'admin_tools'");
$stmt->execute();
$adminContent = $stmt->fetchColumn();

if ($adminContent === false) {
    echo "<p style='color: red;'>Error: No content found for 'admin_tools'.</p>";
}
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
                <?= !empty($adminContent) ? nl2br($adminContent) . "<button type=" : "<p>No admin content available.</p>"; ?>
            </div>
        </div>
    </section>

    <!-- Show TinyMCE Editor Only for Admins -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <section class="admin-tools">
        <h2>Admin Tools Editor</h2>
        <form id="admin-editor-form" action="save_editor_content.php" method="post">
        <textarea id="admin-editor" name="editor_content">Admin Tools Editor</textarea>
        <button type="submit" class="submit-btn">Save Content</button>
    </form>
        </section>

        <script>
        tinymce.init({
            selector: '#admin-editor',
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media',
                'searchreplace', 'table', 'visualblocks', 'wordcount',
                'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker',
                'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage',
                'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags',
                'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Admin',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' }
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant'))
        });
        </script>
    <?php endif; ?>
</main>

<footer class="footer">
    <p>© 2025 Icebreaker Finance. All rights reserved.</p>
</footer>

</body>
</html>
