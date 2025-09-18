$(function(){
     $(".tooltip a").hover(function() {
        $(this).next("span").animate({opacity: "show", top: "-75"}, "slow");}, function() {
               $(this).next("span").animate({opacity: "hide", top: "-85"}, "fast");
     });
});



$(function(){
    // テキストエリアのサイズ変更のUIエレメントを配置
    //$("textarea").resizable();
    // テキストエリアのサイズを自動変更
    $('textarea').autoResize();
});



function resize(Tarea){
    /*
    var areaoj = document.getElementsByClass("resize");
    var tval = areaoj.value;//テキストエリアの文字取得

    //改行文字の数を取得
    var num = tval.match(/\n|\r\n/g);
    if (num != null){var len = num.length + 1;}

    if (tval == ""){
        Tarea.style.width = 200;
    }
    //改行文字の数に合せて高さを変更
    areaoj.style.height = len * 1.2 + "em";
    */
    
    
    var areaW = Tarea.style.width;
    
    /*
    if(areaW > 200){
        Tarea.style.width = (parseInt(Tarea.scrollWidth)) + "px";
    }
    */
    if ( parseInt(areaW) - 200 <=  0 ) {
        areaW = 200;
    }
    Tarea.style.width = parseInt(areaW) + "px";
    Tarea.style.width = (parseInt(Tarea.scrollWidth)) + "px";
    

    var areaH = Tarea.style.height;
    
    /*
    if(areaH > 100){
        Tarea.style.height = areaH + "px";
        Tarea.style.height = (parseInt(Tarea.scrollHeight)) + "px";
    }
    */
    if (parseInt(areaH) - 100 <= 0 ) {
        areaH = 100;
    }
    Tarea.style.height = parseInt(areaH) + "px";
    Tarea.style.height = (parseInt(Tarea.scrollHeight)) + "px";
    
    
}