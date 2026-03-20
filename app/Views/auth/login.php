<div x-data="{ showPassword: false }"
     class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl overflow-hidden">

    <!-- Card header / branding -->
    <div class="px-8 pt-8 pb-6 text-center bg-gradient-to-b from-primary-500/20 to-transparent">
        <!-- Logo mark -->
        <?php
        $loginLogo    = '';
        $loginAppName = 'AquaCRM';
        try {
            $db = \App\Core\Database::getInstance();
            $logoRow = $db->fetch(
                "SELECT `value` FROM `settings` WHERE `key` = 'app_logo' LIMIT 1"
            );
            if ($logoRow && !empty($logoRow['value'])) {
                $loginLogo = $logoRow['value'];
            }
            $nameRow = $db->fetch(
                "SELECT `value` FROM `settings` WHERE `key` = 'app_name' LIMIT 1"
            );
            if ($nameRow && !empty($nameRow['value'])) {
                $loginAppName = $nameRow['value'];
            }
        } catch (\Throwable) {}
        ?>
        <?php if ($loginLogo): ?>
        <div class="inline-flex items-center justify-center mb-4">
            <img src="<?= htmlspecialchars('/assets/uploads/' . $loginLogo, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Logo"
                 class="h-16 w-auto object-contain">
        </div>
        <?php else: ?>
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary-500 shadow-lg shadow-primary-500/40 mb-4">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <?php endif; ?>
        <h1 class="text-3xl font-extrabold text-white tracking-tight"><?= htmlspecialchars($loginAppName, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="mt-1 text-sm text-blue-200">Domestic Water Purification Solutions</p>
    </div>

    <div class="px-8 pb-8">
        <h2 class="text-center text-lg font-semibold text-white mb-6">Sign in to your account</h2>

        <!-- Flash messages -->
        <?php if (!empty($error)): ?>
        <div x-data="{ show: true }" x-show="show"
             class="flex items-center gap-2 mb-4 p-3 bg-red-500/20 border border-red-500/40 rounded-lg text-red-200 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
            <button @click="show = false" class="ml-auto text-red-300 hover:text-white">&times;</button>
        </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
        <div x-data="{ show: true }" x-show="show"
             class="flex items-center gap-2 mb-4 p-3 bg-green-500/20 border border-green-500/40 rounded-lg text-green-200 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
            <button @click="show = false" class="ml-auto text-green-300 hover:text-white">&times;</button>
        </div>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" action="/login" novalidate class="space-y-5">
            <?= $csrfField ?? '' ?>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-blue-100 mb-1.5">
                    Username
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <input type="text"
                           id="username"
                           name="username"
                           value="<?= htmlspecialchars(\App\Core\Session::getFlash('old_username', ''), ENT_QUOTES, 'UTF-8') ?>"
                           required
                           autocomplete="username"
                           placeholder="Enter your username"
                           class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl
                                  text-white placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent
                                  transition-colors">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-blue-100 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input :type="showPassword ? 'text' : 'password'"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full pl-10 pr-12 py-3 bg-white/10 border border-white/20 rounded-xl
                                  text-white placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent
                                  transition-colors">
                    <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white transition-colors">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" value="1"
                           class="w-4 h-4 rounded border-white/30 bg-white/10 text-primary-500
                                  focus:ring-primary-500 focus:ring-offset-0">
                    <span class="text-sm text-blue-100">Remember me</span>
                </label>
                <a href="/forgot-password" class="text-sm text-primary-400 hover:text-primary-300 transition-colors">
                    Forgot password?
                </a>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-primary-500 hover:bg-primary-600
                           text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30
                           transition-all duration-200 hover:scale-[1.02] active:scale-100 focus:outline-none
                           focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Sign In
            </button>
        </form>

        <!-- Footer note -->
        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($loginAppName, ENT_QUOTES, 'UTF-8') ?> &mdash; All rights reserved
        </p>
    </div>
</div>
