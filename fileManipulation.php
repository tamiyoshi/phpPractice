<?php
// 引数のバリデーション
function validateArgs($args) {
    // 引数が3つ未満の場合はエラーを返す
    if (count($args) < 3) {
        // エラーを返す
        die("エラー: 引数が不足しています。\n");
    }
    // コマンドに応じて引数の数をチェック   
    $command = $args[1];
    // コマンドがreverseまたはcopyの場合、引数の数が4つでない場合はエラーを返す
    if (($command === 'reverse' || $command === 'copy') && count($args) != 4) {
        die("エラー: $command の引数の数が正しくありません。\n");
    // コマンドがduplicate-contentsの場合、引数の数が4つでない場合はエラーを返す
    } elseif ($command === 'duplicate-contents' && (count($args) != 4 || !is_numeric($args[3]))) {
        die("エラー: duplicate-contents の引数が正しくありません。n は数値である必要があります。\n");
    // コマンドがreplace-stringの場合、引数の数が5つでない場合はエラーを返す
    } elseif ($command === 'replace-string' && count($args) != 5) {
        die("エラー: replace-string の引数の数が正しくありません。\n");
    // コマンドが存在しない場合はエラーを返す   
    } elseif (!in_array($command, ['reverse', 'copy', 'duplicate-contents', 'replace-string'])) {
        die("エラー: 不明なコマンド $command です。\n");
    }
}

// ファイルの内容を取得する関数
function getFileContents($path) {
    // ファイルの内容を取得
    $contents = file_get_contents($path);
    // ファイルの内容が取得できない場合はエラーを返す
    if ($contents === false) {
        die("エラー: ファイル $path を読み取れませんでした。\n");
    }
    return $contents;
}

// ファイルの内容を出力する関数
function putFileContents($path, $contents) {
    // ファイルの内容を出力
    if (file_put_contents($path, $contents) === false) {
        die("エラー: ファイル $path に書き込めませんでした。\n");
    }
}

// ファイルの内容を逆順にする関数ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
function reverseFile($inputPath, $outputPath) {
    // ファイルの内容を取得
    $contents = getFileContents($inputPath);
    // ファイルの内容を逆順にする
    $reversedContents = mb_strrev($contents);
    // ファイルの内容を出力
    putFileContents($outputPath, $reversedContents);
}
// マルチバイト文字列を逆順にするための関数
function mb_strrev($string) {
    // UTF-8に変換してから文字を分割し、逆順にして結合
    return implode('', array_reverse(preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY)));
}

// ファイルをコピーする関数ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
function copyFile($inputPath, $outputPath) {
    // ファイルをコピー
    if (!copy($inputPath, $outputPath)) {
        // ファイルをコピーできない場合はエラーを返す
        die("エラー: ファイルを $inputPath から $outputPath にコピーできませんでした。\n");
    }
}

// ファイルの内容を複製する関数ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
function duplicateContents($inputPath, $n) {
    // ファイルの内容を取得
    $contents = getFileContents($inputPath);
    // ファイルの内容を複製
    $duplicatedContents = str_repeat($contents, $n);
    // ファイルの内容を出力
    putFileContents($inputPath, $duplicatedContents);
}

// ファイルの内容を置換する関数ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
function replaceString($inputPath, $needle, $newString) {
    // ファイルの内容を取得
    $contents = getFileContents($inputPath);
    // 置換する文字列が存在するか確認
    if (strpos($contents, $needle) === false) {
        die("エラー: 置換する文字列 '$needle' がファイルに存在しません。\n");
    }
    // ファイルの内容を置換
    $replacedContents = str_replace($needle, $newString, $contents);
    // ファイルの内容を出力
    putFileContents($inputPath, $replacedContents);
}

// 引数を取得
$args = $argv;
// 引数のバリデーション
validateArgs($args);

// コマンドを取得
$command = $args[1];
// コマンドに応じて処理を実行
switch ($command) {
    case 'reverse':
        reverseFile($args[2], $args[3]);
        break;
    case 'copy':
        copyFile($args[2], $args[3]);
        break;
    case 'duplicate-contents':
        duplicateContents($args[2], (int)$args[3]);
        break;
    case 'replace-string':
        replaceString($args[2], $args[3], $args[4]);
        break;
}

// 処理が完了したことを通知
echo "コマンド[$command]が正常に完了しました。\n";
