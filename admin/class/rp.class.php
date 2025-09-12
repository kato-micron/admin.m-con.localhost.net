<?php
/*
 * RP
 * Auther : Y.Tsuyuki
 * Update : 2019-04-05
 *
 * 2018-08-01
 * php7対応
 * 
 * 2018-09-25
 * 以下のガラケー専用関数は削除、getUAは今まで通りキャリアを識別する
 * getMobile
 * setCodex
 * getCodex
 * 
 * 2019-04-05
 * $RP->user_agent
 * $RP->ip
 * を追加　全モードで格納する
 * 
 * 2020-03-30
 * コンストラクタの引数でテンプレートディレクトリを直接指定できるようにした
 * 
 */

class RP extends Smarty {
	var $arg;
	var $isDisp;
	var $ua;
	var $param;
	var $path;
	var $mid;
	var $enc;
	var $isEnc;
	var $header;
	var $mode;
	var $carrier;
	var $user_agent;
	var $ip;


	//コンストラクタ
	/*
	 * $RP = new RP($gua,$rqmode,$sign);
	 *
	 * $gua UA識別モード 何も書かないとuaになります
	 * neet 何もしないぶん高速です
	 * ua   UAを識別 i=i-mode,e=EZweb,s=SoftBank,p=iPhone,a=Android,o=otherのどれか
	 * mid  uaと同じ挙動になった
	 *
	 * $rqmode リクエストモード 何も書かないとstandardになります。
	 * neet     何もしないぶん高速です。
   	 * standard $_REQUESTの内容のみ読み込みます。
   	 * path     standardに加えてPATH_INFOの内容を$pathに読み込みます
	 * rewrite  standardに加えて$sign=*** の内容を分解して$pathに読み込みます
	 *
	 * $sign ↑ rewriteモードじゃないときはなくてもいいです
	 *
	 */

	function __construct($gua='ua',$rqmode='standard',$sign='p',$template_dir='default') {
		if(!defined('ROOT_DIR')) {
			echo 'rp.class.php: 定数ROOT_DIRを設定して下さい。';
			exit;
		}

		//Smarty
		parent::__construct();
		$this->template_dir = ROOT_DIR."templates/{$template_dir}";
		$this->compile_dir  = ROOT_DIR."templates_c/{$template_dir}/";
		$this->config_dir   = ROOT_DIR.'configs/';
		$this->cache_dir    = ROOT_DIR.'cache/';

		//初期値セット
		$this->ua     = 'o';
		$this->isDisp = false;
		$this->param  = array();
		$this->path   = array();
		$this->arg    = array();
		$this->mid    = 'none';
		$this->isEnc  = false;
		$this->mode   = 'pc';

		//UA MID
		if($gua == 'ua' || $gua == 'mid')  {
			$this->ua      = $this->getUA();
			$this->carrier = $this->getCarrier($this->ua);
		}


		//ニートでなければ$_REQUEST処理
		if($rqmode != 'neet') {
			$this->param = array();
			foreach($_REQUEST as $key => $val) { $this->param[$key] = $this->validate($val); }
		}


		//PATH_INFO処理
		if($rqmode == 'path') {
			if(isset($_SERVER["PATH_INFO"])) { $this->path = preg_split("/\//",$_SERVER["PATH_INFO"]); }
		}


		//rewriteしてた場合の処理
		if($rqmode == 'rewrite') {
			if(isset($this->param[$sign])) { $this->path = preg_split("/\//",$this->param["p"]); }
		}


		$this->user_agent = '';
		if(isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		}

		$this->ip = '';
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}
	}


	//強制テンプレートセット
	function setTemplateForce($template,$compile) {
		$this->template_dir = $template;
		$this->compile_dir  = $compile;
	}


	//テンプレートセット
	function setTemplate($template,$compile=false) {
		$this->template_dir = ROOT_DIR."templates/{$template}/";
		$this->compile_dir  = ROOT_DIR."templates_c/{$template}/";
	}


	//携帯宣言
	function mobile() {
		$this->isEnc = true;
		$this->register_postfilter("convert_encoding_to_sjis");
	}


	//バリデーション
	function validate($val) {
		if(is_array($val)) {
			foreach($val as $key => $v) {
				$val[$key] = $this->validate($v);
			}
		} else {
			/* ここに好きな処理を書くとユーザー入力全てに実行されるよ */
			$val = htmlspecialchars($val,ENT_QUOTES);
		}
		return $val;
	}


	//文字コードセット
	function setEnc($from,$to) {
		$this->isEnc = true;
		$this->enc = array(
			"from" => $from,
			"to"   => $to
		);
	}



	//文字コード変換
	function encode($val,$rev=false) {
		if(is_array($val)) {
			foreach($val as $k => $v) {
				$val[$k] = $this->encode($v,$rev);
			}
		} else {
			if($rev) {
				$val = mb_convert_encoding($val,"EUCJP-WIN","SJIS-WIN");
				$val = mb_convert_encoding($val,"UTF-8","EUCJP-WIN");
			} else {
				$val = mb_convert_encoding($val,"EUCJP-WIN","UTF-8");
				$val = mb_convert_encoding($val,"SJIS-WIN","EUCJP-WIN");
			}
		}
		return $val;
	}


	//変数セット
	function set($key,$val) {
		if($this->isEnc) {
			$this->assign($key,$this->encode($val));
		} else {
			$this->assign($key,$val);
		}
	}


	//変数ゲット
	/* 第一引数に数値を入れるとパス参照になるよ
	 * $mode 設定すると入力条件を厳しくできる。
	 * num  : 数値じゃないとデフォルトが返る
     */
	function get($key,$default,$mode="neet") {
		if(is_numeric($key)) {
			return $this->getPath($key,$default,$mode);
		} else {
			return $this->getVal($key,$default,$mode);
		}
	}


	//変数全てゲット
	/* 第一引数のキーを全て取得します。*/
	function getAll($keys=array()) {
		$result = array();
		foreach($keys as $key => $val) {
			$result[$key] = $this->get($key,$val);
		}
		return $result;
	}


	//Cookieから
	function Cget($key,$default) {
		if(isset($_COOKIE[$key])) {
			return $_COOKIE[$key];
		} else {
			return $default;
		}
	}	


	//Cookieへ
	function Cset($key,$value,$expire=86400) {
		$expire = time() + $expire;
		setcookie($key,$value,$expire,'/');
	}


	//Sessionから復元
	// keyに配列を渡すと展開して処理する
	function Sget($key,$default='') {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		if(is_array($key)) {
			$result = array();
			foreach($key as $key2 => $value2) {
				$result[$key2] = $this->Sget($key2,$value2);
			}
			return $result;
		} else {
			if(isset($_SESSION[$key])) {
				return $_SESSION[$key];
			} else {
				return $default;
			}
		}
	}


	//Sessionへ保存
	// keyに配列を渡すと展開して処理する
	function Sset($key,$value='') {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		if(is_array($key)) {
			foreach($key as $key2 => $value) {
				$_SESSION[$key2] = $value;
			}
		} else {
			$_SESSION[$key] = $value;
		}
	}



	//パラメタ強制ゲット
	/* $rqmode が rewriteでもgetメソッドみたいにパラメタを取得します
	 * $key     : キー
	 * $default : デフォルト値
	 * $mode    : neet 何もしない num 数値じゃなかったらデフォルトを返す
	 */
	function Fget($key,$default,$mode="neet") {
		//デフォルト値
		$result = $default;

		//検索キー
		$search = "/[\?|\&]{$key}=(.*)/";

		//URI走査
		if(preg_match($search,$_SERVER["REQUEST_URI"],$match)) {
			//けつに&ついてたらとる
			if(preg_match("/\&/",$match[1])) {
				$exploded = explode("&",$match[1]);
				$result = $exploded[0];
			} else {
				$result = $match[1];
			}
		}

		//数値モード
		if($mode == "num" && !is_numeric($result)) {
			$result = $default;
		}
		
		return $result;
	}


	

	//変数リセット
	/* 渡された配列を空にして返す */
	function reset($ar) {
		foreach($ar as $k => $v) {
			$ar[$k] = "";
		}
		return $ar;
	}


	function exists_tempate($tpl) {
		$result = false;
		if(is_array($this->template_dir)) {
			if(file_exists($this->template_dir[0].'/'.$tpl.".tpl")) {
				$result = true;
			}
		} else {
			if(file_exists($this->template_dir.'/'.$tpl.".tpl")) {
				$result = true;
			}
		}
		return $result;
	}


	//ディスプレイ出力
	function disp($tpl) {
		//テンプレートがない場合デフォルトのテンプレートディレクトリに切り替える
		if(is_array($this->template_dir)) {
			if(!file_exists($this->template_dir[0].$tpl.".tpl")) {
				$this->setTemplate('default');
			}
		} else {
			if(!file_exists($this->template_dir.$tpl.".tpl")) {
				$this->setTemplate('default');
			}
		}

		// For Mobile 2018-09-25 削除
		//if($this->carrier == "mobile") { header("Content-type: application/xhtml+xml"); }

		// For Others
		$this->isDisp = true;
		$this->display($tpl.".tpl");
	}


	//メール配送
	/* to、from、subjectをsetしておくこと */
	function mail($tpl,$to,$from,$subject,$additional_header='') {
		$body = $this->buf($tpl);
		
		/* エンコードの処理とかはここに追加しておく */
		mb_internal_encoding("UTF-8");

		/* headerを設定 */
		$charset = "UTF-8";
		$headers['MIME-Version']        = "1.0";
		$headers['Content-Type']        = "text/plain; charset=".$charset;
		$headers['Content-Transfer-Encoding']   = "8bit";
		
		/* headerを編集 */
		foreach ($headers as $key => $val) {
			$arrheader[] = $key . ': ' . $val;
		}
		if(!empty($from)) {
			$arrheader[] = 'From: ' . $from;
		}
		
		/* 追加ヘッダがあれば直接書く */
		if(strlen($additional_header)) {
			$arrheader[] = $additional_header;
		}
		$strHeader = implode("\n", $arrheader);
		
		/* 件名を設定（JISに変換したあと、base64エンコードをしてiso-2022-jpを指定する）*/
		/* 2017-10-06 空だったら無視する */
		if(strlen($subject)) {
			$subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject,"JIS","UTF-8"))."?=";
		}
		
		/* 本文 */
		$body = trim($body);
		$body = preg_replace("/\r\n|\r/","\n",$body);
		
		/* 送ります！ */
		mail(
			$to,
			$subject,
			$body,
			$strHeader
		);
	}

	//ファイル出力
	/* $fileはファイルまでのフルパス ファイルが存在したら空にして上書きするので注意 */
	function StructureHtml($tpl,$file) {
		$body = $this->buf($tpl);
		$fp = fopen($file, "w");
		fputs($fp, $body);
		fclose($fp);
	}


	//csv出力
	/* $fileには保存するときのファイル名を */
	function outputcsv($tpl,$file) {
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename={$filename}");
		$this->Disp($tpl);
	}


	//出力バッファリング
	function buf($tpl) {
		ob_start();
		$this->disp($tpl);
		$body = ob_get_contents();
		ob_end_clean();
		return $body;
	}


	//変数ゲット追加関数
	function getVal($key,$default,$mode="neet") {
		if(isset($this->param[$key])) {
			if(!is_array($this->param[$key])) {
				if(strlen($this->param[$key]) > 0) {
					if($mode == "num") {
						if(!is_numeric($this->param[$key])) {
							return $default;
						} else {
							return $this->param[$key];
						}
					} else {
						return $this->param[$key];
					}
				} else {
					return $default;
				}
			} else {
				return $this->param[$key];
			}
		} else {
			return $default;
		}
	}


	//変数ゲット追加関数
	function getPath($key,$default,$mode="neet") {
		if(isset($this->path[$key])) {
			if(strlen($this->path[$key]) > 0) {
				if($mode == "num") {
					if(!is_numeric($this->path[$key])) {
						return $default;
					} else {
						return $this->path[$key];
					}
				} else {
					return $this->path[$key];
				}
			} else {
				return $default;
			}
		} else {
			return $default;
		}
	}


	//UAゲット
	/*
	 * i : i-mode
	 * e : ezweb
	 * s : yahoo
	 * o : other
	 * p : iphone
	 * a : android
	 */
	function getUA() {
		$mode = "o";

		if(empty($_SERVER["HTTP_USER_AGENT"])) {
			return $mode;
		}
		
		$ua_orig = $_SERVER["HTTP_USER_AGENT"];

		$ua = explode("/", $ua_orig);
		$ez_ua = explode(" ", $ua[0]);

		if ($ua[0] == "DoCoMo") {
			$mode = "i";
		} elseif ($ua[0] == "J-PHONE") {
			$mode = "y";
		} elseif ($ua[0] == "Vodafone") {
			$mode = "y";
		} elseif ($ua[0] == "SoftBank") {
			$mode = "y";
		} elseif (preg_match("/iPhone/",$ua_orig)) {	// iphone
			$mode = "p";
		} elseif (preg_match("/Android/",$ua_orig)) {	// Xperia
			$mode = "a";
		} else {
			if (isset($ez_ua[1])) {
				if(($ez_ua[1] == "UP.Browser") or ($ua[0] == "UP.Browser")) {
					$mode = "e";
				}
			} else {
				$mode = "o";
			}
		}
		return $mode;
	}

	function getCarrier($ua) {
		$carrier = "pc";
		if($ua == "a" || $ua == "p") {
			$carrier = "smartphone";
		}
		if($ua == "i" || $ua == "e" || $ua == "y") {
			$carrier = "mobile";
		}
		return $carrier;
	}


	//端末番号ゲット 2018-09-25 削除
	function getMobile($ua="auto") {
		return 'none';
	}


	//ページング処理
	function paging($page,$sum,$split) {
		if(!is_numeric($sum)) { $sum = 1; }
		if($page < 0) { $page = 1; }
		$result = array(
			"num"     => $sum,                      //総数
			"page"    => $page,                     //現在のページ
			"start"   => $split*($page-1) + 1,      //○○～
			"end"     => $split*($page-1) + $split,              //～○○まで表示中
			"next"    => $page+1,                   //次のページ
			"prev"    => $page-1,                   //前のページ
			"isNext"  => true,                      //次のページがあるか
			"isPrev"  => true,                      //前のページがあるか
			"pageall" => 1 + intval(($sum-1) / $split),     //全部で何ページあるか
			"limit"   => $split*($page-1),          //LIMIT節の左側
			"split"   => $split
		);
		if($result["end"] > $sum) {
			$result["end"] = $sum;
			$result["isNext"] = false;
		}
		if($result["page"] == 1) { $result["isPrev"] = false; }
		if($result["prev"] < 1) { $result["isPrev"] = false; }
		return $result;
	}



	//htmlエンティティを元に戻す
	function unhtmlspecialchars ( $str )
	{
		return htmlspecialchars_decode($str);
	}


	function n2br($str) {
		$str = str_replace("\r\n","\r",$str);
		$str = str_replace("\r","\n",$str);
		$str = str_replace("\n","<br />",$str);
		return $str;
	}


	function br2n($str) {
		$str = str_replace("<br>","\n",$str);
		$str = str_replace("<br />","\n",$str);
		return $str;
	}


	function setCodeX($codex) {
	}


	function codeX($id) {
		echo 0;
	}


	function setHeader($header) {
		$this->header = $header;
	}



	//携帯用のフォーム
	function setDef() {
	}

}
