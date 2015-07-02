<?php
namespace Locomo;
class Help_Usr
{
// index_admin
public $index_admin = <<< EOF

## 概要

ユーザの一覧です。

「検索」のアイコンをクリックすると、検索条件を開くことができます。

また、権限を所有している場合は、「閲覧」「編集」「削除」等を行うことができます。

表の見出しにあたる部分をクリックするとソートできます。

EOF;

// index_deleted
public $index_deleted = <<< EOF

## 概要

ユーザの削除済み項目の一覧です。

削除されているユーザはログインできません。

「検索」のアイコンをクリックすると、検索条件を開くことができます。

また、権限を所有している場合は、「閲覧」「編集」「復活」「完全な削除」等を行うことができます。

表の見出しにあたる部分をクリックするとソートできます。

EOF;

// index_yet
public $index_yet = <<< EOF

## 概要

ユーザの予約項目の一覧です。

予約ユーザは、有効期日になるまでログインできません。

「検索」のアイコンをクリックすると、検索条件を開くことができます。

また、権限を所有している場合は、「閲覧」「編集」「削除」等を行うことができます。

表の見出しにあたる部分をクリックするとソートできます。

EOF;

// index_expired
public $index_expired = <<< EOF

## 概要

有効期限の切れたユーザの一覧です。

有効期限の切れたユーザは、ログインできません。権限を有する者が「編集」を行うことで、有効期限を伸ばせば、ログインできるようになります。

「検索」のアイコンをクリックすると、検索条件を開くことができます。

また、権限を所有している場合は、「閲覧」「編集」「削除」等を行うことができます。

表の見出しにあたる部分をクリックするとソートできます。

EOF;

// index_invisible
public $index_invisible = <<< EOF

## 概要

不可視ユーザの一覧です。

不可視ユーザは、ログインできません。権限を有する者が「編集」で「可視ユーザ」に変更することで、ログインできるようになります。

「検索」のアイコンをクリックすると、検索条件を開くことができます。

また、権限を所有している場合は、「閲覧」「編集」「削除」等を行うことができます。

表の見出しにあたる部分をクリックするとソートできます。

EOF;

// index_all
public $index_all = <<< EOF

## 概要

すべてのユーザです。

「検索」のアイコンをクリックすると、検索条件を開くことができます。

また、権限を所有している場合は、「閲覧」「編集」「削除」等を行うことができます。

表の見出しにあたる部分をクリックするとソートできます。

EOF;

// create
public $create = <<< EOF

## 概要

新規ユーザ作成画面です。

必要項目を入力して、「保存」をしてください。

## 権限

管理者はすべてのユーザのすべての項目を変更できます。

管理者以外のユーザは、自分の情報だけ編集できます。管理者以外のユーザが自分の情報を編集する際は、常に現在のパスワードが必要です。

## ユーザグループ

あらかじめユーザグループが設定されていると、ユーザグループを選択できます。ユーザグループは複数選択が可能です。

また、権限用ユーザグループ以外が存在している場合は、その中から「代表ユーザグループ」を選択できます。代表ユーザグループは、スケジューラ使用時など、主たるユーザグループとして振る舞い、いくつかの場面で役に立ちます。
EOF;

// edit
public $edit = <<< EOF

## 概要

ユーザ編集画面です。

必要項目を入力して、「保存」をしてください。

## 権限

管理者はすべてのユーザのすべての項目を変更できます。

管理者以外のユーザは、自分の情報だけ編集できます。管理者以外のユーザが自分の情報を編集する際は、常に現在のパスワードが必要です。

## ユーザグループ

あらかじめユーザグループが設定されていると、ユーザグループを選択できます。ユーザグループは複数選択が可能です。

また、権限用ユーザグループ以外が存在している場合は、その中から「代表ユーザグループ」を選択できます。代表ユーザグループは、スケジューラ使用時など、主たるユーザグループとして振る舞い、いくつかの場面で役に立ちます。
EOF;


}