{if isset($client_user)}
{include file='client_inc_header.tpl'}
{else}
{include file='inc_header.tpl'}
{/if}


<div class="mgnt20 pdgl20">


<form action="./" method="POST">
<input type="hidden" name="module" value="{$module}">
<input type="hidden" name="mode" value="{$mode}">
<input type="hidden" name="position" value="execute">
<input type="hidden" name="id" value="{$formdata.id}">
<input type="hidden" name="title" value="{$formdata.title}">
<input type="hidden" name="description" value="{$formdata.description}">
<input type="hidden" name="keywords" value="{$formdata.keywords}">
<input type="hidden" name="header1" value="{$formdata.header1}">
<input type="hidden" name="header2" value="{$formdata.header2}">
{if $mode != "register"}
<input type="hidden" name="no" value="{$formdata.no}">
{/if}


<div id="admin_site_detail_wrap">


<table>
	
{if $mode != "register"}
    <tr>
        <th>no</th>
        <td>{$formdata.no}</td>
    </tr>
{/if}
    <tr>
        <th>ID</th>
        <td>{$formdata.id}</td>
    </tr>
    <tr>
        <th>タイトル</th>
        <td>{$formdata.title}</td>
    </tr>
    <tr>
        <th>ディスクリプション</th>
        <td>{$formdata.description}</td>
    </tr>
    <tr>
        <th>キーワード</th>
        <td>{$formdata.keywords}</td>
    </tr>
    <tr>
        <th>h1</th>
        <td>{$formdata.header1}</td>
    </tr>
    <tr>
        <th>h2</th>
        <td>{$formdata.header2}</td>
    </tr>
    <tr>
        <td colspan="2" class="center0"><input type="submit" value="完了"></td>
    </tr>
    
</table>


</div><!-- #admin_site_detail_wrap END -->

        
</div>
    


{include file='inc_footer.tpl'}
