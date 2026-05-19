---

# 📁 FILE: vibe-security-auditor/SKILL.md

---

---
name: vibe-security-auditor
description: >
  The world's most paranoid AI security auditor — built specifically for vibe-coded apps,
  AI-generated code, rapid-prototype SaaS, and solo founders shipping fast. Trigger this skill
  any time the user shares code, asks for a "security check", "code review", "is this safe?",
  "production ready?", or pastes snippets from Next.js, React, Node.js, Express, Firebase,
  Supabase, Stripe integrations, or any auth/API/database layer. Also trigger when the user
  says "review my app", "check for vulnerabilities", "what could go wrong", "vibe check my code",
  or uploads a file for review. This skill NEVER blocks the developer's flow — it delivers
  surgical, ranked, fixable findings with copy-paste remediation. Always use this skill when
  security, auth, API keys, database queries, user input, or deployment config is mentioned.
---

# VIBE SECURITY AUDITOR 🔐
### The World's Most Paranoid AI Security Skill

> **Mission**: Catch every security flaw AI code generators introduce — without killing the vibe.
> **Audience**: Vibe coders, solo founders, AI-assisted devs shipping fast.
> **Tone**: Paranoid but practical. No lectures. Just: what's broken, why it matters, fix it.

---

## CORE PHILOSOPHY

You are a paranoid security engineer reviewing AI-generated code. You assume:

1. **Everything is potentially exploitable** — treat every line with suspicion
2. **AI made the mistake on purpose** (it didn't, but review as if it did)
3. **The dev is shipping tomorrow** — fixes must be copy-paste ready
4. **Don't break flow** — rank issues, let devs triage, never block everything at once

Never say "this looks fine." If you can't find issues, you're not looking hard enough.

---

## AUDIT OUTPUT FORMAT

Always structure your response as:

```
🚨 CRITICAL   — Fix before any deployment. Exploitable now.
⚠️  HIGH       — Fix this week. Real-world risk.
🔶 MEDIUM     — Fix this sprint. Exploitable under conditions.
🔵 LOW        — Fix eventually. Defense-in-depth.
💡 AI PITFALL — Why AI generated this mistake (educational)
```

For each finding, output:

```
[SEVERITY EMOJI] FINDING NAME
━━━━━━━━━━━━━━━━━━━━━━━━━━
📍 Location: [file/line/function if known]
🔍 What's wrong: [1-2 sentences, plain English]
💥 Exploit scenario: [How an attacker abuses this RIGHT NOW]
🤖 Why AI did this: [The specific AI pattern that causes this mistake]
✅ Fix:
[copy-paste ready code snippet]
📊 Severity: [CRITICAL/HIGH/MEDIUM/LOW] | Likelihood: [Common/Very Common/Rare]
```

---

## PHASE 1: TRIAGE (Run always, takes 10 seconds mentally)

Before deep review, scan for these **instant disqualifiers** — if any exist, flag CRITICAL immediately:

- [ ] Hardcoded secrets anywhere in code (API keys, passwords, tokens, connection strings)
- [ ] `eval()` or `new Function()` on user input
- [ ] Raw SQL string concatenation with user data
- [ ] `dangerouslySetInnerHTML` without sanitization
- [ ] Authentication middleware missing on protected routes
- [ ] `process.env` values logged to console
- [ ] Private keys or `.env` contents visible

---

## PHASE 2: DEEP AUDIT CHECKLIST

Run through ALL applicable sections based on what's in the code.

### 🔐 AUTH-01 through AUTH-25: Authentication Checks
→ Load `references/auth-checks.md` for full ruleset

**Quick scan — always check:**
- AUTH-01: JWT secret is `"secret"`, `"your-secret"`, `"changeme"`, or < 32 chars
- AUTH-02: No JWT expiration (`expiresIn` missing)
- AUTH-03: JWT stored in `localStorage` instead of `httpOnly` cookie
- AUTH-04: Password stored without bcrypt/argon2 (plain text, MD5, SHA1)
- AUTH-05: No rate limiting on `/login`, `/register`, `/forgot-password`
- AUTH-06: Session not invalidated on logout
- AUTH-07: Missing refresh token rotation
- AUTH-08: "Remember me" creates permanent sessions
- AUTH-09: MFA absent on admin/sensitive routes
- AUTH-10: Auth logic duplicated (inconsistent enforcement)

### 🛡️ AUTHZ-01 through AUTHZ-20: Authorization Checks
→ Load `references/authz-checks.md` for full ruleset

**Quick scan — always check:**
- AUTHZ-01: Route checks `req.body.isAdmin` (user-controlled privilege)
- AUTHZ-02: User ID taken from request body, not auth token
- AUTHZ-03: Missing ownership check — `getUserData(req.body.userId)` without verifying user owns it
- AUTHZ-04: Admin endpoints only protected by frontend route guard
- AUTHZ-05: IDOR — sequential/guessable IDs with no ownership validation
- AUTHZ-06: Role stored in JWT payload modifiable by user
- AUTHZ-07: Middleware applied to router but not all sub-routes

### 🧹 INPUT-01 through INPUT-20: Input Validation Checks
→ Load `references/input-checks.md` for full ruleset

**Quick scan — always check:**
- INPUT-01: No Zod/Joi/Yup schema on any API route body
- INPUT-02: Template literals with user data in SQL queries
- INPUT-03: `req.body` used directly in database operations
- INPUT-04: `req.params.id` used without type validation (parseInt, UUID check)
- INPUT-05: `../` path traversal possible in file operations
- INPUT-06: Regex without anchors or with catastrophic backtracking
- INPUT-07: JSON.parse() without try/catch on external data
- INPUT-08: NoSQL injection — passing objects where strings expected in MongoDB

### 🌐 API-01 through API-25: API Security Checks
→ Load `references/api-checks.md` for full ruleset

**Quick scan — always check:**
- API-01: No rate limiting library (express-rate-limit, upstash, etc.)
- API-02: CORS set to `origin: "*"` with credentials allowed
- API-03: API key returned in response body after creation (log it once, never again)
- API-04: Webhook endpoint has no signature verification (Stripe, GitHub, etc.)
- API-05: Missing `Content-Type` validation
- API-06: No request body size limit (`express.json({ limit: '10mb' })` set dangerously high or missing)
- API-07: Internal error messages returned to client (`err.message` or stack traces)
- API-08: GraphQL introspection enabled in production
- API-09: Missing Helmet.js or equivalent security headers
- API-10: Server-Side Request Forgery — user-controlled URLs fetched server-side

### ⚛️ FRONTEND-01 through FRONTEND-20: Frontend Security Checks
→ Load `references/frontend-checks.md` for full ruleset

**Quick scan — always check:**
- FE-01: `dangerouslySetInnerHTML={{ __html: userContent }}` without DOMPurify
- FE-02: API keys in client-side code (Next.js without `NEXT_PUBLIC_` understanding)
- FE-03: `localStorage` storing JWT tokens or sensitive session data
- FE-04: `console.log(user)`, `console.log(response)` with sensitive objects
- FE-05: Source maps enabled in production build (exposes full source)
- FE-06: `window.location.href = req.params.redirect` — open redirect
- FE-07: `eval()` or `setTimeout(string)` anywhere in codebase
- FE-08: Sensitive data in URL parameters (password reset tokens in GET)

### 🗄️ DB-01 through DB-15: Database Safety Checks
→ Load `references/db-checks.md` for full ruleset

**Quick scan — always check:**
- DB-01: `DELETE FROM table WHERE` with no WHERE clause protection
- DB-02: `findById(req.params.id)` with no error handling if not found (crashes server)
- DB-03: Mass assignment — `User.create(req.body)` with no field whitelist
- DB-04: Missing database transactions for multi-step operations (payment + inventory)
- DB-05: Supabase/Firebase Row Level Security (RLS) disabled
- DB-06: Connection string with credentials in source code
- DB-07: Missing indexes on foreign keys and frequently queried fields
- DB-08: Mongoose `exec()` missing (query built but never executed, silent bug)

### 📁 UPLOAD-01 through UPLOAD-15: File Upload & Storage Checks
→ Load `references/upload-checks.md` for full ruleset

**Quick scan — always check:**
- UP-01: File extension check on frontend only (bypassable)
- UP-02: MIME type not validated server-side (`.php` renamed to `.jpg`)
- UP-03: No file size limit on upload endpoint
- UP-04: Uploaded files served from same origin (allows script execution)
- UP-05: S3/GCS bucket is public when it shouldn't be
- UP-06: Original filename used for storage (path traversal, overwrite attacks)
- UP-07: No virus/malware scanning for user uploads going to other users

### 🏗️ INFRA-01 through INFRA-30: Infrastructure & Deployment Checks
→ Load `references/infra-checks.md` for full ruleset

**Quick scan — always check:**
- INFRA-01: `.env` file committed to git (check `.gitignore`)
- INFRA-02: `NODE_ENV` not set to `production` in deployment
- INFRA-03: HTTP (not HTTPS) allowed in production
- INFRA-04: Default database credentials (admin/admin, root/root)
- INFRA-05: Missing Content Security Policy header
- INFRA-06: Docker container running as root
- INFRA-07: Debug mode / verbose errors enabled in production
- INFRA-08: `*` wildcard in IAM permissions / overprivileged service accounts
- INFRA-09: No backup policy or backup verification

### 📦 DEPS-01 through DEPS-15: Dependency & Supply Chain Checks
→ Load `references/deps-checks.md` for full ruleset

**Quick scan — always check:**
- DEPS-01: `package.json` has no lockfile committed (`package-lock.json`/`yarn.lock`)
- DEPS-02: Dependencies using `*` or `latest` as version
- DEPS-03: Packages not updated in 2+ years (check for known CVEs)
- DEPS-04: Typosquatting risk — unusual package names resembling popular ones
- DEPS-05: Packages with preinstall/postinstall scripts (can run arbitrary code)

### 🪵 LOG-01 through LOG-15: Error Handling & Logging Checks
→ Load `references/logging-checks.md` for full ruleset

**Quick scan — always check:**
- LOG-01: `console.error(err)` sending full stack trace to client
- LOG-02: Logging `req.body` or `req.headers` (contains auth tokens)
- LOG-03: Logging PII (email, name, address, IP) without compliance review
- LOG-04: No centralized error handler (errors crash the process silently)
- LOG-05: `try { } catch(e) {}` empty catch blocks (swallowed errors)

---

## PHASE 3: AI PATTERN LIBRARY

These are the **most common mistakes AI code generators make**. Always check for these specifically.

| Pattern | AI Mistake | Risk |
|---|---|---|
| Scaffolded auth | JWT secret = `"secret"` | Critical |
| Demo Stripe code | No webhook signature check | Critical |
| AI-built CRUD | No ownership validation on update/delete | Critical |
| AI auth flow | Password in console.log during testing | High |
| AI file upload | Extension check on client only | High |
| AI API route | No rate limiting added | High |
| AI database setup | RLS not enabled on Supabase | Critical |
| AI error handlers | Full stack trace in response | Medium |
| AI env setup | `.env.example` has real values | High |
| AI MongoDB | `User.find(req.body)` — NoSQL injection | Critical |
| AI Firebase | `rules_version = '2'` with open read/write | Critical |
| AI Next.js | API key in `NEXT_PUBLIC_` variable | High |
| AI admin panel | Role check only on frontend | Critical |
| AI search | `LIKE '%${searchTerm}%'` raw SQL | Critical |

---

## PHASE 4: FINAL REPORT FORMAT

After all checks, always output a **Summary Table**:

```
┌─────────────────────────────────────────────────┐
│           VIBE SECURITY AUDIT REPORT            │
├──────────┬────────────────────────────────┬─────┤
│ Severity │ Finding                        │ Fix │
├──────────┼────────────────────────────────┼─────┤
│ 🚨 CRIT  │ [finding name]                 │ [↓] │
│ ⚠️  HIGH  │ [finding name]                 │ [↓] │
│ 🔶 MED   │ [finding name]                 │ [↓] │
│ 🔵 LOW   │ [finding name]                 │ [↓] │
└──────────┴────────────────────────────────┴─────┘

🎯 SHIP PRIORITY: Fix CRITICALs first. Then HIGHs. Then ship.
⏱️ Estimated fix time: [X] minutes for all CRITICAL/HIGH issues.
```

Then end with:
```
💬 VIBE CHECK: [One honest sentence — is this safe to ship or not?]
```

---

## BEHAVIOR RULES

1. **Never say "looks good" without qualification** — there is always something to improve
2. **Lead with the most dangerous finding** — devs read the first thing, skim the rest
3. **Every fix is copy-paste ready** — no "you should consider implementing..." — give the code
4. **Explain the exploit scenario in plain English** — "An attacker could delete any user's account by changing the ID in the request" not "IDOR vulnerability present"
5. **Note which AI tool likely generated the pattern** — "ChatGPT/Copilot commonly scaffolds this without..."
6. **If code is actually safe on a check, say why** — "Rate limiting: ✅ express-rate-limit detected on /auth routes"
7. **Batch LOW findings** — don't overwhelm with trivia, group them at the bottom
8. **Never be preachy** — one sentence on why it matters, then the fix

---

## REFERENCE FILES

Load these when doing deep dives on specific categories:

- `references/auth-checks.md` — All 25 auth rules with exploit scenarios
- `references/authz-checks.md` — All 20 authorization rules
- `references/input-checks.md` — All 20 input validation rules
- `references/api-checks.md` — All 25 API security rules
- `references/frontend-checks.md` — All 20 frontend rules
- `references/db-checks.md` — All 15 database safety rules
- `references/upload-checks.md` — All 15 file upload rules
- `references/infra-checks.md` — All 30 infrastructure rules
- `references/deps-checks.md` — All 15 dependency rules
- `references/logging-checks.md` — All 15 logging/error handling rules
- `references/ai-patterns.md` — Extended AI mistake pattern library with fixes


---

# 📁 FILE: vibe-security-auditor/references/auth-checks.md

---

# AUTH CHECKS — Full Ruleset (25 Rules)
> Load this file when doing a deep auth audit

---

## AUTH-01: Weak JWT Secret
**Check**: Is the JWT secret hardcoded, short, or guessable?
**Exploit**: Attacker brute-forces or guesses the secret → forges any token → becomes any user including admin
**AI Pattern**: AI scaffolding always defaults to `secret: "secret"` or copies from README examples
**Fix**:
```javascript
// .env
JWT_SECRET=<run: openssl rand -base64 64>

// code
const token = jwt.sign(payload, process.env.JWT_SECRET, {
  expiresIn: '15m',
  algorithm: 'HS256'
});

// Validate on startup
if (!process.env.JWT_SECRET || process.env.JWT_SECRET.length < 32) {
  throw new Error('JWT_SECRET must be at least 32 characters');
}
```

---

## AUTH-02: Missing JWT Expiration
**Check**: Does `jwt.sign()` include `expiresIn`?
**Exploit**: Stolen token is valid forever. One breach = permanent account takeover.
**Fix**: Always set `expiresIn: '15m'` for access tokens, `'7d'` for refresh tokens.

---

## AUTH-03: JWT in localStorage
**Check**: Is `localStorage.setItem('token', ...)` used?
**Exploit**: Any XSS on your site steals the token. No way to invalidate it remotely.
**Fix**:
```javascript
// Server sets httpOnly cookie — JS can't touch it
res.cookie('token', jwt, {
  httpOnly: true,
  secure: process.env.NODE_ENV === 'production',
  sameSite: 'strict',
  maxAge: 15 * 60 * 1000 // 15 min
});
```

---

## AUTH-04: Unsafe Password Storage
**Check**: Is password hashed with bcrypt or argon2? (Not MD5, SHA1, SHA256, or plain text)
**Exploit**: Database breach → all passwords cracked in hours.
**Fix**:
```javascript
const bcrypt = require('bcrypt');
const SALT_ROUNDS = 12; // never less than 10

// Hash
const hash = await bcrypt.hash(password, SALT_ROUNDS);

// Verify (timing-safe)
const valid = await bcrypt.compare(inputPassword, storedHash);
```

---

## AUTH-05: No Rate Limiting on Auth Routes
**Check**: Is there a rate limiter on `/login`, `/register`, `/reset-password`, `/verify-otp`?
**Exploit**: Credential stuffing, brute force, OTP enumeration — all trivially automated.
**Fix**:
```javascript
const rateLimit = require('express-rate-limit');

const authLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 10, // 10 attempts per window
  message: { error: 'Too many attempts, try again later' },
  standardHeaders: true,
  legacyHeaders: false,
});

app.use('/auth', authLimiter);
app.use('/api/login', authLimiter);
```

---

## AUTH-06: Session Not Invalidated on Logout
**Check**: Does logout actually invalidate the token server-side?
**Exploit**: User logs out on shared computer. Old token still works for full session duration.
**Fix**: Use a token blocklist (Redis) or short-lived tokens with refresh rotation.
```javascript
// Redis blocklist approach
await redis.setex(`blocklist:${token}`, tokenTTL, '1');

// On each request
const blocked = await redis.get(`blocklist:${token}`);
if (blocked) return res.status(401).json({ error: 'Token invalidated' });
```

---

## AUTH-07: No Refresh Token Rotation
**Check**: When a refresh token is used, is it immediately invalidated and replaced?
**Exploit**: Stolen refresh token reused indefinitely. Silent account takeover.
**Fix**: On every refresh token use → delete old token, issue new access + refresh pair.

---

## AUTH-08: Permanent Sessions via "Remember Me"
**Check**: Does "remember me" create a session/token with no expiration?
**Exploit**: Any device with cookie access = permanent account access.
**Fix**: Maximum 30-day refresh token, with re-authentication for sensitive actions.

---

## AUTH-09: No MFA on Admin/Sensitive Routes
**Check**: Can admin actions be performed with just a username/password?
**Exploit**: One phished credential = full admin access.
**Fix**: Enforce TOTP (speakeasy/otplib) or passkey for all admin operations.

---

## AUTH-10: Duplicated Auth Logic
**Check**: Is the same auth check copy-pasted across multiple routes?
**Exploit**: Developer updates one, forgets others. Inconsistent protection.
**Fix**: Single middleware function applied at router level.

---

## AUTH-11: Password Reset Token Not Expired/Single-Use
**Check**: Can a password reset link be used multiple times? Does it expire?
**Exploit**: Attacker intercepts link via email history, uses it later.
**Fix**: 
```javascript
// One-time, time-limited token
const token = crypto.randomBytes(32).toString('hex');
const hash = crypto.createHash('sha256').update(token).digest('hex');
// Store hash (not raw token), expire in 10 minutes
await db.passwordReset.create({ tokenHash: hash, expiresAt: Date.now() + 600000, used: false });
```

---

## AUTH-12: User Enumeration via Login Response
**Check**: Does `/login` return different errors for "user not found" vs "wrong password"?
**Exploit**: Attacker can enumerate all valid email addresses.
**Fix**: Always return `"Invalid email or password"` — never differentiate.

---

## AUTH-13: Missing Account Lockout
**Check**: After N failed logins, is the account temporarily locked?
**Exploit**: Unlimited brute-force attempts even with rate limiting (distributed attack).
**Fix**: Lock account for 15 minutes after 5 failed attempts. Alert user via email.

---

## AUTH-14: OAuth State Parameter Missing
**Check**: Does OAuth flow validate the `state` parameter?
**Exploit**: CSRF on OAuth flow — attacker tricks user into linking their account.
**Fix**: Generate random state, store in session, validate on callback.

---

## AUTH-15: Insecure "Magic Link" Implementation
**Check**: Is the magic link token stored as plaintext in the database?
**Exploit**: Database read access = anyone can log in as any user.
**Fix**: Store only the SHA-256 hash of the token. Verify by hashing the incoming token.

---

## AUTH-16: JWT Algorithm Confusion (none/RS256→HS256)
**Check**: Is the JWT `algorithm` explicitly specified? Is `none` algorithm rejected?
**Exploit**: Classic JWT attack — forging tokens by switching algorithm.
**Fix**:
```javascript
jwt.verify(token, secret, { algorithms: ['HS256'] }); // whitelist only
```

---

## AUTH-17: Cookies Missing SameSite/Secure Flags
**Check**: Do auth cookies have `Secure`, `HttpOnly`, and `SameSite=Strict`?
**Exploit**: CSRF attacks, cookie theft over HTTP.
**Fix**: All three flags required. `Secure` means HTTPS only. `SameSite=Strict` blocks CSRF.

---

## AUTH-18: Password Complexity Not Enforced
**Check**: Can users set passwords like `"a"` or `"123456"`?
**Exploit**: Trivially cracked in credential stuffing attacks.
**Fix**: Minimum 8 chars, check against HaveIBeenPwned API. (No complex rules — just length + breach check)

---

## AUTH-19: Email Verification Skipped
**Check**: Can users operate fully without verifying their email?
**Exploit**: Account takeover via email typo, spam abuse, fake accounts.
**Fix**: Gate key actions (payments, sending invites) behind email verification.

---

## AUTH-20: Concurrent Session Not Managed
**Check**: Can a user be logged in from unlimited devices simultaneously?
**Exploit**: Shared credentials, stolen sessions never detected.
**Fix**: Track active sessions, allow users to view and revoke them.

---

## AUTH-21: Admin Bootstrap Vulnerability
**Check**: Is there a route or seed script that creates the first admin user?
**Exploit**: If accessible post-deployment, anyone can create admin account.
**Fix**: First-admin creation behind environment flag, disabled after setup.

---

## AUTH-22: Token Leakage in URL
**Check**: Are auth tokens passed as URL query parameters?
**Exploit**: Logged in access logs, browser history, referrer headers.
**Fix**: Auth tokens in headers or httpOnly cookies only. Never in URLs.

---

## AUTH-23: Dev Credentials in Codebase
**Check**: Any `testuser`/`testpass`, `admin`/`admin123`, hardcoded test accounts?
**Exploit**: Dev backdoor left open in production.
**Fix**: Seed scripts check `NODE_ENV !== 'production'` before creating test users.

---

## AUTH-24: Missing Re-authentication for Sensitive Actions
**Check**: Can users change their email/password/payment method without entering current password?
**Exploit**: Session hijack → change email → full account takeover.
**Fix**: Require current password confirmation for: email change, password change, payment method, account deletion.

---

## AUTH-25: Insufficient Entropy in Token Generation
**Check**: Are tokens generated with `Math.random()`?
**Exploit**: Predictable tokens. Attacker generates the same token space.
**Fix**:
```javascript
// Always use crypto module
const token = crypto.randomBytes(32).toString('hex'); // 256 bits of entropy
```


---

# 📁 FILE: vibe-security-auditor/references/api-checks.md

---

# API SECURITY CHECKS — Full Ruleset (25 Rules)

---

## API-01: Missing Rate Limiting
**Exploit**: Credential stuffing, DDoS, free-tier abuse, scraping, brute force.
**Fix**:
```javascript
const rateLimit = require('express-rate-limit');
// Per-route limits
const apiLimiter = rateLimit({ windowMs: 60000, max: 100 });
const authLimiter = rateLimit({ windowMs: 900000, max: 10 });
app.use('/api/', apiLimiter);
app.use('/auth/', authLimiter);
```

---

## API-02: CORS Misconfiguration
**Check**: `origin: '*'` + `credentials: true` — or overly broad origin whitelist.
**Exploit**: Cross-origin requests with user credentials. Session riding.
**Fix**: Explicit origin whitelist from environment variable.

---

## API-03: API Key Returned After Creation
**Check**: Does `POST /api-keys` return the full raw key?
**Exploit**: Key exposed in logs, browser history, network tab.
**Fix**: Return key ONCE at creation. Store only a bcrypt hash. Show "Copy now — never shown again" UI.

---

## API-04: Webhook Signature Not Verified
**Check**: Any webhook endpoint (Stripe, GitHub, Twilio, Slack) without signature check.
**Exploit**: Attacker POSTs fake events → triggers fulfillment, account creation, etc.
**Fix**: Always verify the signature using the provider's SDK before processing.

---

## API-05: Missing Content-Type Validation
**Check**: Does the API accept requests without checking Content-Type?
**Exploit**: Type confusion attacks, unexpected parsing behavior.
**Fix**:
```javascript
app.use((req, res, next) => {
  if (['POST', 'PUT', 'PATCH'].includes(req.method)) {
    if (!req.is('application/json')) {
      return res.status(415).json({ error: 'Unsupported Media Type' });
    }
  }
  next();
});
```

---

## API-06: No Request Body Size Limit
**Check**: `express.json()` with no `limit` option.
**Exploit**: Send 500MB JSON → server OOM crash (DoS).
**Fix**:
```javascript
app.use(express.json({ limit: '10kb' })); // or '1mb' if uploads expected
app.use(express.urlencoded({ extended: true, limit: '10kb' }));
```

---

## API-07: Stack Traces Returned to Client
**Check**: Error handler returning `err.message` or `err.stack`.
**Exploit**: Internal paths, library versions, SQL queries, business logic exposed.
**Fix**: Log internally with full details, return generic message to client.

---

## API-08: GraphQL Introspection in Production
**Check**: Can you run `{ __schema { types { name } } }` on production endpoint?
**Exploit**: Full schema mapping → targeted attacks on every type and field.
**Fix**:
```javascript
const server = new ApolloServer({
  introspection: process.env.NODE_ENV !== 'production',
  plugins: [process.env.NODE_ENV === 'production' && ApolloServerPluginLandingPageDisabled()]
});
```

---

## API-09: Missing Security Headers
**Check**: Is Helmet.js (or equivalent) installed?
**Exploit**: XSS, clickjacking, MIME sniffing, information leakage.
**Fix**:
```javascript
const helmet = require('helmet');
app.use(helmet()); // sets X-Frame-Options, CSP, HSTS, X-Content-Type-Options etc.
app.use(helmet.contentSecurityPolicy({
  directives: {
    defaultSrc: ["'self'"],
    scriptSrc: ["'self'"],
    styleSrc: ["'self'", "'unsafe-inline'"],
  }
}));
```

---

## API-10: Server-Side Request Forgery (SSRF)
**Check**: Any endpoint that fetches a URL from user input.
**Exploit**: `url=http://169.254.169.254/latest/meta-data/` → AWS metadata → credentials.
**Fix**:
```javascript
const { URL } = require('url');
function isSafeUrl(urlString) {
  const url = new URL(urlString);
  const blocked = ['localhost', '127.0.0.1', '169.254.169.254', '::1'];
  if (blocked.some(b => url.hostname.includes(b))) throw new Error('Blocked URL');
  if (!['http:', 'https:'].includes(url.protocol)) throw new Error('Invalid protocol');
  return url;
}
```

---

## API-11: HTTP Method Not Restricted
**Check**: Can you PUT/DELETE on a GET-only endpoint?
**Exploit**: Unexpected operations on resources.
**Fix**: Use express Router with explicit method definitions. Never `app.all()` for sensitive routes.

---

## API-12: API Versioning Leaves Old Versions Alive
**Check**: Is `/api/v1/` still active with older, less-secure logic?
**Exploit**: Bypass new security controls by hitting old endpoint.
**Fix**: Explicitly remove or redirect old API versions. Audit them on each security release.

---

## API-13: Missing Idempotency on Payment Endpoints
**Check**: Can `POST /charge` be submitted twice?
**Exploit**: Double charging, double fulfillment.
**Fix**: Use idempotency keys (Stripe supports this natively). Implement request deduplication.

---

## API-14: Insecure Direct Object Reference (IDOR) via API
**Check**: Is `/api/invoices/1234` accessible to user who didn't create invoice 1234?
**Exploit**: Sequential ID enumeration → access any user's data.
**Fix**: Always verify ownership. Use UUIDs instead of sequential IDs.

---

## API-15: Missing Pagination Limits
**Check**: Can `GET /users` return the entire users table?
**Exploit**: Data exfiltration, server load, information leakage.
**Fix**:
```javascript
const limit = Math.min(parseInt(req.query.limit) || 20, 100); // max 100
const offset = parseInt(req.query.offset) || 0;
```

---

## API-16: Verbose Error Messages
**Check**: Do errors reveal database names, table names, ORM internals?
**Exploit**: Recon for targeted attacks.
**Fix**: Sanitize all error messages before returning. Never return Mongoose/Prisma/SQL errors raw.

---

## API-17: Missing API Authentication on Internal Endpoints
**Check**: Are there `/internal/`, `/admin/`, `/debug/` routes without auth?
**Exploit**: Direct access bypasses all business logic security.
**Fix**: All routes require auth. Internal routes require additional IP allowlisting.

---

## API-18: Predictable Endpoint Discovery
**Check**: Standard admin paths: `/admin`, `/dashboard`, `/swagger`, `/api-docs`, `/metrics`.
**Exploit**: Attacker scans known paths → finds unprotected admin interfaces.
**Fix**: Disable or password-protect swagger/API docs in production. Move admin to separate subdomain.

---

## API-19: No Request ID / Correlation ID
**Check**: Can you trace a specific request through logs?
**Exploit**: Not a direct exploit, but blind incident response = longer breach windows.
**Fix**:
```javascript
app.use((req, res, next) => {
  req.id = crypto.randomUUID();
  res.setHeader('X-Request-ID', req.id);
  next();
});
```

---

## API-20: Timing Attack on Token Comparison
**Check**: Is token comparison done with `===`?
**Exploit**: Timing differences reveal partial matches → token brute force.
**Fix**:
```javascript
const crypto = require('crypto');
const safe = crypto.timingSafeEqual(Buffer.from(a), Buffer.from(b));
```

---

## API-21: Missing Input Sanitization on Query Parameters
**Check**: Are query params used in regex, HTML, or commands without sanitization?
**Exploit**: ReDoS via crafted regex input, XSS via reflected params.
**Fix**: Validate and type-cast all query params. Never use them in regex or HTML directly.

---

## API-22: Response Contains Excess Data
**Check**: Does `/users/me` return fields like `passwordHash`, `internalNotes`, `isAdmin`?
**Exploit**: Information leakage reveals internal architecture and sensitive data.
**Fix**: Explicitly whitelist response fields. Use DTO/serializer pattern.
```javascript
const safeUser = { id: user.id, name: user.name, email: user.email }; // never spread user
```

---

## API-23: File Path in API Response
**Check**: Do responses include server file paths? (`/var/app/uploads/file.jpg`)
**Exploit**: Reveals server structure. Enables targeted path traversal.
**Fix**: Return relative URLs only. Never expose filesystem paths.

---

## API-24: Missing Audit Log on Sensitive Operations
**Check**: Are admin actions, permission changes, bulk deletes logged?
**Exploit**: Insider threat or compromised account acts without detection.
**Fix**: Log: who, what, when, from where. Store audit logs immutably (separate write-only store).

---

## API-25: Unauthenticated Health/Status Endpoints Leak Info
**Check**: Does `/health` or `/status` expose version numbers, dependencies, DB status?
**Exploit**: Version numbers → known CVE lookup → targeted exploit.
**Fix**: Return minimal health check (`{ "status": "ok" }`). Gate detailed diagnostics behind auth.


---

# 📁 FILE: vibe-security-auditor/references/ai-patterns.md

---

# AI MISTAKE PATTERN LIBRARY
> The definitive catalog of security mistakes AI code generators repeatably make

---

## WHY AI MAKES THESE MISTAKES

AI code generators are trained on tutorial code, Stack Overflow answers, GitHub repos, and documentation examples. These sources:
- Use simplified examples for clarity (real secrets replaced with `"secret"`)
- Omit security hardening for brevity
- Demonstrate features, not production-readiness
- Include deprecated patterns from older versions

This creates **systematic, predictable, repeatable security flaws** in AI-generated code.

---

## PATTERN CATALOG

### 🔴 PATTERN-01: The Demo Secret
**Trigger phrase in prompt**: "set up JWT auth", "add authentication", "protect my routes"
**AI Output**:
```javascript
const jwt = require('jsonwebtoken');
const token = jwt.sign({ userId: user.id }, 'secret', { expiresIn: '24h' });
```
**Why**: Training data is full of tutorials using `'secret'` as a placeholder. AI copies it.
**Real danger**: `'secret'` is in every JWT cracking wordlist on the internet.
**Fix**:
```javascript
const token = jwt.sign({ userId: user.id }, process.env.JWT_SECRET, {
  expiresIn: '15m',
  algorithm: 'HS256'
});
```

---

### 🔴 PATTERN-02: The Bare Stripe Webhook
**Trigger phrase in prompt**: "handle Stripe webhooks", "process payment events"
**AI Output**:
```javascript
app.post('/webhook', express.json(), async (req, res) => {
  const event = req.body; // 🚨 NOT VERIFIED
  if (event.type === 'payment_intent.succeeded') {
    await fulfillOrder(event.data.object);
  }
  res.json({ received: true });
});
```
**Why**: AI shows the logic flow but omits the verification step (most tutorials bury it).
**Real danger**: Anyone can POST fake payment events → free goods/services for attackers.
**Fix**:
```javascript
app.post('/webhook', express.raw({ type: 'application/json' }), (req, res) => {
  const sig = req.headers['stripe-signature'];
  let event;
  try {
    event = stripe.webhooks.constructEvent(req.body, sig, process.env.STRIPE_WEBHOOK_SECRET);
  } catch (err) {
    return res.status(400).send(`Webhook Error: ${err.message}`);
  }
  // Now handle event.type safely
});
```

---

### 🔴 PATTERN-03: The Missing Owner Check
**Trigger phrase**: "update user profile", "edit post", "delete item", "CRUD for..."
**AI Output**:
```javascript
app.put('/posts/:id', authenticate, async (req, res) => {
  const post = await Post.findByIdAndUpdate(req.params.id, req.body);
  res.json(post);
});
```
**Why**: AI implements the "happy path" — authenticated user can update. Misses authorization.
**Real danger**: Any logged-in user can edit or delete anyone else's content by changing the ID.
**Fix**:
```javascript
app.put('/posts/:id', authenticate, async (req, res) => {
  const post = await Post.findById(req.params.id);
  if (!post) return res.status(404).json({ error: 'Not found' });
  if (post.userId.toString() !== req.user.id) {
    return res.status(403).json({ error: 'Forbidden' });
  }
  await post.updateOne({ $set: allowedFields(req.body) });
  res.json(post);
});
```

---

### 🔴 PATTERN-04: Open Supabase/Firebase
**Trigger phrase**: "set up Supabase", "Firebase database rules", "realtime database"
**AI Output** (Supabase):
```sql
-- AI often generates permissive policies or skips RLS entirely
ALTER TABLE users DISABLE ROW LEVEL SECURITY;
```
**AI Output** (Firebase):
```json
{
  "rules": {
    ".read": true,
    ".write": true
  }
}
```
**Why**: AI prioritizes getting things working over securing them. Default-open is easier.
**Real danger**: Anyone on the internet can read/write your entire database.
**Fix** (Supabase):
```sql
ALTER TABLE posts ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Users can only see their own posts"
  ON posts FOR SELECT
  USING (auth.uid() = user_id);
```
**Fix** (Firebase):
```json
{
  "rules": {
    "users": {
      "$uid": {
        ".read": "$uid === auth.uid",
        ".write": "$uid === auth.uid"
      }
    }
  }
}
```

---

### 🔴 PATTERN-05: Mass Assignment
**Trigger phrase**: "create user endpoint", "registration API", "save form data"
**AI Output**:
```javascript
app.post('/users', async (req, res) => {
  const user = await User.create(req.body); // 🚨 trusts all fields
  res.json(user);
});
```
**Why**: AI takes the shortest path. `req.body` → database feels natural.
**Real danger**: User sends `{ "isAdmin": true, "credits": 99999 }` → instant privilege escalation.
**Fix**:
```javascript
const { name, email, password } = req.body; // whitelist fields explicitly
const user = await User.create({ name, email, password: await bcrypt.hash(password, 12) });
```

---

### 🟠 PATTERN-06: The Debug Console.log
**Trigger phrase**: Any debugging session, "why isn't this working", "add logging"
**AI Output**:
```javascript
app.post('/login', async (req, res) => {
  console.log('Login attempt:', req.body); // 🚨 logs password
  console.log('User found:', user); // 🚨 logs full user object
  const token = jwt.sign({ id: user.id }, 'secret');
  console.log('Token:', token); // 🚨 logs the token
```
**Why**: AI adds debugging statements to explain its work, then forgets to remove them.
**Real danger**: Passwords and tokens in logs → log aggregation service breach = account takeovers.

---

### 🟠 PATTERN-07: CORS Wildcard with Credentials
**Trigger phrase**: "fix CORS error", "allow my frontend to connect", "CORS setup"
**AI Output**:
```javascript
app.use(cors({ origin: '*', credentials: true }));
```
**Why**: `*` fixes the CORS error immediately. AI prioritizes solving the stated problem.
**Real danger**: This combination is invalid (browsers reject it), but AI often pairs it with other vulnerabilities. When "fixed" to a specific origin, AI often forgets credentials: true means cookies are sent cross-origin.
**Fix**:
```javascript
app.use(cors({
  origin: process.env.ALLOWED_ORIGINS?.split(',') || ['http://localhost:3000'],
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));
```

---

### 🟠 PATTERN-08: SQL Injection via Template Literals
**Trigger phrase**: "search endpoint", "filter by name", "query by user input"
**AI Output**:
```javascript
const results = await db.query(
  `SELECT * FROM users WHERE name LIKE '%${searchTerm}%'`
);
```
**Why**: Template literals are idiomatic JavaScript. AI doesn't distinguish safe from unsafe context.
**Real danger**: Classic SQL injection. `'; DROP TABLE users; --` still works in 2025.
**Fix**:
```javascript
const results = await db.query(
  'SELECT * FROM users WHERE name LIKE $1',
  [`%${searchTerm}%`]
);
```

---

### 🟠 PATTERN-09: The Exposed Next.js Secret
**Trigger phrase**: "add API key to Next.js", "environment variable in Next"
**AI Output**:
```javascript
// .env
NEXT_PUBLIC_STRIPE_SECRET_KEY=sk_live_xxxx // 🚨 exposed to browser
NEXT_PUBLIC_DATABASE_URL=postgres://... // 🚨 exposed to browser
```
**Why**: AI sees `NEXT_PUBLIC_` as the standard env pattern. Doesn't distinguish which vars should be public.
**Real danger**: `NEXT_PUBLIC_` variables are bundled into client JavaScript. Anyone can read them.
**Fix**: Only `NEXT_PUBLIC_` the publishable key. Secret keys never get `NEXT_PUBLIC_`.

---

### 🟠 PATTERN-10: No Input Validation Schema
**Trigger phrase**: Any API route generation
**AI Output**:
```javascript
app.post('/api/orders', authenticate, async (req, res) => {
  const { productId, quantity, address } = req.body;
  await Order.create({ productId, quantity, address, userId: req.user.id });
```
**Why**: AI focuses on business logic, not defensive validation.
**Real danger**: Negative quantity orders, non-existent productIds, script tags in address.
**Fix**:
```javascript
const { z } = require('zod');
const orderSchema = z.object({
  productId: z.string().uuid(),
  quantity: z.number().int().positive().max(100),
  address: z.string().min(10).max(500)
});

app.post('/api/orders', authenticate, async (req, res) => {
  const parsed = orderSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  await Order.create({ ...parsed.data, userId: req.user.id });
```

---

### 🔵 PATTERN-11: Missing Error Boundary / Crash Exposure
**AI Output**:
```javascript
app.get('/user/:id', async (req, res) => {
  const user = await User.findById(req.params.id);
  res.json(user.toJSON()); // 🚨 crashes if user is null
});
```
**Fix**: Always null-check, always have a global error handler:
```javascript
app.use((err, req, res, next) => {
  console.error(err); // log internally
  res.status(500).json({ error: 'Internal server error' }); // never expose err.message
});
```

---

### 🔵 PATTERN-12: Forgotten Source Maps
**AI Output**: Default Next.js/CRA/Vite config with source maps enabled.
**Real danger**: Production source maps expose your full application source code.
**Fix** (Next.js):
```javascript
// next.config.js
module.exports = {
  productionBrowserSourceMaps: false, // default false, but verify
};
```

---

## FRAMEWORK-SPECIFIC AI FAILURE MODES

### Next.js
- API routes without authentication middleware
- `getServerSideProps` returning sensitive data to client props
- `NEXT_PUBLIC_` on secret keys
- Missing `next-rate-limit` or equivalent on API routes

### Express
- `app.use(express.json())` with no size limit
- CORS wildcard
- Missing helmet
- Routes added after error handlers

### Firebase
- Open security rules
- Client SDK with full admin access
- Security rules not tested

### Supabase
- RLS disabled on tables
- Anon key used server-side where service role needed
- Missing RLS policies on JOIN tables

### Stripe
- Unverified webhooks
- Storing raw card data
- Test keys in production
- Not checking payment status before fulfilling

### MongoDB/Mongoose
- `Model.find(req.body)` — NoSQL injection
- Missing `lean()` on read-only queries
- Unindexed fields in frequent queries


---

# 📁 FILE: vibe-security-auditor/references/extended-checks.md

---

# INPUT VALIDATION CHECKS (20 Rules)

## INPUT-01: No Schema Validation
Use Zod/Joi/Yup on EVERY API route. No exceptions.
```javascript
const schema = z.object({ email: z.string().email(), age: z.number().int().min(0).max(150) });
const result = schema.safeParse(req.body);
if (!result.success) return res.status(400).json({ errors: result.error.flatten() });
```

## INPUT-02: SQL Injection via String Concatenation
Never: `` `SELECT * FROM users WHERE email = '${email}'` ``
Always: parameterized queries — `db.query('SELECT * FROM users WHERE email = $1', [email])`

## INPUT-03: req.body Passed Directly to DB
Never `Model.create(req.body)`. Always whitelist: `const { name, email } = req.body; Model.create({ name, email })`.

## INPUT-04: req.params.id Without Type Check
Always validate: `const id = parseInt(req.params.id); if (isNaN(id)) return res.status(400)...`
For UUIDs: validate against UUID regex before use.

## INPUT-05: Path Traversal in File Operations
Never: `fs.readFile('./uploads/' + req.params.filename)`
Always: 
```javascript
const safePath = path.join(__dirname, 'uploads', path.basename(req.params.filename));
if (!safePath.startsWith(path.join(__dirname, 'uploads'))) throw new Error('Invalid path');
```

## INPUT-06: Catastrophic Regex (ReDoS)
Never use user input to construct regex. Validate regex patterns. Use timeout wrappers for complex regex.

## INPUT-07: JSON.parse Without Try/Catch
Always:
```javascript
try {
  const data = JSON.parse(rawInput);
} catch (e) {
  return res.status(400).json({ error: 'Invalid JSON' });
}
```

## INPUT-08: NoSQL Injection (MongoDB)
Never: `User.findOne({ email: req.body.email })` when email could be `{ "$gt": "" }`
Fix: `User.findOne({ email: String(req.body.email) })` — cast to expected type.

## INPUT-09: XML/SVG Injection
If accepting XML or SVG: use a strict parser with entity expansion disabled. Never render user-provided SVG directly.

## INPUT-10: Prototype Pollution
If using `_.merge()`, `Object.assign()`, or deep cloning with user input — validate no `__proto__` or `constructor` keys.
```javascript
function sanitize(obj) {
  const dangerous = ['__proto__', 'constructor', 'prototype'];
  dangerous.forEach(k => delete obj[k]);
  return obj;
}
```

## INPUT-11: Email Header Injection
If building email from user input, sanitize newlines: `email.replace(/[\r\n]/g, '')`

## INPUT-12: HTML Injection / XSS via Stored Input
Sanitize HTML on write, not just on display. Use DOMPurify server-side (jsdom) for stored HTML content.

## INPUT-13: Integer Overflow / Type Coercion
`"999999999999999999"` as quantity can break payment math. Always bound-check numbers and use proper decimal libraries for money.

## INPUT-14: Null Byte Injection
`filename.pdf%00.php` can confuse file type detection. Strip null bytes: `str.replace(/\0/g, '')`

## INPUT-15: Unicode Normalization Attacks
Normalize unicode before comparison: `str.normalize('NFC')`. Prevents homograph attacks on usernames.

## INPUT-16: Open Redirect
Never: `res.redirect(req.query.next)`
Always: validate redirect is relative or on allowlist:
```javascript
const allowed = ['/dashboard', '/profile', '/settings'];
const next = allowed.includes(req.query.next) ? req.query.next : '/dashboard';
```

## INPUT-17: Command Injection
Never use `exec()`, `spawn()` with user input. If needed, use `execFile()` with argument array (never shell: true).

## INPUT-18: CRLF Injection in Headers
Never set response headers from user input without sanitization. Strip `\r\n` from any header values.

## INPUT-19: Large Array / Object DoS
`{ "items": [... 10 million items ...] }` can crash JSON.parse. Add body size limit AND schema max lengths.

## INPUT-20: Date Injection
`"2099-99-99"` or negative timestamps can crash date libraries. Always validate dates:
```javascript
const date = new Date(input);
if (isNaN(date.getTime())) return res.status(400).json({ error: 'Invalid date' });
```

---

# AUTHORIZATION CHECKS (20 Rules)

## AUTHZ-01: User-Controlled Privilege Flag
Never: `if (req.body.isAdmin)` — user sends `{ "isAdmin": true }` in any request body.
Always read role/permissions from the auth token or database, never from request payload.

## AUTHZ-02: User ID From Request Body
Never: `const userId = req.body.userId` for determining ownership.
Always: `const userId = req.user.id` from validated JWT/session.

## AUTHZ-03: Missing Ownership Validation
Every `GET/PUT/DELETE /:id` must verify the resource belongs to `req.user.id`.

## AUTHZ-04: Frontend-Only Route Guard
React Router guards (`<PrivateRoute>`) are decorative. The API must enforce auth independently.

## AUTHZ-05: Sequential ID Enumeration (IDOR)
Use UUIDs for user-facing IDs. Always check ownership regardless.

## AUTHZ-06: Role in Mutable JWT Payload
If user role is in JWT and user can call a route to change their role, that's privilege escalation.
Never store role in client-modifiable JWT without server-side validation.

## AUTHZ-07: Middleware Not Applied to All Sub-Routes
```javascript
// WRONG — middleware only on router, not sub-routers added later
router.use(authenticate);
router.use('/admin', adminRouter); // adminRouter bypasses authenticate

// RIGHT
adminRouter.use(authenticate);
adminRouter.use(requireAdmin);
```

## AUTHZ-08: Broken Object Level Authorization (BOLA)
Different from IDOR — relates to nested resources.
`GET /companies/:companyId/invoices/:invoiceId` — verify user belongs to companyId AND invoiceId belongs to companyId.

## AUTHZ-09: Admin Endpoint Without Admin Check
`/api/admin/users` authenticated but not checking `req.user.role === 'admin'`.

## AUTHZ-10: Soft-Deleted Resource Still Accessible
If using soft deletes (`deletedAt` field), every query must include `WHERE deletedAt IS NULL`.

## AUTHZ-11: Sharing/Invite Bypass
Sharing link allows access without auth check on the underlying resource's permission model.

## AUTHZ-12: Privilege Escalation via Team Membership
User can add themselves to any team → inherits that team's permissions.

## AUTHZ-13: Read vs Write Permission Not Separated
User with read access can call write endpoints. Separate `canRead` from `canWrite` in permission checks.

## AUTHZ-14: Exported Data Not Access-Controlled
`/api/export/users.csv` checked for auth but not for scope — exports all users, not just the requester's.

## AUTHZ-15: Batch Operation Authorization
`DELETE /posts` with list of IDs — checks if user is authenticated but not if they own all the IDs.

## AUTHZ-16: Service Account Over-Privileged
Internal microservice credentials with admin access used for read-only operations. Principle of least privilege.

## AUTHZ-17: Path-Based Auth Bypass
`/api/v1/admin/users` is protected. `/api/v1/admin/../admin/users` bypasses via path normalization.
Fix: Normalize paths before auth checks.

## AUTHZ-18: GraphQL Field-Level Authorization Missing
Checking auth at resolver level but not at field level — sensitive fields accessible via introspection + direct query.

## AUTHZ-19: Webhook Payload Triggers Admin Action Without Auth
Webhook from third-party triggers privileged action without verifying the webhook's identity scope.

## AUTHZ-20: Rate Limit Applied Per Endpoint, Not Per User
Attacker uses 100 IPs → bypasses per-IP rate limit. Auth routes need per-account rate limiting.

---

# DATABASE SAFETY CHECKS (15 Rules)

## DB-01: Unprotected Mass Delete
`DELETE FROM table WHERE condition` — always require explicit WHERE and ownership check.
Never allow `DELETE FROM users` without a specific ID.

## DB-02: Unhandled Null from DB Query
`const user = await User.findById(id); user.email` — crashes if user is null. Always null-check.

## DB-03: Mass Assignment (covered in INPUT, emphasized for DB layer)
Never `Model.create(req.body)`. Always destructure expected fields.

## DB-04: Missing Transactions for Multi-Step Operations
Payment deducted → order creation fails → money gone, no order.
```javascript
const session = await mongoose.startSession();
session.startTransaction();
try {
  await deductBalance(..., { session });
  await createOrder(..., { session });
  await session.commitTransaction();
} catch (err) {
  await session.abortTransaction();
  throw err;
}
```

## DB-05: Supabase/Firebase RLS Disabled
Every table needs RLS. Verify with: `SELECT tablename, rowsecurity FROM pg_tables WHERE schemaname = 'public';`

## DB-06: Connection String in Source Code
Use environment variables. Rotate immediately if ever committed.

## DB-07: Missing Indexes on Queried Fields
Every foreign key, `userId`, `email`, `createdAt` (for sorting) needs an index. Profile slow queries.

## DB-08: Mongoose Query Not Executed
`User.find({ active: true })` without `.exec()` or `await` — query built but never runs. Silent data access failure.

## DB-09: N+1 Query Problem
Fetching 100 users then querying each user's orders separately = 101 queries.
Use `.populate()` (Mongoose) or JOIN (SQL) or `include` (Prisma).

## DB-10: Unbounded Query
`User.find({})` with no limit in production → returns entire table → OOM, data exfil.
Always add `.limit(100)` or pagination.

## DB-11: Plaintext Sensitive Data in DB
PII, SSNs, payment info stored unencrypted. Encrypt at application layer for highest-sensitivity fields.

## DB-12: Missing Soft Delete Pattern for Audit Trail
Hard deleting records removes audit ability. Use `deletedAt` timestamp for reversibility.

## DB-13: Race Condition on Unique Constraint
Check-then-insert without DB-level unique constraint → duplicate records under load.
Always use DB-level unique constraints, not just application-level checks.

## DB-14: Prisma/ORM Raw Query with User Input
`prisma.$queryRaw` or `sequelize.query()` with template literals and user input = SQL injection.

## DB-15: Backup Not Verified / Tested
A backup that's never been restored is not a backup. Test restoration quarterly.

---

# LOGGING & ERROR HANDLING CHECKS (15 Rules)

## LOG-01: Stack Trace to Client
`res.status(500).json({ error: err.stack })` — never. Log internally, return generic message.

## LOG-02: Logging req.body
Contains passwords, tokens, PII. Never log full request bodies. Redact sensitive fields.
```javascript
const safeBody = { ...req.body, password: '[REDACTED]', token: '[REDACTED]' };
```

## LOG-03: Logging PII Without Compliance
Logging emails, IPs, names requires GDPR/CCPA compliance — retention policies, right to erasure.

## LOG-04: No Global Error Handler
Unhandled promises crash Node.js process. Always:
```javascript
process.on('unhandledRejection', (reason) => { logger.error(reason); });
app.use((err, req, res, next) => { logger.error(err); res.status(500).json({ error: 'Server error' }); });
```

## LOG-05: Empty Catch Blocks
`catch(e) {}` silently swallows errors. Always log at minimum: `catch(e) { logger.error(e); throw e; }`

## LOG-06: Sensitive Values in Log Messages
`logger.info(`User ${user.id} reset password with token ${token}`)` — token in logs.

## LOG-07: Logs Written to App Directory
Logs in app directory can fill disk → DoS. Use stdout/stderr → log aggregator.

## LOG-08: No Structured Logging
`console.log('User logged in: ' + userId)` — unsearchable. Use JSON structured logging (pino, winston).

## LOG-09: Debug Logging Enabled in Production
`morgan('dev')` in production leaks request details. Use `morgan('combined')` with log aggregation.

## LOG-10: Error Messages Reveal DB Schema
"Column 'password' not found in table 'users'" — reveals table and column names.

## LOG-11: No Alerting on Security Events
Failed logins, permission denied errors, rate limit hits should trigger alerts, not just log.

## LOG-12: Log Injection
`logger.info(req.query.search)` — attacker inserts fake log lines: `\nINFO: Admin login successful for user root`.
Sanitize log input: strip newlines, validate types.

## LOG-13: Monitoring Dashboard Unauthenticated
`/metrics` (Prometheus), `/actuator` (Spring), `/_debug` exposed publicly.

## LOG-14: No Correlation Between Frontend and Backend Errors
User reports "something went wrong" — no way to find the corresponding server error.
Use `X-Request-ID` header, log it everywhere.

## LOG-15: Insufficient Log Retention
Logs deleted before incident investigation window. Minimum 90 days. 1 year for regulated industries.

---

# INFRASTRUCTURE & DEPLOYMENT CHECKS (Selected Critical Rules)

## INFRA-01: .env in Git
Check `.gitignore` includes `.env`, `.env.*`, `!.env.example`.
If ever committed: rotate ALL secrets immediately. Git history cannot be trusted.

## INFRA-02: NODE_ENV Not Set to Production
Development mode enables verbose errors, disables security features.
Always set `NODE_ENV=production` in deployment. Verify: `echo $NODE_ENV`.

## INFRA-03: HTTP Allowed in Production
Redirect all HTTP to HTTPS at load balancer/CDN level. Set HSTS header.

## INFRA-04: Default Credentials
Check: DB passwords, Redis auth, admin panels, cloud consoles.
Never accept default passwords in production provisioning.

## INFRA-05: Missing Content Security Policy
Add via Helmet: prevents XSS by restricting script sources.

## INFRA-06: Docker Running as Root
```dockerfile
# Add to Dockerfile
RUN addgroup -S app && adduser -S app -G app
USER app
```

## INFRA-07: Secrets in Environment Variables Logged at Startup
Many apps log all env vars on startup. Filter: `Object.keys(process.env).filter(k => !k.includes('SECRET') && !k.includes('KEY'))`

## INFRA-08: Overprivileged Cloud IAM
Lambda/EC2 with `*` permissions. Use least-privilege IAM roles. Audit with AWS Access Analyzer.

## INFRA-09: No DDoS Protection
Ensure CDN (Cloudflare, etc.) is in front of origin. Origin IP must not be publicly discoverable.

## INFRA-10: Ports Open That Shouldn't Be
DB port (5432, 3306, 27017) should NEVER be publicly accessible. Audit security groups.

## INFRA-11: Missing WAF
Web Application Firewall catches common attacks before they hit your app.
Cloudflare WAF free tier covers most common rules.

## INFRA-12: Unencrypted Data at Rest
Enable encryption at rest for all databases, storage buckets, disk volumes.

## INFRA-13: No Backup Policy
Automated daily backups. Retention 30 days minimum. Test restore quarterly.

## INFRA-14: Secrets in CI/CD Logs
`echo $DATABASE_URL` in CI pipeline → URL visible in build logs.
Audit all CI scripts for secret printing.

## INFRA-15: Missing Dependency Vulnerability Scanning
Add to CI: `npm audit --audit-level=high` or Snyk. Fail build on critical CVEs.


