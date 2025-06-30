<?php

namespace App\Documentation\Model;

/**
 * Class TypeacteCreate
 *
 * @OA\Schema(
 *     schema="TypeacteCreate",
 *     title="TypeacteCreate class",
 *     description="TypeacteCreate class",
 * )
 */
class TypeacteCreate
{
    /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     * @var string
     */
    private $code;

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $libelle;
}