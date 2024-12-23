<?php
include 'db_connect.php'; // Koble til databasen

// Sjekk at alle nødvendige felt er sendt
if (!isset($_POST['project_id'], $_POST['document_type'], $_POST['document_name'])) {
    die('Alle felt må fylles ut.');
}

// Hent inn data fra skjemaet
$projectId = $_POST['project_id'];
$documentType = $_POST['document_type'];
$documentName = $_POST['document_name'];

// Finn neste ledige document_id for valgt prosjekt og dokumenttype
$query = "SELECT MAX(document_id) AS max_id FROM Documents WHERE project_id = ? AND document_type = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $projectId, $documentType);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$nextId = $row['max_id'] ? $row['max_id'] + 1 : 1;

// Sett opp standardverdier for nytt dokument
$revisionNumber = 'R00';
$documentLink = ''; // Lenke kan legges til senere

// Sett inn det nye dokumentet i databasen
$insertQuery = "INSERT INTO Documents (document_id, project_id, document_type, revision_number, document_name, document_link) 
                VALUES (?, ?, ?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param("iissss", $nextId, $projectId, $documentType, $revisionNumber, $documentName, $documentLink);

if ($insertStmt->execute()) {
    // Omdiriger brukeren tilbake til projects.php med filteret
    header("Location: projects.php?project_id=$projectId&document_type=$documentType");
    exit;
} else {
    die("Kunne ikke legge til dokumentet: " . $conn->error);
}
?>





