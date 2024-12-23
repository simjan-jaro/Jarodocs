<?php
include 'db_connect.php'; // Koble til databasen

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $document_id = $_POST['document_id'];
    $document_link = $_POST['document_link'];

    // Oppdater lenken i databasen
    $sql = "UPDATE Documents SET document_link = ? WHERE document_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $document_link, $document_id);

    if ($stmt->execute()) {
        // Behold prosjekt- og dokumenttypevalg etter lagring
        $redirect_url = "projects.php?project_id=" . $_POST['project_id'];
        if (!empty($_POST['document_type'])) {
            $redirect_url .= "&document_type=" . $_POST['document_type'];
        }
        header("Location: $redirect_url"); // Omdiriger til samme side med valg
        exit;
    } else {
        echo "Feil ved lagring av lenke: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>





