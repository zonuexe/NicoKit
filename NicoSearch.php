<?php
/*******************************************************************
*
*
* ニコニコ検索クラス
*
* @author Yuki-Yamamoto
*
* $nico = new NicoSearch('アプリケーション名');
* $nico->query = '検索キーワード';
* $nico->feild = array('検索対象 【例: TITLE】');
* $nico->sort = '並べ替えフィールド名 【例: UPLOAD_TIME】';
* $nico->order = '並べ替え順序: DESC もしくは ASC';
* $nico->res_start = 'レスポンス取得開始位置 【0～1600】';
* $nico->res_size = 'レスポンスの数 【0 ~ 100】';
* $api = $nico->get_api();
*
*
********************************************************************/
// エンドポイント
define('NICO_SEARCH', 'http://api.search.nicovideo.jp/api/');

// 検索対象
define('TITLE', 'title');
define('TAG', 'tags');
define('DESCRIPTION', 'description');

// 並べ替えフィールド名
define('COMMENT_TIME', 'last_comment_time'); // コメント時間
define('VIEW_COUNTER', 'view_counter'); // 再生数
define('UPLOAD_TIME', 'start_time'); // 投稿日時
define('MYLIST_COUNTER', 'mylist_counter'); // マイリス数
define('COMMENT_COUNTER', 'comment_counter'); // コメント数
define('MOVIE_LENGTH', 'length_seconds'); // 再生時間

// 並べ替え順序
define('DESC', 'desc'); // 降順
define('ASC', 'asc'); // 昇順

class NicoSearch {
	/**
	 * プロパティ
	 */
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

	// 検索キーワードに該当するデータ数
	public $total;

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
			"service" => array('video'),
			"search" => $this->feild,
			"join" => array(
						"cmsid", 
						"title", 
						"view_counter", 
						"tags",
						"start_time",
						"thumbnail_url",
						"comment_counter",
						"mylist_counter",
						"length_seconds"
						),
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

		$api_data = file_get_contents(NICO_SEARCH, false, $context);
		$json = $this->json_parse($api_data);
		return $json;
	}

	// 複数のJSONデータを一つにする
	private function json_parse($api_data) {
		$data = explode("\n", $api_data);

		if(!strpos($data[0], 'errid')) {
			// 該当するデータがなかった場合、エラー内容を返却
			if($this->total === 0) {
				$err_id = 404;
				return $err_id;
			}
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
			// エラーがあった場合、エラー内容を返却
			$err_id = $status_obj->err_id;
			return $err_id;
		}
	}
}
