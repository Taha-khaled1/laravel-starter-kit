<?php

namespace App\Enums;

enum TypeUserEnum: string
{
  case SUPERVISOR = 'supervisor';
  case ADMIN = 'admin';
  case USER = 'user';
}
