<?php

//エンコード
define("CHAR_SET", "utf-8");

//Simple HTML DOM Parserの読み込み
include('./simple_html_dom.php');

//変数初期化
$spot		= "";
$line		= "";
$start		= "";
$times		= array();

//対象のサイトのhtmlソースを取得
$html = file_get_html('http://www.city.kyoto.jp/kotsu/busdia/hyperdia/034021.htm');

//対象のサイトがutf-8以外の場合に備えてutf-8に変換。京都市バスのサイトはshitjis
mb_language("Japanese"); // mb_convert_encoding()でautoを使うときに必要。
$source = mb_convert_encoding($html, CHAR_SET, "auto");

// DOM化
$html = str_get_html($source);

foreach($html->find('[bgcolor=#ffebc3]') as $stop)

foreach($html->find('table tr') as $tr){
	foreach($tr->find('b') as $b){

	}
}
// $tr = $html->find('table tr');
/*
	find()は$htmlオブジェクトにしか使えない
*/
$spot = $html->find('table tr',0)->find('td',0)->innertext;
$line = $html->find('table tr',1)->find('b',0)->innertext;
$start = $html->find('table tr',5)->find('td',1)->innertext;

//分を時間台配列に格納していく
$minuts1 = $html->find('table tr', 2);

//foreachのループ箇所をしているための何列目かのインデックスの初期化
$i = 0;

foreach($html->find('table tr') as $tr){
	//trが５個目から２３個目の間
	if($i >= 3 && $i <= 23){
		$j = 0;
		foreach($tr->find('td') as $td){
			if($j != 1 && $j != 3){
				if($j == 2){ //$j2→1 $j→2 にして[平日:0,土曜:1,日曜:2]
					$j = $j -1;
				}elseif($j == 4){
					$j = $j -2;
				}
				$times[$i][$j] = $td->innertext;
				if($j == 1){
					$j = $j + 1;
				}elseif($j == 2){
					$j = $j +2;
				}
			}
			$j++;
		}
	}
	$i++;
}
//DB接続
try {
	$pdo = new PDO(
		// 'mysql:dbname=KyotoCityBus;host=127.0.0.1;charset=utf8',//mampだとエラーでた。
		'mysql:dbname=KyotoCityBus;host=localhost;charset=utf8',
		'root',
		'root',
		array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
		)
	);
	//DBstopテーブル書き込み
	// $name = $spot;
	// $stmt = $pdo -> prepare("INSERT INTO stop (name) VALUES (:name)");
	// $stmt->bindValue(':name', $name, PDO::PARAM_STR);
	// $stmt->execute();

	//DB timeテーブル書き込み
	$stmt = $pdo -> prepare("INSERT INTO time (stop_id, h5, h6, h7, h8, h9, h10, h11, h12, h13, h14, h15, h16, h17, h18, h19, h20, h21, h22, h23) VALUES ( :stop_id, :h5, :h6, :h7, :h8, :h9, :h10, :h11, :h12, :h13, :h14, :h15, :h16, :h17, :h18, :h19, :h20, :h21, :h22, :h23)");
	$stmt->bindValue(':stop_id', 1, PDO::PARAM_STR);
	$stmt->bindValue(':h5', $times[5][0], PDO::PARAM_STR);
	$stmt->bindValue(':h6', $times[6][0], PDO::PARAM_STR);
	$stmt->bindValue(':h7', $times[7][0], PDO::PARAM_STR);
	$stmt->bindValue(':h8', $times[8][0], PDO::PARAM_STR);
	$stmt->bindValue(':h9', $times[9][0], PDO::PARAM_STR);
	$stmt->bindValue(':h10', $times[10][0], PDO::PARAM_STR);
	$stmt->bindValue(':h11', $times[11][0], PDO::PARAM_STR);
	$stmt->bindValue(':h12', $times[12][0], PDO::PARAM_STR);
	$stmt->bindValue(':h13', $times[13][0], PDO::PARAM_STR);
	$stmt->bindValue(':h14', $times[14][0], PDO::PARAM_STR);
	$stmt->bindValue(':h15', $times[15][0], PDO::PARAM_STR);
	$stmt->bindValue(':h16', $times[16][0], PDO::PARAM_STR);
	$stmt->bindValue(':h17', $times[17][0], PDO::PARAM_STR);
	$stmt->bindValue(':h18', $times[18][0], PDO::PARAM_STR);
	$stmt->bindValue(':h19', $times[19][0], PDO::PARAM_STR);
	$stmt->bindValue(':h20', $times[20][0], PDO::PARAM_STR);
	$stmt->bindValue(':h21', $times[21][0], PDO::PARAM_STR);
	$stmt->bindValue(':h22', $times[22][0], PDO::PARAM_STR);
	$stmt->bindValue(':h23', $times[23][0], PDO::PARAM_STR);
	$stmt->execute();
	//完了
	echo "データベースへ{$spot}の新規追加完了<br />";
} catch(Exception $e) {
	//エラーメッセージを表示
	echo $e->getMessage();
}
// 切断
$pdo = null;

 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title><?php echo $spot; ?></title>

	<!-- CSS -->
	<link rel="stylesheet" href="./style.css">

	<!-- JS -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

	<!-- favicon -->
	<link rel="shortcut icon" href="favicon.ico" />

	<!-- for IEs -->
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
	<!--[if lt IE 8]><script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script><![endif]-->
	<!--[if lt IE 9]><script src="http://html4shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

	<!-- OGP -->
	<meta property="og:title" content="" />
	<meta property="og:type" content="" />
	<meta property="og:url" content="" />
	<meta property="og:site_name" content="" />
	<meta property="og:description" content="" />
	<meta property="og:image" content="" />
	<meta property="og:locale" content="ja_JP" />
	<meta property="fb:app_id" content="" />
	<meta property="fb:page_id" content="" />
	<meta property="fb:admins" content="" />
</head>
<body>
<?php
	echo "停留所　:　".$spot."<br />";
	echo "系統　:　".$line."<br />";
	echo "始発時間　:　".$start."時台<br />";
?>
<table>
	<tr>
		<th>時間台</th>
		<td>平日</td>
		<td>土曜日</td>
		<td>日曜日</td>
	</tr>
	<tr>
		<th>5</th>
		<td><?php echo $times[5][0]; ?></td>
		<td><?php echo $times[5][1]; ?></td>
		<td><?php echo $times[5][2]; ?></td>
	</tr>
	<tr>
		<th>6</th>
		<td><?php echo $times[6][0]; ?></td>
		<td><?php echo $times[6][1]; ?></td>
		<td><?php echo $times[6][2]; ?></td>
	</tr>
	<tr>
		<th>7</th>
		<td><?php echo $times[7][0]; ?></td>
		<td><?php echo $times[7][1]; ?></td>
		<td><?php echo $times[7][2]; ?></td>
	</tr>
	<tr>
		<th>8</th>
		<td><?php echo $times[8][0]; ?></td>
		<td><?php echo $times[8][1]; ?></td>
		<td><?php echo $times[8][2]; ?></td>
	</tr>
	<tr>
		<th>9</th>
		<td><?php echo $times[9][0]; ?></td>
		<td><?php echo $times[9][1]; ?></td>
		<td><?php echo $times[9][2]; ?></td>
	</tr>
	<tr>
		<th>10</th>
		<td><?php echo $times[10][0]; ?></td>
		<td><?php echo $times[10][1]; ?></td>
		<td><?php echo $times[10][2]; ?></td>
	</tr>
	<tr>
		<th>11</th>
		<td><?php echo $times[11][0]; ?></td>
		<td><?php echo $times[11][1]; ?></td>
		<td><?php echo $times[11][2]; ?></td>
	</tr>
	<tr>
		<th>12</th>
		<td><?php echo $times[12][0]; ?></td>
		<td><?php echo $times[12][1]; ?></td>
		<td><?php echo $times[12][2]; ?></td>
	</tr>
	<tr>
		<th>13</th>
		<td><?php echo $times[13][0]; ?></td>
		<td><?php echo $times[13][1]; ?></td>
		<td><?php echo $times[13][2]; ?></td>
	</tr>
	<tr>
		<th>14</th>
		<td><?php echo $times[14][0]; ?></td>
		<td><?php echo $times[14][1]; ?></td>
		<td><?php echo $times[14][2]; ?></td>
	</tr>
	<tr>
		<th>15</th>
		<td><?php echo $times[15][0]; ?></td>
		<td><?php echo $times[15][1]; ?></td>
		<td><?php echo $times[15][2]; ?></td>
	</tr>
	<tr>
		<th>16</th>
		<td><?php echo $times[16][0]; ?></td>
		<td><?php echo $times[16][1]; ?></td>
		<td><?php echo $times[16][2]; ?></td>
	</tr>
	<tr>
		<th>17</th>
		<td><?php echo $times[17][0]; ?></td>
		<td><?php echo $times[17][1]; ?></td>
		<td><?php echo $times[17][2]; ?></td>
	</tr>
	<tr>
		<th>18</th>
		<td><?php echo $times[18][0]; ?></td>
		<td><?php echo $times[18][1]; ?></td>
		<td><?php echo $times[18][2]; ?></td>
	</tr>
	<tr>
		<th>19</th>
		<td><?php echo $times[19][0]; ?></td>
		<td><?php echo $times[19][1]; ?></td>
		<td><?php echo $times[19][2]; ?></td>
	</tr>
	<tr>
		<th>20</th>
		<td><?php echo $times[20][0]; ?></td>
		<td><?php echo $times[20][1]; ?></td>
		<td><?php echo $times[20][2]; ?></td>
	</tr>
	<tr>
		<th>21</th>
		<td><?php echo $times[21][0]; ?></td>
		<td><?php echo $times[21][1]; ?></td>
		<td><?php echo $times[21][2]; ?></td>
	</tr>
	<tr>
		<th>22</th>
		<td><?php echo $times[22][0]; ?></td>
		<td><?php echo $times[22][1]; ?></td>
		<td><?php echo $times[22][2]; ?></td>
	</tr>
	<tr>
		<th>23</th>
		<td><?php echo $times[23][0]; ?></td>
		<td><?php echo $times[23][1]; ?></td>
		<td><?php echo $times[23][2]; ?></td>
	</tr>
</table>


</body>
</html>
