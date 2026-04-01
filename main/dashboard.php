<?php
session_start();
if (!($_SESSION['user'] ?? false)) { 
    header("Location: ../index.php"); 
    exit; 
}

$message = "";
$csvPath = "../data/etudiants.csv";
$usersPath = "../data/users.csv";

// --- LOGIC SECTION ---

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=etudiants.csv');
    readfile($csvPath);
    exit;
}

// Global POST Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Multiple Delete
    if (isset($_POST['delete_selected'])) {
        $idsToDelete = $_POST['selected'] ?? [];
        if (!empty($idsToDelete)) {
            $rows = [];
            if (($file = fopen($csvPath, "r")) !== false) {
                while (($data = fgetcsv($file)) !== false) {
                    if (!in_array($data[0], $idsToDelete)) {
                        $rows[] = $data;
                    } else {
                        $photo = $data[5] ?? '';
                        if ($photo && $photo !== 'default.jpg' && file_exists("../photos/" . $photo)) {
                            unlink("../photos/" . $photo);
                        }
                    }
                }
                fclose($file);
            }
            $file = fopen($csvPath, "w");
            foreach ($rows as $row) fputcsv($file, $row);
            fclose($file);
            $message = "Suppression effectuee avec succes";
        }
    }

    // User Panel Actions (Add/Delete/Password)
    if (isset($_POST['add_user'])) {
        $f = fopen($usersPath, "a");
        fputcsv($f, [$_POST['new_username'], password_hash($_POST['new_password'], PASSWORD_DEFAULT), $_POST['new_role']]);
        fclose($f);
        $message = "Utilisateur ajoute";
    }

    if (isset($_POST['delete_user'])) {
        $username = $_POST['delete_username'];
        $rows = [];
        if (($file = fopen($usersPath, "r")) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                if ($data[0] !== $username) $rows[] = $data;
            }
            fclose($file);
        }
        $file = fopen($usersPath, "w");
        foreach ($rows as $row) fputcsv($file, $row);
        fclose($file);
        $message = "Utilisateur supprime";
    }

    // Importer
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        $csvTmp = $_FILES['csv_file']['tmp_name'];
        $rows = [];
        if (($handle = fopen($csvTmp, "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) $rows[] = $data;
            fclose($handle);
        }
        $existing = [];
        if (($file = fopen($csvPath, "r")) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                $existing[] = strtolower(($data[1] ?? '') . "_" . ($data[2] ?? ''));
            }
            fclose($file);
        }
        $file = fopen($csvPath, "a");
        foreach ($rows as $row) {
            $nom    = strtolower($row[1] ?? '');
            $prenom = strtolower($row[2] ?? '');
            $key    = $nom . "_" . $prenom;
            if (!in_array($key, $existing)) {
                fputcsv($file, $row);
                $existing[] = $key;
            } else {
                $message .= "Doublon ignoré : $nom $prenom<br>";
            }
        }
        fclose($file);
        foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
            $filename = $_FILES['photos']['name'][$index];
            if ($tmpName) move_uploaded_file($tmpName, "../photos/" . basename($filename));
        }
        if (!$message) $message = "Import terminé avec succès";
    }
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Gestion Etudiants</title>
    <link rel="stylesheet" href="../styles/output.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-slate-100 min-h-screen font-sans antialiased">
    
    <?php require_once(__DIR__ . "/components/navbar.php") ?>
    <?php require_once(__DIR__ . "/components/importModal.php") ?>

    <div class="fixed top-0 right-0 -z-20 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="fixed bottom-0 left-0 -z-20 w-96 h-96 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == "admin") { ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <a href="add.php" class="bg-slate-900 text-white px-4 py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-slate-800 shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajouter
                </a>
                
                <a href="dashboard.php?export=csv" class="bg-white text-slate-700 border border-slate-200 px-4 py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-slate-50 shadow-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Exporter
                </a>

                <button onclick="toggleModal('importModal', true)" class="bg-white text-slate-700 border border-slate-200 px-4 py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-slate-50 shadow-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    Importer
                </button>

                <button type="submit" form="deleteForm" class="bg-red-50 text-red-600 border border-red-100 px-4 py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-red-100 shadow-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Supprimer
                </button>
            </div>
        <?php }; ?>

        <?php if ($message): ?>
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-xl mb-6 font-bold animate-slideUp">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden">
            <form method="post" id="deleteForm">
                <input type="hidden" name="delete_selected" value="1">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == "admin") { ?>
                                    <th class="p-4"><input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 text-blue-600"></th>
                                <?php }; ?>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Etudiant</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Contact</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            if (file_exists($csvPath) && ($file = fopen($csvPath, "r")) !== false) {
                                while (($data = fgetcsv($file)) !== false) {
                                    $id = $data[0]; $nom = $data[1]; $prenom = $data[2]; $tel = $data[3]; $email = $data[4]; $photo = (!empty($data[5])) ? $data[5] : 'default.jpg';
                                    ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == "admin") { ?>
                                            <td class="p-4"><input type="checkbox" name="selected[]" value="<?= $id ?>" class="w-4 h-4 rounded border-slate-300"></td>
                                        <?php }; ?>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <img src="../photos/<?= htmlspecialchars($photo) ?>" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                                <div>
                                                    <div class="font-bold text-slate-900"><?= htmlspecialchars($nom) ?></div>
                                                    <div class="text-xs text-slate-500 uppercase"><?= htmlspecialchars($prenom) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-sm">
                                            <div class="font-medium text-slate-700"><?= htmlspecialchars($tel) ?></div>
                                            <div class="text-xs text-slate-400"><?= htmlspecialchars($email) ?></div>
                                        </td>
                                        <td class="p-4 text-center">
                                            <a href="detail.php?id=<?= $id ?>" class="p-2 inline-block text-slate-400 hover:text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                                        </td>
                                    </tr>
                                    <?php
                                } fclose($file);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Select All Checkboxes
        const selectAll = document.getElementById('selectAll');
        if(selectAll) {
            selectAll.addEventListener('click', function() {
                document.querySelectorAll('input[name="selected[]"]').forEach(cb => cb.checked = this.checked);
            });
        }
    </script>
</body>
</html>