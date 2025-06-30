<?php

namespace App\Documentation\Model;

/**
 * Class TypeprimeCreate
 *
 * @OA\Schema(
 *     schema="TypeprimeCreate",
 *     title="TypeprimeCreate class",
 *     description="TypeprimeCreate class",
 * )
 */
class TypeprimeCreate
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