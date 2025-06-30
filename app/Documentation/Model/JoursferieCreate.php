<?php

namespace App\Documentation\Model;

/**
 * Class JoursferieCreate
 *
 * @OA\Schema(
 *     schema="JoursferieCreate",
 *     title="JoursferieCreate class",
 *     description="JoursferieCreate class",
 * )
 */
class JoursferieCreate
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
