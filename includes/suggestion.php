<?php
function suggest_table_combination($conn, $guest_count) {
    $result = mysqli_query($conn, "SELECT id, seats, zone FROM tables WHERE status = 'available' ORDER BY seats ASC, id ASC");
    $tables = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tables[] = $row;
    }

    $bestCombo = [];
    $minOverflow = PHP_INT_MAX;
    $minCount = PHP_INT_MAX;

    $n = count($tables);
    $maxComboSize = min(5, $n); // Batas kombinasi maksimal

    for ($r = 1; $r <= $maxComboSize; $r++) {
        $combinations = combination($tables, $r);
        foreach ($combinations as $combo) {
            $totalSeats = array_sum(array_column($combo, 'seats'));
            if ($totalSeats < $guest_count) continue;

            $overflow = $totalSeats - $guest_count;
            $zones = array_unique(array_column($combo, 'zone'));
            $ids = array_column($combo, 'id');
            $idDiff = max($ids) - min($ids);

            $validGroup = (count($zones) == 1 || $idDiff <= $r);

            if ($validGroup && ($overflow < $minOverflow || ($overflow == $minOverflow && count($combo) < $minCount))) {
                $bestCombo = $combo;
                $minOverflow = $overflow;
                $minCount = count($combo);

                if ($overflow == 0) return $bestCombo;
            }
        }
    }

    return $bestCombo;
}

function combination($arr, $r) {
    $results = [];
    $n = count($arr);
    if ($r > $n) return $results;

    $indices = range(0, $r - 1);
    while (true) {
        $combo = [];
        foreach ($indices as $i) $combo[] = $arr[$i];
        $results[] = $combo;

        $i = $r - 1;
        while ($i >= 0 && $indices[$i] == $n - $r + $i) $i--;
        if ($i < 0) break;

        $indices[$i]++;
        for ($j = $i + 1; $j < $r; $j++) {
            $indices[$j] = $indices[$j - 1] + 1;
        }
    }

    return $results;
}
?>
