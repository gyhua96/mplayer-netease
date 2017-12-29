var $=jQuery.noConflict();
jQuery(document).ready(function () {
    $("#music-player-close").click(function () {
        $("#music-player-close").hide();
        $("#music-player-open").show();
        $("#music-player").animate({
            left: '-332px'
        });
    });
    $("#music-player-open").click(function () {
        $("#music-player-open").hide();
        $("#music-player-close").show();
        $("#music-player").animate({
            left: '0px'
        });
    });
    $(".aplayer-list li").click(function(){
        src='//music.163.com/outchain/player?type=2&id='+$(this).attr("id")+'&auto=0&height=66';
        $("#mplayer_page").attr('src',src);
        $(".aplayer-list-light").removeClass("aplayer-list-light");
        $(this).addClass("aplayer-list-light");
    });
});
