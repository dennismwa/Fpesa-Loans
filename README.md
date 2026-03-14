# Fpesa - Loan Management Platform

Production-ready fintech loan platform built with PHP 8+, MySQL, Tailwind CSS.

## Complete File Structure (52 files)

```
fpesa/
├── .htaccess                  # Apache config, clean URLs, security, caching
├── index.php                  # Landing page (8 sections + calculators)
├── install.php                # One-time DB installer (DELETE after use)
├── database.sql               # Full schema (14 tables + default data)
├── robots.txt                 # SEO robots
├── sitemap.xml.php            # Dynamic sitemap generator
│
├── config/
│   ├── database.php           # PDO singleton connection
│   └── helpers.php            # All utilities (auth, CSRF, EMI, wallet, uploads, etc.)
│
├── includes/
│   ├── header.php             # HTML head, Tailwind, fonts, all CSS
│   ├── navbar.php             # Responsive nav, loan dropdown, mobile menu
│   └── footer.php             # Footer, social links, scripts init
│
├── auth/
│   ├── login.php              # User sign in with validation
│   ├── register.php           # Registration + wallet creation
│   └── logout.php             # Session destroy
│
├── pages/
│   ├── loans.php              # All loan types grid
│   ├── loan-details.php       # Individual loan type + calculator
│   ├── loan-calculator.php    # Standalone calculator
│   ├── apply-loan.php         # Full application form + EMI preview
│   ├── about.php              # About us + stats
│   ├── contact.php            # Contact form + info
│   ├── terms.php              # Terms & conditions
│   ├── privacy.php            # Privacy policy
│   ├── 404.php                # Not found page
│   └── 500.php                # Server error page
│
├── user/
│   ├── layout.php             # Dashboard sidebar + topbar + notifications
│   ├── layout_footer.php      # Layout closing
│   ├── dashboard.php          # Stats cards + upcoming payments + recent apps
│   ├── my-loans.php           # Loan list + detail view + installment schedule
│   ├── loan-status.php        # Application tracking + fee payment links
│   ├── wallet.php             # Balance card + transaction history
│   ├── payments.php           # Pay fees + submit payments + history
│   ├── documents.php          # Upload signed docs + download agreements
│   └── profile.php            # Edit profile + change password
│
├── admin/
│   ├── login.php              # Admin sign in (dark theme)
│   ├── logout.php             # Admin session destroy
│   ├── layout.php             # Dark sidebar + all nav modules
│   ├── layout_footer.php      # Layout closing
│   ├── dashboard.php          # 6 stat cards + recent apps + payments
│   ├── users.php              # Search, list, view, edit, toggle, delete
│   ├── loan-types.php         # Full CRUD with icon/rate/term config
│   ├── applications.php       # Review, approve (→ create loan + disburse), reject
│   ├── loans.php              # View loans + mark installments paid
│   ├── payments.php           # Confirm/reject payments + screenshot view
│   ├── wallets.php            # View all wallets + adjust balances
│   ├── payment-methods.php    # CRUD paybill/till/bank/cash methods
│   ├── settings.php           # Site name, logo, colors, fees, contacts, SEO
│   ├── documents.php          # View all + download + delete
│   └── reports.php            # Stats, metrics, loan type breakdown, CSV export
│
├── api/
│   └── index.php              # AJAX endpoints (calculator, notifications)
│
├── pwa/
│   ├── manifest.json          # PWA manifest
│   └── service-worker.js      # Offline caching
│
├── assets/
│   └── js/app.js              # Common JS utilities
│
└── uploads/                   # User file uploads
    ├── agreements/
    ├── documents/
    ├── avatars/
    └── logos/
```

## Deployment on cPanel

1. **Upload** all files from `fpesa/` to `public_html/`
2. **Create MySQL database** `vxjtgclw_loans` with user `vxjtgclw_loans`
3. **Grant ALL PRIVILEGES** to the user on the database
4. **Visit** `https://yourdomain.com/install.php` to create tables
5. **DELETE** `install.php` and `database.sql` after setup
6. **Login** at `/admin/login.php` → `admin@fpesa.co.ke` / `password`
7. **Change admin password** immediately
8. **Configure** Settings → site name, logo, colors, fees, contacts, SEO

## Working Features

### Public Website
- Hero with animated loan estimate calculator
- 7 loan type cards with detail pages + individual calculators
- How it works (4 steps), benefits, Lipa Mdogo Mdogo section
- Testimonials, FAQ with accordion, CTA
- Full loan calculator page
- About, Contact (with form), Terms, Privacy
- SEO: meta tags, OpenGraph, sitemap, robots.txt
- PWA: installable, offline support
- Mobile responsive throughout

### User Dashboard
- Stats cards (active loans, borrowed, repaid, wallet)
- Apply for loans → auto EMI calculation → fee payment flow
- Track all applications with status badges
- View loans with full installment schedule + progress ring
- Wallet with credit/debit transaction history
- Submit payments with M-Pesa/bank ref + screenshot upload
- Upload signed agreements, download loan documents
- Edit profile + change password

### Admin Panel
- 6 KPI stat cards on dashboard
- **Users**: search, list, view detail, edit, toggle active/inactive, delete
- **Loan Types**: full CRUD with icon, rate, amount range, term range
- **Applications**: filter by status, review detail, approve (→ auto creates loan + installments + disburses to wallet), reject with comments
- **Loans**: view detail + mark individual installments as paid, close loans
- **Payments**: confirm/reject with screenshot viewer
- **Wallets**: view all balances + credit/debit adjustment modal
- **Payment Methods**: CRUD for paybill/till/bank/cash with instructions
- **Settings**: site name, logo, colors, fees, contacts, social links, SEO meta
- **Documents**: view all uploaded docs, download, delete
- **Reports**: financial summary, key metrics, loan type breakdown, CSV export (loans/payments/users)

### Security
- CSRF tokens on every form
- Prepared SQL statements (no SQL injection)
- Password hashing (bcrypt)
- Session-based authentication
- Input sanitization (htmlspecialchars)
- Upload file type/size validation
- .htaccess security headers + directory protection

## Tech Stack
- PHP 8+ (no frameworks, no build tools)
- MySQL with PDO
- Tailwind CSS via CDN
- Vanilla JavaScript
- Lucide Icons
- AOS Animations
- Google Fonts (DM Sans + Outfit)
- Apache (.htaccess)
