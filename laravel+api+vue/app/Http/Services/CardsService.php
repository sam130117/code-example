<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 04.01.19
 * Time: 10:30
 */

namespace App\Http\Services;


use App\Http\Repositories\CardsRepository;

class CardsService extends BaseService
{
    protected $cardsRepository;

    public function __construct(CardsRepository $cardsRepository)
    {
        $this->cardsRepository = $cardsRepository;
    }
}
