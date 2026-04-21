<?php
include("../config/db.php");

$success = false;
$error = '';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } elseif (strlen($name) < 2) {
        $error = "Name must be at least 2 characters long!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        // Check if email already exists
        $check = $conn->query("SELECT * FROM users WHERE email='$email'");
        
        if ($check->num_rows > 0) {
            $error = "Email already exists! Please use a different email address.";
        } else {
            // Insert new user (in production, use password_hash())
            $conn->query("INSERT INTO users (name, email, password, role, created_at) 
                          VALUES ('$name', '$email', '$password', 'student', NOW())");
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Disaster Preparedness Training</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="register-icon">🎓</div>
            <h2>Create Account</h2>
            <p>Join our disaster preparedness training program</p>
        </div>
        
        <div class="register-form">
            <?php if ($success): ?>
                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle"></i>
                    <span>Registered successfully! You can now login to your account.</span>
                    <a href="login.php" style="margin-left: auto; color: #155724; font-weight: bold;">Login →</a>
                    <i class="fas fa-times close-btn" onclick="this.parentElement.style.display='none'"></i>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message" id="errorMessage">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                    <i class="fas fa-times close-btn" onclick="this.parentElement.style.display='none'"></i>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    <div class="validation-message" id="nameValidation"></div>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="validation-message" id="emailValidation"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                    </div>
                    <div class="password-strength">
                        <div class="strength-meter">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Enter a password</div>
                    </div>
                    <div class="validation-message" id="passwordValidation"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">
                        <i class="fas fa-check-circle"></i> Confirm Password
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-check"></i>
                        <input type="password" id="confirmPassword" placeholder="Confirm your password" required>
                    </div>
                    <div class="validation-message" id="confirmValidation"></div>
                </div>
                
                <div class="terms">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">
                        I agree to the <a href="#" onclick="showTerms()">Terms of Service</a> and 
                        <a href="#" onclick="showPrivacy()">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" name="register" class="register-btn" id="registerBtn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login here</a></p>
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
        
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let message = '';
            let color = '';
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            if (password.length === 0) {
                message = 'Enter a password';
                color = '#e0e0e0';
            } else if (strength <= 2) {
                message = 'Weak password';
                color = '#ff6b6b';
            } else if (strength <= 4) {
                message = 'Medium password';
                color = '#ffa500';
            } else {
                message = 'Strong password';
                color = '#27ae60';
            }
            
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            
            bar.style.width = (strength / 6 * 100) + '%';
            bar.style.backgroundColor = color;
            text.textContent = message;
            text.className = 'strength-text';
            
            if (strength <= 2 && password.length > 0) {
                text.classList.add('weak');
            } else if (strength <= 4) {
                text.classList.add('medium');
            } else if (strength > 4) {
                text.classList.add('strong');
            }
            
            return strength >= 3;
        }
        
        // Real-time validation
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirmPassword');
        
        nameInput.addEventListener('input', function() {
            const name = this.value.trim();
            const validation = document.getElementById('nameValidation');
            
            if (name.length === 0) {
                validation.innerHTML = '';
                this.classList.remove('valid', 'error');
            } else if (name.length < 2) {
                validation.innerHTML = '<i class="fas fa-times-circle"></i> Name must be at least 2 characters';
                validation.className = 'validation-message error';
                this.classList.add('error');
                this.classList.remove('valid');
            } else {
                validation.innerHTML = '<i class="fas fa-check-circle"></i> Looks good!';
                validation.className = 'validation-message success';
                this.classList.add('valid');
                this.classList.remove('error');
            }
        });
        
        emailInput.addEventListener('input', function() {
            const email = this.value.trim();
            const validation = document.getElementById('emailValidation');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email.length === 0) {
                validation.innerHTML = '';
                this.classList.remove('valid', 'error');
            } else if (!emailRegex.test(email)) {
                validation.innerHTML = '<i class="fas fa-times-circle"></i> Enter a valid email address';
                validation.className = 'validation-message error';
                this.classList.add('error');
                this.classList.remove('valid');
            } else {
                validation.innerHTML = '<i class="fas fa-check-circle"></i> Valid email address';
                validation.className = 'validation-message success';
                this.classList.add('valid');
                this.classList.remove('error');
            }
        });
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const validation = document.getElementById('passwordValidation');
            const isValid = checkPasswordStrength(password);
            
            if (password.length === 0) {
                validation.innerHTML = '';
                this.classList.remove('valid', 'error');
            } else if (password.length < 6) {
                validation.innerHTML = '<i class="fas fa-times-circle"></i> Password must be at least 6 characters';
                validation.className = 'validation-message error';
                this.classList.add('error');
                this.classList.remove('valid');
            } else if (!isValid) {
                validation.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Add numbers or special characters for stronger password';
                validation.className = 'validation-message error';
                this.classList.add('error');
                this.classList.remove('valid');
            } else {
                validation.innerHTML = '<i class="fas fa-check-circle"></i> Strong password!';
                validation.className = 'validation-message success';
                this.classList.add('valid');
                this.classList.remove('error');
            }
            
            // Check confirm password if it has value
            if (confirmInput.value) {
                checkConfirmPassword();
            }
        });
        
        function checkConfirmPassword() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            const validation = document.getElementById('confirmValidation');
            
            if (confirm.length === 0) {
                validation.innerHTML = '';
                confirmInput.classList.remove('valid', 'error');
            } else if (password !== confirm) {
                validation.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
                validation.className = 'validation-message error';
                confirmInput.classList.add('error');
                confirmInput.classList.remove('valid');
                return false;
            } else {
                validation.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                validation.className = 'validation-message success';
                confirmInput.classList.add('valid');
                confirmInput.classList.remove('error');
                return true;
            }
            return false;
        }
        
        confirmInput.addEventListener('input', checkConfirmPassword);
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            const terms = document.getElementById('terms').checked;
            const submitBtn = document.getElementById('registerBtn');
            
            let isValid = true;
            
            if (name.length < 2) {
                showError('Name must be at least 2 characters long');
                isValid = false;
            } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                showError('Please enter a valid email address');
                isValid = false;
            } else if (password.length < 6) {
                showError('Password must be at least 6 characters long');
                isValid = false;
            } else if (password !== confirm) {
                showError('Passwords do not match');
                isValid = false;
            } else if (!terms) {
                showError('Please agree to the Terms of Service');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
        });
        
        // Show error message
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
                <i class="fas fa-times close-btn" onclick="this.parentElement.style.display='none'"></i>
            `;
            
            const form = document.getElementById('registerForm');
            form.parentNode.insertBefore(errorDiv, form);
            
            setTimeout(() => {
                if (errorDiv) errorDiv.remove();
            }, 5000);
        }
        
        // Show terms modal (simplified)
        function showTerms() {
            alert("Terms of Service:\n\nBy registering, you agree to:\n1. Complete all training modules\n2. Maintain safety standards\n3. Keep account information confidential\n4. Follow all safety guidelines");
        }
        
        function showPrivacy() {
            alert("Privacy Policy:\n\nWe value your privacy:\n• Your data is securely stored\n• Information used only for training purposes\n• No sharing with third parties\n• You can request data deletion anytime");
        }
        
        // Auto-hide messages
        setTimeout(() => {
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            
            if (successMsg) {
                setTimeout(() => {
                    successMsg.style.opacity = '0';
                    setTimeout(() => successMsg.style.display = 'none', 300);
                }, 5000);
            }
            
            if (errorMsg) {
                setTimeout(() => {
                    errorMsg.style.opacity = '0';
                    setTimeout(() => errorMsg.style.display = 'none', 300);
                }, 5000);
            }
        }, 1000);
        
        // Focus on first input
        nameInput.focus();
        
        // Animate registration icon
        // const icon = document.querySelector('.register-icon');
        // setInterval(() => {
        //     icon.style.transform = 'scale(1.1)';
        //     setTimeout(() => {
        //         icon.style.transform = 'scale(1)';
        //     }, 300);
        // }, 3000);
    </script>
</body>
</html>