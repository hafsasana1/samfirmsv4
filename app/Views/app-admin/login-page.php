<?php
$web     = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));
$webLogo = base_url() . 'resource/logo.png';
$favicon = base_url() . 'resource/favicon.ico';

if ($web && !empty($web->webLogo) && file_exists(RESOURCE_PATH . '' . $web->webLogo)) {
    $webLogo = base_url() . 'resource/' . $web->webLogo;
}
if ($web && !empty($web->favicon) && file_exists(RESOURCE_PATH . '' . $web->favicon)) {
    $favicon = base_url() . 'resource/' . $web->favicon;
}
$webLogo = $webLogo . '?v=' . time();
$favicon = $favicon . '?v=' . time();

$num1    = rand(0, 19);
$num2    = rand(0, 19);
$qanswer = $num1 + $num2;

session()->remove('seq-ans');
session()->set('seq-ans', $qanswer);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Login .::. <?= $web->webTitle ?? 'Admin' ?></title>
        <link rel="shortcut icon" href="<?= $favicon ?>">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        <meta name="theme-color" content="#0a0a0f">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/tailwind-output.css?v=<?= time() ?>">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            @keyframes neon-glow {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
            
            @keyframes gradient-shift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            .neon-bg {
                background: linear-gradient(-45deg, #0a0a0f, #1a0a2e, #16213e, #0f3460);
                background-size: 400% 400%;
                animation: gradient-shift 15s ease infinite;
            }
            
            .neon-orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.3;
                pointer-events: none;
            }
            
            .orb-1 {
                width: 500px;
                height: 500px;
                background: radial-gradient(circle, #00d4ff, transparent);
                top: -10%;
                left: -10%;
                animation: float 8s ease-in-out infinite;
            }
            
            .orb-2 {
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, #ff00ff, transparent);
                bottom: -10%;
                right: -10%;
                animation: float 10s ease-in-out infinite reverse;
            }
            
            .orb-3 {
                width: 300px;
                height: 300px;
                background: radial-gradient(circle, #00ff88, transparent);
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                animation: float 12s ease-in-out infinite;
            }
            
            .neon-card {
                background: rgba(15, 15, 30, 0.85);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.15);
                box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5),
                           0 0 40px rgba(0, 212, 255, 0.15),
                           inset 0 1px 0 rgba(255, 255, 255, 0.1);
            }
            
            .neon-input {
                background: rgba(20, 20, 35, 0.8);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: #fff;
                transition: all 0.3s ease;
                font-weight: 500;
            }
            
            .neon-input:focus {
                background: rgba(25, 25, 40, 0.9);
                border-color: #00d4ff;
                box-shadow: 0 0 20px rgba(0, 212, 255, 0.4),
                           inset 0 0 10px rgba(0, 212, 255, 0.15);
                outline: none;
            }
            
            .neon-input::placeholder {
                color: rgba(255, 255, 255, 0.4);
            }
            
            .neon-button {
                background: linear-gradient(135deg, #00d4ff, #0099cc);
                border: none;
                box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3),
                           inset 0 -1px 0 rgba(0, 0, 0, 0.2);
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            
            .neon-button:hover {
                background: linear-gradient(135deg, #00e5ff, #00b8e6);
                box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5),
                           inset 0 -1px 0 rgba(0, 0, 0, 0.2);
                transform: translateY(-2px);
            }
            
            .neon-button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.5s ease;
            }
            
            .neon-button:hover::before {
                left: 100%;
            }
            
            .neon-logo {
                filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.5))
                       drop-shadow(0 0 40px rgba(0, 212, 255, 0.3));
                animation: neon-glow 2s ease-in-out infinite;
            }
            
            .neon-text {
                color: #fff;
                text-shadow: 0 0 10px rgba(0, 212, 255, 0.5),
                            0 0 20px rgba(0, 212, 255, 0.3);
            }
            
            .neon-error {
                background: rgba(220, 38, 38, 0.1);
                border: 1px solid rgba(220, 38, 38, 0.3);
                backdrop-filter: blur(10px);
                animation: neon-glow 1.5s ease-in-out infinite;
            }
            
            .neon-label {
                color: rgba(255, 255, 255, 0.9);
                font-weight: 600;
                letter-spacing: 0.3px;
            }
            
            .security-box {
                background: rgba(0, 212, 255, 0.15);
                border: 2px solid rgba(0, 212, 255, 0.5);
                box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
            }
            
            .neon-link {
                color: #00d4ff;
                text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
                transition: all 0.3s ease;
            }
            
            .neon-link:hover {
                color: #00e5ff;
                text-shadow: 0 0 15px rgba(0, 212, 255, 0.8);
            }
        </style>
    </head>
    <body class="neon-bg min-h-screen flex items-center justify-center p-4 font-sans antialiased relative overflow-hidden">
        <!-- Animated Neon Orbs -->
        <div class="neon-orb orb-1"></div>
        <div class="neon-orb orb-2"></div>
        <div class="neon-orb orb-3"></div>
        
        <!-- Login Card -->
        <div class="w-full max-w-md relative z-10">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="<?= base_url() ?>" class="inline-block">
                    <img src="<?= $webLogo ?>" alt="<?= $web->webTitle ?? '' ?>" class="neon-logo h-16 w-auto mx-auto mb-4">
                </a>
                <h1 class="text-3xl font-bold neon-text mb-2">Welcome Back</h1>
                <p class="text-gray-200 text-base">Sign in to your account</p>
            </div>

            <!-- Login Form Card -->
            <div class="neon-card rounded-2xl p-8">
                <!-- Error Message -->
                <?php if (session()->get('error')): ?>
                    <div class="mb-6 p-4 neon-error rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                            <p class="text-sm text-red-200"><?= session()->get('error') ?></p>
                        </div>
                    </div>
                    <?php session()->remove('error'); ?>
                <?php endif; ?>

                <!-- Form -->
                <form method="post" action="<?= base_url('app-admin/login') ?>" class="space-y-6">
                    <?= csrf_field() ?>
                    <input type="hidden" name="qanswer" value="">

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm neon-label mb-2">
                            <i class="fas fa-user text-cyan-400 mr-2"></i>Username
                        </label>
                        <input id="username" 
                               type="text" 
                               name="username" 
                               required
                               spellcheck="false" 
                               autocomplete="off"
                               value="<?= get_cookie('loginUsername') ?? '' ?>"
                               class="neon-input w-full px-4 py-3 rounded-lg"
                               placeholder="Enter your username">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm neon-label mb-2">
                            <i class="fas fa-lock text-cyan-400 mr-2"></i>Password
                        </label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required
                               minlength="6"
                               class="neon-input w-full px-4 py-3 rounded-lg"
                               placeholder="Enter your password">
                    </div>

                    <!-- Security Question -->
                    <div>
                        <label class="block text-sm neon-label mb-2">
                            <i class="fas fa-shield-alt text-cyan-400 mr-2"></i>Security Question
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 px-4 py-3 security-box rounded-lg font-bold text-white text-lg">
                                <span><?= $num1 ?></span>
                                <i class="fas fa-plus text-sm text-cyan-400"></i>
                                <span><?= $num2 ?></span>
                                <span class="text-cyan-400">=</span>
                            </div>
                            <input type="text" 
                                   name="sanswer" 
                                   required
                                   class="neon-input flex-1 px-4 py-3 rounded-lg text-center font-bold text-lg"
                                   placeholder="?">
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" 
                                   name="remember_me" 
                                   value="1"
                                   <?= get_cookie('rememberme') ? 'checked' : '' ?>
                                   class="w-5 h-5 rounded border-2 border-cyan-400 bg-transparent text-cyan-400 focus:ring-2 focus:ring-cyan-400 focus:ring-offset-0 cursor-pointer transition-all">
                            <span class="text-sm text-gray-300 group-hover:text-cyan-400 transition-colors">
                                <i class="fas fa-clock text-cyan-400 mr-1"></i>
                                Keep me signed in for 30 days
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            name="submit" 
                            value="1"
                            class="neon-button w-full text-white font-semibold py-3 px-4 rounded-lg flex items-center justify-center gap-2 group">
                        <span>Sign In</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-6 pt-6 border-t border-gray-700 border-opacity-50 text-center">
                    <a href="<?= base_url() ?>" 
                       class="neon-link text-sm inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Back to Website
                    </a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center mt-8">
                <p class="text-sm text-gray-400">
                    &copy; <?= date('Y') ?> <?= $web->webTitle ?? '' ?>. All rights reserved.
                </p>
            </div>
        </div>

        <script src="<?= base_url() ?>assets/js/vendor.min.js"></script>
    </body>
</html>
