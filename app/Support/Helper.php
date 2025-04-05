<?php
namespace App\Support;

use Illuminate\Support\Facades\Http;

class Helper
{
    /**
     * packages
     *
     * @param string $string
     * @return string
     */
    public static function packagePath(string $string): string
    {
        $path = storage_path('packages'. DIRECTORY_SEPARATOR . $string);

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    /**
     * packages json
     *
     * @param string $path
     * @return array
     */
    public function packagesJson(string $path): array
    {
        if (file_exists(self::packagePath($path))) {
            return json_decode(file_get_contents(self::packagePath($path)), true);
        }

        return [];
    }


    /**
     * 检查包名是否在 Packagist 中，不允许冲突
     *
     * @param $name
     * @return bool
     */
    public static function checkPackageInPackagist($name): bool
    {
        try {
            return Http::get('https://packagist.org/search.json', [
                'q' => $name,
            ])->json('total') > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
