# Smooth Goals — Project Handoff

Static marketing site for **smoothgoals.com** (goal-coaching for entrepreneurs by
"Michael K." — a pen name; never use the real name in public copy or JSON-LD).

Plain **HTML + CSS + JS + PHP** on cPanel/Apache shared hosting. **No build step, no
framework** beyond Bootstrap 5.0.2 (CDN). Edit files directly and upload via cPanel File
Manager.

---

## Working directory
`C:\Users\Admin\Desktop\SGoalsBootstrapNew` → deploys to `public_html/` on the server.

---

## Design system (already established — match it exactly)

- **Fonts:** Montserrat (Google Fonts) + Bootstrap 5.0.2 via CDN. Single
  `bootstrap.bundle.min.js` (NOT separate Popper; NOT jQuery — both removed).
- **Colors (CSS variables in `css/styles.css`):**
  - Warm black base `--sg-black #1c1a18`, darker `--sg-black-2 #161412`, nav `--sg-nav #0e0d0c`
  - **Red `--sg-accent #c0392b` is CTA-ONLY** — red appears at most ONCE in a page body
    (the primary "Book a Free Call" button). The nav CTA is the standard exception.
  - **Gold `--sg-gold #c9a227`** = premium accents, borders, flagship treatment.
  - Cream `--sg-cream #faf6ee` for light sections.
- **Shared classes:** `sg-nav`, `sg-brand`, `btn-sg-accent` (red), `btn-sg-ghost`,
  `btn-sg-dark`, `btn-sg-gold`, `btn-sg-outline`, `sg-section` (+`-cream`/`-dark`/`-darker`),
  `sg-label`, `sg-section-title`, `sg-text-light`, `sg-text-muted`, `sg-page-hero`
  (image + scrim page header), `sg-footer`.
- **All new component CSS** goes in `css/styles.css` under clearly commented sections.
  Do not break existing homepage styles.

### Canonical nav (identical on every page)
Home · Books · Coaching · Free Course · Guides · Contact · **[Book a Free Call]**
- "Guides" → `https://smoothgoals.com/blog/` (the live WordPress blog)
- CTA "Book a Free Call" → `schedule.html`
- Each page marks its own item `active` / `aria-current="page"`.

### Canonical footer
`sg-footer` with 3 inline-SVG social icons (Facebook / Instagram / LinkedIn) and
`© <year> Smooth Goals · Michael K.` — newer pages render the year dynamically via a
`#sg-year` span + small inline script.

---

## Pages (all refactored onto the design system)

| File | Purpose | Active nav item |
|------|---------|-----------------|
| `index.html` | Homepage (hero carousel, books, free-course email capture, coaching) | Home |
| `about.html` | Coaching — 3 pricing tiers (Optimal featured) | Coaching |
| `book.html` | Books — long-form Sprint + Mastery (flagship) | Books |
| `free.html` | Free tools hub: Wheel of Life, method videos, excerpt, course placeholder | Free Course |
| `contact.html` | Contact form + trust strip (photo of Michael K.) | Contact |
| `schedule.html` | Calendly-style booking tool (JS calendar → `book-appointment.php`) | — |
| `confirm.html` | Brevo double-opt-in landing ("check your inbox"); `noindex` | — |
| `indexWheel.html` | Wheel of Life interactive tool. **DIFFERENT nav (logo only).** Do NOT force the standard nav on it. Uses `wheeljs/` + `wheelcss/`. | — |

---

## Backend (PHP)

- **`config.php`** holds DB + SMTP credentials as constants. **Lives ABOVE web root** at
  `/home/smoothgoalscom/config.php` (NOT in public_html). PHP files require it by that
  absolute path. An `.htaccess` also denies web access to `config.php` as a backstop.
- **`connect.php`** — contact form handler (writes `userReg` table, sends mail).
- **`book-appointment.php`** — booking handler (writes `bookings` table, sends mail).
- **`mailer.php`** — PHPMailer wrapper `sg_send_mail()` over authenticated SMTP
  (info@smoothgoals.com). PHPMailer is vendored at `vendor/phpmailer/src/`.
- DB user is least-privilege `smoothgoalscom_form` scoped to `smoothgoalscom_formUser`.
- Both handlers keep: honeypot (`website`) + time-delay spam checks, prepared statements,
  server-side error logging (never echo SQL/connection errors to users).
- **Leads arrive at `smoothgoals@gmail.com`** and in DB tables `userReg` / `bookings`
  (view in phpMyAdmin).

## Email signup (Brevo)
Homepage "Momentum Method" form POSTs to Brevo. Email field MUST be `name="EMAIL"`; hidden
`email_address_check` (honeypot) + `locale` fields required. After submit, Brevo redirects
to `confirm.html` (set this in Brevo). The course itself is still "coming soon" (placeholder
slot in `free.html`).

---

## Conventions / guardrails
- Match the existing nav/footer/design exactly on any new page. Reuse classes; don't invent
  colors, fonts, or pull in Bootstrap components beyond what's used.
- Red exactly once per page body (the CTA). Gold for everything "premium".
- Pen name **"Michael K."** in all visible copy + JSON-LD.
- `lang="en"`, one `<h1>` per page, semantic headings, alt text, WCAG AA contrast,
  mobile-first single-column.
- Do NOT touch `indexWheel.html` / `wheeljs/` / `wheelcss/` unless explicitly asked.
- Do NOT change DB/SMTP credential VALUES (already correct and working).

---

## Known open items / flags
- **Book-name inconsistency:** older copy referenced "Goals Manual"; current books are
  "Goal Mastery" and "7-Day Goal Sprint". `free.html` video captions were softened to
  "the book" to avoid naming a non-existent title — confirm the intended title.
- **Excerpt cover image:** `free.html` excerpt uses `images/FrontCover.jpg` (exists on
  server per owner). Other pages use `cover-mastery.jpg` / `cover-sprint.jpg`.
- **Momentum Method course** content + live gated booklet not built yet (placeholder in
  `free.html`).
- `indexWheel.html` head/meta + nav never refactored to the new system (intentional).

---

## How to deploy
Edit locally, then upload changed files via cPanel File Manager to `public_html/`
(keeping the same folder structure). `config.php` stays at `/home/smoothgoalscom/`.
PHPMailer's 3 files live in `vendor/phpmailer/src/`.
