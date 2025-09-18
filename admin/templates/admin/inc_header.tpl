<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{$main_domain} 管理ページ</title>
<link rel="stylesheet" href="{$admin_url}css/admin.css" />
<link rel="stylesheet" href="{$admin_url}css/common.css" />
<script src="{$admin_url}js/default.js"></script>
<script src="{$admin_url}js/rm.js"></script>
<script src="{$admin_url}js/jquery-1.8.2.js"></script>
</head>
<body>

	
<div id="wrap">

	
<div id="navi">
	<h1>{$main_domain} 管理ページ</h1>
</div>
{*<h1 id="管理ページi">{$main_domain} 管理ページ</h1>*}


<div id="menu">
	<h2>メインメニュー</h2>
	<ul>
		<!--li><a href="{$admin_url}">稼動状況</a></li-->
		<li><a href="{$admin_url}?module=log_site_jmp">広告別詳細</a></li>
		<li><a href="{$admin_url}?module=log_site">サイト別詳細</a></li>
		<li><a href="{$admin_url}?module=log_kuchikomi">クチコミ投稿</a></li>
		<li><a href="{$admin_url}?module=headers">ヘッダ</a></li>
		<li><a href="{$admin_url}?module=replaceword">置換コード</a></li>
		<li><a href="{$admin_url}?module=prefs">都道府県No確認用</a></li>
		<li><a href="{$admin_url}?module=log_traffic_movie">動画サーバー転送量</a></li>
	</ul>
</div>

	
<div id="msgs" class="floatL">
	<h3>{$current_page}</h3>
	{$description}
	<div id="comment">{$msg}</div>
</div>

	

<div id="contents" {if $mode != "list"}style="width:85%;"{/if}>


