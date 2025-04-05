<?php
namespace App\Enums;

/**
 * 状态
 */
enum Status : int {
    case DISABLE = 0;
    case ENABLE = 1;


    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ENABLE => '开启',
            self::DISABLE => '关闭',
        };
    }

}
