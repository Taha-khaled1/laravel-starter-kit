<?php

namespace App\Enums;

enum EventTypeEnum: string
{
  // نوع الفعالية
  case GROUPING = 'grouping';
  case MOVEMENT = 'movement';
  case DEPARTURE = 'departure';
}
