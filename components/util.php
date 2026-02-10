<?php

function monthName(int $month) : string {
  switch ($month) {
    case 1:
      return "leden";
    case 2:
      return "únor";
    case 3:
      return "březen";
    case 4:
      return "duben";
    case 5:
      return "květen";
    case 6:
      return "červen";
    case 7:
      return "červenec";
    case 8:
      return "srpen";
    case 9:
      return "září";
    case 10:
      return "říjen";
    case 11:
      return "listopad";
    case 12:
      return "prosinec";
  }
  return "";
}

function dayName(int $day) : string {
  switch ($day) {
    case 1:
      return "pondělí";
    case 2:
      return "úterý";
    case 3:
      return "středa";
    case 4:
      return "čtvrtek";
    case 5:
      return "pátek";
    case 6:
      return "sobota";
    case 7:
      return "neděle";
  }
  return "";
}

?>