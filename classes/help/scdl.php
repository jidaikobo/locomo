<?php
namespace Locomo;
class Help_Scdl
{
// calendar
public $calendar = <<< EOF

## 概要

 スケジューラのトップは、当年当月カレンダが表示されます。当日は、薄い黄色で日付が示されます。カレンダのタイトルが表示され、マウスオーバーをするとそのスケジュールの概要が画面に表示されます。新しいスケジュールを登録する場合、カレンダの日にち右横に表示されている「+」または、コンテクストメニューの「新規作成」を押す事で登録画面へジャンプします。

## コンテクストメニュー

### 今月

 当年当月のカレンダが、表示されます。

### 月表示

 選択月のカレンダが、表示されます。

### 週表示

 選択週のカレンダが、週表示されます。

### 日表示

 選択日のカレンダが、日表示されます。

### 新規作成

 スケジュールの新規作成画面へジャンプします。


## 検索項目

### ユーザーグループ

 リストボックスより、事務所やあいあい教室などを選択しカレンダに登録されているユーザーグループを絞り込んで検索します。

### ユーザー

 リストボックスより、ユーザーを選択しカレンダに登録されているユーザーを絞り込んで検索します。

### 絞り込み（ボタン）

 指定した検索条件で、カレンダ情報の抽出を実行します。

### 絞り込みを解除（ボタン）

 指定した検索条件をクリアします（全件表示）。


EOF;

// create
public $create = <<< EOF

## 概要

 スケジューラの新規作成を行います。仮登録や非公開・重複チェックなどの機能があります。

## 項目一覧

### タイトル

 スケジュールのタイトルを、入力します。

### 繰り返し区分

 リストボックスより、毎日や毎週など、繰り返してスケジュールを登録する際に選択します。

### 期間

 スケジュールの期間を、カレンダと時刻から選択して入力します。直接入力も可能です。

### 詳細設定

 スケジュールが、仮登録か終日・非公開・重複チェックを行うかの設定をします。

### メッセージ

 スケジュールの概要や内容を、入力します。

### メンバー

 スケジュールに参加するメンバーを、リストボックスより指定します。また、出席確認を取ったり、特定の人へスケジュールを表示する場合、氏名一覧から選択することも可能です。

### 施設選択

 スケジュールを開催する施設を、リストボックスより指定します。特定の施設を利用する場合、施設一覧より選択を行います。

### 表示するグループ

 スケジュールを表示するグループを、ラジオボタンで指定します。グループ指定をする場合は、表示グループをリストボックスより指定します。

### 施設使用目的区分

 リストボックスより、貸室・会議を指定します。

### 施設利用人数

 施設を利用する人数を、入力します。

### 作成者

 スケジュールを作成したグループと氏名を、リストボックスより選択します。

### 保存する（ボタン）

 作成したスケジュールを、保存します。


EOF;

// viewdetail
public $viewdetail = <<< EOF

## 概要

 カレンダに既に入力済みのスケジュールを押すと、スケジュールの詳細を閲覧する事が出来ます。


EOF;

// edit
public $edit = <<< EOF

## 概要

 既に登録しているスケジュールの編集を行います。

## 項目一覧

### タイトル

 スケジュールのタイトルを、入力します。

### 繰り返し区分

 リストボックスより、毎日や毎週など、繰り返してスケジュールを登録する際に選択します。

### 期間

 スケジュールの期間を、カレンダと時刻から選択して入力します。直接入力も可能です。

### 詳細設定

 スケジュールが、仮登録か終日・非公開・重複チェックを行うかの設定をします。

### メッセージ

 スケジュールの概要や内容を、入力します。

### メンバー

 スケジュールに参加するメンバーを、リストボックスより指定します。また、出席確認を取ったり、特定の人へスケジュールを表示する場合、氏名一覧から選択することも可能です。

### 施設選択

 スケジュールを開催する施設を、リストボックスより指定します。特定の施設を利用する場合、施設一覧より選択を行います。

### 表示するグループ

 スケジュールを表示するグループを、ラジオボタンで指定します。グループ指定をする場合は、表示グループをリストボックスより指定します。

### 施設使用目的区分

 リストボックスより、貸室・会議を指定します。

### 施設利用人数

 施設を利用する人数を、入力します。

### 作成者

 スケジュールを作成したグループと氏名を、リストボックスより選択します。

### 保存する（ボタン）

 作成したスケジュールを、保存します。


EOF;

// attend
public $attend = <<< EOF

EOF;

// somedelete
public $somedelete = <<< EOF

EOF;

// regchange
public $regchange = <<< EOF

EOF;

// copy
public $copy = <<< EOF

## 概要

 カレンダに既に入力済みのスケジュールをコピーし、スケジュールの新規作成が行えます。


EOF;

// delete
public $delete = <<< EOF

## 概要

 既に入力済みのスケジュールを削除します。繰り返し分も対象となります。


EOF;

// delete_others
public $delete_others = <<< EOF

## 概要

 既に入力済みのスケジュールを削除します。指定したスケジュールのみ削除を行います。


EOF;
}
