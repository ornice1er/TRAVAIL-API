<?php

namespace App\Documentation\Model;

/**
 * Class Primestatut
 *
 * @OA\Schema(
 *     schema="Primestatut",
 *     title="Primestatut class",
 *     description="Primestatut class",
 * )
 */
class Primestatut
{
    /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     * @var int
     */
    private $statut_id;

    /**
     * @OA\Property()
     *
     * @var int
     */
    private $prime_id;
}