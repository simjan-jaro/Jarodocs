<?php
include 'db_connect.php'; // Koble til databasen

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $document_id = $_POST['document_id'];
    $iso_code = $_POST['iso_code'];
    $document_type = $_POST['document_type'];
    $document_link = $_POST['document_link'];

    // Oppdater lenken for dokumentet
    $updateQuery = "UPDATE iso9001_documents 
                    SET document_link = ? 
                    WHERE document_id = ? AND iso_code = ? AND document_type = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("siis", $document_link, $document_id, $iso_code, $document_type);

    if ($stmt->execute()) {
        // Tilbake til siden du kom fra
        if (isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: isodocuments.php");
        }
        exit;
    } else {
        echo "Feil ved oppdatering av lenken: " . $conn->error;
    }
} else {
    echo "Ugyldig forespørsel.";
}

$conn->close();






