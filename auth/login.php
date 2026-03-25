<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: ../main/dashboard.php");
    exit;
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password']) ?? '';
    if (($handle = fopen("../data/users.csv", "r")) !== false) {
        while (($data = fgetcsv($handle)) !== false) {
            if ($data[0] === $username && password_verify($password, trim($data[1]))) {
                $_SESSION['user'] = $username;
                $_SESSION['role'] = $data[2];
                fclose($handle);
                header("Location: ../main/dashboard.php");
                exit;
            }
        }
        fclose($handle);
    }
    $error = "Identifiants incorrects.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion Étudiants</title>
    <link rel="stylesheet" href="../styles/output.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Gestion Étudiants</h1>
            <p class="text-slate-600">Connectez-vous à votre compte</p>
        </div>

        <!-- Card -->
        <div class="card">
            <form method="post" class="space-y-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger flex items-center gap-2 animate-slideUp">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="form-label">Nom d'utilisateur</label>
                    <input type="text" name="username" class="form-control" placeholder="john" required autofocus>
                </div>

                <div>
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="********" required>
                </div>

                <button type="submit" class="btn-primary-lg w-full flex items-center justify-center gap-2 mt-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        m<line x1="15" x2="3" y1="12" y2="12"></line>
                    </svg>
                    Se connecter
                </button>
            </form>
        </div>

        <div class="absolute top-0 right-0 -z-10 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 -z-10 w-96 h-96 bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    </div>
</body>
</html>