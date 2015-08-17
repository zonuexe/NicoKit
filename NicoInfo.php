<?php
/***********************************************************
*
*
* NicoInfo.php -ニコニコ動画情報クラス-
*
* $nico = new NicoInfo('動画ID または 動画URL');
* $nico->XXX; // プロパティで各動画情報を取得
* 【例：$nico->video_id;】
*
*
************************************************************/

// ---------------------------------------------------------
// ニコニコ動画情報クラス
// ---------------------------------------------------------
class NicoInfo {
    const ENDPOINT = 'http://ext.nicovideo.jp/api/getthumbinfo/';

	/**
	 * プロパティ
	 */

	// 動画ID
	public $video_id = '';

	// 動画タイトル
	public $title = '';

	// 動画説明文
	public $description = '';

	// 動画サムネ
	public $thumb_url = '';

	// 動画アップロード日時
	public $upload_date;

	// 動画時間
	public $length;

	// 動画再生数
	public $view_counter;

	// コメント数
	public $comment_counter;

	// マイリスト数
	public $mylist_counter;

	// 動画URL
	public $movie_url = '';

	// ユーザID
	public $user_id;

	// ユーザ名
	public $user_name = '';

	// ユーザアイコン
	public $user_icon = '';

	/**
	 * メソッド
	 */

	// コンストラクタ
	public function __construct($video_url) {
		if(!strpos($video_url, 'http')) {
			$video_id = $this->getMovieID($video_url);
			$this->getAPI($video_id);
		} else {
			$this->getAPI($video_url);
		}
	}

	// 動画ID取り出し関数
	public function getMovieID($video_url) {
		$first = strpos($video_url, 'sm');
		if(!$first) $first = strpos($video_url, 'so');
		$video_id = substr($video_url, $first);

		return $video_id;
	}

	// API情報取得
	public function getAPI($video_id) {
		$api_data  = simplexml_load_file(NicoInfo::ENDPOINT . $video_id);
		$nico = $api_data->thumb;

		// 動画情報をプロパティに格納
		$this->video_id = $nico->video_id;
		$this->title = $nico->title;
		$this->description = $nico->description;
		$this->thumb_url =$nico->thumbnail_url;
		$this->upload_date = $nico->first_retrieve;
		$this->length = $nico->length;
		$this->view_counter = $nico->view_counter;
		$this->comment_counter = $nico->comment_num;
		$this->mylist_counter = $nico->mylist_counter;
		$this->movie_url = $nico->watch_url;
		$this->user_id = $nico->user_id;
		$this->user_name = $nico->user_nickname;
		$this->user_icon = $nico->user_icon_url;
	}
}
