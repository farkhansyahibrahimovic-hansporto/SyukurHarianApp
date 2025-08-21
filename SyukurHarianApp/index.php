
<?php
session_start();

// Project by FarkhansyahOffi
// Database Configuration
$host = 'localhost';
$dbname = 'gratitude_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Create database if not exists
    try {
        $pdo_create = new PDO("mysql:host=$host;charset=utf8", $username, $password);
        $pdo_create->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create table
        $sql = "CREATE TABLE IF NOT EXISTS reflections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            gratitude_text TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

// Motivational quotes in Indonesian
$quotes = [
    "Bersyukur adalah kunci kebahagiaan yang sesungguhnya. 🌟",
    "Hal-hal kecil yang kita syukuri hari ini akan menjadi kenangan indah di masa depan. 🌸",
    "Rasa syukur mengubah apa yang kita miliki menjadi lebih dari cukup. 💫",
    "Setiap hari adalah kesempatan baru untuk menemukan kebaikan dalam hidup. 🌅",
    "Ketika kita bersyukur, hidup terasa lebih ringan dan penuh makna. ✨",
    "Syukur adalah magnet kebahagiaan yang paling kuat. 🧲",
    "Hati yang bersyukur selalu menemukan alasan untuk tersenyum. 😊",
    "Bersyukur hari ini adalah investasi terbaik untuk kebahagiaan esok. 🌱",
    "Dalam kesederhanaan, kita menemukan kekayaan yang sesungguhnya. 🍃",
    "Syukur membuat hidup yang biasa menjadi luar biasa. 🌈"
];

// Handle form submission
if ($_POST && isset($_POST['gratitude'])) {
    $gratitude = trim($_POST['gratitude']);
    if (!empty($gratitude)) {
        $stmt = $pdo->prepare("INSERT INTO reflections (gratitude_text) VALUES (?)");
        $stmt->execute([$gratitude]);
        $random_quote = $quotes[array_rand($quotes)];
        $_SESSION['success'] = true;
        $_SESSION['quote'] = $random_quote;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Get previous reflections
$stmt = $pdo->query("SELECT * FROM reflections ORDER BY created_at DESC LIMIT 8");
$reflections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = isset($_SESSION['success']) ? $_SESSION['success'] : false;
$quote = isset($_SESSION['quote']) ? $_SESSION['quote'] : '';
unset($_SESSION['success'], $_SESSION['quote']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syukur Harian - Aplikasi Refleksi</title>
  <link rel="icon" type="image/x-icon" href="/RefleksiHarian/favicon.ico">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0F2027 0%, #203A43 50%, #2C5364 100%);
            min-height: 100vh;
            color: #333;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Elements */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="%2303a9f4"></path><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="%2303a9f4"></path><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="%2303a9f4"></path></svg>') repeat-x;
            animation: wave 10s linear infinite;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: float 8s infinite ease-in-out;
        }

        .particle:nth-child(1) { width: 60px; height: 60px; top: 10%; left: 5%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 40px; height: 40px; top: 20%; right: 10%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 80px; height: 80px; top: 70%; left: 15%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 50px; height: 50px; top: 40%; right: 20%; animation-delay: 6s; }
        .particle:nth-child(5) { width: 30px; height: 30px; top: 80%; right: 5%; animation-delay: 1s; }
        .particle:nth-child(6) { width: 70px; height: 70px; top: 60%; left: 80%; animation-delay: 3s; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        /* Header with enhanced animation */
        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(3, 169, 244, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse-glow 3s ease-in-out infinite;
        }

        .header h1 {
            color: #ffffff;
            font-size: 3.5rem;
            margin-bottom: 15px;
            text-shadow: 0 0 20px rgba(3, 169, 244, 0.5);
            font-weight: 700;
            letter-spacing: -1px;
            position: relative;
            z-index: 2;
            animation: textGlow 2s ease-in-out infinite alternate;
        }

        .header .subtitle {
            color: #B8E6FF;
            font-size: 1.3rem;
            margin-bottom: 20px;
            opacity: 0.9;
            font-weight: 300;
            position: relative;
            z-index: 2;
        }

        .header .icon-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            animation: iconFloat 3s ease-in-out infinite;
        }

        .header .icon-item {
            font-size: 2rem;
            animation: bounce 2s ease-in-out infinite;
        }

        .header .icon-item:nth-child(1) { animation-delay: 0s; }
        .header .icon-item:nth-child(2) { animation-delay: 0.3s; }
        .header .icon-item:nth-child(3) { animation-delay: 0.6s; }
        .header .icon-item:nth-child(4) { animation-delay: 0.9s; }

        /* Enhanced Main Card */
        .main-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 50px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            animation: cardSlideUp 1s ease-out;
        }

        .main-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        .question {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .question .question-icon {
            font-size: 3rem;
            color: #03A9F4;
            margin-bottom: 20px;
            animation: rotate 4s linear infinite;
        }

        .question h2 {
            color: #ffffff;
            font-size: 2.2rem;
            margin-bottom: 20px;
            font-weight: 600;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            line-height: 1.3;
        }

        .question p {
            color: #B8E6FF;
            font-size: 1.2rem;
            line-height: 1.7;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Enhanced Form Styling */
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }

        .input-container {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
        }

        .gratitude-input {
            width: 100%;
            padding: 25px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 1.2rem;
            line-height: 1.6;
            resize: vertical;
            min-height: 150px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            color: #ffffff;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }

        .gratitude-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .gratitude-input:focus {
            outline: none;
            border-color: #03A9F4;
            box-shadow: 
                0 0 0 4px rgba(3, 169, 244, 0.2),
                0 10px 30px rgba(3, 169, 244, 0.1);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #03A9F4 0%, #0288D1 50%, #0277BD 100%);
            color: white;
            border: none;
            padding: 20px 40px;
            border-radius: 20px;
            font-size: 1.3rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(3, 169, 244, 0.3);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(3, 169, 244, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-2px);
        }

        /* Enhanced Success Message */
        .success-message {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(76, 175, 80, 0.3);
            animation: successSlideIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .success-message::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 255, 255, 0.05) 10px,
                rgba(255, 255, 255, 0.05) 20px
            );
            animation: diagonalMove 20s linear infinite;
        }

        .quote {
            font-size: 1.4rem;
            line-height: 1.7;
            margin-bottom: 15px;
            font-style: italic;
            position: relative;
            z-index: 2;
            font-weight: 300;
        }

        .quote-author {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
            font-weight: 500;
        }

        /* Enhanced History Section */
        .history-section {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: cardSlideUp 1s ease-out 0.3s both;
        }

        .history-title {
            color: #ffffff;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .history-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #03A9F4, #0288D1);
            border-radius: 2px;
        }

        .reflections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .reflection-item {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: 4px solid #03A9F4;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: itemFadeIn 0.6s ease-out;
        }

        .reflection-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(3, 169, 244, 0.1), rgba(3, 169, 244, 0.05));
            transition: width 0.4s ease;
        }

        .reflection-item:hover::before {
            width: 100%;
        }

        .reflection-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(3, 169, 244, 0.2);
            border-left-color: #0288D1;
        }

        .reflection-text {
            color: #ffffff;
            line-height: 1.7;
            margin-bottom: 15px;
            font-size: 1.1rem;
            position: relative;
            z-index: 2;
        }

        .reflection-date {
            color: #B8E6FF;
            font-size: 0.95rem;
            opacity: 0.8;
            position: relative;
            z-index: 2;
            font-weight: 500;
        }

        /* Animations */
        @keyframes wave {
            0% { background-position-x: 0; }
            100% { background-position-x: 1200px; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            33% { transform: translateY(-30px) rotate(120deg); opacity: 1; }
            66% { transform: translateY(15px) rotate(240deg); opacity: 0.8; }
        }

        @keyframes pulse-glow {
            0%, 100% { transform: translateX(-50%) scale(1); opacity: 0.6; }
            50% { transform: translateX(-50%) scale(1.2); opacity: 0.9; }
        }

        @keyframes textGlow {
            0% { text-shadow: 0 0 20px rgba(3, 169, 244, 0.5); }
            100% { text-shadow: 0 0 30px rgba(3, 169, 244, 0.8), 0 0 40px rgba(3, 169, 244, 0.3); }
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes cardSlideUp {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        @keyframes successSlideIn {
            0% {
                opacity: 0;
                transform: scale(0.8) translateY(-30px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes diagonalMove {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @keyframes itemFadeIn {
            0% {
                opacity: 0;
                transform: translateX(-20px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header h1 {
                font-size: 2.5rem;
            }

            .header .subtitle {
                font-size: 1.1rem;
            }

            .main-card, .history-section {
                padding: 30px 25px;
                margin-bottom: 25px;
            }

            .question h2 {
                font-size: 1.8rem;
            }

            .question p {
                font-size: 1.1rem;
            }

            .gratitude-input {
                padding: 20px;
                min-height: 120px;
                font-size: 1.1rem;
            }

            .submit-btn {
                padding: 18px 30px;
                font-size: 1.1rem;
            }

            .reflections-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .reflection-item {
                padding: 20px;
            }

            .particle {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .header .subtitle {
                font-size: 1rem;
            }

            .header .icon-row {
                gap: 15px;
            }

            .header .icon-item {
                font-size: 1.5rem;
            }

            .main-card, .history-section {
                padding: 25px 20px;
                border-radius: 20px;
            }

            .question h2 {
                font-size: 1.5rem;
            }

            .question p {
                font-size: 1rem;
            }

            .gratitude-input {
                padding: 18px;
                font-size: 1rem;
            }

            .submit-btn {
                padding: 16px 25px;
                font-size: 1rem;
            }

            .quote {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="wave"></div>
    </div>

    <div class="container">
        <div class="header">
            <h1>🌱 Syukur Harian</h1>
            <p class="subtitle">Temukan kebahagiaan dalam hal-hal kecil setiap hari</p>
            <div class="icon-row">
                <div class="icon-item">🌸</div>
                <div class="icon-item">✨</div>
                <div class="icon-item">🌊</div>
                <div class="icon-item">💙</div>
            </div>
        </div>

        <div class="main-card">
            <?php if ($success && $quote): ?>
                <div class="success-message">
                    <div class="quote"><?php echo htmlspecialchars($quote); ?></div>
                    <div class="quote-author">🙏 Terima kasih telah berbagi rasa syukur hari ini!</div>
                </div>
            <?php endif; ?>

            <div class="question">
                <div class="question-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h2>Hal kecil apa yang membuatmu bersyukur hari ini?</h2>
                <p>Luangkan sejenak untuk merenungkan momen indah, kebaikan yang kamu terima, atau hal sederhana yang membuat harimu lebih berarti.</p>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <div class="input-container">
                        <textarea 
                            name="gratitude" 
                            class="gratitude-input" 
                            placeholder="Tuliskan refleksi syukurmu di sini... ✨"  required
                        ></textarea>
                    </div>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Bagikan
                </button>
            </form>
        </div>

        <?php if (!empty($reflections)): ?>
            <div class="history-section">
                <h3 class="history-title">
                    <i class="fas fa-book-open"></i> Catatan Sebelumnya
                </h3>
                <div class="reflections-grid">
                    <?php foreach ($reflections as $index => $reflection): ?>
                        <div class="reflection-item" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                            <div class="reflection-text">
                                "<?php echo htmlspecialchars($reflection['gratitude_text']); ?>"
                            </div>
                            <div class="reflection-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo date('d F Y, H:i', strtotime($reflection['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Enhanced interactions
        document.addEventListener('DOMContentLoaded', function() {
            const submitBtn = document.querySelector('.submit-btn');
            const textarea = document.querySelector('.gratitude-input');
            
            // Button interaction
            submitBtn.addEventListener('click', function() {
                this.style.transform = 'translateY(-2px) scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });

            // Auto-resize textarea with animation
            function adjustTextareaHeight() {
                textarea.style.height = 'auto';
                textarea.style.height = Math.max(150, textarea.scrollHeight) + 'px';
            }

            textarea.addEventListener('input', adjustTextareaHeight);
            textarea.addEventListener('focus', adjustTextareaHeight);

            // Typing indicator effect
            let typingTimer;
            textarea.addEventListener('input', function() {
                this.style.borderColor = '#03A9F4';
                this.style.boxShadow = '0 0 0 4px rgba(3, 169, 244, 0.3), 0 10px 30px rgba(3, 169, 244, 0.2)';
                
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    this.style.boxShadow = 'none';
                }, 1500);
            });

            // Smooth scroll to success message
            <?php if ($success): ?>
                setTimeout(() => {
                    const successMessage = document.querySelector('.success-message');
                    if (successMessage) {
                        successMessage.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }, 500);
            <?php endif; ?>

            // Add parallax effect to floating elements
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const particles = document.querySelectorAll('.particle');
                
                particles.forEach((particle, index) => {
                    const speed = 0.5 + (index * 0.1);
                    particle.style.transform = `translateY(${scrolled * speed}px) rotate(${scrolled * 0.1}deg)`;
                });
            });

            // Interactive reflection items
            const reflectionItems = document.querySelectorAll('.reflection-item');
            reflectionItems.forEach((item, index) => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02) rotateY(5deg)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1) rotateY(0deg)';
                });
            });

            // Character counter for textarea
            const charCounter = document.createElement('div');
            charCounter.style.cssText = `
                position: absolute;
                bottom: 10px;
                right: 15px;
                color: rgba(255, 255, 255, 0.6);
                font-size: 0.85rem;
                pointer-events: none;
                transition: all 0.3s ease;
            `;
            
            const inputContainer = document.querySelector('.input-container');
            inputContainer.style.position = 'relative';
            inputContainer.appendChild(charCounter);
            
            function updateCharCounter() {
                const count = textarea.value.length;
                charCounter.textContent = `${count} karakter`;
                
                if (count > 500) {
                    charCounter.style.color = '#FF6B6B';
                } else if (count > 300) {
                    charCounter.style.color = '#FFD93D';
                } else {
                    charCounter.style.color = 'rgba(255, 255, 255, 0.6)';
                }
            }
            
            textarea.addEventListener('input', updateCharCounter);
            updateCharCounter();

            // Add floating text animation
            function createFloatingText(text, x, y) {
                const floatingText = document.createElement('div');
                floatingText.textContent = text;
                floatingText.style.cssText = `
                    position: fixed;
                    left: ${x}px;
                    top: ${y}px;
                    color: #03A9F4;
                    font-size: 1.2rem;
                    pointer-events: none;
                    z-index: 1000;
                    animation: floatUp 2s ease-out forwards;
                `;
                
                document.body.appendChild(floatingText);
                
                setTimeout(() => {
                    floatingText.remove();
                }, 2000);
            }

            // Add CSS for floating text animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes floatUp {
                    0% {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                    100% {
                        opacity: 0;
                        transform: translateY(-50px) scale(0.8);
                    }
                }
            `;
            document.head.appendChild(style);

            // Trigger floating text on form submission
            submitBtn.addEventListener('click', function(e) {
                if (textarea.value.trim()) {
                    const emojis = ['✨', '💙', '🌟', '🙏', '💫', '🌸'];
                    const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)];
                    
                    const rect = this.getBoundingClientRect();
                    createFloatingText(randomEmoji, 
                        rect.left + rect.width / 2, 
                        rect.top + window.scrollY
                    );
                }
            });

            // Add ripple effect to button
            submitBtn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Add ripple animation CSS
            const rippleStyle = document.createElement('style');
            rippleStyle.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(rippleStyle);

            // Add smooth loading animation for reflection items
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateX(0)';
                        }, index * 100);
                    }
                });
            });

            reflectionItems.forEach((item) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-30px)';
                item.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                observer.observe(item);
            });

            // Add breathing animation to main elements
            setInterval(() => {
                const questionIcon = document.querySelector('.question-icon');
                if (questionIcon) {
                    questionIcon.style.transform = 'scale(1.1) rotate(360deg)';
                    setTimeout(() => {
                        questionIcon.style.transform = 'scale(1) rotate(0deg)';
                    }, 1000);
                }
            }, 10000);

            // Add dynamic background color change based on time
            function updateBackgroundBasedOnTime() {
                const hour = new Date().getHours();
                let gradientColors;
                
                if (hour >= 6 && hour < 12) {
                    // Morning - lighter blues
                    gradientColors = 'linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #3b73c7 100%)';
                } else if (hour >= 12 && hour < 18) {
                    // Afternoon - balanced blues
                    gradientColors = 'linear-gradient(135deg, #0F2027 0%, #203A43 50%, #2C5364 100%)';
                } else if (hour >= 18 && hour < 22) {
                    // Evening - warmer blues
                    gradientColors = 'linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #4a6741 100%)';
                } else {
                    // Night - deeper blues
                    gradientColors = 'linear-gradient(135deg, #0c1f2e 0%, #1a2942 50%, #243b55 100%)';
                }
                
                document.body.style.background = gradientColors;
            }
            
            updateBackgroundBasedOnTime();
            setInterval(updateBackgroundBasedOnTime, 60000); // Update every minute
        });

        //Project by FarkhansyahOffi
    </script>
</body>
</html>