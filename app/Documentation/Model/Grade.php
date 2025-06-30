<?php

namespace App\Documentation\Model;

/**
 * Class Grade
 *
 * @OA\Schema(
 *     schema="Grade",
 *     title="Grade class",
 *     description="Grade class",
 * )
 */
class Grade
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