<?php


namespace App\Entity;

/**
 * Interface ImportDatasInterface.
 */
interface ImportDatasInterface
{
    public static function createFromCsv(array $datas);
}