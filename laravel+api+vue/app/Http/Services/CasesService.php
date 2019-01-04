<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 04.01.19
 * Time: 10:34
 */

namespace App\Http\Services;


use App\Http\Repositories\CasesRepository;
use App\Http\Repositories\UsersRepository;

class CasesService extends BaseService
{
    protected $casesRepository;
    protected $usersRepository;

    public function __construct(CasesRepository $casesRepository, UsersRepository $usersRepository)
    {
        $this->casesRepository = $casesRepository;
        $this->usersRepository = $usersRepository;
    }

    public function getCaseWithUsers($id): array
    {
        $case = $this->casesRepository->getCaseWithCards($id);
        $users = $this->usersRepository->getAll();
        return ['case' => $case, 'users' => $users];
    }
}
