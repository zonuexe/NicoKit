<?php
require_once dirname(__DIR__) . '/NicoSearch.php';

$nico = new NicoSearch('アプリケーション名');
$nico->service = NicoSearch::VIDEO;
$nico->query = '初音ミク';
$nico->feild = array(NicoSearch::TITLE);
$nico->sort = NicoSearch::UPLOAD_TIME;
$nico->order = NicoSearch::DESC;
$nico->res_start = 0;
$nico->res_size = 1;
$api = $nico->get_api();

// JSONでの返却
print('<pre>');
print_r($api);
print('</pre>');

// JSONをオブジェクト化
$data = json_decode($api);
foreach($data as $d) {
	echo '動画ID：'.$d->cmsid.'<br />';
	echo '動画タイトル：'.$d->title.'<br />';
	echo 'タグ：'.$d->tags.'<br />';
	echo 'アップロード日：'.$d->start_time.'<br />';
	echo '<img src="'.$d->thumbnail_url.'" alt="" /><br />';
	echo '再生数：'.$d->view_counter.'<br />';
	echo 'コメント数：'.$d->comment_counter.'<br />';
	echo 'マイリスト数：'.$d->mylist_counter.'<br />';
	echo '再生時間：'.$d->length_seconds.'<br /><br />';
}
