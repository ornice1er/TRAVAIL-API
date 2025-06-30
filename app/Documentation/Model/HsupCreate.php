<?php

namespace App\Documentation\Model;

/**
 * Class HsupCreate
 *
 * @OA\Schema(
 *     schema="HsupCreate",
 *     title="HsupCreate class",
 *     description="HsupCreate class",
 * )
 */
class HsupCreate
{
    /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     * @var int
     */
    private $agent_id;
    
    /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property()
     *
     * @var date
     */
    private $nbHeure;
}
