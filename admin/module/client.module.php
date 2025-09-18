<?php
/*
	client
*/
$RP->set("module",$module);

$split = 100;	// １ページ区切り件数
$pno   = $RP->get("pno",1);
$mode  = $RP->get("mode","list");


$RP->set("mode",$mode);
switch($mode) {
	default:
	break;


	case "register":
		$position = $RP->get("position","form");
		
		switch($position) {
			case "form":
			default:
				$formdata = array(
					"client_id" => "",
					"caption"   => "",
					"rstamp"    => 0,
				);
				$RP->set("formdata",$formdata);
				$RP->set("current_page","新規登録");
				$RP->set("next_position","modify");
				$RP->disp("client_form");
			break;


			case "modify":
			case "execute":
				$formdata = array(
					"client_id"      => $RP->get("client_id",""),
					"caption"=> $RP->get("caption",""),
					"rstamp"   => $RP->get("rstamp",""),
				);
				$err = "";
				if(strlen($err)>0) {
					$RP->set("msg", $err);
					$RP->set("formdata",$formdata);
					$RP->set("current_page","新規登録");
					$RP->set("next_position","modify");
					$RP->disp("client_form");
					break 2;
				}
				if($position == "modify") {
					$RP->set("formdata",$formdata);
					$RP->set("current_page","確認画面");
					$RP->set("next_position","execute");
					$RP->disp("client_modify");
				} else {
					//linkの生成と文字のエスケープ
					$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					$formdata["client_id"]      = mysqli_real_escape_string($link, htmlspecialchars_decode($formdata["client_id"],ENT_QUOTES));
					$formdata["caption"]= mysqli_real_escape_string($link, htmlspecialchars_decode($formdata["caption"],ENT_QUOTES));
					$formdata["rstamp"]   = mysqli_real_escape_string($link, htmlspecialchars_decode($formdata["rstamp"],ENT_QUOTES));
					unset($link);
					
					$query = "INSERT INTO {$module} (`client_id`, `caption`, `rstamp`) 
							  VALUES('{$formdata["client_id"]}','{$formdata["caption"]}','{$formdata["rstamp"]}')";
					$DB->query($query);
					$msg = "登録完了";
					$RP->set("msg",$msg);
				}
			break;
		}
	break;


	case "update":
		$position = $RP->get("position","form");
		$no       = $RP->get("no","");
		$chk      = $DB->getOne("SELECT count(no) FROM {$module} WHERE no = '{$no}'");
		
		if($chk) {
			switch($position) {
				case "form":
				default:
					$formdata = $DB->getRow("SELECT no,`client_id`,`caption`,`rstamp` FROM {$module} WHERE no = {$no}");
					$RP->set("formdata",$formdata);
					$RP->set("current_page","修正");
					$RP->set("next_position","modify");
					$RP->disp("client_form");
				break;


				case "modify":
				case "execute":
					$formdata = array(
						"no"   => $RP->get("no",$no),
						"id"         => $RP->get("id",""),
						"client_id"      => $RP->get("client_id",""),
						"caption"=> $RP->get("caption",""),
						"rstamp"   => $RP->get("rstamp","")
					);
					$err = "";
					if(strlen($err)>0) {
						$RP->set("msg",$err);
						$RP->set("formdata",$formdata);
						$RP->set("current_page","修正");
						$RP->set("next_position","modify");
						$RP->disp("client_form");
						break 2;
					}
					if($position == "modify") {
						$RP->set("formdata",$formdata);
						$RP->set("current_page","修正確認");
						$RP->set("next_position","execute");
						$RP->disp("client_modify");
					} else {
						//linkの生成と文字のエスケープ
						$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						$formdata["client_id"]      = mysqli_real_escape_string($link, htmlspecialchars_decode($formdata["client_id"],ENT_QUOTES));
						$formdata["caption"]= mysqli_real_escape_string($link, htmlspecialchars_decode($formdata["caption"],ENT_QUOTES));
						$formdata["rstamp"]   = mysqli_real_escape_string($link, htmlspecialchars_decode($formdata["rstamp"],ENT_QUOTES));
						unset($link);
						
						$query = "UPDATE {$module} SET
							`id` = '{$formdata["id"]}',
							`client_id` = '{$formdata["client_id"]}',
							`caption` = '{$formdata["caption"]}',
							`rstamp` = '{$formdata["rstamp"]}',
						WHERE no = {$no} LIMIT 1";
						$msg = "修正完了";
						
						$DB->query($query);
						$RP->set("msg",$msg);
					}
				break;
			}
		} else {
			$RP->set("msg","Noが見つかりません");
		}
	break;


	case "delete":
		$position = $RP->get("position","form");
		$no       = $RP->get("no","");
		$chk      = $DB->getOne("SELECT count(no) FROM {$module} WHERE no = '{$no}'");
		if($chk) {
			switch($position) {
				case "form":
				default:
					$formdata = $DB->getRow("SELECT no,`client_id`,`caption`,`rstamp` FROM {$module} WHERE no = {$no}");
					$RP->set("formdata",$formdata);
					$RP->set("current_page","削除確認");
					$RP->set("next_position","execute");
					$RP->disp("client_modify");
				break;


				case "execute":
					$query = "DELETE FROM {$module} WHERE no = '{$no}' LIMIT 1";
					$DB->query($query);
					$msg = "削除完了";
					$RP->set("msg",$msg);
				break;
			}
		} else {
			$RP->set("msg","Noが見つかりません");
		}
	break;
}




/*　ヘッダ一覧　*/
if(!$RP->isDisp) {
	$count = $DB->getOne("SELECT count(no) FROM {$module}");
	
	if(!empty($count)) {
		$page = $RP->paging($pno, $count, $split);
		$RP->set("page", $page);
		$client = $DB->getAll("SELECT no,`client_id`,`caption`,`rstamp` FROM {$module} order by no ASC LIMIT {$page['limit']},{$split}");
		$RP->set("client", $client);
	}
	
	$RP->set("current_page","client一覧");
	$RP->disp("client");
}
