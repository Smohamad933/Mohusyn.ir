# MOHUSYN — Personal Website + CMS

**Mohusyn (Seyyed Mohammad Hossein Sheikholeslami)** personal website — pure **PHP** for **IIS**, no database required.

## Features

- 🌍 **English-only public site** with an hdshy-inspired minimal design:
  - `/` landing — big portfolio cover grid, collaborators, numbered experience,
    numbered capabilities, skills pills marquee, **Current Availability** card,
    recent writing
  - `/work/{slug}` case-study pages per project (title, cover, long-form body,
    related projects)
  - `/about/` about me (bio, experiences, skills)
  - `/blog/` optional blog (hidden from the menu until a post is published)
  - Old bilingual URLs (`/fa/...`, `/en/...`) redirect automatically
- 🗂 **Admin panel (Persian UI)** at `/admin/`:
  - Projects, experiences, services, skills, collaborators, posts
  - **Pages** panel: page titles + show/hide each section
  - **Media** library + inline AJAX uploader next to every image field
  - **Fonts**: upload custom fonts (woff2/woff/ttf) or use an external CSS link
  - **Per-tag fonts**: choose a different font for `h1`, `h2`, `h3`, `h4` and `p`
  - **Custom CSS**: write your own CSS rules that apply site-wide
  - Draft / publish for every item
- 🎨 Design matching the original site: Doto font, light theme, thin borders,
  grayscale→color image hover, black buttons (`#332b29` on hover), dark footer;
  optional dark mode toggle for visitors
- 🧱 **Visual drag & drop builder** (`/admin/blocks.php`): the real site is shown in a live
  preview; drag heading / text / image / button / spacer / divider blocks straight onto
  the page, edit them in the side inspector, then **save as draft** (site unchanged,
  auto-saved) or **publish**. Drafts live in `blocks_draft.json`, live blocks in `blocks.json`
- 🎨 **Full-page custom CSS editor** in the admin panel
- 🔤 Per-tag font selection (`h1`–`h4`, `p`) + uploaded fonts
- 🔗 Footer admin link can be hidden (Settings → نمایش)
- 🕐 Live Tehran clock (top bar)
- 🔒 Session auth + CSRF tokens
- 💾 **Database storage**: SQLite by default (zero-config) or MySQL via `config.php`.
  JSON files in `/data` are used only as the initial seed / fallback.

## Requirements (host)

| Item | Notes |
| --- | --- |
| IIS | with the **URL Rewrite** module |
| PHP | **7.4 or newer** (FastCGI), with `pdo_sqlite` (default) or `pdo_mysql` |
| Database | SQLite is automatic; MySQL optional via `config.php` |

### Database

Default mode is **SQLite** — the database file `data/cms-database.sqlite` is
created automatically on the first visit and seeded from the JSON files.
To use **MySQL** instead, create an empty database and edit `config.php`:

```php
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'mohusyn');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpass');
```

The `cms_documents` table is created automatically on the first run.

## Installing on IIS

1. Copy all files to the site root.
2. Make sure **PHP** and **URL Rewrite Module** are enabled.
3. Change the admin password in `config.php`:

   ```php
   define('ADMIN_PASSWORD', 'your-new-password');
   ```

4. Give the Application Pool user (usually `IIS_IUSRS`) **Modify/Write**
   permission on the `data/` and `uploads/` folders.
5. Open `https://mohusyn.ir/admin/` and log in.

### Local test

```bash
php -S 0.0.0.0:3000 router.php
```

## Folder structure

```
├── index.php            # front controller (landing, about, blog)
├── web.config           # IIS config (rewrite + security)
├── config.php           # admin password ← change it
├── router.php           # local dev only (php -S)
├── app/                 # helpers, UI strings, views
├── admin/               # CMS panel (Persian UI)
├── assets/              # CSS / JS / default images
├── data/                # JSON content (blocked from web)
└── uploads/             # files uploaded from the panel (+ fonts subfolder)
```

## Security notes

- Change the default password `mohusyn2026` in `config.php` right after install.
- `data/` and `app/` are blocked from web access by `web.config`.
- Run the site over **HTTPS**.

## If image uploads fail on IIS

The app pool identity needs **write permission** on the `uploads/` folder
(and the file is also recorded in the database/`data/media.json`):

1. Right-click the `uploads` folder → Properties → Security → Edit.
2. Grant **Modify/Write** to `IIS_IUSRS` (or the site's application pool identity, e.g. `IIS APPPOOL\YourPool`).
3. Do the same for the `data` folder so the database and content files can be saved.

Also check PHP limits in `php.ini`: `upload_max_filesize` and `post_max_size`
should be at least `8M` (the panel's own cap is set in `config.php`).

## Contact form & messages

- The public page `/contact/` ("Start a Project") stores submissions in the
  database (`messages.json` collection) — no email is required.
- Read/reply/delete messages in the panel under **پیام‌ها** (the unread count
  is shown in the sidebar; the dashboard lists the latest messages).
