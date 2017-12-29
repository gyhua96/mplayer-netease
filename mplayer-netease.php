<?php
/*
Plugin Name: 网易歌单背景音乐
Plugin URI: http://www.gongyuhua.cn/2017/12/11/512.html
Description: 将网易的歌单作为 背景音乐
Version: 0.1
Author: 矢小北
Author URI: http://www.gongyuhua.cn
*/
/*  Copyright 2017-12-18  矢小北  (email : 920109015@qq.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php


/* 注册激活插件时要调用的函数 */ 
register_activation_hook( __FILE__, 'mplayer_netease_install');   

/* 注册停用插件时要调用的函数 */ 
register_deactivation_hook( __FILE__, 'mplayer_netease_remove' );  

function mplayer_netease_install() {  
    /* 在数据库的 wp_options 表中添加一条记录，第二个参数为默认值 */ 
    add_option("mplayer_netease_id", "947206539", '', 'yes'); 
    add_option("mplayer_netease_default_id", "1", '', 'yes');  
}

function mplayer_netease_remove() {  
    /* 删除 wp_options 表中的对应记录 */ 
    delete_option('mplayer_netease_id');  
    delete_option('mplayer_netease_default_id');  
}

if( is_admin() ) {
    /*  利用 admin_menu 钩子，添加菜单 */
    add_action('admin_menu', 'mplayer_netease_menu');
}

function mplayer_netease_menu() {
    /* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */
    /* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */
    add_options_page('Set mplayer', 'mplayer netease', 'administrator','mplayer_netease', 'mplayer_netease_html_page');
}

function get_music_list($id){
    $url='http://music.163.com/api/playlist/detail?id='.$id;
    $html = file_get_contents($url);
    //echo $html;
    $json=json_decode($html,true);
    $list=$json['result']['tracks'];
    $mlist=array();
    foreach($list as $item){
         $artists=array();
        foreach($item['artists'] as $artist){
             array_push($artists,$artist['name']);
            }
        array_push($mlist,[
            'name'=>$item['name'],
            'id'=>$item['id'],
            'artists'=>$artists
        ]);
    }
    return $mlist;
}

function mplayer_netease_html_page() {
    ?>
    <div>  
        <h2>设置歌单 ID</h2>  
        <form method="post" action="options.php">  
            <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>  
            <?php wp_nonce_field('update-options'); ?>  
 
            <p>  
                <textarea  
                    name="mplayer_netease_id" 
                    id="mplayer_netease_id" 
                    cols="40" 
                    rows="1"><?php echo get_option('mplayer_netease_id'); ?>
                </textarea>  
            </p>  
 
            <p>  
                <input type="hidden" name="action" value="update" />  
                <input type="hidden" name="page_options" value="mplayer_netease_id" />  
 
                <input type="submit" value="Save" class="button-primary" />  
            </p>  
        </form>  
        <h2>设置默认 歌曲</h2>  
        <form method="post" action="options.php">  
            <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>  
            <?php wp_nonce_field('update-options'); ?>  
 
            <p>  
                <textarea  
                    name="mplayer_netease_default_id" 
                    id="mplayer_netease_default_    id" 
                    cols="40" 
                    rows="1"><?php echo get_option('mplayer_netease_default_id'); ?>
                </textarea>  
            </p>  
 
            <p>  
                <input type="hidden" name="action" value="update" />  
                <input type="hidden" name="page_options" value="mplayer_netease_default_id" />  
 
                <input type="submit" value="Save" class="button-primary" />  
            </p>  
        </form>  
    </div>  
<?php  
}  

function mplayer_netease() {
    $crrentid="";
    ?>
<div id="music-player" >

    <div class="aplayer  aplayer-withlist" style="margin:0 30px 0 25px;background:#fff;"  id="mplayer">
                    <div class="aplayer-list" style="max-height: 280px; ">
                        <ol>
                        <?php 
                        $list=get_music_list(get_option('mplayer_netease_id'));
                        $count=0;
                        foreach($list as $music)
                        {
                            $count++;
                            ?>
                            <li id="<?php echo $music['id']; ?>" <?php if($count==get_option('mplayer_netease_default_id')){ echo 'class="aplayer-list-light"'; $currentid= $music['id']; }?>>
                                <span class="aplayer-list-cur" style="background: #ad7a86;"></span>
                                <span class="aplayer-list-index"><?php echo $count; ?> </span>
                                <span class="aplayer-list-title"><?php echo $music['name']; ?></span>
                                <span class="aplayer-list-author"><?php foreach($music['artists'] as $artist){ echo ' '.$artist; } ?></span>
                            </li>
                            <?php
                        }
                        ?>
                        </ol>
                    </div>
                </div>
                <div id="music-player-close"></div>
             <div style="float:left;">
        <iframe id="mplayer_page" style="margin:0px;padding:0;" frameborder="no" border="0"  marginwidth="0" marginheight="0" width=330 height=86 src="//music.163.com/outchain/player?type=2&id=<?php echo  $currentid; ?>&auto=0&height=66"></iframe>


    </div>
    <div id="music-player-open"></div>
</div>
<?php
}
function mplayer_css(){
    $url = plugin_dir_url(__FILE__);
    echo  '<script type="text/javascript" src="'.$url.'mplayer_netease.js"></script>';
    echo  '<link type="text/css" rel="stylesheet" href=" '.$url.'style.css" />';
    
}

add_action( 'wp_footer', 'mplayer_netease' );
add_action( 'wp_head', 'mplayer_css' );

?>