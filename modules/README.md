# 🔐 OTP Authentication Setup for Drupal

This guide explains how to configure the **OTP Authentication** module in a Drupal project.

## 📸 Screenshot

![OTP Authentication Screenshot](images/otp-login-form.png)

> Replace the image path with your actual screenshot location if different.

---

## 🧩 Setup & Testing Steps

1. **Download and install Drupal 10**
   ```bash
   git clone <repository-url> /path/to/your/webserver-root
   cd /path/to/your/webserver-root
2. **clone modules folder in your project**
   ```bash
   git clone <repository-url> /path/to/your/project
   cd /path/to/your/webserver-root

3. **Enable the OTP Authentication Module**
    From the top menu, go to:
    Extend → Check OTP Authentication
    It will automatically enable its dependencies:

    Mail Manager

    SMTP Authentication Support


4. **Enable the OTP Authentication module**  
   - Go to the top menu: **Extend**.  
   - Enable **OTP Authentication**.  
   - This will automatically enable the required dependencies:
     - Mail Manager
     - SMTP Authentication Support

5. **Install PHPMailer**  
   Run the following command inside your project root:
   ```bash
   composer require phpmailer/phpmailer
   ```
6. **Configure OTP Settings**  
   Navigate to:  
   `Administration > Configuration > People > OTP Authentication Settings`  
   Then:
   - Set the **OTP Length** (e.g., 6)  
   - Set the **Expiration Time** in seconds (e.g., 300)

7. **Configure the Mail System**  
   Go to:  
   `Administration > Configuration > System > Mail System`  
   Make sure **SMTP Mailer** is selected for:
   - Default Mail System
   - Formatter
   - Sender

8. **Configure SMTP Authentication**  
   Navigate to:  
   `Administration > Configuration > System > SMTP Authentication Support`  
   Fill in your SMTP configuration:
   - SMTP Server (e.g., `smtp.mailtrap.io`)
   - SMTP Port (e.g., `2525` or `587`)
   - SMTP Username and Password
   - From Email Address

9. **Add a Test User**  
    Go to:  
    `People > Add user`  
    - Create a user with a **valid email address** for testing.

10. **Open the Website in an Incognito Window**  
    - Navigate to the login screen in a private/incognito browser window.

11. **Login with Valid Credentials**  
    - Enter the test user's **username** and **password**.  
    - You will be redirected to the **OTP Verification** form.

12. **Get the OTP Code**  
    - Check the user’s email inbox (e.g., via Mailtrap or your SMTP inbox),  
    - Or go to:  
      `Administration > Reports` to find the OTP if it’s logged.

13. **Enter the OTP Code**  
    - Input the OTP received via email.  
    - If correct, you’ll be logged in successfully.

14. **OTP Verification Enforcement**  
    - If a user tries to visit any page **before verifying the OTP**,  
      they will be **redirected back** to the OTP verification screen.

---

## 📝 Notes

- You can test email functionality using services like [Mailtrap](https://mailtrap.io).
- Ensure your SMTP credentials and email sender settings are valid.
- PHPMailer is required for sending SMTP emails, so don’t skip it setup step.