<?php

namespace App\Documentation\Model;

/**
 * Class PeriodeCreate
 *
 * @OA\Schema(
 *     schema="PeriodeCreate",
 *     title="PeriodeCreate class",
 *     description="PeriodeCreate class",
 * )
 */
class PeriodeCreate
{
    /**
     * @OA\Property()
     *
     * @var string
     */
    private $type_periode;

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
     * @var string
     */
    private $periode;    
}
