<?php

namespace App\Documentation\Model;

/**
 * Class Joursferie
 *
 * @OA\Schema(
 *     schema="Joursferie",
 *     title="Joursferie class",
 *     description="Joursferie class",
 * )
 */
class Joursferie
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

     /**
     * @OA\Property()
     *
     * @var date
     */
    private $date;
}