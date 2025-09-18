/*
　表示しているページ
　選択されたページリンク
　表示件数
　全表示件数
から次のリンクを求めて
項目の表示も切り替える
*/
function nchange_page(current_page, display_site_num, all_site_num) {
$(function(){
        var all_page = all_site_num / display_site_num;
	
        if (current_page == 1) { //最初のページの場合
		$(document).ready(function(){
                        for(i=0; i<all_site_num; ++i){
                            var cp_first = (current_page - 1) * diplay_site_num;
                            if(i > cp_first && i <= cp_first + diplay_site_num){
                                $(".side-rank-li").eq(i).show();
                            }else{
                                $(".side-rank-li").eq(i).hide();
                            }
                        }
			$("#prev").hide();
			$("#next").show();
			return false;
		});
	} else if(current_page == all_page) {// 最後のページの場合
		$(document).ready(function(){
			$("#prev").show();
			$("#next").hide();
			return false;
		});
                
	}else{
		$(document).ready(function(){
			$("#prev").show();
			$("#next").show();
			return false;
		});
            
        }
});
}

$(function(){
    $('a[href=#]').click(function(){
        return false;
    })
});