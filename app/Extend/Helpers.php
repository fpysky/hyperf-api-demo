<?php

declare(strict_types=1);

if (! function_exists('isProd')) {
    /**
     * 判断是否是生产环境.
     * @return bool
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
     */
    function isProd(): bool
    {
        return env('APP_ENV') == 'prod';
    }
}
