<?php

namespace App\Documentation\Model;

/**
 * Class CorpsCreate
 *
 * @OA\Schema(
 *     schema="CorpsCreate",
 *     title="CorpsCreate class",
 *     description="CorpsCreate class",
 * )
 */
class CorpsCreate
{
    /**
     * @OA\Property()
     *
     * @var string
     */
    private $libelle;

      /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     * @var string
     */
    private $code;
}
