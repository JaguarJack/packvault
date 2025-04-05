<?php
namespace App\Enums;

/**
 * 打包任务状态
 */
enum BuildJobStatus : int {
    case UN_START = 0;
    case RUNNING = 1;
    case SUCCESS = 2;
    case FAILED = 3;
}
