<?php

namespace App\Documentation\Model;

/**
 * Class Notationagent
 *
 * @OA\Schema(
 *     schema="Notationagent",
 *     title="Notationagent class",
 *     description="Notationagent class",
 * )
 */
class Notationagent
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
     * @var int
     */
    private $periode_id;

    /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     * @var string
     */
    private $note;
 
}