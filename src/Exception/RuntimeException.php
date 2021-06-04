<?php
declare(strict_types=1);


namespace App\Exception;

/**
 * Class RuntimeException
 * @package App\Exception
 *
 * これを継承する例外はハンドリングする必要がない。
 * プログラマが予期して発生させたRuntimeExceptionと、予期せず発生したRuntimeException(\RuntimeException)を区別するために作成
 */
abstract class RuntimeException extends \RuntimeException implements TodoAppExceptionInterface
{
    /**
     * ログに出力するための詳細な情報を取得する
     *
     * @return string
     */
    abstract public function getLoggingMessage(): string;

}