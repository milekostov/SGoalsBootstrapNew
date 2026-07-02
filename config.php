<?php
/**
 * Smooth Goals — Central configuration.
 *
 * SECURITY: This file should live ABOVE the web root (outside public_html).
 *           If it must stay inside the web root, the .htaccess in this directory
 *           blocks direct browser access to it. Move it as soon as you can:
 *
 *           1. Upload config.php to /home/smoothgoalscom/config.php
 *           2. In connect.php and book-appointment.php, change the require path:
 *              require __DIR__ . '/../config.php';
 *              →  require '/home/smoothgoalscom/config.php';
 */

// ── Database (least-privilege user, scoped to smoothgoalscom_formUser) ──
define('DB_HOST', 'localhost');
define('DB_USER', 'smoothgoalscom_form');
define('DB_PASS', 'dX=fjtmg)~WHP0$o');
define('DB_NAME', 'smoothgoalscom_formUser');

// ── SMTP (fill these in from cPanel → Email Accounts → info@smoothgoals.com) ──
define('SMTP_HOST', 'mail.smoothgoals.com');   // usually mail.smoothgoals.com
define('SMTP_PORT', 465);                       // 465 for SSL, 587 for TLS
define('SMTP_USER', 'info@smoothgoals.com');    // the mailbox login
define('SMTP_PASS', '1ec*MP_b&~x52[vg');                        // ⬅ FILL THIS IN — the mailbox password

// ── Mail identity ──
define('MAIL_FROM_ADDR', 'info@smoothgoals.com');
define('MAIL_FROM_NAME', 'Smooth Goals');
define('MAIL_OWNER',     'smoothgoals@gmail.com');
