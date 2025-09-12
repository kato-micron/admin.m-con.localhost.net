<?php
http_response_code(200);
?><!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>m-con.net Admin</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:2rem;color:#222}
    .wrap{max-width:720px;margin:auto}
    code{background:#f3f4f6;padding:.1rem .3rem;border-radius:4px}
    .ok{display:inline-block;background:#e6ffed;color:#036b26;border:1px solid #9ae6b4;padding:.25rem .5rem;border-radius:4px;font-size:.9rem}
  </style>
  </head>
<body>
  <div class="wrap">
    <h1>m-con.net Admin</h1>
    <p class="ok">Admin App is up.</p>
    <ul>
      <li>Container: <code>php:8.2-apache</code></li>
      <li>DocumentRoot: <code>/var/www/admin/public</code></li>
      <li>Mounted: <code>./admin -&gt; /var/www/admin</code></li>
    </ul>
    <p>次フェーズで DB 接続や既存資産の取り込みを検討します。</p>
  </div>
</body>
</html>

