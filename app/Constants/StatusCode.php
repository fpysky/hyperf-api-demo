<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;

/**
 * http状态码
 * @method static getMessage($codeMsg)
 */
class StatusCode extends AbstractConstants
{
    /* -------------2xx------------ */

    /** @Message("ok") */
    public const OK = 200;

    /* -------------4xx------------ */

    /** @Message("Bad Request") */
    public const BAD_REQUEST = 400;

    /** @Message("unauthoried") */
    public const UNAUTHORIZED = 401;

    /** @Message("Forbidden") */
    public const FORBIDDEN = 403;

    /** @Message("not found") */
    public const NOT_FOUND = 404;

    /** @Message("method not allowed") */
    public const METHOD_NOT_ALLOWED = 405;

    /** @Message("unprocessable entity") */
    public const UNPROCESSABLE_ENTITY = 422;

    /* -------------5xx------------ */

    /** @Message("server error") */
    public const SERVER_ERROR = 500;

    /** @Message("bad gateway") */
    public const BAD_GATEWAY = 502;

    /** @Message("gateway timeout") */
    public const GATEWAY_TIMEOUT = 504;
}
