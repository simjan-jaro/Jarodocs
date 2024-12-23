<?php

include 'db_connect.php'; // Koble til databasen

// Hent prosjektnumre for dropdown-menyen
$projectsQuery = "SELECT project_id, project_number FROM Projects";
$projectsResult = $conn->query($projectsQuery);

// Hent tilgjengelige dokumenttyper for dropdown-menyen
$docTypesQuery = "SELECT document_type_code, document_type_name FROM DocumentTypes";
$docTypesResult = $conn->query($docTypesQuery);

// Hent valgt prosjektnummer og dokumenttype
$selectedProject = isset($_GET['project_id']) ? $_GET['project_id'] : null;
$selectedDocType = isset($_GET['document_type']) ? $_GET['document_type'] : null;
?>

<?php
$documentsQuery = "SELECT
                       p.project_number, 
                       d.document_id, 
                       d.document_type, 
                       d.revision_number, 
                       d.document_name, 
                       d.document_link
                   FROM Projects p
                   INNER JOIN Documents d ON p.project_id = d.project_id
                   WHERE 1=1";

if ($selectedProject) {
    $documentsQuery .= " AND p.project_id = $selectedProject";
}

if ($selectedDocType) {
    $documentsQuery .= " AND d.document_type = '$selectedDocType'";
}

$documentsQuery .= " ORDER BY d.document_name";
$documentsResult = $conn->query($documentsQuery);

if (!$documentsResult) {
    die("Feil i spørringen: " . $conn->error); // Feilsjekk spørringen
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prosjektdokumentasjon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        select {
            padding: 5px 10px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #eaeaea;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        tbody tr:hover {
    background-color: #ffff99; /* Lys gul farge når man holder musepekeren over */
}

.new-entry {
    background-color: #ffeb3b; /* Sterk gul farge for nye revisjoner/dokumenter */
    transition: background-color 0.3s ease; /* Myk overgang */
}

    </style>

</head>
<script>
function addLink(button) {
    const form = button.nextElementSibling; // Hent skjemaet rett etter knappen
    form.style.display = "block"; // Vis skjemaet
    button.style.display = "none"; // Skjul "Add Link"-knappen
}

function editLink(button) {
    const linkElement = button.previousElementSibling; // Hent den klikkbare lenken
    const form = button.nextElementSibling; // Hent skjemaet rett etter knappen
    linkElement.style.display = "none"; // Skjul lenken
    button.style.display = "none"; // Skjul "Endre lenke"-knappen
    form.style.display = "block"; // Vis skjemaet
}

function confirmNewRevision() {
    return confirm("Er du sikker på at du vil opprette en ny revisjon?");
}
</script>


<body>
    <h1>Prosjektdokumentasjon</h1>
    <form action="index.php" method="get">
    <button type="submit">Tilbake til forsiden</button>
</form>


    <!-- Rullgardin for prosjektnummer -->
    <form method="GET" action="projects.php">
        <label for="project_id">Velg prosjektnummer:</label>
        <select name="project_id" id="project_id" onchange="this.form.submit()">
            <option value="">-- Velg prosjekt --</option>
            <?php while ($project = $projectsResult->fetch_assoc()): ?>
                <option value="<?= $project['project_id'] ?>" <?= $selectedProject == $project['project_id'] ? 'selected' : '' ?>>
                    <?= $project['project_number'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

   <!-- Ny dropdown for dokumenttype -->
<?php if ($selectedProject): ?>
    <form method="GET" action="projects.php">
        <input type="hidden" name="project_id" value="<?= $selectedProject ?>">
        <label for="document_type">Filtrer etter dokumenttype:</label>
        <select name="document_type" id="document_type" onchange="this.form.submit()">
            <option value="">-- Alle dokumenttyper --</option>
            <?php while ($docType = $docTypesResult->fetch_assoc()): ?>
                <option value="<?= $docType['document_type_code'] ?>" <?= $selectedDocType == $docType['document_type_code'] ? 'selected' : '' ?>>
                    <?= $docType['document_type_code'] ?> - <?= $docType['document_type_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>
<?php endif; ?>

<!-- Knapp for nytt dokument -->
<?php if ($selectedProject && $selectedDocType): ?>
<form method="POST" action="add_document.php">
    <input type="hidden" name="project_id" value="<?= $selectedProject ?>">
    <input type="hidden" name="document_type" value="<?= $selectedDocType ?>">
    <input type="hidden" name="document_link" value="">
    <label for="document_name">Dokumentnavn:</label>
    <input type="text" id="document_name" name="document_name" placeholder="Skriv inn dokumentnavn" required>
    <button type="submit">Opprett nytt dokument</button>
</form>
<?php endif; ?>  


<!-- Tabell med dokumenter -->
<?php if ($selectedProject): ?>
    <table border="1">
    <tr>
        <th>Prosjektnummer</th>
        <th>Dokumenttype</th>
        <th>Document ID</th>
        <th>Revisjonsnummer</th>
        <th>Navn på dokument</th>
        <th>Lenke</th>
        <th>Ny revisjon</th>
    </tr>
    <?php if ($documentsResult->num_rows > 0): ?>
        <?php while ($doc = $documentsResult->fetch_assoc()): ?>
            <tr>
                <td><?= $doc['project_number'] ?></td>
                <td><?= $doc['document_type'] ?></td>
                <td><?= str_pad($doc['document_id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= $doc['revision_number'] ?></td>
                <td><?= $doc['document_name'] ?></td>
                <td>
                    <?php if (!empty($doc['document_link'])): ?>
                        <!-- Hvis lenken allerede finnes -->
                        <a href="<?= htmlspecialchars($doc['document_link']) ?>" target="_blank">Åpne lenke</a>
                        <button onclick="editLink(this)">Endre lenke</button>
                        <form method="POST" action="save_link.php" style="display: none;">
                            <input type="hidden" name="document_id" value="<?= $doc['document_id'] ?>">
                            <input type="hidden" name="project_id" value="<?= $selectedProject ?>">
                            <input type="hidden" name="document_type" value="<?= $doc['document_type'] ?>">
                            <input type="url" name="document_link" value="<?= htmlspecialchars($doc['document_link']) ?>" placeholder="Oppdater lenke" required>
                            <button type="submit">Lagre lenke</button>
                        </form>
                    <?php else: ?>
                        <!-- Hvis lenken ikke finnes -->
                        <button onclick="addLink(this)">Add Link</button>
                        <form method="POST" action="save_link.php" style="display: none;">
                            <input type="hidden" name="document_id" value="<?= $doc['document_id'] ?>">
                            <input type="hidden" name="project_id" value="<?= $selectedProject ?>">
                            <input type="hidden" name="document_type" value="<?= $doc['document_type'] ?>">
                            <input type="url" name="document_link" placeholder="Legg til lenke" required>
                            <button type="submit">Lagre lenke</button>
                        </form>
                    <?php endif; ?>
                </td>
                <td>
                <!-- Ny revisjon-knappen -->
                <form method="POST" action="save_revision.php" onsubmit="return confirmNewRevision();">
                        <input type="hidden" name="document_id" value="<?= $doc['document_id'] ?>">
                        <input type="hidden" name="project_id" value="<?= $selectedProject ?>">
                        <input type="hidden" name="document_type" value="<?= $doc['document_type'] ?>">
                        <input type="hidden" name="revision_number" value="<?= $doc['revision_number'] ?>">
                        <button type="submit">Ny revisjon</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">Ingen dokumenter funnet for dette prosjektet.</td>
        </tr>
    <?php endif; ?>
</table>
<?php endif; ?>







</body>
</html>







