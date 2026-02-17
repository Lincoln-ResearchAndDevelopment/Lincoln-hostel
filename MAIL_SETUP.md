# Gmail Setup for LincHostel (deborahokike773@gmail.com)

Use this guide to send LincHostel emails (leave notifications, application alerts, etc.) from **deborahokike773@gmail.com** via Gmail SMTP.

---

## Step 1: Turn on 2-Step Verification (required for App Passwords)

1. Go to [https://myaccount.google.com/security](https://myaccount.google.com/security)
2. Sign in with **deborahokike773@gmail.com**
3. Under **"How you sign in to Google"**, click **2-Step Verification**
4. If it’s off, click **Get started** and follow the steps (phone number, code, etc.) until 2-Step Verification is **On**

---

## Step 2: Create an App Password

1. Go to [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)  
   - Or: **Google Account** → **Security** → **2-Step Verification** → at the bottom, **App passwords**
2. You may need to sign in again
3. Under **"Select app"** choose **Mail**
4. Under **"Select device"** choose **Other (Custom name)** and type: **LincHostel**
5. Click **Generate**
6. Google shows a **16-character password** (e.g. `abcd efgh ijkl mnop`)
7. **Copy it** and keep it somewhere safe. You won’t see it again.  
   - You’ll use this in `.env` as `MAIL_PASSWORD` (without spaces: `abcdefghijklmnop`)

---

## Step 3: Edit your `.env` file

1. Open the file **`.env`** in your project root:  
   `c:\xampp\htdocs\LincHostel\.env`
2. Find the `MAIL_` lines. If they’re missing, add them. Set them **exactly** like this:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=deborahokike773@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=deborahokike773@gmail.com
MAIL_FROM_NAME="LincHostel"
```

3. **Replace** `abcdefghijklmnop` with the **actual 16-character App Password** from Step 2 (no spaces).
4. Save the file.

---

## Step 4: Clear config cache

In a terminal (PowerShell or Command Prompt) in the project folder run:

```bash
cd c:\xampp\htdocs\LincHostel
php artisan config:clear
```

---

## Step 5: Test that mail works

1. Start your app (e.g. `php artisan serve` or use XAMPP).
2. Trigger an email, for example:
   - Submit a **hostel application** (sends confirmation to the applicant), or  
   - Have a **student apply for leave** and **admin approve** it (sends to student and parent).

If you see **"Username and Password not accepted"** or **"Less secure app"** errors, usually:

- 2-Step Verification is not turned on, or  
- You’re using the normal Gmail password instead of the **App Password**.

Go back to Step 1 and 2 and use the App Password in `MAIL_PASSWORD`.

---

## Summary of what you need to do

| Step | What to do |
|------|------------|
| 1 | Turn on **2-Step Verification** for deborahokike773@gmail.com |
| 2 | Create an **App Password** for "LincHostel" and copy the 16-character code |
| 3 | In **`.env`**, set `MAIL_USERNAME`, `MAIL_PASSWORD` (App Password), `MAIL_FROM_ADDRESS` to **deborahokike773@gmail.com** and the rest as in the block above |
| 4 | Run **`php artisan config:clear`** |
| 5 | Test by submitting an application or approving a leave |

---

## Leave notification behaviour (already implemented)

- **When admin approves leave**  
  - Student: in-app notification, email, SMS  
  - Parent/guardian: email and SMS (using the email/phone from the **hostel application** or student profile)

- **When admin rejects leave**  
  - Student only: in-app notification, email, SMS  
  - Parent/guardian: **no** email and **no** SMS

Parent/guardian contact is taken from the student’s profile; if missing, it falls back to the original **HostelApplication** (the form students fill before admin adds them).
