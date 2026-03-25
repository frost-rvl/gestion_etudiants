<?php
session_start();
if (!($_SESSION['user'] ?? false)) { 
    header("Location: ../index.php"); 
    exit; 
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("<div class='min-h-screen bg-slate-50 flex items-center justify-center p-4'><div class='bg-white p-8 rounded-2xl shadow-xl text-center'><h3 class='text-xl font-bold text-red-600 mb-4'>Aucun étudiant sélectionné</h3><a href='dashboard.php' class='px-6 py-2 bg-slate-900 text-white rounded-xl font-bold'>Retour</a></div></div>");
}

$rows = [];
if (($file = fopen("../data/etudiants.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($file)) !== FALSE) { $rows[] = $data; }
    fclose($file);
}

$current = null;
foreach ($rows as $etudiant) {
    if ($etudiant[0] == $id) { $current = $etudiant; break; }
}

if (!$current) {
    die("<div class='min-h-screen bg-slate-50 flex items-center justify-center p-4'><div class='bg-white p-8 rounded-2xl shadow-xl text-center'><h3 class='text-xl font-bold text-amber-600 mb-4'>Étudiant introuvable</h3><a href='dashboard.php' class='px-6 py-2 bg-slate-900 text-white rounded-xl font-bold'>Retour</a></div></div>");
}

// POST Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tel   = $_POST['tel']   ?? '';
    $email = $_POST['email'] ?? '';

    foreach ($rows as $index => $etudiant) {
        if ($etudiant[0] == $id) {
            $rows[$index][3] = $tel;
            $rows[$index][4] = $email;
            break;
        }
    }

    $file = fopen("../data/etudiants.csv", "w");
    foreach ($rows as $row) { fputcsv($file, $row); }
    fclose($file);

    header("Location: dashboard.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Étudiant - <?= htmlspecialchars($current[1]) ?></title>
    <link rel="stylesheet" href="../styles/output.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-slate-100 min-h-screen">
    
    <?php require_once(__DIR__ . "/components/navbar.php") ?>

    <div class="fixed top-0 right-0 -z-20 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="fixed bottom-0 left-0 -z-20 w-96 h-96 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-6">
                <a href="dashboard.php" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour à la liste
                </a>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Fiche Étudiant</h1>
                    <p class="text-slate-500 font-mono text-sm mt-1">ID: #<?= htmlspecialchars($current[0]) ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 text-center">
                        <div class="relative group inline-block">
                            <img id="currentPhoto" src="../photos/<?= htmlspecialchars($current[5]) ?>" 
                                 class="w-48 h-48 object-cover rounded-2xl shadow-md border-4 border-white ring-1 ring-slate-200 mb-4 mx-auto">
                            
                            <input type="file" id="photoInput" class="hidden" accept="image/*">
                        </div>
                        
                        <h2 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($current[2]) ?> <?= htmlspecialchars($current[1]) ?></h2>
                        <p class="text-sm text-slate-400 mb-6">Étudiant inscrit</p>

                        <div id="dropzone" class="border-2 border-dashed border-slate-100 rounded-xl p-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all group">
                            <svg class="w-6 h-6 mx-auto text-slate-300 group-hover:text-blue-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Changer la photo</p>
                        </div>
                        <div id="preview" class="mt-2 text-xs"></div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
                        <form method="post" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom (Lecture seule)</label>
                                    <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-500 cursor-not-allowed font-medium" value="<?= htmlspecialchars($current[1]) ?>" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Prénom (Lecture seule)</label>
                                    <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-500 cursor-not-allowed font-medium" value="<?= htmlspecialchars($current[2]) ?>" readonly>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Numéro de téléphone</label>
                                <input type="tel" name="tel" value="<?= htmlspecialchars($current[3]) ?>" 
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all" placeholder="+212 6 ...">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($current[4]) ?>" 
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all" placeholder="exemple@mail.com">
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                                <button type="submit" class="flex-1 bg-slate-900 text-white font-bold py-4 rounded-xl hover:bg-slate-800 shadow-lg shadow-slate-200 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Enregistrer les modifications
                                </button>
                                <a href="dashboard.php" class="sm:w-1/3 bg-white text-slate-600 border border-slate-200 font-bold py-4 rounded-xl hover:bg-slate-50 text-center transition-all">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="bg-red-50/50 border border-red-100 rounded-2xl p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-red-100 text-red-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-red-900">Zone de danger</h4>
                                <p class="text-xs text-red-700">La suppression de cet étudiant est irréversible.</p>
                            </div>
                        </div>
                        <a href="delete.php?id=<?= $id ?>" class="text-xs font-bold text-red-600 hover:underline">Supprimer le profil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dropzone = document.getElementById('dropzone');
        const photoInput = document.getElementById('photoInput');
        const preview = document.getElementById('preview');
        const currentPhoto = document.getElementById('currentPhoto');
        const studentId = "<?= htmlspecialchars($id) ?>";

        dropzone.addEventListener('click', () => photoInput.click());

        // Drag & Drop visual feedback
        ['dragover', 'mouseenter'].forEach(e => dropzone.addEventListener(e, () => dropzone.classList.add('border-blue-400', 'bg-blue-50/50')));
        ['dragleave', 'mouseleave'].forEach(e => dropzone.addEventListener(e, () => dropzone.classList.remove('border-blue-400', 'bg-blue-50/50')));

        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if(files.length) uploadPhoto(files[0]);
        });

        photoInput.addEventListener('change', () => {
            if (photoInput.files.length > 0) uploadPhoto(photoInput.files[0]);
        });

        function uploadPhoto(file) {
            if (!file.type.startsWith('image/')) return alert('Image invalide');
            
            const formData = new FormData();
            formData.append("photo", file);
            formData.append("id", studentId);

            preview.innerHTML = '<span class="text-blue-600 font-bold animate-pulse">Chargement...</span>';

            fetch("../utils/upload_photo.php", { method: "POST", body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentPhoto.src = "../photos/" + data.filename + "?t=" + Date.now();
                    preview.innerHTML = '<span class="text-green-600 font-bold">✓ Mise à jour réussie</span>';
                    setTimeout(() => preview.innerHTML = '', 3000);
                } else {
                    alert("Erreur: " + data.error);
                    preview.innerHTML = '';
                }
            })
            .catch(err => {
                alert("Erreur réseau");
                preview.innerHTML = '';
            });
        }
    </script>
</body>
</html>