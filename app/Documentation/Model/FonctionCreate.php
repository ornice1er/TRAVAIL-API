<?php

namespace App\Documentation\Model;

/**
 * Class FonctionCreate
 *
 * @OA\Schema(
 *     schema="FonctionCreate",
 *     title="FonctionCreate class",
 *     description="FonctionCreate class",
 * )
 */
class FonctionCreate
{
     /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     */
    private $id;

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
