<?php
/**
 * Created by PhpStorm.
 * User: lifepet
 * Date: 29/08/17
 * Time: 16:40
 */

namespace App\Models;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
	public static $rules = [];

	public $fillable = [
		'name',
		'display_name',
		'description',
		'menu'
	];
}