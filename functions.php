<?php

//******************************************
//テキストデータベースををmysqlにコンバート
//******************************************


function convertdb()
{
    $filepath = dirname(__FILE__).'/kakusuu.txt';
    $f = file_exists($filepath)? fopen($filepath, 'r'): exit('対応するファイルが存在しません');
    $pdo = dbconnect();
    while ($line = fgets($f)) {

        if (!empty($line)) {
            $array = explode("\t", trim($line));
            if (!empty($array[2])) {
                $array[2] = ($offset = mb_strpos($array[2], ','))? mb_substr($array[2], 0, $offset):  $array[2];
            } else {
                $array[2] = 0;
            }
            if (!empty($array[1])) {
                insert($pdo,$array);
            }
        }
    }
}

//*********************************************
//DBと接続
//接続できない場合は強制終了
//*********************************************

function dbconnect()
{
    try{
        return new PDO(
            'mysql:host=localhost;dbname=seimei;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
            );
    }catch(PDOException $e){
        header('Content-Type: text/plain; charset=UTF-8',true,500);
        exit($e->getMessage());
    }
}

//*****************************************************
//データベースに登録
//*****************************************************

function insert($pdo,array $array)
{
    $stmt = $pdo->prepare("INSERT INTO data (mojicode,kanji,kakusuu) VALUES (:unicode, :kanji, :number_of_stroke)");
    $stmt->bindValue(':unicode',$array[0],PDO::PARAM_STR);
    $stmt->bindValue(':kanji',$array[1],PDO::PARAM_STR);
    $stmt->bindValue(':number_of_stroke',(int)$array[2],PDO::PARAM_INT);
    $stmt->execute();
}

//******************************************************* 
//データベースから漢字の画数を取得
//******************************************************** 

function get_strokes($name, $pdo)
{
    foreach($name as $kanji){
        $stmt =$pdo->prepare('SELECT kakusuu FROM data WHERE kanji = ? ');
        $stmt->bindValue(1, $kanji, PDO::PARAM_STR);
        $stmt->execute();
        $fetch[] = $stmt->fetch(PDO::FETCH_COLUMN);
    }
        $coversion = array_map('intval',$fetch);
        return array_sum($coversion);
}

//**********************************************************
//占い結果を出力
//********************************************************** 

function fortuner(int $index)
{
    $kanji_array = [2 => '凶', 3 => '吉', 4 => '凶', 5 => '吉', 6 => '吉', 7 => '半吉', 8 => '吉', 9 => '凶', 10 => '凶', 11 => '大吉', 12 => '凶', 13 => '吉', 14 => '凶', 15 => '吉', 16 => '大吉', 17 => '半吉', 18 => '吉', 19 => '凶', 20 => '凶', 21 => '大吉', 22 => '凶', 23 => '大吉', 24 => '吉', 25 => '吉', 26 => '凶', 27 => '半吉', 28 => '凶', 29 => '吉', 30 => '半吉', 31 => '大吉', 32 => '大吉', 33 => '吉', 34 => '半吉', 35 => '半吉', 36 => '半吉', 37 => '吉', 38 => '半吉', 39 => '吉', 40 => '半吉', 41 => '大吉', 42 => '半吉', 43 => '半吉', 44 => '吉', 45 => '吉', 46 => '凶', 47 => '吉', 48 => '吉', 49 => '半吉', 50 => '凶', 53 => '半吉', 54 => '凶', 55 => '凶', 56 => '凶', 57 => '半吉', 58 => '半吉', 59 => '凶', 60 => '凶', 61 => '半吉', 63 => '半吉'];
    return array_key_exists($index, $kanji_array)? $kanji_array[$index]: false;
}


?>