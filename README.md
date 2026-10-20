# Mohusyn.ir — Portfolio + Admin Panel

- `site/` → **IIS web root** (built React app + PHP JSON API + data + uploads). Deploy this folder.
- `frontend/` → React/Vite/TypeScript source. `npm install && npm run build` rebuilds into `site/`.

## Run on IIS (Windows Server / Windows 10-11)

1. **Install IIS** (Control Panel → Programs → Turn Windows features on → Internet Information Services, including *CGI*).
2. **Install PHP** (8.x NTS x64 from windows.php.net) e.g. to `C:\php`.
   In `php.ini` enable: `extension=mbstring`, `extension=openssl`, `extension=fileinfo`, and set `upload_max_filesize=30M`, `post_max_size=32M`.
   Or simply use *Web Platform Installer* → "PHP 8.x".
3. **Map PHP in IIS**: IIS Manager → server node → *Handler Mappings* → *Add Module Mapping*:
   Request path `*.php`, Module `FastCgiModule`, Executable `C:\php\php-cgi.exe`, Name `PHP`.
4. **Install URL Rewrite 2.1** (https://www.iis.net/downloads/microsoft/url-rewrite). Required for the `web.config` SPA routes.
5. **Copy `site\` contents** to e.g. `C:\inetpub\mohusyn` and create a website (or application) pointing to it.
6. **Permissions**: give `IIS_IUSRS` (and the AppPool identity) *Modify* rights on `data\` and `uploads\`.
7. Open `http://your-host/` → site; `http://your-host/admin` → admin panel (default password in `site/api/config.php`, change it from the panel).
8. Health check: `http://your-host/api/index.php?r=health` should return JSON with `"ok":true` and `dataWritable:true`.

Contact-form email: set `NOTIFY_EMAIL` and configure SMTP in `php.ini` (`SMTP=`, `smtp_port=`, `sendmail_from=`). Messages are always stored in the admin panel regardless.
