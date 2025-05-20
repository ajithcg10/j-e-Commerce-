<?php

namespace App;

enum VedorStatusEnum : string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
