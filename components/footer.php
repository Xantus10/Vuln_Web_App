<p>Xantus10</p>
<p>
  <?php
    include_once("util.php");
    $day = date("d");
    $month = monthName(intval(date("m")));
    $year = date("Y");
    $weekday = dayName(date("N"));

    echo "Dnes je {$weekday} {$day}/{$month}/{$year} " . date("H:i:s");
  ?>
</p>
<a target="_blank" href="https://github.com/Xantus10/Vuln_Web_App"><p>Github</p></a>