<?php

namespace App\Documentation\Model;

/**
 * Class GradeCreate
 *
 * @OA\Schema(
 *     schema="GradeCreate",
 *     title="GradeCreate class",
 *     description="GradeCreate class",
 * )
 */
class GradeCreate
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
