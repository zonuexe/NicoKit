<?php
/**********************************************************
*
*
* ニコニコ関連タグクラス
*
* @author Yuki-Yamamoto
*
* $nico = new NicoTags('アプリケーション名');
* $nico->query = '検索クエリ';
* $nico->service = '検索対象サービス：【例: NicoTags::VIDEO】';
* $nico->res_start = 'データを取得する起点：0～1600';
* $nico->res_size = 'データを取得する数：10～100';
* $api_data = $nico->get_api();
*
*
***********************************************************/
class NicoTags {
	/**
	 * 定数
	 */
	// エンドポイント
	const ENDPOINT = 'http://api.search.nicovideo.jp/api/tag/';

	// 対象サービス
	const VIDEO = 'tag_video';
	const LIVE = 'tag_live';
	const ILLUST = 'tag_illust';
	const MANGA = 'tag_manga';
	const BOOK = 'tag_book';
	const CHANNEL = 'tag_channel';
	const NEWS = 'tag_news';

	/**
	 * プロティ
	 */
	// 検索クエリ
	public $query;

	//　検索対象サービス
	public $service;

	// データ取得の起点
	public $res_start;

	// データ取得の数
	public $res_size;

	// アプリケーション名
	public $app_name;

	// 検索ヒット数
	public $total;
	

	/**
	 * メソッド
	 */
	// コンストラクタ
	function __construct($app_name = 'noname') {
		$this->app_name = $app_name;
	}

	// APIデータを取得するメソッド
	public function get_api() {
		// POSTデータ
		$post_data = array(
			'query' => $this->query,
			'service' => array($this->service),
			'from' => $this->res_start,
			'size' => $this->res_size,
			'issuer' => $this->app_name,
			'reason' => 'ma10'
		);

		// コンテキストの作成
		$context = stream_context_create(
			array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/json; charset=utf-8',
					'content' => json_encode($post_data)
				)
			)
		);

		// APIデータを取得する
		$api_data = file_get_contents(NicoTags::ENDPOINT, false, $context);
		$json = $this->data_parse($api_data);
		return $json;
	}

	// 取得したデータをJSONにする
	public function data_parse($api_data) {
		// APIから取得した個々のJSONを配列に格納
		$data = explode("\n", $api_data);
		$obj_data = json_decode($data[0]);
		$obj_status = json_decode($data[1]);

		// 取得データのJSON
		$json = json_encode($obj_data->values);

		// データヒット件数
		$this->total = $obj_status->values[0]->total;

		return $json;
	}
}