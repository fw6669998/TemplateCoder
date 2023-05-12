<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use src\DB;
use src\util;

$table = util::param('table');

$cols = DB::getColumnInfos($table);
//获取最后一个元素的key
$lastKey = count($cols) == 0 ? '' : $lastKey = array_keys($cols)[count($cols) - 1];
?>
[
<?php foreach ($cols as $col => $info) {
    if ($info->isReserved()) continue; ?>
    {
        "description": "<?= $info->getComment() ?>",
        "field_type": "<?=util::getApiPostType($info) ?>",
        "is_checked": 1,
        "key": "<?= $col ?>",
        "value": "<?=util::getExampleVal($info)?>",
        "not_null": <?= $info->getNotnull()?1:-1 ?>,
        "type": "Text",
        "contentType": ""
    }<?= $col == $lastKey ? '' : ','; ?>

<?php } ?>
]
