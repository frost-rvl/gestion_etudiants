<nav class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <div class="flex items-center gap-2 sm:gap-3">
                <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                    Gestion <span class="text-blue-600">Étudiants</span>
                </h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-6"> 
                
                <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl">
                    <div class="w-2 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-xs sm:text-sm font-medium text-slate-600">
                        <span class="">Connecté :</span>
                        <span class="text-slate-900 font-bold ml-1">
                            <?= htmlspecialchars($_SESSION['user']) ?>
                        </span>
                    </span>
                </div>

                <div class="hidden xs:block h-6 w-px bg-slate-200"></div>

                <div class="flex items-center gap-2">
                    <a href="../auth/logout.php"
                       class="text-xs sm:text-sm font-bold text-red-600 hover:text-red-700 transition-colors px-2 py-2 rounded-lg hover:bg-red-50">
                        Déconnexion
                    </a>

                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == "admin") { ?>
                        <a href="users.php" class="p-2.5 text-slate-500 bg-white border border-slate-200 hover:text-slate-900 hover:bg-slate-50 rounded-xl transition-all shadow-sm flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </a>
                    <?php }; ?>
                </div>
            </div>
        </div>
    </div>
</nav>