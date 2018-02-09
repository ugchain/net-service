<?php
/*
 * 线段法, 随机生成几个数字, 将一个直线分成几段, 每段的长度(这个波动比较大, 不是很平均, 如果限制了每个红包的大小, 会比较麻烦)
 * 数据较平均，
 * 但无法计算小数点
 */
function abc ($total_bean, $total_packet)
{    
    $min = 1;
    $max = $total_bean -1;
    $list = [];
    
    $maxLength = $total_packet - 1;
    while(count($list) < $maxLength) {
        $rand = mt_rand($min, $max);
        empty($list[$rand]) && ($list[$rand] = $rand);
    }
    
    $list[0] = 0; //第一个
    $list[$total_bean] = $total_bean; //最后一个
    
    sort($list); //不再保留索引
    
    $beans = [];
    for ($j=1; $j<=$total_packet; $j++) {
        $beans[] = $list[$j] - $list[$j-1];
    }
    
    // return $beans;
    echo '<pre>'; print_r($beans); echo array_sum($beans);
}

abc(5, 6);
