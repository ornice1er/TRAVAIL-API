<?php

namespace App\Documentation\Model;

/**
 * Class AgentCreate
 *
 * @OA\Schema(
 *     schema="AgentCreate",
 *     title="AgentCreate class",
 *     description="AgentCreate class",
 * )
 */
class AgentCreate
{
    /**
     * @OA\Property()
     *
     * @var string
     */
    private $identite;
     /**
     * @OA\Property()
     *
     * @var string
     */
    private $code;
}
