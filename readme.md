# Filemanager for web application

Simple and intuitive file manager.

What it can really do?
- creating directories
- upload a file by dragging from disk
- possibility of integration into wysiwyg editor

Instalation
-----------

The recommended way to install is via Composer:

```
composer require kevas/filemanager
```

Usage
-----

Create a filemanager content and insert it into your templates.

```php
use Kevas\Filemanager\Filemanager;
$filemanager = new Filemanager;

// Set upload dir relative to your document root
$filemanager->setUploadDir('user_uploads');
$filemanagerContent = $filemanager->render();
```
Now you have to copy the assets directory, which is in `vendor/kevas/filemanager/src/assets` and paste it into the root web.

Set css and js path
```html
<link rel="stylesheet" href="/assets/fontawesome/css/all.css">
<link rel="stylesheet" href="/assets/dropzone/dropzone.css">
<link rel="stylesheet" href="/assets/filemanager/css/main.css">

<script src="/assets/jquery/jquery.js"></script>
<script src="/assets/dropzone/dropzone.js"></script>
<script src="/assets/filemanager/js/main.js"></script>
```

Screenshots
-----------
![Base view on filemanager](screenshots/img1.png)

![Upload files](screenshots/img2.png)

![Create dir](screenshots/img3.png)

