<?php
session_start();
if (!isset($_SESSION['user'])) { 
    header("Location: ../index.php"); 
    exit; 
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = uniqid();
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $photo = "";
    
    // Create photos directory if it doesn't exist (at project root)
    if (!is_dir("../photos")) {
        mkdir("../photos", 0755, true);
    }
    
    if (!empty($_FILES['photo']['name'])) {
        $photo = $id . "_" . basename($_FILES['photo']['name']);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], "../photos/" . $photo)) {
            $photo = ""; // If upload fails, continue without photo
        }
    }
    $file = fopen("../data/etudiants.csv", "a");
    fputcsv($file, [$id, $nom, $prenom, $tel, $email, $photo]);
    fclose($file);
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un étudiant - Gestion Étudiants</title>
    <link rel="stylesheet" href="../styles/output.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-slate-100 min-h-screen">
    
    <?php require_once(__DIR__ . "/components/navbar.php") ?>

    <div class="fixed top-0 right-0 -z-10 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="fixed bottom-0 left-0 -z-10 w-96 h-96 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        <div class="max-w-2xl mx-auto">
            
            <div class="mb-6">
                <a href="dashboard.php" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour au tableau de bord
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Nouvel Étudiant</h1>
                <p class="text-slate-500 mt-1">Créez une nouvelle fiche d'étudiant dans la base de données.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-8 mb-6">
                <form method="post" enctype="multipart/form-data" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nom <span class="text-red-500">*</span></label>
                            <input type="text" name="nom" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-300" placeholder="Ex: DOE" required autofocus>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                            <input type="text" name="prenom" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-300" placeholder="Ex: John" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Téléphone</label>
                            <input type="tel" name="tel" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-300" placeholder="+212 6..." >
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-300" placeholder="john@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Photo de profil</label>
                        <div class="relative group">
                            <input type="file" name="photo" id="photoInput" class="hidden" accept="image/*" onchange="previewPhoto()">
                            <label for="photoInput" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-2xl p-8 cursor-pointer group-hover:border-blue-400 group-hover:bg-blue-50/30 transition-all">
                                <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-700">Cliquez pour importer</p>
                                <p class="text-xs text-slate-400 mt-1">PNG, JPG jusqu'à 10MB</p>
                            </label>
                            <div id="photoPreview" class="mt-4 flex justify-center"></div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" class="flex-1 bg-slate-900 text-white font-bold py-4 rounded-xl hover:bg-slate-800 shadow-lg shadow-slate-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Enregistrer l'étudiant
                        </button>
                        <a href="dashboard.php" class="sm:w-1/3 bg-white text-slate-600 border border-slate-200 font-bold py-4 rounded-xl hover:bg-slate-50 text-center transition-all">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex items-start gap-3 p-4 bg-blue-100/50 border border-blue-100 rounded-xl">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-xs text-blue-800 font-medium leading-relaxed">
                    <strong>Conseil :</strong> Utilisez une photo carrée et bien éclairée pour un meilleur rendu dans le tableau de bord.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Keep your existing JavaScript functions exactly as they are
        function previewPhoto() {
            const input = document.getElementById('photoInput');
            const preview = document.getElementById('photoPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="relative inline-block group">
                            <img src="${e.target.result}" class="w-24 h-24 object-cover rounded-2xl shadow-lg border-2 border-white ring-4 ring-blue-50">
                            <button type="button" onclick="removePhoto()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function removePhoto() {
            document.getElementById('photoInput').value = '';
            document.getElementById('photoPreview').innerHTML = '';
        }
    </script>
</body>
</html>