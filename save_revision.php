<?php
include 'db_connect.php'; // Koble til databasen

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $document_id = $_POST['document_id'];
    $project_id = $_POST['project_id'];
    $document_type = $_POST['document_type'];

    // Finn alle eksisterende revisjonsnumre for dokumentet
    $query = "SELECT revision_number 
              FROM documents 
              WHERE document_id = ? AND project_id = ? AND document_type = ?
              ORDER BY revision_number ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $document_id, $project_id, $document_type);
    $stmt->execute();
    $result = $stmt->get_result();

    $existing_revisions = [];
    while ($row = $result->fetch_assoc()) {
        $existing_revisions[] = $row['revision_number'];
    }

    // Finn neste ledige revisjonsnummer
    $new_revision = null;
    for ($i = 0; $i < 100; $i++) { // Søk opptil R99
        $potential_revision = 'R' . str_pad($i, 2, '0', STR_PAD_LEFT);
        if (!in_array($potential_revision, $existing_revisions)) {
            $new_revision = $potential_revision;
            break;
        }
    }

    if ($new_revision === null) {
        echo "Ingen ledige revisjonsnumre tilgjengelig.";
        exit;
    }

    // Sett opp en ny revisjon basert på eksisterende dokument
    $insertQuery = "INSERT INTO documents (document_id, project_id, document_type, revision_number, document_name, document_link)
                    SELECT document_id, project_id, document_type, ?, document_name, NULL 
                    FROM documents 
                    WHERE document_id = ? AND project_id = ? AND document_type = ?
                    LIMIT 1";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("siis", $new_revision, $document_id, $project_id, $document_type);

    if ($insertStmt->execute()) {
        echo "Ny revisjon opprettet: $new_revision<br>";
        // Tilbake til prosjektsiden
        header("Location: projects.php?project_id=$project_id&document_type=$document_type");
        exit;
    } else {
        echo "Feil ved oppretting av ny revisjon: " . $conn->error;
    }
} else {
    echo "Ugyldig forespørsel.";
}

$conn->close();
?>





