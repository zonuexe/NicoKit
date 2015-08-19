<?php
require_once dirname(__DIR__).'/NicoTags.php';

$nico = new NicoTags('NicoTags');
$nico->query = '初音ミク';
$nico->service = NicoTags::VIDEO;
$nico->res_start = 0;
$nico->res_size = 100;
$api_data = $nico->get_api();

// JSONでの返却
print('<pre>');
print_r($api_data);
print('</pre>');

// JSONデータをオブジェクト化
$object = json_decode($api_data);
foreach ($object as $obj) {
	echo 'タグ名：'.$obj->tag.'<br />';
	echo 'ヒット件数：'.$obj->tag_counter.'<br /><br />';
}