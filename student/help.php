<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user']['name'];
$user_email = $_SESSION['user']['email'];

// Handle support ticket submission
$ticket_submitted = false;
$ticket_error = false;

if (isset($_POST['submit_ticket'])) {
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    
    if (!empty($subject) && !empty($message)) {
        $user_id = $_SESSION['user']['id'];
        $conn->query("INSERT INTO support_tickets (user_id, subject, message, priority, status, created_at) 
                       VALUES ('$user_id', '$subject', '$message', '$priority', 'open', NOW())");
        $ticket_submitted = true;
    } else {
        $ticket_error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Disaster Preparedness Training</title>
    <link rel="stylesheet" href="css/help.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="help-container">
        <!-- Header Section -->
        <div class="help-header">
            <div class="header-content">
                <div class="help-icon">❓</div>
                <h1>Help Center</h1>
                <p>How can we assist you today?</p>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchHelp" placeholder="Search for help topics..." onkeyup="searchHelp()">
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-card" onclick="scrollToSection('faq')">
                <i class="fas fa-question-circle"></i>
                <h4>FAQ</h4>
                <p>Frequently asked questions</p>
            </div>
            <div class="action-card" onclick="scrollToSection('ticket')">
                <i class="fas fa-ticket-alt"></i>
                <h4>Support Ticket</h4>
                <p>Submit a support request</p>
            </div>
            <div class="action-card" onclick="scrollToSection('contact')">
                <i class="fas fa-envelope"></i>
                <h4>Contact Us</h4>
                <p>Get in touch with us</p>
            </div>
            <div class="action-card" onclick="window.open('guides.php', '_blank')">
                <i class="fas fa-book"></i>
                <h4>User Guide</h4>
                <p>Download user manual</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="help-content">
            <!-- FAQ Section -->
            <div id="faq" class="faq-section">
                <div class="section-title">
                    <i class="fas fa-question-circle"></i>
                    <h2>Frequently Asked Questions</h2>
                </div>
                
                <div class="faq-grid">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>📚 How do I start a training module?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To start a training module, simply go to your dashboard and click on the "Start Module" button under any disaster category (Earthquake, Flood, Fire, or Typhoon). You'll be directed to the training video and quiz.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>📝 What is the passing score for quizzes?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>The passing score for each quiz is 70%. You need to answer at least 7 out of 10 questions correctly to pass the module and receive your certificate.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>🎓 How do I get my certificate?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Once you achieve a passing score (70% or higher) on any quiz, you can download your certificate from the results page. You can also view all your certificates in the "My Certificates" section.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>🔄 Can I retake a quiz?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! You can retake any quiz as many times as you want. Your best score will be recorded, and you can improve your knowledge by reviewing the training materials.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>⚠️ How do I receive emergency alerts?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Emergency alerts appear on your dashboard when you log in. Make sure to check your dashboard regularly for important safety announcements and updates.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>📱 Is the platform mobile-friendly?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! Our platform is fully responsive and works on all devices including smartphones, tablets, and desktop computers.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>🔒 Is my data secure?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Absolutely! We take data security seriously. Your personal information and quiz results are protected and only accessible to you and authorized administrators.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>⏱️ How long does each module take?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Each module typically takes 15-20 minutes to complete, including watching the training video (3-5 minutes) and taking the quiz (10-15 minutes).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Ticket Section -->
            <div id="ticket" class="ticket-section">
                <div class="section-title">
                    <i class="fas fa-ticket-alt"></i>
                    <h2>Submit a Support Ticket</h2>
                    <p>Need help? Our support team will get back to you within 24 hours.</p>
                </div>

                <?php if ($ticket_submitted): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Ticket Submitted Successfully!</strong>
                            <p>Your support request has been received. We'll respond to your email (<?php echo htmlspecialchars($user_email); ?>) within 24 hours.</p>
                        </div>
                        <i class="fas fa-times close-btn" onclick="this.parentElement.style.display='none'"></i>
                    </div>
                <?php endif; ?>

                <?php if ($ticket_error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $ticket_error; ?></span>
                        <i class="fas fa-times close-btn" onclick="this.parentElement.style.display='none'"></i>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="ticketForm" class="ticket-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="subject">
                                <i class="fas fa-tag"></i> Subject <span class="required">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" placeholder="Brief description of your issue" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="priority">
                                <i class="fas fa-flag"></i> Priority
                            </label>
                            <select id="priority" name="priority">
                                <option value="low">Low - General question</option>
                                <option value="medium">Medium - Technical issue</option>
                                <option value="high">High - Urgent problem</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">
                            <i class="fas fa-comment"></i> Message <span class="required">*</span>
                        </label>
                        <textarea id="message" name="message" rows="5" placeholder="Please provide detailed information about your issue..." required></textarea>
                    </div>
                    
                    <button type="submit" name="submit_ticket" class="submit-ticket-btn">
                        <i class="fas fa-paper-plane"></i> Submit Ticket
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div id="contact" class="contact-section">
                <div class="section-title">
                    <i class="fas fa-address-card"></i>
                    <h2>Contact Information</h2>
                </div>
                
                <div class="contact-grid">
                    <div class="contact-card">
                        <i class="fas fa-envelope"></i>
                        <h4>Email Support</h4>
                        <p>support@disasterprep.com</p>
                        <small>Response within 24 hours</small>
                    </div>
                    
                    <div class="contact-card">
                        <i class="fas fa-phone-alt"></i>
                        <h4>Emergency Hotline</h4>
                        <p>1-800-DISASTER</p>
                        <small>24/7 Emergency Support</small>
                    </div>
                    
                    <div class="contact-card">
                        <i class="fas fa-clock"></i>
                        <h4>Support Hours</h4>
                        <p>Monday - Friday: 9AM - 6PM</p>
                        <small>Saturday: 10AM - 2PM</small>
                    </div>
                    
                    <div class="contact-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <h4>Office Address</h4>
                        <p>123 Safety Street,<br>Disaster Preparedness Center</p>
                    </div>
                </div>
            </div>

            <!-- Resources Section -->
            <div class="resources-section">
                <div class="section-title">
                    <i class="fas fa-download"></i>
                    <h2>Useful Resources</h2>
                </div>
                
                <div class="resources-grid">
                    <a href="#" class="resource-card" onclick="downloadGuide('user_guide')">
                        <i class="fas fa-file-pdf"></i>
                        <h4>User Guide PDF</h4>
                        <p>Complete platform guide</p>
                    </a>
                    
                    <a href="#" class="resource-card" onclick="downloadGuide('quick_tips')">
                        <i class="fas fa-file-alt"></i>
                        <h4>Quick Tips Sheet</h4>
                        <p>Emergency preparedness tips</p>
                    </a>
                    
                    <a href="#" class="resource-card" onclick="downloadGuide('checklist')">
                        <i class="fas fa-check-circle"></i>
                        <h4>Safety Checklist</h4>
                        <p>Emergency kit checklist</p>
                    </a>
                    
                    <a href="#" class="resource-card" onclick="downloadGuide('glossary')">
                        <i class="fas fa-book-open"></i>
                        <h4>Terminology Glossary</h4>
                        <p>Disaster management terms</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard Button -->
        <div class="back-to-dashboard">
            <a href="dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // FAQ Toggle Function
        function toggleFaq(element) {
            const faqItem = element.parentElement;
            const answer = faqItem.querySelector('.faq-answer');
            const icon = element.querySelector('.fa-chevron-down');
            
            // Close all other FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                    item.querySelector('.faq-answer').style.maxHeight = null;
                    item.querySelector('.fa-chevron-down').style.transform = 'rotate(0deg)';
                }
            });
            
            // Toggle current FAQ
            faqItem.classList.toggle('active');
            
            if (faqItem.classList.contains('active')) {
                answer.style.maxHeight = answer.scrollHeight + "px";
                icon.style.transform = 'rotate(180deg)';
            } else {
                answer.style.maxHeight = null;
                icon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Search Help Function
        function searchHelp() {
            const searchTerm = document.getElementById('searchHelp').value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            let hasResults = false;
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show no results message
            const existingMsg = document.querySelector('.no-results');
            if (!hasResults && searchTerm !== '') {
                if (!existingMsg) {
                    const msg = document.createElement('div');
                    msg.className = 'no-results';
                    msg.innerHTML = '<i class="fas fa-search"></i><p>No results found for "' + searchTerm + '"</p>';
                    document.querySelector('.faq-grid').appendChild(msg);
                }
            } else if (existingMsg) {
                existingMsg.remove();
            }
        }
        
        // Scroll to Section
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        // Download Guide
        function downloadGuide(guideType) {
            // Simulate download - in production, this would link to actual files
            showNotification('Downloading ' + guideType + '...', 'info');
            setTimeout(() => {
                showNotification('Download complete!', 'success');
            }, 1500);
        }
        
        // Show Notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Form Validation
        document.getElementById('ticketForm')?.addEventListener('submit', function(e) {
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            const submitBtn = this.querySelector('.submit-ticket-btn');
            
            if (!subject || !message) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
                return false;
            }
            
            if (subject.length < 5) {
                e.preventDefault();
                showNotification('Subject must be at least 5 characters', 'error');
                return false;
            }
            
            if (message.length < 20) {
                e.preventDefault();
                showNotification('Please provide more details (minimum 20 characters)', 'error');
                return false;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        });
        
        // Auto-hide messages
        setTimeout(() => {
            const messages = document.querySelectorAll('.success-message, .error-message');
            messages.forEach(msg => {
                setTimeout(() => {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.style.display = 'none', 300);
                }, 5000);
            });
        }, 1000);
        
        // Add animation to FAQ items on load
        window.addEventListener('load', () => {
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
                item.classList.add('animate');
            });
        });
        
        // Keyboard shortcut: Ctrl + / to focus search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                e.preventDefault();
                document.getElementById('searchHelp').focus();
            }
        });
        
        // Live character counter for message field
        const messageField = document.getElementById('message');
        if (messageField) {
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            counter.innerHTML = '<span>Characters: <span id="charCount">0</span></span>';
            messageField.parentNode.appendChild(counter);
            
            function updateCharCount() {
                const count = messageField.value.length;
                document.getElementById('charCount').textContent = count;
            }
            
            messageField.addEventListener('input', updateCharCount);
            updateCharCount();
        }
    </script>
</body>
</html>