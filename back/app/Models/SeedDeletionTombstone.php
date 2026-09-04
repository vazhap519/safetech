<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Records an administrator's intentional removal of a canonical seed record.
 *
 * Seeders are run during every production deployment. Without this small
 * durable marker, firstOrCreate()/firstOrNew() would recreate a record that an
 * administrator had deliberately deleted from the CMS.
 */
class SeedDeletionTombstone extends Model
{
    protected $guarded = ['id'];
}
