<?php
namespace Locomo;
class Help_Scdl
{
// index_admin
public $index_admin = <<< EOF

## 検索項目

### フリーワード

 入力した文字から、表題で部分一致するメッセージを検索します。

### 絞り込みを解除（ボタン）

 指定した検索条件をクリアにします（全件表示）。

### 表示件数 

 検索結果を1ページに表示する件数（10件〜250件）を、リストボックスより指定します。初期値は25件です。

### 検索（ボタン）

 指定した検索条件で、メッセージの抽出を実行します。


## 検索結果項目

### ID

 メッセージIDが表示されます。

### 表題

 メッセージの表題が表示されます。

### カテゴリ

 メッセージのカテゴリが表示されます。

### 作成日時

 メッセージの作成日時が表示されます。

### 有効期日

 メッセージの表示有効期日が表示されます。

### 公開

 メッセージの公開または非公開が表示されます。

### 投稿者

 メッセージの投稿者名が表示されます。

### 操作

 項目一覧から選択したメッセージの、閲覧・編集・削除を実行します。


EOF;

// index_draft
public $index_draft = <<< EOF

## メッセージボード下書き

 ここでは、ダッシュボード上に表示するメッセージの下書きしたものについて、閲覧・編集・削除を行います。

### 表題

 メッセージタイトルを入力します。

### 公開範囲

 ログインユーザすべて・事務所など、メッセージを公開する範囲をリストボックスから選択します。

### カテゴリ

 リストボックスから選択します。

### 本文

 メッセージ本文を、入力します。

### 先頭表示

 メッセージを、ダッシュボードの先頭に固定するか否かを、リストボックスより選択します。

### 公開

 メッセージが公開か下書きかを、リストボックスより選択します。

### 公開期限

 メッセージの公開期限を指定する場合、直接入力またはカレンダーより公開期限日時を指定します。

### 作成日

 メッセージの作成日を、直接入力またはカレンダーより日時を指定します。

### 保存（ボタン）

 入力したメッセージを、保存します。


EOF;

// edit_categories
public $edit_categories = <<< EOF

## メッセージボード新規作成・編集

 ここでは、ダッシュボード上に表示するメッセージの新規作成および編集を行います。

### 表題

 メッセージタイトルを入力します。

### 公開範囲

 ログインユーザすべて・事務所など、メッセージを公開する範囲をリストボックスから選択します。

### カテゴリ

 リストボックスから選択します。

### 本文

 メッセージ本文を、入力します。

### 先頭表示

 メッセージを、ダッシュボードの先頭に固定するか否かを、リストボックスより選択します。

### 公開

 メッセージが公開か下書きかを、リストボックスより選択します。

### 公開期限

 メッセージの公開期限を指定する場合、直接入力またはカレンダーより公開期限日時を指定します。

### 作成日

 メッセージの作成日を、直接入力またはカレンダーより日時を指定します。

### 保存（ボタン）

 入力したメッセージを、保存します。


EOF;
}
