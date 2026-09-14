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

## Contact-form attachments (20 MB)

Visitors can attach one file (PDF / ZIP / images / docs / video) to the "Start a project" form.
The file is uploaded first via `contact-upload.php` (animated progress ring; the **Send** button is
disabled until the upload finishes), stored under `uploads/attachments/` with a random name, and
linked to the message in the admin inbox. Requirements on IIS:

- `upload_max_filesize` and `post_max_size` in `php.ini` ≥ `20M` (e.g. `25M`).
- `web.config` already sets `maxAllowedContentLength` to 25 MB.
- The app-pool identity needs write access to `uploads/` (a `web.config` that disables
  script execution is created inside `uploads/attachments/` automatically).

## SEO

Admin → **سئو** (`/admin/seo.php`): Persian + English keywords, per-page title/description,
Person/WebSite identity, OG image, Google/Bing/Yandex verification tags and extra `<head>` code.
The site emits: title/description/keywords/robots/author, canonical + hreflang, Open Graph and
Twitter cards, JSON-LD (Person, WebSite, AboutPage, CollectionPage, CreativeWork, BlogPosting,
ContactPage, BreadcrumbList), and generates `/sitemap.xml` (with images) and `/robots.txt`.
Projects and posts have their own SEO title / description / keywords fields.

## Portfolio archive

`/work/` lists every published project with category filters; the landing page shows the first
*N* projects (admin → معرفی و متن‌ها → پورتفولیو) with a "View all" button. **Portfolio** replaced
**Blog** in the menu; the blog can be re-enabled in the menu from the same settings section.

## Analytics (built-in, privacy-friendly)

`assets/js/analytics.js` sends anonymous beacons to `/track.php` (page views, clicks with button
labels, per-section dwell time, scroll depth 25/50/75/100 %, device class, referrer host, visit
duration). Nothing personal is stored — only daily counters in `data/analytics.json` (120 days).
Admin → **بازدید و رفتار** shows daily trend, most-clicked buttons (%), where users pause,
scroll funnel per page, contact funnel, devices and referrers. Disable with
`ANALYTICS_ENABLED = false` in `config.php`. Honors Do-Not-Track.
