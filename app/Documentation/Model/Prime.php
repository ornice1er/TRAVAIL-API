<?php

namespace App\Documentation\Model;

/**
 * Class Prime
 *
 * @OA\Schema(
 *     schema="Prime",
 *     title="Prime class",
 *     description="Prime class",
 * )
 */
class Prime
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