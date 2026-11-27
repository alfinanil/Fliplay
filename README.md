# FlipPlay

Satu project untuk seluruh demo praktikum Pemrograman Web.

    flipplay/
    ├── src/               Frontend (Modul 1 & 2): HTML, Tailwind CSS, JavaScript
    │   ├── index.html     Landing page (Modul 1)
    │   ├── katalog.html   Katalog "The Vibe Grid" (Modul 2, Kitsu API)
    │   ├── katalog.js
    │   ├── script.js
    │   ├── main.css / output.css
    │   └── images/
    ├── backend/           Backend engine PHP OOP (Modul 3)
    │   ├── src/
    │   │   ├── Models/    Media, Anime, Drama, Manga, LightNovel, User
    │   │   └── Services/  Library
    │   └── index.php      Entry point simulasi
    ├── package.json
    └── package-lock.json

## Frontend (Modul 1 & 2)
Buka `src/index.html` di browser (atau Live Server).
Jika mengubah class Tailwind, compile ulang:

    npm install
    npx @tailwindcss/cli -i ./src/main.css -o ./src/output.css --watch

## Backend (Modul 3)
Butuh PHP 8.x. Jalankan dari root project:

    php backend/index.php

atau dari dalam folder backend:

    cd backend
    php index.php
