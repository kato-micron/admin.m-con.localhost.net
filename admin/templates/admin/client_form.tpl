{if isset($client_user)}
{include file='client_inc_header.tpl'}
{else}
{include file='inc_header.tpl'}
{/if}


<h3>{$msgs.current_page}</h3>
<div id="description">{$msgs.description}</div>
<div id="comment">{$msgs.comment}</div>


<form action="./" method="POST">
<input type="hidden" name="module" value="{$module}">
<input type="hidden" name="mode" value="{$mode}">
<input type="hidden" name="position" value="modify">
{if $mode != "register"}
<input type="hidden" name="no" value="{$formdata.no}">
{/if}


<table>
    <tr>
        <th>id</th>
        <td>
            <input type="text" name="id" value="{$formdata.id}">
        </td>
    </tr>
    <tr>
        <th>タイトル</th>
        <td>
            <input type="text" name="title" value="{$formdata.title|htmlspecialchars}" style="width: 800px;">
        </td>
    </tr>
    <tr>
        <th>ディスクリプション</th>
        <td>
            <input type="text" name="description" value="{$formdata.description|htmlspecialchars}" style="width: 800px;">
        </td>
    </tr>
    <tr>
        <th>キーワード</th>
        <td>
            <input type="text" name="keywords" value="{$formdata.keywords|htmlspecialchars}" style="width: 800px;">
        </td>
    </tr>
    <tr>
        <th>h1</th>
        <td>
            <input type="text" name="header1" value="{$formdata.header1|htmlspecialchars}" style="width: 800px;">
        </td>
    </tr>
    <tr>
        <th>h2</th>
        <td>
            <input type="text" name="header2" value="{$formdata.header2|htmlspecialchars}" style="width: 800px;">
        </td>
    </tr>

    <tr>
        <td colspan="2" class="center0"><input type="submit" value="確認画面へ"></td>
    </tr>
</table>


</form>

            
{include file='inc_footer.tpl'}
