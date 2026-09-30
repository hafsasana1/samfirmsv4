<?php
/**
 * Simple 404 Error Page (Fallback)
 * 
 * This is the Layer 2 fallback when the main error-404.php fails.
 * Uses inline CSS and no external dependencies to guarantee it works.
 */

$baseUrl = base_url();
$siteName = 'SamFirms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - <?= esc($siteName) ?></title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container { 
            text-align: center; 
            max-width: 600px;
            background: rgba(255,255,255,0.05);
            padding: 60px 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .error-code { 
            font-size: 120px; 
            font-weight: 700; 
            color: #FF6B35;
            margin-bottom: 20px;
            line-height: 1;
            text-shadow: 0 4px 20px rgba(255,107,53,0.3);
        }
        .error-title { 
            font-size: 32px; 
            margin-bottom: 20px;
            color: #fff;
            font-weight: 600;
        }
        .error-message { 
            font-size: 18px; 
            color: rgba(255,255,255,0.7);
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .error-button { 
            display: inline-block;
            background: #FF6B35;
            color: #fff;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255,107,53,0.3);
        }
        .error-button:hover { 
            background: #E65A28;
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255,107,53,0.4);
        }
        .error-details {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .error-suggestions {
            list-style: none;
            padding: 0;
            margin: 20px 0 0 0;
            text-align: left;
            display: inline-block;
        }
        .error-suggestions li {
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            margin-bottom: 10px;
            padding-left: 24px;
            position: relative;
        }
        .error-suggestions li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #FF6B35;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .error-code { font-size: 80px; }
            .error-title { font-size: 24px; }
            .error-message { font-size: 16px; }
            .error-container { padding: 40px 20px; }
            .error-suggestions {
                text-align: center;
                display: block;
            }
            .error-suggestions li {
                padding-left: 0;
            }
            .error-suggestions li:before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            Sorry, the page you're looking for doesn't exist or has been moved.
            <br>Please check the URL or return to the homepage.
        </p>
        <a href="<?= esc($baseUrl) ?>" class="error-button">Back to Homepage</a>
        
        <div class="error-details">
            <ul class="error-suggestions">
                <li>Check if the URL is spelled correctly</li>
                <li>The page may have been removed or renamed</li>
                <li>Try searching for what you need from the homepage</li>
            </ul>
        </div>
    </div>
</body>
</html>
