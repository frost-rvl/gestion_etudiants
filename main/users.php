<?php
session_start();
if (!isset($_SESSION['user'])) { 
    header("Location: ../index.php"); 
    exit; 
}

$message = "";
$usersPath = "../data/users.csv";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Add User
    if (isset($_POST['add_user'])) {
        $username = $_POST['new_username'];
        $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $role = $_POST['new_role'];
        $file = fopen($usersPath, "a");
        fputcsv($file, [$username, $password, $role]);
        fclose($file);
        $message = "Utilisateur créé avec succès";
    }

    // 2. Toggle Role
    if (isset($_POST['toggle_role'])) {
        $target = $_POST['target_username'];
        $rows = [];
        if (($file = fopen($usersPath, "r")) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                if ($data[0] === $target) {
                    $data[2] = ($data[2] === 'admin') ? 'student' : 'admin';
                }
                $rows[] = $data;
            }
            fclose($file);
        }
        $file = fopen($usersPath, "w");
        foreach ($rows as $row) fputcsv($file, $row);
        fclose($file);
        $message = "Rôle mis à jour";
    }

    // 3. Update Password
    if (isset($_POST['update_password'])) {
        $target = $_POST['target_username'];
        $newPass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $rows = [];
        if (($file = fopen($usersPath, "r")) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                if ($data[0] === $target) $data[1] = $newPass;
                $rows[] = $data;
            }
            fclose($file);
        }
        $file = fopen($usersPath, "w");
        foreach ($rows as $row) fputcsv($file, $row);
        fclose($file);
        $message = "Mot de passe modifié";
    }

    // 4. Delete User
    if (isset($_POST['delete_user'])) {
        $target = $_POST['delete_username'];
        $rows = [];
        if (($file = fopen($usersPath, "r")) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                if ($data[0] !== $target) $rows[] = $data;
            }
            fclose($file);
        }
        $file = fopen($usersPath, "w");
        foreach ($rows as $row) fputcsv($file, $row);
        fclose($file);
        $message = "Compte supprimé";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - Administration</title>
    <link rel="stylesheet" href="../styles/output.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-slate-100 min-h-screen">
    
    <?php require_once(__DIR__ . "/components/navbar.php") ?>

    <div class="fixed top-0 right-0 -z-10 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="fixed bottom-0 left-0 -z-10 w-96 h-96 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-6">
                <a href="dashboard.php" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour au tableau de bord
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Administration Système</h1>
                <p class="text-slate-500 mt-1">Gérez les comptes utilisateurs et les niveaux d'accès.</p>
            </div>

            <?php if ($message): ?>
                <div class="bg-white border-l-4 border-slate-900 text-slate-900 p-4 rounded-xl shadow-sm mb-6 font-bold text-sm animate-slideUp">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-8 mb-10">
                <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Nouveau compte
                </h2>
                <form method="post" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Identifiant</label>
                        <input type="text" name="new_username" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="Nom d'utilisateur" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Mot de passe</label>
                        <input type="password" name="new_password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="••••••••" required>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Rôle</label>
                        <select name="new_role" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 outline-none transition-all">
                            <option value="student">Étudiant</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="bg-slate-900 text-white font-bold py-3.5 rounded-xl hover:bg-slate-800 transition-all">
                        Ajouter
                    </button>
                </form>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400">Utilisateur</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400">Accès</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        if (file_exists($usersPath) && ($file = fopen($usersPath, "r")) !== false) {
                            while (($data = fgetcsv($file)) !== false) {
                                $u = htmlspecialchars($data[0]);
                                $r = htmlspecialchars($data[2]);
                                ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-700"><?= $u ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border <?= $r === 'admin' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-500 border-slate-200' ?>">
                                            <?= $r ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end items-center gap-4">
                                            <form method="post" class="inline">
                                                <input type="hidden" name="target_username" value="<?= $u ?>">
                                                <button type="submit" name="toggle_role" class="text-xs font-bold text-blue-600 hover:underline">Changer Rôle</button>
                                            </form>
                                            <button onclick="promptPass('<?= $u ?>')" class="text-xs font-bold text-slate-400 hover:text-slate-900">Password</button>
                                            <form method="post" class="inline" onsubmit="return confirm('Supprimer <?= $u ?> ?')">
                                                <input type="hidden" name="delete_username" value="<?= $u ?>">
                                                <button type="submit" name="delete_user" class="text-xs font-bold text-red-400 hover:text-red-600">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            fclose($file);
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form id="passForm" method="post" class="hidden">
        <input type="hidden" name="target_username" id="passTarget">
        <input type="hidden" name="new_password" id="passValue">
        <input type="hidden" name="update_password" value="1">
    </form>

    <script>
        function promptPass(user) {
            const p = prompt("Nouveau mot de passe pour " + user + " :");
            if (p && p.length > 3) {
                document.getElementById('passTarget').value = user;
                document.getElementById('passValue').value = p;
                document.getElementById('passForm').submit();
            }
        }
    </script>
</body>
</html>