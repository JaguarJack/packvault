<?php
namespace App\Enums;

/**
 * 打包任务状态
 */
enum SubscribeType : string {
    case YEAR = 'year';
    case FOREVER = 'forever';


    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::YEAR => '年付订阅',
            self::FOREVER => '永久订阅',
        };
    }

}
