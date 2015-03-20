<?php
namespace Locomo;
class Help_Acl
{
// actionset_index
public $actionset_index = <<< EOF
# 依存関係について
依存した行為を許可すると、自動的にほかの行為が許可される場合があります。たとえば「項目を編集する権利」を持った人は、「通常項目を閲覧する権利」が自動的に許可されます。

# ログインユーザ権限
「ログインユーザすべて」に行為を許可している場合、個別にアクセス権を与えなくても、許可された状態になっていることがあります。
EOF;
}
