<?php

function timeAgo($datetime, $full = false)
{
  $now = new DateTime();
  $createdTime = new DateTime($datetime);
  $interval = $now->diff($createdTime);

  $units = [
    'ano(s)' => $interval->y,
    'mes(es)' => $interval->m,
    'semana(s)' => floor($interval->d / 7),
    'dia(s)' => $interval->d % 7,
    'hora(s)' => $interval->h,
    'minuto(s)' => $interval->i,
    'segundo(s)' => $interval->s,
  ];

  foreach ($units as $unit => $value) {
    if ($value) {
      return "Há " . $value . " " . $unit . ($value > 1 ? '' : '');
    }
  }

  return 'agora';
}
