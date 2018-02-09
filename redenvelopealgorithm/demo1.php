<?php
/**
 * 每个红包的最大金额是: (剩余金额/剩余红包数)*2, 需要开始的时候预先分配给每个人一个豆
 *
 */
function randBean($total_bean, $total_packet)
{
    $min_bean = 1;
    $max_bean = 5000;
    $range = 2;
    
    $total_bean = $total_bean - $total_packet * $min_bean; //每个人预留一个最小值

    $list = [];
    $min = 1;
    while(count($list) < $total_packet){
        $max = floor($total_bean / $total_packet) * $range;
        $bean = rand($min, $max);

        if ($bean <= $max_bean - 1) {
            $list[] = $bean;
            $total_bean -= $bean;
        }
    }

    $list[] = $total_bean;//剩余的金豆作为最后一个红包

    //合并
    foreach ($list as $k => $v) {
        $list[$k] += $min_bean;
    }

    return $list;
}

print_r(randBean(100, 20));
