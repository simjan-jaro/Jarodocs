<?php
include 'db_connect.php'; // Kobler til databasen

// Hent tilgjengelige dokumenttyper for dropdown-menyen
$docTypesQuery = "SELECT document_type_code, document_type_name FROM DocumentTypes WHERE document_type_code IN ('KA', 'XD', 'PF')";
$docTypesResult = $conn->query($docTypesQuery);

if (!$docTypesResult) {
    die("Feil i spørringen for dokumenttyper: " . $conn->error);
}

// Hent dokumenter fra iso9001_documents-tabellen
$selectedDocType = isset($_GET['document_type']) ? $_GET['document_type'] : null;

$documentsQuery = "SELECT iso_code, document_type, document_id, revision_number, document_name, document_link FROM iso9001_documents";
if ($selectedDocType) {
    $documentsQuery .= " WHERE document_type = '$selectedDocType'";
}
$documentsQuery .= " ORDER BY document_name";

$documentsResult = $conn->query($documentsQuery);

if (!$documentsResult) {
    die("Feil i spørringen for dokumenter: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISO 9001 Dokumenter</title>
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
    background-color: #ffff99; /* Lys blå farge */
}

.new-entry {
    background-color: #ffeb3b; /* Lys grønn farge */
    transition: background-color 0.3s ease; /* For en myk overgang */
}

    </style>
    <script>
        function addLink(button) {
            const form = button.nextElementSibling; // Hent skjemaet rett etter knappen
            form.style.display = "block"; // Vis skjemaet
            button.style.display = "none"; // Skjul "Add Link"-knappen
        }

        function editLink(button) {
            const linkElement = button.previousElementSibling; // Hent lenken
            const form = button.nextElementSibling; // Hent skjemaet rett etter knappen
            linkElement.style.display = "none"; // Skjul lenken
            button.style.display = "none"; // Skjul "Endre lenke"-knappen
            form.style.display = "block"; // Vis skjemaet
        }
    </script>
</head>
<body>
    <h1>ISO 9001 Dokumenter</h1>
    <form action="index.php" method="get">
    <button type="submit">Tilbake til forsiden</button>
</form>

    <!-- Filter for dokumenttyper -->
    <form method="GET" action="">
    <label for="document_type">Filtrer etter dokumenttype:</label>
    <select name="document_type" id="document_type" onchange="this.form.submit()">
        <option value="">-- Alle dokumenttyper --</option>
        <?php while ($docType = $docTypesResult->fetch_assoc()): ?>
            <option value="<?= $docType['document_type_code'] ?>" <?= $selectedDocType == $docType['document_type_code'] ? 'selected' : '' ?>>
                <?= $docType['document_type_code'] . ' - ' . $docType['document_type_name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>



    <!-- Dokumenttabell -->
    <table>
        <thead>
            <tr>
                <th>ISO</th>
                <th>Dokumenttype</th>
                <th>Document ID</th>
                <th>Revisjonsnummer</th>
                <th>Navn på dokument</th>
                <th>Lenke</th>
                <th>Ny revisjon</th>
            </tr>
        </thead>
        <tbody>
    <?php if ($documentsResult->num_rows > 0): ?>
        <?php while ($doc = $documentsResult->fetch_assoc()): ?>
            <tr>
                <td><?= $doc['iso_code'] ?></td>
                <td><?= $doc['document_type'] ?></td>
                <td><?= str_pad($doc['document_id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= $doc['revision_number'] ?></td>
                <td><?= $doc['document_name'] ?></td>
                <td>
                   <?php if (!empty($doc['document_link'])): ?>
                   <!-- Hvis lenken allerede finnes -->
                   <a href="<?= htmlspecialchars($doc['document_link']) ?>" target="_blank">Åpne lenke</a>
                   <button onclick="editLink(this)">Endre lenke</button>
                   <form method="POST" action="iso_save_link.php" style="display: none;">
                       <input type="hidden" name="document_id" value="<?= $doc['document_id'] ?>">
                       <input type="hidden" name="iso_code" value="<?= $doc['iso_code'] ?>">
                       <input type="hidden" name="document_type" value="<?= $doc['document_type'] ?>">
                       <input type="url" name="document_link" value="<?= htmlspecialchars($doc['document_link']) ?>" placeholder="Oppdater lenke" required>
                       <button type="submit">Lagre lenke</button>
                   </form>
               <?php else: ?>
                  <!-- Hvis lenken ikke finnes -->
                  <button onclick="addLink(this)">Add Link</button>
                  <form method="POST" action="iso_save_link.php" style="display: none;">
                      <input type="hidden" name="document_id" value="<?= $doc['document_id'] ?>">
                      <input type="hidden" name="iso_code" value="<?= $doc['iso_code'] ?>">
                      <input type="hidden" name="document_type" value="<?= $doc['document_type'] ?>">
                      <input type="url" name="document_link" placeholder="Legg til lenke" required>
                      <button type="submit">Lagre lenke</button>
                  </form>
             <?php endif; ?>
         </td>

                <td>
                   <form method="POST" action="iso_save_revision.php" onsubmit="return confirm('Er du sikker på at du vil opprette en ny revisjon?');">
                      <input type="hidden" name="document_id" value="<?= $doc['document_id'] ?>">
                      <input type="hidden" name="iso_code" value="<?= $doc['iso_code'] ?>">
                      <input type="hidden" name="document_type" value="<?= $doc['document_type'] ?>">
                      <input type="hidden" name="revision_number" value="<?= $doc['revision_number'] ?>">
                      <button type="submit" class="btn">Ny revisjon</button>
                  </form>
              </td>

            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">Ingen dokumenter funnet.</td>
        </tr>
    <?php endif; ?>
</tbody>

    </table>
</body>
</html>







