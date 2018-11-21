<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
    <div class="inner page-inner">
        <h1 class="page-stitle">Акции</h1>
        <div class="akcii-calendar-wrap">
            <div class="akcii-calendar">

                <?
                $date = date("Y-m-d");

                //$date = "2018-06-10";

                $sd = explode("-", $date);
                $year = $sd[0];
                $month = $sd[1];
                $day = $sd[2];

                // Вычисляем число дней в текущем месяце
                $dayofmonth = date('t',
                    mktime(0, 0, 0, $month, 1, $year));


                // Счётчик для дней месяца
                $day_count = 1;

                // 1. Первая неделя
                $num = 0;
                for ($i = 0; $i < 7; $i++) {
// Вычисляем номер дня недели для числа
                    $dayofweek = date('w',
                        mktime(0, 0, 0, $month, $day_count, $year));
// Приводим к числа к формату 1 - понедельник, ..., 6 - суббота
                    $dayofweek = $dayofweek - 1;
                    if ($dayofweek == -1) $dayofweek = 6;

                    if ($dayofweek == $i) {
// Если дни недели совпадают,
// заполняем массив $week
// числами месяца
                        $week[$num][$i] = $day_count;
                        $day_count++;
                    } else {
                        $week[$num][$i] = "";
                    }
                }

                // 2. Последующие недели месяца
                while (true) {
                    $num++;
                    for ($i = 0; $i < 7; $i++) {
                        $week[$num][$i] = $day_count;
                        $day_count++;
// Если достигли конца месяца - выходим
// из цикла
                        if ($day_count > $dayofmonth) break;
                    }
// Если достигли конца месяца - выходим
// из цикла
                    if ($day_count > $dayofmonth) break;
                }

                $rusdays = array('ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС');
                $rusmonth = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');


                // 3. Выводим содержимое массива $week
                // в виде календаря
                //Заголовок
                echo '<div class="akcii-calendar-title">' . $rusmonth[$month - 1] . '</div>';
                echo '<div class="akcii-calendar-content-top">';
                foreach ($rusdays as $rusday) {
                    $we = '';
                    if (($rusday == 'СБ') || ($rusday == 'ВС')) $we = ' acb-title-we';
                    echo '<div class="akcii-calendar-bl acb-title ' . $we . '">' . $rusday . '</div>';
                }
                echo '</div>'; //akcii-calendar-content-top
                echo '<div class="akcii-calendar-content">';
                //тело календаря
                $future = false;
                for ($i = 0; $i < count($week); $i++) {
                    for ($j = 0; $j < 7; $j++) {
                        if (!empty($week[$i][$j])) {

                            // Если имеем дело с выбраной датой подсвечиваем ee
                            if ($week[$i][$j] == $day) {
                                echo '<div class="akcii-calendar-bl acb-active acb-cur">';
                                $future = true;
                            } else {
                                if($future) echo '<div class="akcii-calendar-bl acb-active">';
                                else echo '<div class="akcii-calendar-bl">';
                            }

                            // Если в есть акция на текущую дату то добавляем ссылку
                            if (false)//($d[$week[$i][$j]]) //Сделат провеку на наличе акций
                            {
                                //echo '<a href="/afisha/'.$d[$week[$i][$j]].'/">'.$week[$i][$j].'</a>';
                                echo '<span class="acb-badge">17<br>м</span><span class="acb-star"></span>'.$week[$i][$j];
                            }
                            else
                            {
                                echo $week[$i][$j];
                            }

                            echo '</div>'; //akcii-calendar-bl
                        } else echo '<div class="akcii-calendar-bl"> </div>'; //вышли за границы месяца
                    }
                }
                echo '</div>'; // akcii-calendar-content
                ?>
            </div> <!-- akcii-calendar -->
        <div class="akcii-calendar-legend">
            <div class="akcii-calendar-legend-1">— Специальное предложение</div>
            <div class="akcii-calendar-legend-2">— Выгодное время</div>
        </div>
    </div> <!-- akcii-calendar-wrap -->
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
