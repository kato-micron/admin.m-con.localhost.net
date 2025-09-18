{if isset($client_user)}
{include file='client_inc_header.tpl'}
{else}
{include file='inc_header.tpl'}
{/if}



<div id="asd_wrap">
	

<div class="clearL">
	[ <a href="./?module={$module}&mode=register" class="sizeXL">新規登録</a> ]
</div>




<br />
<!-- ページ -->
{if isset($page)}
<div class="pageNavi mrgT10">
	<span class="pageNavi-li">
		{if $page.isPrev}
		<a href="./?module={$module}&pno={$page.prev}&refine_site={$refine_site}">前ページ</a>&nbsp;{/if}
		{if $page.isNext}
		<a href="./?module={$module}&pno={$page.next}&refine_site={$refine_site}">次ページ</a>&nbsp;{/if}
		（&nbsp; {$page.page}&nbsp;／&nbsp; {$page.pageall} &nbsp;）
	</span>
</div>
{/if}




{if !empty($headers)}
	<table class="clearR">
		<tr>
				<th>No</th>
				<th>ID</th>
				<th>タイトル</th>
				<th>修正</th>
				<th>削除</th>
		</tr>
		{foreach from=$headers item=head}
		{cycle values="0,1" print=false assign="i"}
		<tr class="viewflag_on">
				<td class="center{$i}">{$head.no}</td>
				<td class="left{$i}">{$head.id}</td>
				<td class="left{$i}">{$head.title|htmlspecialchars}</td>
				<td class="center{$i}"><a href="./?module={$module}&mode=update&no={$head.no}">修正</a></td>
				<td class="center{$i}"><a href="./?module={$module}&mode=delete&no={$head.no}">削除</a></td>
		</tr>
		{/foreach}

</table>
{else}
	<p class="pdgT10 pdgL20 pdgB10 bold">ヘッダがありません。</p>
{/if}




<!-- ページ -->
<div class="pageNavi mrgT10">
	<span class="pageNavi-li">
		{if isset($page)}
			{if $page.isPrev}
			<a href="./?module={$module}&pno={($page.prev)}&refine_site={$refine_site}">前ページ</a>&nbsp;{/if}
			{if $page.isNext}
			<a href="./?module={$module}&pno={($page.next)}&refine_site={$refine_site}">次ページ</a>&nbsp;{/if}
			（&nbsp; {($page.page)}&nbsp;／&nbsp; {$page.pageall} &nbsp;）
		{/if}
	 </span>
</div>



</div>


{include file='inc_footer.tpl'}
