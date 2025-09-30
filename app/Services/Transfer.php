<?php

namespace App\Services;

use App\Repositories\TransferRepository;

class Transfer
{
    public function __construct(protected TransferRepository $transferRepo){}


}