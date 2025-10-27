<?php
// ====== Лабораторная работа №9. Вариант 6 ======
$studentName = "Никаева Марьям Руслановна"; // ФИО
$group       = "241-362";               // Группа
$variant     = "№6";                      // Вариант

// Параметры табулирования (можно менять из формы)
$x_start   = isset($_GET['x_start']) ? floatval($_GET['x_start']) : -10; //начальное значение аргумента функции
$step      = isset($_GET['step']) ? floatval($_GET['step']) : 2;  // шаг изменения значения 
$count     = isset($_GET['count']) ? intval($_GET['count']) : 20; //количество вычисляемых значений функции
$min_stop  = isset($_GET['min_stop']) ? floatval($_GET['min_stop']) : -1e9; // минимальное значение функции
$max_stop  = isset($_GET['max_stop']) ? floatval($_GET['max_stop']) :  1e9; // максимальное значение функции
$layoutType= isset($_GET['layout']) ? strtoupper($_GET['layout']) : 'A'; // A|B|C|D|E

$precision = 3; // количество знаков после запятой

/**
 * Вариант 6:
 *  f(x) = { x^2 * 0.33 + 4,    при x <= 10
 *         { 18 * x - 3,        при 10 < x < 20
 *         { 1 / (x * 0.1 - 2), при x >= 20
 * При делении на ноль → 'error'.
 */

function computeF($x){
    if ($x <= 10){
        return $x*$x*0.33 + 4;
    } elseif ($x < 20){
        return 18*$x - 3;
    } else { // x >= 20
        $den = $x*0.1 - 2.0; // =0 при x=20
        if (abs($den) < 1e-12) return 'error';
        return 1.0/$den;
    }
}

// функция округления или pass-through для 'error'
function roundOrError($v, $precision){
    return ($v === 'error') ? 'error' : round($v, $precision);
}

// Подготовка данных
$rows = [];  // [ [i, x, f] ... ]
$x = $x_start;
$sum = 0.0; $cnt = 0;
$minVal = null; $maxVal = null;
for($i=0; $i < $count; $i++, $x += $step){
    $f = computeF($x);
    $fRounded = roundOrError($f, $precision);

    // Остановка по диапазону значений
    if ($f !== 'error'){
        if ($f >= $max_stop || $f < $min_stop){
            $rows[] = [$i+1, $x, $fRounded];
            // учитываем в статистике только числовые значения
            $sum += $f; $cnt++;
            $minVal = is_null($minVal) ? $f : min($minVal, $f);
            $maxVal = is_null($maxVal) ? $f : max($maxVal, $f);
            break;
        }
    }

    $rows[] = [$i+1, $x, $fRounded];
    if ($f !== 'error'){
        $sum += $f; $cnt++;
        $minVal = is_null($minVal) ? $f : min($minVal, $f);
        $maxVal = is_null($maxVal) ? $f : max($maxVal, $f);
    }
}

$avg = $cnt ? ($sum/$cnt) : null;
$sumRounded = $cnt ? round($sum, $precision) : "—";
$minRounded = is_null($minVal) ? "—" : round($minVal, $precision);
$maxRounded = is_null($maxVal) ? "—" : round($maxVal, $precision);
$avgRounded = is_null($avg) ? "—" : round($avg, $precision);

// функция вывода строки "f(x)=y"
function lineFmt($x,$f){
    $xStr = rtrim(rtrim(number_format($x, 6, '.', ''), '0'), '.');
    return "f($xStr)=" . (is_string($f) ? "<span class='error'>error</span>" : $f);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ЛР9 · Вариант <?=htmlspecialchars($variant)?> · <?=htmlspecialchars($studentName)?> (<?=htmlspecialchars($group)?>)</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <img src="images/MPU_logo.png" alt="Логотип">
  <div>
    <h1>Лабораторная работа №9 — Основы алгоритмов</h1>
    <div class="title-sub">Вариант <?=htmlspecialchars($variant)?> • <?=htmlspecialchars($studentName)?> • <?=htmlspecialchars($group)?></div>
  </div>
</header>

<main>
  <div class="controls">
    <form method="get">
      <div>
        <label>Начальное x</label>
        <input type="number" step="any" name="x_start" value="<?=htmlspecialchars($x_start)?>">
      </div>
      <div>
        <label>Шаг</label>
        <input type="number" step="any" name="step" value="<?=htmlspecialchars($step)?>">
      </div>
      <div>
        <label>Количество точек</label>
        <input type="number" name="count" min="1" value="<?=htmlspecialchars($count)?>">
      </div>
      <div>
        <label>Мин. порог f (останов)</label>
        <input type="number" step="any" name="min_stop" value="<?=htmlspecialchars($min_stop)?>">
      </div>
      <div>
        <label>Макс. порог f (останов)</label>
        <input type="number" step="any" name="max_stop" value="<?=htmlspecialchars($max_stop)?>">
      </div>
      <div>
        <label>Тип верстки</label>
        <select name="layout">
          <?php foreach (['A','B','C','D','E'] as $t): ?>
          <option value="<?=$t?>" <?=$layoutType===$t?'selected':''?>><?=$t?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>&nbsp;</label>
        <button type="submit">Пересчитать</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h3>Результаты</h3>
    <?php if ($layoutType==='A'): ?>
      <?php foreach ($rows as $row): ?>
        <?php echo lineFmt($row[1], $row[2]); if ($row !== end($rows)) echo "<br>"; ?>
      <?php endforeach; ?>
    <?php elseif ($layoutType==='B'): ?>
      <ul>
      <?php foreach ($rows as $row): ?>
        <li><?php echo lineFmt($row[1], $row[2]); ?></li>
      <?php endforeach; ?>
      </ul>
    <?php elseif ($layoutType==='C'): ?>
      <ol>
      <?php foreach ($rows as $row): ?>
        <li><?php echo lineFmt($row[1], $row[2]); ?></li>
      <?php endforeach; ?>
      </ol>
    <?php elseif ($layoutType==='D'): ?>
      <table class="table">
        <thead><tr><th>#</th><th>x</th><th>f(x)</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?=htmlspecialchars($row[0])?></td>
            <td><?=htmlspecialchars($row[1])?></td>
            <td><?=is_string($row[2]) ? "<span class='error'>error</span>" : htmlspecialchars($row[2])?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: /* E */ ?>
      <?php foreach ($rows as $row): ?>
        <div class="block-row"><?php echo lineFmt($row[1], $row[2]); ?></div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="card stats">
    <div class="stat"><b>Сумма</b><span><?=htmlspecialchars($sumRounded)?></span></div>
    <div class="stat"><b>Минимум</b><span><?=is_string($minRounded)?$minRounded:htmlspecialchars($minRounded)?></span></div>
    <div class="stat"><b>Максимум</b><span><?=is_string($maxRounded)?$maxRounded:htmlspecialchars($maxRounded)?></span></div>
    <div class="stat"><b>Среднее</b><span><?=is_string($avgRounded)?$avgRounded:htmlspecialchars($avgRounded)?></span></div>
  </div>
</main>

<footer>
  <div class="inner">
    <div>Тип верстки: <strong><?=htmlspecialchars($layoutType)?></strong></div>
  </div>
</footer>
</body>
</html>
