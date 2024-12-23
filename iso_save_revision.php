<?php
include 'db_connect.php'; // Koble til databasen

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $document_id = $_POST['document_id'];
    $iso_code = $_POST['iso_code'];
    $document_type = $_POST['document_type'];

    // Finn det høyeste eksisterende revisjonsnummeret for dokumentet
    $query = "SELECT MAX(revision_number) AS highest_revision 
              FROM iso9001_documents 
              WHERE document_id = ? AND iso_code = ? AND document_type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $document_id, $iso_code, $document_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $highest_revision = $row['highest_revision'] ?? null;

    // Generer neste revisjonsnummer
    if ($highest_revision !== null) {
        // Fjern 'R' fra det høyeste revisjonsnummeret og øk med 1
        $next_revision_number = (int)substr($highest_revision, 1) + 1;
        $new_revision = 'R' . str_pad($next_revision_number, 2, '0', STR_PAD_LEFT);
    } else {
        // Hvis ingen revisjoner finnes, start med R00
        $new_revision = 'R00';
    }

    // Opprett ny revisjon
    $insertQuery = "INSERT INTO iso9001_documents (iso_code, document_id, document_type, revision_number, document_name, document_link)
                    SELECT iso_code, document_id, document_type, ?, document_name, NULL 
                    FROM iso9001_documents 
                    WHERE document_id = ? AND iso_code = ? AND document_type = ?
                    LIMIT 1";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("siis", $new_revision, $document_id, $iso_code, $document_type);

    if ($insertStmt->execute()) {
        // Tilbake til ISO-dokument-siden
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        echo "Feil ved oppretting av ny revisjon: " . $conn->error;
    }
} else {
    echo "Ugyldig forespørsel.";
}

$conn->close();



