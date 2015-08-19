<?php
/*******************************************************************
*
*
* ニコニコ検索クラス
*
* @author Yuki-Yamamoto
*
* $nico = new NicoSearch('アプリケーション名');
* $nico->service = '対象サービス：【例: NicoSearch::VIDEO】';
* $nico->query = '検索キーワード';
* $nico->feild = array('検索対象 【例: NicoSearch::TITLE】');
* $nico->sort = '並べ替えフィールド名 【例: NicoSearch::UPLOAD_TIME】';
* $nico->order = '並べ替え順序: 【例：NicoSearch::DESC】';
* $nico->res_start = 'レスポンス取得開始位置 【0～1600】';
* $nico->res_size = 'レスポンスの数 【0 ~ 100】';
* $api = $nico->get_api();
*
*
********************************************************************/
class NicoSearch {
	/**
	 * 定数
	 */
	// エンドポイント
    const ENDPOINT = 'http://api.search.nicovideo.jp/api/';
 
    //　対象サービス
    const VIDEO = 'video';
    const LIVE = 'live';
    const ILLUST = 'illust';
    const MANGA = 'manga';
    const BOOK = 'book';
    const CHANNEl = 'channel';
    const CHANNELARTICLE = 'channelarticle';
    const NEWS = 'news';

    // 検索対象
    const TITLE = 'title';
    const TAG = 'tags';
    const DESCRIPTION = 'description';
    const BODY = 'body'; // ニュース専用
    const CAPTION = 'caption'; //ニュース用

    // 並べ替えフィールド名
    const COMMENT_TIME = 'last_comment_time';
    const VIEW_COUNTER = 'view_counter';
    const UPLOAD_TIME = 'start_time';
    const MYLIST_COUNTER = 'mylist_counter';
    const COMMENT_COUNTER = 'comment_counter';
    const MOVIE_LENGTH = 'length_seconds';

    // 並べ替え順序
    const DESC = 'desc';
    const ASC = 'asc';

    // 返却される項目
    const JOIN = array(
		"cmsid", 
		"title", 
		"tags",
		"thumbnail_url",
		"start_time",
		"view_counter",
		"comment_counter",
		"mylist_counter",
		"length_seconds"
	);

	/**
	 * プロパティ
	 */
	// 対象サービス
	public $service;

	// 検索キーワード
	public $query;

	// 検索対象
	public $feild;

	// 並べ替えフィールド名
	public $sort;

	// 並べ替え順序
	public $order;

	// レスポンス取得開始位置
	public $res_start;

	// レスポンスの数
	public $res_size;

	// アプリケーション名
	public $app_name;

	/**
	 * メソッド
	 */
	// コンストラクタ
	public function __construct($app_name = 'NicoSearch') {
		$this->app_name = $app_name;
	}

	// APIデータを取得する
	public function get_api() {
		// POSTデータ
		$post_data = array(
			"query" => $this->query,
			"service" => array($this->service),
			"search" => $this->feild,
			"join" => NicoSearch::JOIN,
			"sort_by" => $this->sort,
			"order" => $this->order,
			"from" => $this->res_start,
			"size" => $this->res_size,
			"issuer" => $this->app_name,
			"reason" =>"ma10"
		);

		// コンテキストの作成
		$context = stream_context_create(
			array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/json; charset=UTF-8',
					'content' => json_encode($post_data)
				)
			)
		);

		$api_data = file_get_contents(NicoSearch::ENDPOINT, false, $context);
		$json = $this->json_parse($api_data);
		return $json;
	}

	// 複数のJSONデータを一つにする
	private function json_parse($api_data) {
		$data = explode("\n", $api_data);

		if(!strpos($data[0], 'errid')) {
			// 複数のJSONをまとめてJSONにする
			$json = array();
			$num = 0;
			for($i = 2; $i < count($data)-2; $i++) {
				$obj = json_decode($data[$i]);
				$val = $obj->values;
				for($n = 0; $n < count($val); $n++) {
					$json[] = array(
						"cmsid" => $val[$n]->cmsid,
						"title" => $val[$n]->title,
						"view_counter" => $val[$n]->view_counter,
						"tags" => $val[$n]->tags,
						"start_time" => $val[$n]->start_time,
						"thumbnail_url" => $val[$n]->thumbnail_url,
						"comment_counter" => $val[$n]->comment_counter,
						"mylist_counter" => $val[$n]->mylist_counter,
						"length_seconds" => $val[$n]->length_seconds
					);
				}
			}
			return json_encode($json);
		} else {
			return false;
		}
	}
}
