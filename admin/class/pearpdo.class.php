<?php
/*
 * PDOをMDB - Pearのように使うラッパ
 * 
 * Auther: y.tsuyuki, a.katou
 * Update: 2020-01-20
 * 
 * 定数DB_HOST DB_NAME DB_USER DB_PASSWORDを指定して
 * $DB = new PearPDO();
 * 
 * または
 * $dsn = 'dbhost/dbname/dbuser/dbpass';
 * $DB = new PearPDO($dsn);
 * 
 * 2018-10-09
 * getCol 挙動を変更した。最初の1カラムのみ取得する。
 * getArray 挙動を変更した。キーにカラム名を含まず、3次元添字配列が返る
 * 
 * 2018-10-10
 * getRow 挙動を変更した。キーはカラム名のみ
 * 
 * 2019-01-25
 * SSL対応した
 * 
 * 2019-02-04
 * プレースホルダの検索条件を変更した。
 * 
 * 2019-02-05
 * ログ機能を追加した
 * get***やqueryの直後に$DB->logをダンプするとログが見れます
 * 
 * 2019-02-08
 * プレースホルダのバグを修正した。
 *
 * 2019-12-25
 * プレースホルダのバグを修正した。
 * 
 */

class PearPDO {
	var $pdo;
	var $sql;
	var $message;
	var $fetchmode;
	var $log;

	//コンストラクタ
	function PearPDO($dsn=null,$ca=null) {
		$this->fetchmode = 'both';

		/* 2019-01-25 SSL認証対応 */
		$options = array();
		if(strlen($ca)) {
			if(file_exists($ca)) {
				$options = array(PDO::MYSQL_ATTR_SSL_CA => $ca);
			}
		}

		//接続情報が渡された
		if($dsn) {
			//$conの形式をチェック host,dbname,dbuser,dbpass
			if(preg_match("/^(.*)\/(.*)\/(.*)\/(.*)$/",$dsn,$match)) {
				list($dbhost,$dbname,$dbuser,$dbpass) = explode("/",$dsn);
				$this->pdo = new PDO("mysql:host={$dbhost}; dbname={$dbname}",$dbuser,$dbpass,$options);
				$this->pdo->query("SET NAMES utf8");
			} else {
				$this->message = 'invalid dsn';
				echo 'invalid dsn';
				exit();
			}

		} else {
			//接続情報が無かった場合のみ、定数がセットされていればそれを使用する
			if(defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASSWORD')) {
				$this->pdo = new PDO("mysql:host=".DB_HOST."; dbname=".DB_NAME, DB_USER, DB_PASSWORD,$options);
				$this->pdo->query("SET NAMES utf8");
			} else {
				$this->message = 'invalid defined';
				echo 'invalid defined';
				exit();
			}
		}
	}


	//クエリを実行して、結果オブジェクトを返す
	function query($sql,$param=array()) {
		//クエリから改行コードを削除する
		$sql = preg_replace("/\r\n|\r|\n/", " ", $sql);

		$mode = 'number';

		//ログを初期化
		$this->log = array();
		$this->log[] = "proceccing {$sql}";

		//配列ではない場合、配列にする
		if(!is_array($param)) {
			$this->log[] = "param is not array";
			$param = array($param);
		}

		//$paramに何か入っていたらプリペアドステートメントとして扱う
		if(count($param)) {
			if(isset($param[0])) {
				$this->log[] = "mode: numbering place holder";
			} else {
				$mode = 'named';
				$this->log[] = "mode: named place holder";

				//連想配列が渡されたらプレースホルダに対応するキーがあるか確認する
				if(preg_match_all("/\:([A-z0-9]{1,})/",$sql,$match)) {
					foreach($match[1] as $key => $val) {
						if($key > 0) {
							$this->log[] = "find placeholder:{$val}\n";

							//キーが無い場合はメッセージに追加
							if(!isset($param[$val]) && !isset($param[":{$val}"])) {
								$this->log[] = "undefined param:{$val}\n";

								$param[$val] = 'undefined';
								$this->message .= "replacement key {$val} is not definition";
								//$this->message;
							}
						}
					}

				}
			}

			//プリペア
			$rs = $this->pdo->prepare($sql);

			//クエリ内の名無しプレースホルダの数を把握
			$num = 0;
			if(preg_match_all("/\?/",$sql,$match)) {
				$num = count($match[0]);
			}
			if($mode == 'number') {
				$this->log[] = "numbering place holder count {$num}";
			}


			//名無しプレースホルダのカウンタ
			$current = 0;

			//全パラメタ
			foreach($param as $key => $val) {
				//キーが数値かどうか
				if(is_numeric($key)) {
					//名無しプレースホルダを使い切ってなければ
					if($current < $num) {
						//中身が数値かチェック
						if(is_numeric($val)) {
							//バインド
							$this->log[] = "bind {$key} to {$val}";
							$rs->bindValue($key+1,$val,PDO::PARAM_INT);
						} else {
							//バインド
							$this->log[] = "bind {$key} to {$val}";
							$rs->bindValue($key+1,$val,PDO::PARAM_STR);
						}
						//カウントを進める
						$current++;
					}
				} else {
					//キーが数値ではない

					//キーがコロンから始まっていたら取り除く
					if(preg_match("/^\:(.*)$/",$key,$match)) {
						$key = $match[1];
					}

					//クエリに該当の名前付きプレースホルダがあればバインドする
					if(preg_match("/\:{$key}(\s|\,|\(|\)|$)/",$sql)) {
						//数値かどうかチェック
						if(is_numeric($val)) {
							//バインド
							$this->log[] = "bind {$key} to {$val}";
							$rs->bindValue(':'.$key,$val,PDO::PARAM_INT);
						} else {
							//バインド
							$this->log[] = "bind {$key} to {$val}";
							$rs->bindValue(':'.$key,$val,PDO::PARAM_STR);
						}

					}
				}
			}

			//実行
			$rs->execute();
			$this->log[] = "execute sql: {$rs->queryString}";
		} else {
			$rs = $this->pdo->query($sql);
		}

		//結果セットを各メソッドに返す
		return $rs;
	}


	//全て取得
	function getAll($sql,$param=array()) {
		$rs = $this->query($sql,$param);
		if(method_exists($rs,"fetchAll")) {
			$result = $rs->fetchAll(PDO::FETCH_ASSOC);
			$rs->closeCursor();
			return $result;
		} else {
			echo $sql;
		}
	}


	//ひとつ取得
	function getOne($sql,$param=array()) {
		$rs     = $this->query($sql,$param);
		if(method_exists($rs,"fetch")) {
			$result = $rs->fetch();
			$rs->closeCursor();
			return (is_array($result) ? $result[0] : $result);
		} else {
			echo $sql;
		}
	}


	//最初の1列を添字配列として取得
	function getCol($sql,$param=array()) {
		$rs  = $this->query($sql,$param);
		if(method_exists($rs,"fetch")) {
			$all = $rs->fetchAll(PDO::FETCH_COLUMN);
			$result = array();
			foreach($all as $val) {
				$result[] = $val;
			}
			$rs->closeCursor();
			return $result;
		} else {
			echo $sql;
		}
	}


	//最初のキーを連装配列のキーとして取得
	function getAssoc($sql,$param=array()) {
		$rs  = $this->query($sql,$param);
		if(method_exists($rs,"fetch")) {
			$result = $rs->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);

			/*
			$all = $rs->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);
			$result = array();
			foreach($all as $arr) {
				$result[$arr[0]] = $arr;
			}
			$rs->closeCursor();
			*/
			return $result;
		} else {
			echo $sql;
		}
	}


	//添字配列として取得
	function getArray($sql,$param=array()) {
		$rs  = $this->query($sql,$param);
		if(method_exists($rs,"fetch")) {
			$result = $rs->fetchAll(PDO::FETCH_NUM);
			/*
			$all = $rs->fetchAll();
			$result = array();
			foreach($all as $arr) {
				$result[$arr[0]] = $arr[1];
			}
			$rs->closeCursor();
			*/
			return $result;
		} else {
			echo $sql;
		}
	}



	//実行するだけ　帰り値がいらない時用
	function exec($sql,$param=array()) {
		$this->query($sql,$param);
	}


	//一行取得
	function getRow($sql,$param=array()) {
		$rs = $this->query($sql,$param);
		if(method_exists($rs,"fetch")) {
			$result = $rs->fetch(PDO::FETCH_ASSOC);
			$rs->closeCursor();
			return $result;
		} else {
			echo $sql;
		}
	}
}


