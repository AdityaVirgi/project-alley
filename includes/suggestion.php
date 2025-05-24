<?php
function suggest_table_combination($conn, $guest_count) {
    $result = mysqli_query($conn, "SELECT id, seats, zone FROM tables WHERE status = 'available' ORDER BY seats DESC, id ASC");
    $tables = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tables[] = $row;
    }

    $n = count($tables);
    for ($r = 1; $r <= $n; $r++) {
        $combinations = combination($tables, $r);
        foreach ($combinations as $combo) {
            $total = array_sum(array_column($combo, 'seats'));
            $zones = array_unique(array_column($combo, 'zone'));
            $ids = array_column($combo, 'id');
            $id_diff = max($ids) - min($ids);

            if ($total >= $guest_count && (count($zones) == 1 || $id_diff <= $r)) {
                return $combo;
            }
        }
    }
    return [];
}

function combination($arr, $r) {
    $results = [];
    $n = count($arr);
    $indices = range(0, $r - 1);

    while (true) {
        $combo = [];
        foreach ($indices as $i) $combo[] = $arr[$i];
        $results[] = $combo;

        $i = $r - 1;
        while ($i >= 0 && $indices[$i] == $n - $r + $i) $i--;
        if ($i < 0) break;
        $indices[$i]++;
        for ($j = $i + 1; $j < $r; $j++) $indices[$j] = $indices[$j - 1] + 1;
    }
    return $results;
}
?>
