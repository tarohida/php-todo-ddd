<?php
declare(strict_types=1);


namespace App\Exception;


use Exception;

/**
 * Class TodoAppException
 * @package App\Exception
 *
 * TodoAppで発生する例外の基底クラス。
 * この例外を継承した例外が発生する可能性がある処理を呼び出した際は、例外のハンドリングを行わねばならない。
 */
abstract class TodoAppException extends Exception implements TodoAppExceptionInterface
{

}