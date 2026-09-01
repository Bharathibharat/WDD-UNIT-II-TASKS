<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Registration - Task 10</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Store Registration</h1>
        <p>Sign up to start shopping</p>
    </header>

    <div class="container">
        <div class="card">
            <form action="process.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="user_123" required>
                </div>
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullName" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" required>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="pwd" required oninput="checkStrength()">
                    <button type="button" class="pwd-toggle" onclick="togglePwd('pwd')">Show</button>
                    <div id="pwd-meter"><div id="pwd-fill"></div></div>
                    <small id="pwd-text" style="color:#666;">Enter password...</small>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirmPassword" id="cpwd" required>
                    <button type="button" class="pwd-toggle" onclick="togglePwd('cpwd')">Show</button>
                </div>

                <div class="form-group">
                    <label>Referral Code (Optional)</label>
                    <input type="text" name="referral">
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" name="terms" id="terms" value="yes" required>
                    <label for="terms" style="margin:0;">I agree to the Terms & Conditions</label>
                </div>

                <button type="submit" class="btn">Register</button>
            </form>
        </div>
    </div>

    <script>
        function togglePwd(id) {
            const el = document.getElementById(id);
            if (el.type === "password") {
                el.type = "text";
                event.target.innerText = "Hide";
            } else {
                el.type = "password";
                event.target.innerText = "Show";
            }
        }

        function checkStrength() {
            const pwd = document.getElementById('pwd').value;
            let strength = 0;
            if(pwd.length >= 8) strength += 25;
            if(pwd.match(/[A-Z]/)) strength += 25;
            if(pwd.match(/[0-9]/)) strength += 25;
            if(pwd.match(/[^a-zA-Z0-9]/)) strength += 25;

            const fill = document.getElementById('pwd-fill');
            const text = document.getElementById('pwd-text');
            fill.style.width = strength + '%';
            
            if(strength <= 25) { fill.style.background = '#c62828'; text.innerText = 'Weak'; }
            else if(strength <= 50) { fill.style.background = '#f9a825'; text.innerText = 'Fair'; }
            else if(strength <= 75) { fill.style.background = '#9ccc65'; text.innerText = 'Good'; }
            else { fill.style.background = '#2e7d32'; text.innerText = 'Strong'; }
        }
    </script>
</body>
</html>
