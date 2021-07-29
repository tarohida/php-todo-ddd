<?php
declare(strict_types=1);


namespace App\Exception;


/**
 * Class LogicException
 * @package App\Exception
 *
 * これを継承する例外はハンドリングする必要がない。
 * プログラマーが意図的に発生させたLogicExceptionと、意図せず発生したLogicException(\LogicException)を区別するために作成
 */
class LogicException extends \LogicException implements TodoAppExceptionInterface
{

}