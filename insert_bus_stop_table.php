<?php

/*
	国土交通省の全国バス停座標データ(XML)を利用
	京都府内の全てのバス（民間含む）が入ってあるので、京都市営バスのみ抜き出す。
	このデータは前半がバス停のidと位置座標、後半がバス停名や運営元等のデータに分かれている。

	(取り方)
	まず前半パートを別ファイルに分けて、id=>座標の配列データ(php)にする。
	次に、後半パートをphpで読み込んで、オブジェクト内の"busType"の値で市営バスかどうか判別し
	市営の場合、"$busStopArray"にid=>バス停名として格納していく。
*/

//座標ファイル(配列)を読み込み
include_once('point_list.php');

//変数初期化
$busStopArray = array();

$xml = simplexml_load_file('stop_name.xml');
//使用している名前空間を取得します。
$nameSpaces = $xml->getNamespaces(true);
// var_dump($nameSpaces);exit;


// $xml=$xml->children($nameSpaces['ksj']);
// var_dump($xml);exit;

//XMLオブジェクトとBusStopオブジェクト単位にバラしてループ
foreach($xml->children($nameSpaces['ksj']) as $BusStop) {

	//まずバス停名とidを変数に格納
	$BusStop_id =  $BusStop->position;
	$BusStop_name = $BusStop->busStopName;

	foreach($BusStop->children($nameSpaces['ksj']) as $bri){

		if($bri->BusRouteInformation->busType == "2") {
			//busType2がある場合、$busStopArrayに格納してこのBusStopのチェックを終える。
			$busStopArray["$BusStop_id"] = $BusStop_name;
			continue;
		}
	}
}
// var_dump($busStopArray);
// var_dump($point_list);

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


	//DB bus_stopテーブル書き込み
	//プリペアドステートメントは固定でバインドをループ内で入れ替えていく。
	$stmt = $pdo -> prepare("INSERT INTO bus_stop(bus_stop_id, bus_stop_name, north_latitude, east_longitude) VALUES ( :bus_stop_id, :bus_stop_name, :north_latitude, :east_longitude)");
	foreach($busStopArray as $id => $name){
		$stmt->bindValue(':bus_stop_id', $id, PDO::PARAM_INT);
		$stmt->bindValue(':bus_stop_name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':north_latitude', $point_list["$id"][0], PDO::PARAM_STR);
		$stmt->bindValue(':east_longitude', $point_list["$id"][1], PDO::PARAM_STR);
		$stmt->execute();
	}
	//完了
	// echo "データベースへ{$spot}の新規追加完了<br />";
} catch(Exception $e) {
	//エラーメッセージを表示
	echo $e->getMessage();
}
// 切断
$pdo = null;

?>