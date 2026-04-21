<?php
session_start();
include("../config/db.php");

$error = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Note: In production, use password_hash() and password_verify()
    
    // Updated query to use prepared statement (security best practice)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=? AND role='student'");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $_SESSION['user'] = $res->fetch_assoc();
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Disaster Preparedness Training</title>
    <link rel="stylesheet" href="css/login.css">
    <!-- Font Awesome for icons (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Welcome Back! 👋</h2>
            <p>Login to continue your disaster preparedness training</p>
        </div>
        
        <div class="login-form">
            <?php if ($error): ?>
                <div class="error-message" id="errorMessage">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                    <i class="fas fa-times close-btn" onclick="this.parentElement.style.display='none'"></i>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                    </div>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="additional-links">
                <a href="forgot_password.php">
                    <i class="fas fa-question-circle"></i> Forgot Password?
                </a>
                <br><br>
                <a href="register.php">
                    <i class="fas fa-user-plus"></i> Don't have an account? Register here
                </a>
            </div>
            
            <!-- Demo Credentials (Optional - remove in production) -->
            <div class="demo-credentials">
                <p><i class="fas fa-info-circle"></i> Demo Credentials:</p>
                <p>Email: <span class="cred">student@example.com</span> | Password: <span class="cred">password123</span></p>
            </div>
            
            <!-- Social Login (Optional) -->
            <div class="social-login">
                <p>Or login with</p>
                <div class="social-buttons">
                    <a href="#" class="social-btn google">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Form validation with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = document.querySelector('.login-btn');
            
            if (!email || !password) {
                e.preventDefault();
                showError('Please fill in all fields');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                showError('Please enter a valid email address');
                return;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
        });
        
        // Email validation function
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Show error message
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
                <i class="fas fa-times close-btn" onclick="this.parentElement.style.display='none'"></i>
            `;
            
            const form = document.getElementById('loginForm');
            form.parentNode.insertBefore(errorDiv, form);
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                if (errorDiv) errorDiv.style.display = 'none';
            }, 5000);
        }
        
        // Auto-fill demo credentials on click (for demo purposes)
        document.querySelectorAll('.demo-credentials .cred').forEach(cred => {
            cred.addEventListener('click', function() {
                if (this.textContent.includes('@')) {
                    document.getElementById('email').value = this.textContent;
                } else if (this.textContent === 'password123') {
                    document.getElementById('password').value = this.textContent;
                }
            });
        });
        
        // Remember me functionality
        if (localStorage.getItem('rememberedEmail')) {
            document.getElementById('email').value = localStorage.getItem('rememberedEmail');
            document.getElementById('remember').checked = true;
        }
        
        document.getElementById('loginForm').addEventListener('submit', function() {
            if (document.getElementById('remember').checked) {
                localStorage.setItem('rememberedEmail', document.getElementById('email').value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
        
        // Animated background effect
        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', function(e) {
            mouseX = e.clientX / window.innerWidth;
            mouseY = e.clientY / window.innerHeight;
            
            document.body.style.background = `radial-gradient(circle at ${mouseX * 100}% ${mouseY * 100}%, #667eea 0%, #764ba2 100%)`;
        });
        
        // Auto-hide error message after 5 seconds
        setTimeout(() => {
            const errorMsg = document.getElementById('errorMessage');
            if (errorMsg) {
                errorMsg.style.opacity = '0';
                setTimeout(() => {
                    if (errorMsg) errorMsg.style.display = 'none';
                }, 300);
            }
        }, 5000);
    </script>
</body>
</html>