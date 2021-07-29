<?php
declare(strict_types=1);


namespace App\Exception;


use Throwable;

/**
 * Interface TodoAppExceptionInterface
 * @package App\Exception
 *
 * TodoApp上で発生した例外を認識するために作成したInterface
 * catch (TodoAppException|LogicException|RuntimeException) {}
 * とするところを、
 * catch (TodoAppExceptionInterface)と書くことができるようになる。それだけのもの。
 */
interface TodoAppExceptionInterface extends Throwable
{

}