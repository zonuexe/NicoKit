<?php
require_once dirname(__DIR__) . '/NicoInfo.php';

$nico = new NicoInfo('http://www.nicovideo.jp/watch/sm26374280');

echo $nico->video_id.'<br />';
echo $nico->title.'<br />';
echo $nico->description.'<br />';
echo '<img src="'.$nico->thumb_url.'" alt="" /><br />';
echo $nico->upload_date.'<br />';
echo $nico->length.'<br />';
echo $nico->view_counter.'<br />';
echo $nico->comment_counter.'<br />';
echo $nico->mylist_counter.'<br />';
echo $nico->movie_url.'<br />';
echo $nico->user_id.'<br />';
echo $nico->user_name.'<br />';
echo '<img src="'.$nico->user_icon.'" alt="" />';
